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
                <h5 class="fw-bold mb-3">API Access</h5>
                
                @php
                    $application = Auth::user()->apiApplication;
                @endphp
                
                 @if(Auth::user()->role === 'api' && Auth::user()->api_token)
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
                            <i class="fas fa-clock me-2"></i>
                            <div>
                                <strong>Application Under Review</strong>
                                <p class="mb-0 small">We received your request on {{ $application->created_at->format('M d, Y') }} and our team is reviewing it.</p>
                            </div>
                        </div>
                    @elseif($application && $application->status === 'rejected')
                        <div class="alert alert-danger d-flex align-items-center mb-3">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <div>
                                <strong>Application Rejected</strong>
                                <p class="mb-0 small">
                                    Your previous request was not approved. 
                                    @if($application->comment)
                                        <a href="javascript:void(0)" class="text-primary fw-medium" data-bs-toggle="modal" data-bs-target="#rejectionModal">
                                            View Reason
                                        </a>
                                    @endif
                                    You can submit a new application below.
                                </p>
                            </div>
                        </div>
                    @endif

                    @if(!$application || $application->status !== 'pending')
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-muted mb-0">Request access to our API for personal or business use.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#apiAccessModal">
                                Request Access
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Transactions and Statistics Row -->
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
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="min-height: 200px;">
                <form action="{{ route('api.application.store') }}" method="POST" id="apiAccessForm">
                    @csrf
                    <div class="modal-header border-bottom-0 bg-light">
                        <h5 class="modal-title fw-bold text-primary" id="apiAccessModalLabel">Request API Access</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label for="api_type" class="form-label fw-medium">API Type <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3" id="api_type" name="api_type" required>
                                <option value="" selected disabled>Select Type</option>
                                <option value="personal">Personal</option>
                                <option value="business">Business</option>
                                <option value="partner">Partner</option>
                            </select>
                            <div class="invalid-feedback">Please select an API type</div>
                        </div>
                        <div class="mb-3">
                            <label for="business_name" class="form-label fw-medium">Business Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3" id="business_name" name="business_name" placeholder="Your Business Name" required>
                            <div class="invalid-feedback">Please provide your business name</div>
                        </div>
                        <div class="mb-3">
                            <label for="website_link" class="form-label fw-medium">Website Link <span class="text-muted small">(Optional)</span></label>
                            <input type="url" class="form-control rounded-3" id="website_link" name="website_link" placeholder="https://example.com">
                            <div class="form-text">Provide your website URL if applicable</div>
                        </div>
                        <div class="mb-3">
                            <label for="business_nature" class="form-label fw-medium">Nature of Business <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3" id="business_nature" name="business_nature" placeholder="e.g. Fintech, E-commerce" required>
                            <div class="invalid-feedback">Please specify the nature of your business</div>
                        </div>
                        <div class="mb-3">
                            <label for="business_description" class="form-label fw-medium">Business Description <span class="text-danger">*</span></label>
                            <textarea class="form-control rounded-3" id="business_description" name="business_description" rows="4" placeholder="Tell us about how you plan to use the API..." required></textarea>
                            <div class="invalid-feedback">Please provide a business description</div>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" value="1" id="terms" name="terms" required>
                            <label class="form-check-label small" for="terms">
                                I agree to the <a href="#" class="text-primary">Terms and Conditions</a> for API usage.
                            </label>
                            <div class="invalid-feedback">You must agree to the terms and conditions</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 bg-light">
                        <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rejection Reason Modal -->
    @if($application && $application->status === 'rejected')
    <div class="modal fade" id="rejectionModal" tabindex="-1" aria-labelledby="rejectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0 bg-light">
                    <h5 class="modal-title fw-bold text-danger" id="rejectionModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Application Rejection Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="alert alert-light border">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-info fs-4 mt-1"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="alert-heading fw-semibold mb-2">Reason for Rejection</h6>
                                <p class="mb-0 text-dark">{{ $application->comment ?? 'No specific reason provided.' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h6 class="fw-semibold mb-2">Application Details</h6>
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between px-0 border-0">
                                <span class="text-muted">Submitted Date:</span>
                                <span class="fw-medium">
                                    {{ $application->created_at ? $application->created_at->format('M d, Y') : 'N/A' }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 border-0">
                                <span class="text-muted">API Type:</span>
                                <span class="badge bg-secondary">
                                    {{ $application->api_type ? ucfirst($application->api_type) : 'N/A' }}
                                </span>
                            </li>
                            @if($application->website_link)
                            <li class="list-group-item d-flex justify-content-between px-0 border-0">
                                <span class="text-muted">Website:</span>
                                <a href="{{ $application->website_link }}" target="_blank" class="text-primary text-decoration-none">
                                    {{ $application->website_link }}
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                    
                    <div class="alert alert-info mt-4 mb-0">
                        <div class="d-flex">
                            <i class="fas fa-lightbulb text-warning me-2 mt-1"></i>
                            <div>
                                <strong>Tip:</strong> Address the feedback above and submit a new application for reconsideration.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary rounded-3 px-4" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#apiAccessModal">
                        Reapply
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
            const apiAccessForm = document.getElementById('apiAccessForm');
            if (apiAccessForm) {
                apiAccessForm.addEventListener('submit', function(event) {
                    if (!this.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    this.classList.add('was-validated');
                });
            }

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
            padding: 1.25rem 1.5rem;
        }
        
        .modal-body {
            padding: 1.5rem;
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .modal-footer {
            padding: 1rem 1.5rem;
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
    </style>
</x-app-layout>