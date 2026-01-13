<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'Dashboard' }}</title>
    
    <!-- Add space between header and content -->
    <div class="mt-4">
        <!-- User + Wallet Section -->
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body user-wallet-wrap">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <!-- User Image -->
                    <div class="avatar flex-shrink-0">
                        <img src="{{ Auth::user()->photo ?? asset('assets/img/profiles/avatar-31.jpg') }}"
                             class="rounded-circle border border-3 border-primary shadow-sm user-avatar"
                             alt="User Avatar">
                    </div>

                    <!-- Welcome Message -->
                    <div class="me-auto">
                        <h4 class="fw-semibold text-dark mb-1 welcome-text">
                            Welcome back, {{ Auth::user()->first_name . ' ' . Auth::user()->surname ?? 'User' }} 👋
                        </h4>
                        <small class="text-danger">Your Wallet Id is {{ $wallet->wallet_number ?? 'N/A' }}</small>
                    </div>

                    <!-- Wallet Info -->
                    <div class="d-flex align-items-center gap-2 ms-2">
                        <span class="fw-medium text-muted small mb-0">Balance:</span>

                        <h5 id="wallet-balance" class="mb-0 text-success fw-bold balance-text">
                            ₦{{ number_format($wallet->balance ?? 0, 2) }}
                        </h5>

                        <!-- Toggle Balance Button -->
                        <button id="toggle-balance" class="btn btn-sm btn-outline-secondary ms-1 p-1 toggle-btn"
                                aria-pressed="true" title="Toggle balance visibility">
                            <i class="fas fa-eye eye-icon" aria-hidden="true"></i>
                        </button>

                        <!-- Wallet Icon -->
                        <a href="#" class="btn btn-light ms-1 border-0 p-0 wallet-btn"
                           title="View Wallet Details" aria-label="View wallet">
                            <i class="fas fa-wallet wallet-icon text-primary"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @include('pages.alart')

        <!-- API Access Section -->
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body">
                
                @php
                    $application = Auth::user()->apiApplication;
                    $isApiUser = !empty(Auth::user()->api_token);
                @endphp
                
                 @if($isApiUser)
                    <div class="alert alert-success d-flex align-items-center mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        <div>
                            <strong>You are an API User</strong>
                            <p class="mb-0 small">You have full access to our API resources.</p>
                        </div>
                    </div>
                @else
                    @if($application && $application->status === 'pending')
                        <div class="alert alert-info d-flex align-items-center mb-0">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <strong>Application Under Review</strong>
                                <p class="mb-0 small">We received your request on {{ $application->created_at->format('M d, Y') }} and our team is reviewing it.</p>
                            </div>
                        </div>
                    @elseif($application && $application->status === 'rejected')
                        <div class="alert alert-danger d-flex align-items-start mb-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle fs-4 mt-1"></i>
                            </div>
                            <div class="ms-3">
                                <strong>Application Rejected</strong>
                                <p class="mb-0 small">
                                    Your request was not approved. 
                                    @if($application->comment)
                                        <a href="javascript:void(0)" class="text-danger fw-bold text-decoration-underline" data-bs-toggle="modal" data-bs-target="#rejectionModal">
                                            View Reason
                                        </a>
                                    @endif
                                </p>
                                <button class="btn btn-sm btn-outline-danger mt-2" data-bs-toggle="modal" data-bs-target="#apiAccessModal">
                                    <i class="fas fa-redo me-1"></i> Try Again
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(!$application)
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Unlock Developer Features</h6>
                                <p class="text-muted small mb-0">Integrate our services directly into your business or application.</p>
                            </div>
                            <button type="button" class="btn btn-primary px-4 rounded-pill fw-medium shadow-sm" data-bs-toggle="modal" data-bs-target="#apiAccessModal">
                                Request Access <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Monthly Statistics Section -->
        @if($isApiUser)
        <div class="row g-3 mb-4">
            <div class="col-12">
                <h5 class="fw-bold mb-0">Monthly Service Overview</h5>
                <p class="text-muted small">Your transaction counts for this month.</p>
            </div>
            
            <!-- verification Card -->
            <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.1s;">
                <div class="financial-card shadow-sm h-100 p-4" style="background: var(--primary-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Verification</p>
                            <h3 class="stats-value mb-0">{{ number_format(($monthlyStats['nin'] ?? 0) + ($monthlyStats['bvn'] ?? 0) + ($monthlyStats['tin'] ?? 0)) }}</h3>
                            <small class="text-white-50 fs-12 fw-medium">Total Verifications</small>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="fas fa-id-card fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- validation and ipe Card -->
            <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.2s;">
                <div class="financial-card shadow-sm h-100 p-4" style="background: var(--success-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Validation and IPE</p>
                            <h3 class="stats-value mb-0">{{ number_format(($monthlyStats['validation'] ?? 0) + ($monthlyStats['ipe'] ?? 0)) }}</h3>
                            <small class="text-white-50 fs-12 fw-medium">Total Validation and IPE</small>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="fas fa-fingerprint fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- modifications Card -->
            <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.3s;">
                <div class="financial-card shadow-sm h-100 p-4" style="background: var(--info-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">NIN & BVN Modifications</p>
                            <h3 class="stats-value mb-0">{{ number_format(($monthlyStats['nin_modification'] ?? 0) + ($monthlyStats['nin modification'] ?? 0) + ($monthlyStats['bvn_modification'] ?? 0)) + ($monthlyStats['bvn modification'] ?? 0) }}</h3>
                            <small class="text-white-50 fs-12 fw-medium">Total NIN & BVN Modifications</small>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="fas fa-user-edit fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- utility card -->
            <div class="col-xl-3 col-md-6 fade-in-up" style="animation-delay: 0.4s;">
                 <div class="financial-card shadow-sm h-100 p-4" style="background: var(--warning-gradient);">
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div>
                            <p class="stats-label mb-1" style="color: white;">Bill Payments</p>
                            <h3 class="stats-value mb-0">{{ number_format($monthlyStats['validation'] ?? 0) }}</h3>
                            <small class="text-white-50 fs-12 fw-medium">Total bill payments transaction</small>
                        </div>
                        <div class="avatar avatar-lg bg-white bg-opacity-25 rounded-3">
                            <i class="fas fa-money-bill-wave fs-24 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif


        <div class="row g-3">
            <!-- Recent Transactions -->
            <div class="col-xxl-8 col-xl-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap border-bottom-0">
                        <h5 class="mb-0 fw-bold text-dark">Recent Transactions</h5>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-light text-primary fw-medium">View All</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">  
                            <table class="table table-hover table-nowrap mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-secondary small fw-semibold ps-4">#</th>
                                        <th class="text-secondary small fw-semibold">Ref ID</th>
                                        <th class="text-secondary small fw-semibold">Type</th>
                                        <th class="text-secondary small fw-semibold">Amount</th>
                                        <th class="text-secondary small fw-semibold">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTransactions as $transaction)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="text-muted small">{{ $loop->iteration }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-dark">#{{ substr($transaction->transaction_ref, 0, 10) }}...</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ match($transaction->type) {
                                                'credit' => 'success-subtle text-success',
                                                'debit' => 'danger-subtle text-danger',
                                                'refund' => 'info-subtle text-info',
                                                'chargeback' => 'warning-subtle text-warning',
                                                default => 'secondary-subtle text-secondary'
                                            } }} border-0 rounded-pill px-2 py-1">
                                                <i class="ti ti-{{ $transaction->type == 'credit' ? 'arrow-down-left' : 'arrow-up-right' }} me-1"></i>
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                    
                                        <td>
                                            <span class="fw-bold {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->type == 'credit' ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted small">{{ $transaction->created_at->format('d M Y, h:i A') }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="ti ti-receipt-off fs-1 text-muted mb-2"></i>
                                                <p class="text-muted mb-0">No recent transactions found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Recent Transactions -->

            <!-- Transaction Statistics -->
            <div class="col-xxl-4 col-xl-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body pt-5">
                        <div class="position-relative mb-5 d-flex justify-content-center">
                            <div style="height: 180px; width: 180px;">
                                <canvas id="transactionChart"></canvas>
                            </div>
                            <div class="position-absolute top-50 start-50 translate-middle text-center" style="margin-top: 5px;">
                                <p class="fs-12 text-muted mb-0">Total</p>
                                <h3 class="fw-bold text-dark mb-0">{{ $totalTransactions }}</h3>
                            </div>
                        </div>

                        <div class="row g-2 mb-4">
                            <div class="col-4">
                                <div class="p-3 rounded-3 text-center h-100" style="background-color: #d1fae5;">
                                    <h6 class="fw-bold text-dark mb-1">{{ $completedPercentage }}%</h6>
                                    <span class="fs-10 text-muted text-uppercase fw-semibold" style="font-size: 10px;">SUCCESS</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded-3 text-center h-100" style="background-color: #fef3c7;">
                                    <h6 class="fw-bold text-dark mb-1">{{ $pendingPercentage }}%</h6>
                                    <span class="fs-10 text-muted text-uppercase fw-semibold" style="font-size: 10px;">PENDING</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded-3 text-center h-100" style="background-color: #fee2e2;">
                                    <h6 class="fw-bold text-dark mb-1">{{ $failedPercentage }}%</h6>
                                    <span class="fs-10 text-muted text-uppercase fw-semibold" style="font-size: 10px;">FAILED</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 rounded-3 d-flex align-items-center justify-content-between" style="background-color: #fafafa;">
                            <div>
                                <h4 class="fw-bold mb-0" style="color: #f97316;">₦{{ number_format($totalTransactionAmount, 2) }}</h4>
                                <p class="fs-12 text-muted mb-0">Total Spent This Month</p>
                            </div>
                            <a href="{{ route('transactions.index') }}" class="btn btn-sm rounded-pill px-3 text-white" style="background-color: #f97316; border-color: #f97316;">
                                View Report <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Transaction Statistics -->
        </div>
        <!-- /Transactions and Statistics Row -->
    </div>

    <!-- API Access Modal -->
    <div class="modal fade" id="apiAccessModal" tabindex="-1" aria-labelledby="apiAccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom-0 bg-light py-4 px-4 px-md-5">
                    <div>
                        <h5 class="modal-title fw-bold text-primary mb-1" id="apiAccessModalLabel">Request API Access</h5>
                        <p class="text-muted small mb-0">Fill in the details below to integrate with our services.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4 p-md-5 bg-white">
                    <form action="{{ route('api.application.store') }}" method="POST" id="apiAccessForm" class="needs-validation" novalidate>
                        @csrf
                        <div class="row g-4">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select border-light bg-light rounded-3" id="api_type" name="api_type" required>
                                        <option value="" selected disabled>Select Type</option>
                                        <option value="personal">Personal Usage</option>
                                        <option value="business">Business / Enterprise</option>
                                        <option value="partner">Partnership</option>
                                    </select>
                                    <label for="api_type">Account Type <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please select an account type.</div>
                                </div>
                                
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control border-light bg-light rounded-3" id="business_name" name="business_name" placeholder="Business Name" required>
                                    <label for="business_name">Business / App Name <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please enter your business or app name.</div>
                                </div>
                                
                                <div class="form-floating">
                                    <input type="url" class="form-control border-light bg-light rounded-3" id="website_link" name="website_link" placeholder="https://..." required>
                                    <label for="website_link">Website URL <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please enter a valid URL (include http/https).</div>
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="form-floating h-100">
                                    <textarea class="form-control border-light bg-light rounded-3 h-100" id="business_description" name="business_description" placeholder="Description" style="min-height: 150px" required></textarea>
                                    <label for="business_description">Business Description & Use Case <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please verify how you intend to use the API.</div>
                                </div>
                            </div>
                            
                            <!-- Full Width -->
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control border-light bg-light rounded-3" id="business_nature" name="business_nature" placeholder="e.g. Fintech" required>
                                    <label for="business_nature">Nature of Business / Industry <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please specify your industry.</div>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="p-3 rounded-3 bg-light border border-light">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="terms" name="terms" required>
                                        <label class="form-check-label small text-secondary" for="terms">
                                            By submitting this request, I agree to the <a href="#" class="text-primary fw-medium text-decoration-none">API Usage Policy</a> and <a href="#" class="text-primary fw-medium text-decoration-none">Terms of Service</a>. I understand that my application will be reviewed by the compliance team.
                                        </label>
                                        <div class="invalid-feedback">You must agree to the terms to proceed.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="modal-footer border-top-0 d-flex justify-content-between p-4 px-md-5 bg-white">
                    <button type="button" class="btn btn-light text-muted px-4 rounded-pill fw-medium" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="apiAccessForm" class="btn btn-primary px-5 rounded-pill fw-medium shadow-sm">
                        <i class="fas fa-paper-plane me-2"></i> Submit Application
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Reason Modal -->
    @if($application && $application->status === 'rejected')
    <div class="modal fade" id="rejectionModal" tabindex="-1" aria-labelledby="rejectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 bg-danger bg-opacity-10 py-3">
                    <h5 class="modal-title fw-bold text-danger" id="rejectionModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Application Rejection
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border shadow-sm rounded-3 mb-4">
                        <h6 class="text-dark fw-bold mb-2">Reviewer Feedback:</h6>
                        <p class="mb-0 text-secondary">{{ $application->comment ?? 'No specific reason provided.' }}</p>
                    </div>
                    
                    <h6 class="fw-bold mb-3 small text-uppercase text-muted">Application Summary</h6>
                    <div class="card bg-light border-0 rounded-3">
                        <div class="card-body py-3">
                            <div class="row g-2 small">
                                <div class="col-6 text-muted">Submitted:</div>
                                <div class="col-6 fw-medium text-end">{{ $application->created_at ? $application->created_at->format('M d, Y') : 'N/A' }}</div>
                                
                                <div class="col-6 text-muted">Type:</div>
                                <div class="col-6 fw-medium text-end">{{ ucfirst($application->api_type ?? 'N/A') }}</div>
                                
                                @if($application->website_link)
                                <div class="col-6 text-muted">Website:</div>
                                <div class="col-6 fw-medium text-end text-truncate">
                                    <a href="{{ $application->website_link }}" target="_blank">{{ $application->website_link }}</a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#apiAccessModal">
                        Fix & Reapply
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Transaction Chart
            var ctx = document.getElementById('transactionChart');
            if (ctx) {
                ctx = ctx.getContext('2d');
                var transactionChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Success', 'Pending', 'Failed'],
                        datasets: [{
                            data: [{{ $completedTransactions }}, {{ $pendingTransactions }}, {{ $failedTransactions }}],
                            backgroundColor: [
                                '#22c55e', // Success - Green (Tailwind green-500)
                                '#fbbf24', // Pending - Amber (Tailwind amber-400)
                                '#ef4444'  // Failed - Red (Tailwind red-500)
                            ],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        cutout: '80%', // Thinner ring
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true
                            }
                        }
                    }
                });
            }

            // Fix modal issues and add validation
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('show.bs.modal', function (event) {
                    document.body.style.overflow = 'hidden';
                    document.body.style.paddingRight = '0';
                });
                
                modal.addEventListener('hidden.bs.modal', function (event) {
                    document.body.style.overflow = 'auto';
                    document.body.style.paddingRight = '0';
                });
            });

            // Form validation
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            });

            // Auto-focus first input in modal
            const apiAccessModal = document.getElementById('apiAccessModal');
            if (apiAccessModal) {
                apiAccessModal.addEventListener('shown.bs.modal', function () {
                    const firstInput = this.querySelector('input, select, textarea');
                    if (firstInput) {
                        firstInput.focus();
                    }
                });
            }

            // Toggle balance visibility
            const toggleBalanceBtn = document.getElementById('toggle-balance');
            if (toggleBalanceBtn) {
                toggleBalanceBtn.addEventListener('click', function() {
                    const balanceText = document.getElementById('wallet-balance');
                    const eyeIcon = this.querySelector('.eye-icon');
                    const isVisible = balanceText.textContent !== '₦******';
                    
                    if (isVisible) {
                        balanceText.textContent = '₦******';
                        eyeIcon.classList.remove('fa-eye');
                        eyeIcon.classList.add('fa-eye-slash');
                    } else {
                        balanceText.textContent = `₦{{ number_format($wallet->balance ?? 0, 2) }}`;
                        eyeIcon.classList.remove('fa-eye-slash');
                        eyeIcon.classList.add('fa-eye');
                    }
                });
            }
        });
    </script>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --success-gradient: linear-gradient(135deg, #22c55e 0%, #10b981 100%);
            --info-gradient: linear-gradient(135deg, #3b82f6 0%, #0ea5e9 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #f43f5e 100%);
        }

        .financial-card {
            position: relative;
            overflow: hidden;
            border: none;
            border-radius: 1rem;
            color: white;
        }
        .financial-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }
        .financial-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(-30%, 30%);
        }
        
        .stats-label { font-size: 0.875rem; font-weight: 500; opacity: 0.9; }
        .stats-value { font-size: 1.5rem; font-weight: 700; letter-spacing: -0.025em; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        .avatar-lg { width: 3rem; height: 3rem; display: flex; align-items: center; justify-content: center; }

        /* Fix modal shaking and improve styling */
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal.fade .modal-dialog {
            transform: translate(0, -50px);
            transition: transform 0.3s ease-out;
        }
        
        .modal.show .modal-dialog {
            transform: translate(0, 0);
        }
        
        .modal-content {
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.2);
            border: none;
        }
        
        .modal-header {
            /* padding: 1.25rem 1.5rem; */ /* Overwritten by new styles */
        }
        
        .modal-body {
            /* padding: 1.5rem; */ /* Overwritten by new styles */
        }
        
        .modal-footer {
            /* padding: 1rem 1.5rem; */ /* Overwritten by new styles */
        }
        
        /* Smooth transitions */
        .btn {
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
            border-color: #86b7fe;
        }
        
        /* Ensure cards have consistent height */
        .card {
            height: 100%;
        }
        
        /* Ensure tables are responsive */
        .table-responsive {
            min-height: 400px;
        }
        
        /* Custom Scrollbar for Modal */
        .modal-body::-webkit-scrollbar {
            width: 6px;
        }
        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .modal-body::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }
        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #bbb;
        }
    </style>
</x-app-layout>