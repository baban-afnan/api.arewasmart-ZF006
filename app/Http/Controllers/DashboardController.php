<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\User;
use App\Models\BonusHistory;
use App\Models\VirtualAccount;
use App\Models\Transaction;
use App\Models\AgentService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        $virtualAccount = VirtualAccount::where('user_id', $user->id)->first();
        $bonusHistory = BonusHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Date Filtering Logic
        $isFiltered = $request->has('start_date') && $request->has('end_date');

        if ($isFiltered) {
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        } else {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        // 1. Total Transaction Amount (Debit)
        $totalTransactionAmount = Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('trans_source', 'api')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // 2. Total Agency Requests
        $totalAgencyRequests = AgentService::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // 3. Total Funded Amount (Credit)
        $totalFundedAmount = Transaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // 4. Total Referrals
        // Default to All Time if not filtered, otherwise use filter range
        $referralQuery = BonusHistory::where('user_id', $user->id);
        
        if ($isFiltered) {
            $referralQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $totalReferrals = $referralQuery->count();

        // 5. Recent 10 Transactions
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->latest()
            ->where('trans_source', 'api')
            ->take(10)
            ->get();


        // 6. Transaction Statistics
        $transactionStats = Transaction::where('user_id', $user->id)
            ->where('trans_source', 'api')
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when status = 'completed' then 1 end) as completed")
            ->selectRaw("count(case when status = 'pending' then 1 end) as pending")
            ->selectRaw("count(case when status = 'failed' then 1 end) as failed")
            ->first();

        $totalTransactions = $transactionStats->total ?? 0;
        $completedTransactions = $transactionStats->completed ?? 0;
        $pendingTransactions = $transactionStats->pending ?? 0;
        $failedTransactions = $transactionStats->failed ?? 0;

        // Calculate percentages
        $completedPercentage = $totalTransactions > 0 ? round(($completedTransactions / $totalTransactions) * 100) : 0;
        $pendingPercentage = $totalTransactions > 0 ? round(($pendingTransactions / $totalTransactions) * 100) : 0;
        $failedPercentage = $totalTransactions > 0 ? round(($failedTransactions / $totalTransactions) * 100) : 0;

        // --- NEW: API User Statistics ---
        $statusCounts = [];
        $monthlyStats = [];
        $isApiUser = !empty($user->api_token);

        if ($isApiUser) {
            // 1. Status Counts (AgentService)
            $statusCountsRaw = AgentService::where('user_id', $user->id)
                ->selectRaw("count(case when status = 'pending' then 1 end) as pending")
                ->selectRaw("count(case when status = 'processing' then 1 end) as processing")
                ->selectRaw("count(case when status = 'resolved' or status = 'successful' or status = 'completed' then 1 end) as resolved")
                ->selectRaw("count(case when status = 'rejected' or status = 'failed' then 1 end) as rejected")
                ->first();
            
            $statusCounts = [
                'pending' => $statusCountsRaw->pending ?? 0,
                'processing' => $statusCountsRaw->processing ?? 0,
                'resolved' => $statusCountsRaw->resolved ?? 0,
                'rejected' => $statusCountsRaw->rejected ?? 0,
            ];

            // 2. Monthly Verification Counts (Verification Table)
            // NIN (610), BVN (600), TIN (800, 801)
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            $monthlyVerifications = \App\Models\Verification::where('user_id', $user->id)
                ->whereBetween('verifications.created_at', [$startOfMonth, $endOfMonth])
                ->join('service_fields', 'verifications.service_field_id', '=', 'service_fields.id')
                ->selectRaw("count(case when service_fields.field_code = '610' then 1 end) as nin")
                ->selectRaw("count(case when service_fields.field_code = '600' then 1 end) as bvn")
                ->selectRaw("count(case when service_fields.field_code = '800' or service_fields.field_code = '801' then 1 end) as tin")
                ->first();

            $monthlyStats['nin'] = $monthlyVerifications->nin ?? 0;
            $monthlyStats['bvn'] = $monthlyVerifications->bvn ?? 0;
            $monthlyStats['tin'] = $monthlyVerifications->tin ?? 0;

            // 3. Monthly Agency Services (AgentService Table)
            // Validation (015), IPE (002), NIN Modify (Contains 'Modification')
            $monthlyAgency = AgentService::where('user_id', $user->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->selectRaw("count(case when field_code like '%015%' or service_type = 'NIN_VALIDATION' then 1 end) as validation")
                ->selectRaw("count(case when field_code like '%002%' or service_type = 'IPE' then 1 end) as ipe")
                ->selectRaw("count(case when (service_field_name like '%NIN%' and service_field_name like '%Modification%') or (description like '%NIN%' and description like '%Modification%') or service_type = 'NIN MODIFICATION' then 1 end) as nin_modification")
                ->selectRaw("count(case when (service_field_name like '%BVN%' and service_field_name like '%Modification%') or (description like '%BVN%' and description like '%Modification%') or service_type = 'BVN MODIFICATION' then 1 end) as bvn_modification")
                ->first();

            $monthlyStats['validation'] = $monthlyAgency->validation ?? 0;
            $monthlyStats['ipe'] = $monthlyAgency->ipe ?? 0;
            $monthlyStats['nin_modification'] = $monthlyAgency->nin_modification ?? 0;
            $monthlyStats['bvn_modification'] = $monthlyAgency->bvn_modification ?? 0;

            // 4. Bonus/Commission Total (type = 'bonus')
            $monthlyStats['bonus_total'] = Transaction::where('user_id', $user->id)
                ->where('type', 'bonus')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            // Aggregated for UI Cards
            $monthlyStats['total_verifications'] = $monthlyStats['nin'] + $monthlyStats['bvn'] + $monthlyStats['tin'];
            $monthlyStats['total_validation_ipe'] = $monthlyStats['validation'] + $monthlyStats['ipe']; 
        }

        $application = $user->apiApplication;

        return view('dashboard', compact(
            'user', 
            'wallet', 
            'virtualAccount', 
            'bonusHistory',
            'totalTransactionAmount',
            'totalAgencyRequests',
            'totalFundedAmount',
            'totalReferrals',
            'isFiltered',
            'startDate',
            'endDate',
            'recentTransactions',
            'totalTransactions',
            'completedTransactions',
            'pendingTransactions',
            'failedTransactions',
            'completedPercentage',
            'pendingPercentage',
            'failedPercentage',
            'statusCounts',
            'monthlyStats',
            'application',
            'isApiUser'
        ));
    }
}
