<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', Auth::id());
    

        // Date Filtering Logic (Default to this month)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = \Carbon\Carbon::parse($request->start_date)->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();
        } else {
            $startDate = \Carbon\Carbon::now()->startOfMonth();
            $endDate = \Carbon\Carbon::now()->endOfMonth();
        }
        $query->whereBetween('created_at', [$startDate, $endDate]);

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Calculate Totals based on current filters
        $totalCredit = $query->clone()->where('type', 'credit')->sum('amount');
        $totalDebit = $query->clone()->where('type', 'debit')->sum('amount');
        $totalRefund = $query->clone()->where('type', 'refund')->sum('amount');
        $totalBonus = $query->clone()->where('type', 'bonus')->sum('amount');

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('pages.dashboard.transactions', compact(
            'transactions',
            'totalCredit',
            'totalDebit',
            'totalRefund',
            'totalBonus',
            'startDate',
            'endDate'
        ));
    }
}
