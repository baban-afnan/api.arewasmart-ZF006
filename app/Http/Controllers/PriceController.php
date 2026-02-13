<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceField;
use App\Models\SmeData;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PriceController extends Controller
{
    /**
     * Display a unified table of service prices and commissions.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role ?? 'user';

        // 1. Airtime
        $airtimePrices = collect($this->getAirtimeCommissions($role));
        $airtimePaginator = $this->paginate($airtimePrices, 10, 'airtime_page');

        // 2. Data Bundles (Flattening nested plans for pagination)
        $dataPlans = collect();
        foreach ($this->getDataCommissions($role) as $group) {
            foreach ($group['plans'] as $plan) {
                // Attach group context to each row
                $plan->network_name = $group['network'];
                $plan->network_status = $group['status'];
                $plan->network_commission = $group['commission'] ?? 0;
                $dataPlans->push($plan);
            }
        }
        $dataPaginator = $this->paginate($dataPlans, 10, 'data_page');

        // 3. SME Data (Flattening nested plans)
        $smePlans = collect();
        foreach ($this->getSmePrices($role) as $group) {
            foreach ($group['plans'] as $plan) {
                $plan->network_name = $group['network'];
                $smePlans->push($plan);
            }
        }
        $smePaginator = $this->paginate($smePlans, 10, 'sme_page');

        // 4. Verification
        $verificationPrices = collect($this->getVerificationPrices($role));
        $verifyPaginator = $this->paginate($verificationPrices, 10, 'verify_page');

        // 5. Modifications (Flattening nested category plans)
        $modPlans = collect();
        foreach ($this->getModificationPrices($role) as $group) {
            foreach ($group['plans'] as $plan) {
                $plan->category_name = $group['category'];
                $modPlans->push($plan);
            }
        }
        $modifyPaginator = $this->paginate($modPlans, 10, 'modify_page');

        return view('prices.index', compact(
            'user',
            'airtimePaginator',
            'dataPaginator',
            'smePaginator',
            'verifyPaginator',
            'modifyPaginator'
        ));
    }

    /**
     * Helper to create LengthAwarePaginator from Collection
     */
    private function paginate(Collection $items, $perPage = 10, $pageName = 'page')
    {
        $page = request()->get($pageName, 1);
        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
                'pageName' => $pageName
            ]
        );
    }

    private function getAirtimeCommissions($role)
    {
        $service = Service::where('name', 'Airtime')->first();
        $prices = [];

        if ($service) {
            $networks = [
                '101' => 'MTN',
                '100' => 'Airtel',
                '102' => 'Glo',
                '103' => '9mobile'
            ];

            foreach ($networks as $code => $network) {
                $field = $service->fields()->where('field_code', $code)->first();
                $commission = 0;
                $status = 0;

                if ($field) {
                    $priceObj = DB::table('service_prices')
                        ->where('service_fields_id', $field->id)
                        ->where('user_type', $role)
                        ->first();
                    
                    $commission = $priceObj ? $priceObj->price : ($field->base_price ?? 0);
                    $status = $field->is_active;
                }

                $prices[] = [
                    'network' => $network,
                    'commission' => $commission,
                    'status' => $status
                ];
            }
        }

        return $prices;
    }

    private function getDataCommissions($role)
    {
        $service = Service::where('name', 'Data')->first();
        $groups = [];

        if ($service) {
            $networks = [
                '104' => 'MTN',
                '105' => 'Airtel',
                '106' => 'Glo',
                '107' => '9mobile'
            ];

            foreach ($networks as $code => $network) {
                $field = $service->fields()->where('field_code', $code)->first();
                if (!$field) continue;

                $priceObj = DB::table('service_prices')
                    ->where('service_fields_id', $field->id)
                    ->where('user_type', $role)
                    ->first();
                
                $commission = $priceObj ? $priceObj->price : ($field->base_price ?? 0);

                // Map standard database network codes to variation network codes if different
                $variationServiceId = strtolower($network) . '-data';
                if ($network === '9mobile') $variationServiceId = 'etisalat-data';

                $plans = DB::table('data_variations')
                    ->where('service_id', $variationServiceId)
                    ->get();

                if ($plans->isNotEmpty()) {
                    $groups[] = [
                        'network' => $network,
                        'commission' => $commission,
                        'status' => $field->is_active,
                        'plans' => $plans
                    ];
                }
            }
        }

        return $groups;
    }

    private function getSmePrices($role)
    {
        $networks = ['MTN', 'AIRTEL', 'GLO', '9MOBILE'];
        $groups = [];

        foreach ($networks as $network) {
            $plans = SmeData::where('network', $network)->get();

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
            if ($field) {
                $services[] = [
                    'name' => 'NIN Verification',
                    'code' => '610',
                    'price' => $field->getPriceForUserType($role),
                    'type' => 'Verification',
                    'status' => $field->is_active
                ];
            }
            
            // BVN Verification (600)
            $field = $ninService->fields()->where('field_code', '600')->first();
            if ($field) {
                $services[] = [
                    'name' => 'BVN Verification',
                    'code' => '600',
                    'price' => $field->getPriceForUserType($role),
                    'type' => 'Verification',
                    'status' => $field->is_active
                ];
            }
        }

        // NIN Validation (015)
        $validationService = Service::where('name', 'Validation')->first();
        if ($validationService) {
            $field = $validationService->fields()->where('field_code', '015')->first();
            if ($field) {
                $services[] = [
                    'name' => 'NIN Validation',
                    'code' => '015',
                    'price' => $field->getPriceForUserType($role),
                    'type' => 'Validation',
                    'status' => $field->is_active
                ];
            }
        }

        // IPE (002)
        $ipeService = Service::where('name', 'IPE')->first();
        if ($ipeService) {
            $field = $ipeService->fields()->where('field_code', '002')->first();
            if ($field) {
                $services[] = [
                    'name' => 'IPE Tracking',
                    'code' => '002',
                    'price' => $field->getPriceForUserType($role),
                    'type' => 'IPE',
                    'status' => $field->is_active
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
                ->get();

            if ($fields->isNotEmpty()) {
                $plans = [];
                foreach ($fields as $field) {
                    $plans[] = (object)[
                        'name' => $field->field_name,
                        'code' => $field->field_code,
                        'price' => $field->getPriceForUserType($role),
                        'status' => $field->is_active
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
            $fields = ServiceField::whereIn('field_code', $codes)->get();

            if ($fields->isNotEmpty()) {
                $plans = [];
                foreach ($fields as $field) {
                    $plans[] = (object)[
                        'name' => $field->field_name,
                        'code' => $field->field_code,
                        'price' => $field->getPriceForUserType($role),
                        'status' => $field->is_active
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
