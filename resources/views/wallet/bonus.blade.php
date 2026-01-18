<x-app-layout>
     <title>Arewa Smart - {{ $title ?? 'transaction Bonus' }}</title>
    
    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-1">
                            <i class="bi bi-gift-fill text-warning me-2"></i> Available Balance
                        </h3>
                        <p class="text-muted mb-0">Manage and transfer your pending funds</p>
                    </div>
                    <a href="{{ route('wallet') }}" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-2"></i> Back to Wallet
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Balance Card -->
            <div class="col-lg-8">
                <!-- Premium Balance Display Card -->
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden premium-card">
                    <!-- Gradient Header -->
                    <div class="card-header text-white p-5 position-relative" style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);">
                        <div class="position-absolute top-0 end-0 p-4 opacity-10">
                            <i class="bi bi-cash-stack" style="font-size: 120px;"></i>
                        </div>
                        <div class="position-relative z-1">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <small class="text-white-50 text-uppercase ls-1 d-block mb-2 fw-semibold">Pending Funds Available</small>
                                    <h1 class="display-3 fw-bold mb-0 animate-balance">₦{{ number_format($walletData['available_balance'] ?? 0, 2) }}</h1>
                                    <small class="text-white-50 mt-2 d-block">
                                        <i class="bi bi-info-circle me-1"></i> Ready for transfer to main wallet
                                    </small>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    @if(($walletData['available_balance'] ?? 0) > 0)
                                        <button class="btn btn-primary btn-lg rounded-pill px-4 shadow-lg hover-lift" onclick="confirmTransfer()">
                                            <i class="bi bi-arrow-down-circle-fill me-2"></i> Transfer
                                        </button>
                                    @else
                                        <button class="btn btn-secondary btn-lg rounded-pill px-4" disabled>
                                            <i class="bi bi-x-circle me-2"></i> No Balance
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Body with Stats -->
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Main Wallet Balance -->
                            <div class="col-md-6">
                                <div class="stat-card p-4 rounded-4 h-100 position-relative overflow-hidden" style="background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);">
                                    <div class="position-absolute top-0 end-0 opacity-5 p-3">
                                        <i class="bi bi-wallet2" style="font-size: 60px;"></i>
                                    </div>
                                    <div class="position-relative z-1">
                                        <small class="text-muted text-uppercase d-block mb-2 fw-bold">Main Wallet</small>
                                        <h3 class="fw-bold text-dark mb-0">₦{{ number_format($walletData['wallet_balance'] ?? 0, 2) }}</h3>
                                        <small class="text-muted">Current balance</small>
                                    </div>
                                </div>
                            </div>

                            <!-- After Transfer Preview -->
                            <div class="col-md-6">
                                <div class="stat-card p-4 rounded-4 h-100 position-relative overflow-hidden" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);">
                                    <div class="position-absolute top-0 end-0 opacity-10 p-3">
                                        <i class="bi bi-arrow-up-circle-fill text-success" style="font-size: 60px;"></i>
                                    </div>
                                    <div class="position-relative z-1">
                                        <small class="text-success text-uppercase d-block mb-2 fw-bold">After Transfer</small>
                                        <h3 class="fw-bold text-success mb-0">₦{{ number_format(($walletData['wallet_balance'] ?? 0) + ($walletData['available_balance'] ?? 0), 2) }}</h3>
                                        <small class="text-success">Projected balance</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transfer Info -->
                        @if(($walletData['available_balance'] ?? 0) > 0)
                            <div class="alert alert-info border-0 rounded-4 d-flex align-items-start mt-4 shadow-sm">
                                <div class="flex-shrink-0">
                                    <div class="icon-circle bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-lightbulb-fill"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-bold mb-1">Ready to Transfer</h6>
                                    <p class="mb-0 small">You have <strong class="text-info">₦{{ number_format($walletData['available_balance'], 2) }}</strong> waiting. Click "Transfer Now" to move these funds to your main wallet instantly.</p>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-light border rounded-4 text-center mt-4 py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                                <h6 class="fw-bold text-dark">No Available Balance</h6>
                                <p class="text-muted mb-0">Your available balance is empty. Funds will appear here when you receive commissions or pending credits.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Stats -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-graph-up text-primary me-2"></i> Quick Stats
                        </h6>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-2" style="background-color: #f8f9fa;">
                            <span class="text-muted">Total Available</span>
                            <span class="fw-bold text-dark">₦{{ number_format($walletData['available_balance'] ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background-color: #f8f9fa;">
                            <span class="text-muted">Main Wallet</span>
                            <span class="fw-bold text-dark">₦{{ number_format($walletData['wallet_balance'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>

               
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Transfer Confirmation with SweetAlert
        function confirmTransfer() {
            const availableBalance = {{ $walletData['available_balance'] ?? 0 }};
            const currentBalance = {{ $walletData['wallet_balance'] ?? 0 }};
            const newBalance = availableBalance + currentBalance;
            
            Swal.fire({
                title: 'Transfer Bonus Balance?',
                html: `
                    <div class="text-start">
                        <p class="mb-3">You are about to transfer:</p>
                        <div class="alert alert-light border rounded-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Amount:</span>
                                <strong class="text-primary">₦${availableBalance.toLocaleString('en-NG', {minimumFractionDigits: 2})}</strong>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span>New Balance:</span>
                                <strong class="text-success">₦${newBalance.toLocaleString('en-NG', {minimumFractionDigits: 2})}</strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="swal-input-pin" class="form-label small fw-bold text-dark">Enter Transaction PIN (5 digits)</label>
                            <input type="password" id="swal-input-pin" class="form-control text-center fw-bold fs-30" 
                                   placeholder="•••••" maxlength="5" inputmode="numeric" 
                                   style="letter-spacing: 12px; height: 55px; border-radius: 12px; border: 2px solid #e0e0e0;">
                        </div>
                        <p class="small text-muted mb-0"><i class="bi bi-info-circle me-1"></i> This action is instant and irreversible</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-check-circle me-2"></i> Confirm Transfer',
                cancelButtonText: '<i class="bi bi-x-circle me-2"></i> Cancel',
                showLoaderOnConfirm: true,
                backdrop: true,
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'rounded-pill px-4',
                    cancelButton: 'rounded-pill px-4'
                },
                preConfirm: () => {
                    const pin = document.getElementById('swal-input-pin').value;
                    if (!pin || pin.length !== 5 || isNaN(pin)) {
                        Swal.showValidationMessage('Please enter a valid 5-digit transaction PIN');
                        return false;
                    }

                    return fetch('{{ route('wallet.transfer') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ pin: pin })
                    })
                    .then(async response => {
                        const isJson = response.headers.get('content-type')?.includes('application/json');
                        const data = isJson ? await response.json() : null;

                        if (!response.ok) {
                            const errorMsg = data?.message || (data?.errors ? Object.values(data.errors).flat()[0] : 'Transfer failed. Please try again.');
                            throw new Error(errorMsg);
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(error.message || 'Request failed. Please try again.');
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    Swal.fire({
                        title: 'Transfer Successful!',
                        html: `
                            <div class="text-center py-3">
                                <div class="mb-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 60px;"></i>
                                </div>
                                <h5 class="fw-bold mb-3">₦${result.value.amount.toLocaleString('en-NG', {minimumFractionDigits: 2})} Transferred</h5>
                                <div class="alert alert-success border-0 rounded-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>New Balance:</span>
                                        <strong>₦${result.value.new_balance.toLocaleString('en-NG', {minimumFractionDigits: 2})}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Transaction Ref:</span>
                                        <small class="font-monospace">${result.value.transaction_ref}</small>
                                    </div>
                                </div>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonColor: '#198754',
                        confirmButtonText: '<i class="bi bi-check-lg me-2"></i> Done',
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'rounded-pill px-5'
                        }
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        }
    </script>

    <style>
        .ls-1 {
            letter-spacing: 1px;
        }
        
        .text-white-50 {
            color: rgba(255, 255, 255, 0.5) !important;
        }
        
        /* Premium Card Effects */
        .premium-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .premium-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        }
        
        /* Stat Card Styling */
        .stat-card {
            transition: transform 0.2s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important;
        }
        
        /* Animate Balance */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-balance {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Hover Lift Button */
        .hover-lift {
            transition: all 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
        }
        
        /* Icon Circles */
        .icon-circle {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .icon-sm {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        /* Custom Alert Styling */
        .alert {
            border-left: 4px solid currentColor;
            border-right: none;
            border-top: none;
            border-bottom: none;
        }
        
        /* Card Header Fix */
        .card-header {
            border-bottom: none;
        }
        
        /* Smooth Transitions */
        .btn, .stat-card, .premium-card {
            transition: all 0.3s ease;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .display-3 {
                font-size: 2.5rem;
            }
            
            .card-header {
                padding: 2rem 1.5rem !important;
            }
            
            .btn-lg {
                padding: 0.5rem 1.5rem;
                font-size: 1rem;
            }
        }
        
        /* V-stack utility (if not provided by Bootstrap) */
        .v-stack {
            display: flex;
            flex-direction: column;
        }
        
        .gap-3 {
            gap: 1rem;
        }
        
        /* Font monospace for transaction ref */
        .font-monospace {
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }
    </style>
</x-app-layout>