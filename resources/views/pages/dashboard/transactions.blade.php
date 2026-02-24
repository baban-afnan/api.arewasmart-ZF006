<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'Transactions' }}</title>
    
    <div class="row g-3 mb-4">
        <!-- Total Credit -->
        <div class="col-6 col-md-3">
            <div class="card bg-success-subtle border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold mb-2">Total Credit</h6>
                    <h4 class="mb-0 text-success fw-bold">₦{{ number_format($totalCredit, 2) }}</h4>
                </div>
            </div>
        </div>
        
        <!-- Total Debit -->
        <div class="col-6 col-md-3">
            <div class="card bg-danger-subtle border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold mb-2">Total Debit</h6>
                    <h4 class="mb-0 text-danger fw-bold">₦{{ number_format($totalDebit, 2) }}</h4>
                </div>
            </div>
        </div>

        <!-- Total Refund -->
        <div class="col-6 col-md-3">
            <div class="card bg-info-subtle border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold mb-2">Total Refund</h6>
                    <h4 class="mb-0 text-info fw-bold">₦{{ number_format($totalRefund, 2) }}</h4>
                </div>
            </div>
        </div>

            <!-- Total Bonus -->
        <div class="col-6 col-md-3">
            <div class="card bg-warning-subtle border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold mb-2">Total Bonus</h6>
                    <h4 class="mb-0 text-warning fw-bold">₦{{ number_format($totalBonus, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('transactions.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label small">Start Date</label>
                    <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" 
                            value="{{ request('start_date') ?? $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label small">End Date</label>
                    <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" 
                            value="{{ request('end_date') ?? $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label small">Status</label>
                    <select class="form-select form-select-sm" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label small">Type</label>
                    <select class="form-select form-select-sm" id="type" name="type">
                        <option value="">All Types</option>
                        <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Credit</option>
                        <option value="manual_credit" {{ request('type') == 'manual_credit' ? 'selected' : '' }}>Manual Credit</option>
                        <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Debit</option>
                        <option value="manual_debit" {{ request('type') == 'manual_debit' ? 'selected' : '' }}>Manual Debit</option>
                        <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Refund</option>
                        <option value="chargeback" {{ request('type') == 'chargeback' ? 'selected' : '' }}>Chargeback</option>
                        <option value="api" {{ request('type') == 'api' ? 'selected' : '' }}>API</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Transactions</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Ref ID</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}</td>
                                        <td>{{ $transaction->transaction_ref }}</td>
                                        <td>
                                            @php
                                                $typeColor = match($transaction->type) {
                                                    'credit', 'manual_credit'          => 'success',
                                                    'bonus'                            => 'success',
                                                    'debit', 'manual_debit'            => 'danger',
                                                    'refund'                           => 'info',
                                                    'chargeback'                       => 'warning',
                                                    'api'                              => 'primary',
                                                    default                            => 'secondary',
                                                };
                                                $typeLabel = match($transaction->type) {
                                                    'manual_credit' => 'Credit',
                                                    'manual_debit'  => 'Debit',
                                                    default         => ucfirst($transaction->type),
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $typeColor }}">
                                                {{ $typeLabel }}
                                            </span>
                                        </td>
                                        <td class="fw-bold {{ in_array($transaction->type, ['credit', 'manual_credit', 'bonus', 'refund']) ? 'text-success' : 'text-danger' }}">
                                            {{ in_array($transaction->type, ['credit', 'manual_credit', 'bonus', 'refund']) ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $transaction->created_at->format('M d, Y H:i A') }}</td>
                                        <td title="{{ $transaction->description }}">
                                            {{ \Illuminate\Support\Str::limit($transaction->description, 15) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No transactions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $transactions->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>