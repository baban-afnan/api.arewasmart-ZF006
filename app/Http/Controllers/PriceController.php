<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceField;
use App\Models\SmeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PriceController extends Controller
{
    /**
     * Display a unified table of service prices and commissions.
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role ?? 'user';

        // 1. Airtime & Data Bundles (Commissions)
        $airtimePrices = $this->getAirtimeCommissions($role);
        $dataGroups = $this->getDataCommissions($role);

        // 2. SME Data (Fixed Prices)
        $smeGroups = $this->getSmePrices($role);

        // 3. Verification & Validation (Fixed Prices)
        $verificationPrices = $this->getVerificationPrices($role);

        // 4. Modification Services (Fixed Prices)
        $modificationGroups = $this->getModificationPrices($role);

        return view('prices.index', compact(
            'user',
            'airtimePrices',
            'dataGroups',
            'smeGroups',
            'verificationPrices',
            'modificationGroups'
        ));
    }

    private function getAirtimeCommissions($role)
    {
        $networks = ['MTN', 'AIRTEL', 'GLO', '9MOBILE'];
        $prices = [];

        foreach ($networks as $network) {
            $service = Service::where('name', $network . ' Airtime')->first();
            $commission = 0;

            if ($service) {
                // Assuming airtime commission is stored in a service price or field
                // Based on standard implementation, we look for cashback/commission
                $commission = DB::table('service_prices')
                    ->where('service_id', $service->id)
                    ->where('user_type', $role)
                    ->value('commission') ?? 0;
            }

            $prices[] = [
                'network' => $network,
                'commission' => $commission,
            ];
        }

        return $prices;
    }

    private function getDataCommissions($role)
    {
        $networks = ['MTN', 'AIRTEL', 'GLO', '9MOBILE'];
        $groups = [];

        foreach ($networks as $network) {
            $service = Service::where('name', $network . ' Data')->first();
            if (!$service) continue;

            $commission = DB::table('service_prices')
                ->where('service_id', $service->id)
                ->where('user_type', $role)
                ->value('commission') ?? 0;

            $plans = DB::table('data_variations')
                ->where('service_id', $service->id)
                ->where('is_active', 1)
                ->get();

            if ($plans->isNotEmpty()) {
                $groups[] = [
                    'network' => $network,
                    'commission' => $commission,
                    'plans' => $plans
                ];
            }
        }

        return $groups;
    }

    private function getSmePrices($role)
    {
        $networks = ['MTN', 'AIRTEL', 'GLO', '9MOBILE'];
        $groups = [];

        foreach ($networks as $network) {
            $plans = SmeData::where('network', $network)
                ->where('status', 'enabled')
                ->get();

            if ($plans->isNotEmpty()) {
                foreach ($plans as $plan) {
                    // Calculate dynamic price: Base + Fee + Role Markup
                    $plan->total_price = $plan->calculatePriceForRole($role);
                }

                $groups[] = [
                    'network' => $network,
                    'plans' => $plans
                ];
            }
        }

        return $groups;
    }

    private function getVerificationPrices($role)
    {
        $services = [];
        
        // NIN Verification (610)
        $ninService = Service::where('name', 'Verification')->first();
        if ($ninService) {
            $field = $ninService->fields()->where('field_code', '610')->first();
            if ($field && $field->is_active) {
                $services[] = [
                    'name' => 'NIN Verification',
                    'code' => '610',
                    'price' => $field->getPriceForUserType($role),
                    'type' => 'Verification'
                ];
            }
            
            // BVN Verification (600)
            $field = $ninService->fields()->where('field_code', '600')->first();
            if ($field && $field->is_active) {
                $services[] = [
                    'name' => 'BVN Verification',
                    'code' => '600',
                    'price' => $field->getPriceForUserType($role),
                    'type' => 'Verification'
                ];
            }
        }

        // NIN Validation (015)
        $validationService = Service::where('name', 'Validation')->first();
        if ($validationService) {
            $field = $validationService->fields()->where('field_code', '015')->first();
            if ($field && $field->is_active) {
                $services[] = [
                    'name' => 'NIN Validation',
                    'code' => '015',
                    'price' => $field->getPriceForUserType($role),
                    'type' => 'Validation'
                ];
            }
        }

        // IPE (002)
        $ipeService = Service::where('name', 'IPE')->first();
        if ($ipeService) {
            $field = $ipeService->fields()->where('field_code', '002')->first();
            if ($field && $field->is_active) {
                $services[] = [
                    'name' => 'IPE Tracking',
                    'code' => '002',
                    'price' => $field->getPriceForUserType($role),
                    'type' => 'IPE'
                ];
            }
        }

        return $services;
    }

    private function getModificationPrices($role)
    {
        $groups = [];

        // 1. NIN Modification
        $ninModService = Service::where('name', 'LIKE', '%NIN Modification%')->first();
        if ($ninModService) {
            $targetCodes = ['032', '033', '034', '035', '037'];
            $fields = $ninModService->fields()
                ->whereIn('field_code', $targetCodes)
                ->where('is_active', 1)
                ->get();

            if ($fields->isNotEmpty()) {
                $plans = [];
                foreach ($fields as $field) {
                    $plans[] = (object)[
                        'name' => $field->field_name,
                        'code' => $field->field_code,
                        'price' => $field->getPriceForUserType($role)
                    ];
                }
                $groups[] = [
                    'category' => 'NIN Modification',
                    'plans' => $plans
                ];
            }
        }

        // 2. BVN Modification (Keystone, FirstBank, Agency)
        $bvnModCodes = [
            'Keystone' => ['67', '68', '69', '70', '71', '72', '73'],
            'First Bank' => ['003', '004', '005', '006', '007', '008', '009', '010', '060', '050'],
            'Agency' => ['022', '023', '024', '025', '026', '027', '028', '66']
        ];

        foreach ($bvnModCodes as $bank => $codes) {
            $fields = ServiceField::whereIn('field_code', $codes)
                ->where('is_active', 1)
                ->get();

            if ($fields->isNotEmpty()) {
                $plans = [];
                foreach ($fields as $field) {
                    $plans[] = (object)[
                        'name' => $field->field_name,
                        'code' => $field->field_code,
                        'price' => $field->getPriceForUserType($role)
                    ];
                }
                $groups[] = [
                    'category' => "BVN Modification ($bank)",
                    'plans' => $plans
                ];
            }
        }

        return $groups;
    }
}
