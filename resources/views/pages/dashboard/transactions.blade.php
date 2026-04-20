<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'Transactions' }}</title>
    
    <div class="container-fluid px-0 px-md-3 py-4">
        <div class="row g-3 mb-4">
            <!-- Total Credit -->
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase fw-bold mb-2">Total Credit</h6>
                        <h4 class="mb-0 text-success fw-bold load-skeleton">₦{{ number_format($totalCredit, 2) }}</h4>
                    </div>
                </div>
            </div>
            
            <!-- Total Debit -->
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase fw-bold mb-2">Total Debit</h6>
                        <h4 class="mb-0 text-danger fw-bold load-skeleton">₦{{ number_format($totalDebit, 2) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Total Refund -->
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase fw-bold mb-2">Total Refund</h6>
                        <h4 class="mb-0 text-info fw-bold load-skeleton">₦{{ number_format($totalRefund, 2) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Total Bonus -->
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase fw-bold mb-2">Total Bonus</h6>
                        <h4 class="mb-0 text-warning fw-bold load-skeleton">₦{{ number_format($totalBonus, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

    <!-- Filter Section -->
    <div class="card shadow-lg border-0 rounded-0 rounded-md-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('transactions.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label small fw-bold">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                            value="{{ request('start_date') ?? $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label small fw-bold">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                            value="{{ request('end_date') ?? $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label small fw-bold">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label small fw-bold">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Credit</option>
                        <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Debit</option>
                        <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Refund</option>
                        <option value="bonus" {{ request('type') == 'bonus' ? 'selected' : '' }}>Bonus</option>
                        <option value="api" {{ request('type') == 'api' ? 'selected' : '' }}>API</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary"><i class="ti ti-refresh"></i></a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-0 rounded-md-4 overflow-hidden">
                <div class="card-header py-4 border-bottom-0 bg-transparent">
                    <h5 class="mb-0 fw-bold">Transaction History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-premium table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">S/N</th>
                                    <th>Ref ID</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="pe-4">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td class="ps-4 small text-muted">{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}</td>
                                        <td><span class="fw-medium">#{{ substr($transaction->transaction_ref, 0, 12) }}...</span></td>
                                        <td>
                                            @php
                                                $typeClass = match($transaction->type) {
                                                    'credit', 'manual_credit', 'bonus' => 'success',
                                                    'debit', 'manual_debit'            => 'danger',
                                                    'refund'                           => 'info',
                                                    'chargeback'                        => 'warning',
                                                    default                             => 'primary',
                                                };
                                                $typeLabel = match($transaction->type) {
                                                    'manual_credit' => 'Credit',
                                                    'manual_debit'  => 'Debit',
                                                    default         => ucfirst($transaction->type),
                                                };
                                            @endphp
                                            <span class="badge-subtle badge-subtle-{{ $typeClass }}">
                                                {{ $typeLabel }}
                                            </span>
                                        </td>
                                        <td class="fw-bold {{ in_array($transaction->type, ['credit', 'manual_credit', 'bonus', 'refund']) ? 'text-success' : 'text-danger' }}">
                                            {{ in_array($transaction->type, ['credit', 'manual_credit', 'bonus', 'refund']) ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($transaction->status) {
                                                    'completed' => 'success',
                                                    'pending'   => 'warning',
                                                    'failed'    => 'danger',
                                                    default     => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge-subtle badge-subtle-{{ $statusClass }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td class="small">{{ $transaction->created_at->format('M d, Y H:i A') }}</td>
                                        <td class="pe-4 small text-muted" title="{{ $transaction->description }}">
                                            {{ \Illuminate\Support\Str::limit($transaction->description, 25) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="ti ti-receipt-off fs-1 text-muted d-block mb-3"></i>
                                            <p class="text-muted">No transactions found match your criteria.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 py-4">
                    {{ $transactions->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Premium Skeleton Transition
            const skeletonTargets = document.querySelectorAll('.load-skeleton');
            skeletonTargets.forEach(target => {
                const finalContent = target.innerHTML;
                target.innerHTML = `<span class="skeleton-shimmer sk-title" style="width: 100px;"></span>`;
                
                setTimeout(() => {
                    target.innerHTML = finalContent;
                    target.classList.remove('load-skeleton');
                }, 800 + Math.random() * 400);
            });

            // Table Content Animation
            const tableRows = document.querySelectorAll('.table-premium tbody tr');
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(10px)';
                row.style.transition = `all 0.3s ease-out ${index * 0.05}s`;
                requestAnimationFrame(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</x-app-layout>
