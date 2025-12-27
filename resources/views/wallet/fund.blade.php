<x-app-layout>
  <title>Arewa Smart - {{ $title ?? 'Wallet Funding' }}</title>
  
  <div class="container-fluid py-4">
    <div class="row g-4 align-items-start">

      <!-- Left Column: Funding Details (Visible on All Devices) -->
      <div class="col-lg-6 col-md-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
          <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold text-primary mb-0">
              <i class="bi bi-wallet2 me-2"></i>Wallet Funding
            </h5>
            <p class="text-muted small mt-1">Fund your wallet instantly via bank transfer.</p>
          </div>
          
          <div class="card-body p-4">
            <!-- Alerts -->
            @if (session('success'))
              <div class="alert alert-success d-flex align-items-center rounded-3 border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
              </div>
            @endif
            @if (session('error'))
              <div class="alert alert-danger d-flex align-items-center rounded-3 border-0 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
              </div>
            @endif

            @php
              $ws = \App\Models\Webservice::where('name', 'wallet funding')->first();
            @endphp

            @if($ws && $ws->status == 'active')
              @if($virtualAccount)
                <!-- Virtual Account Card -->
                <div class="account-details-card p-3 rounded-3 mb-3">
                  <div class="row g-3">
                    <div class="col-12">
                      <label class="text-xs text-uppercase fw-bold text-muted mb-1">Bank Name</label>
                      <div class="d-flex align-items-center justify-content-between bg-white p-2 rounded border">
                        <span class="fw-bold text-dark">{{ $virtualAccount->bankName }}</span>
                        <i class="bi bi-bank text-primary"></i>
                      </div>
                    </div>
                    <div class="col-12">
                      <label class="text-xs text-uppercase fw-bold text-muted mb-1">Account Number</label>
                      <div class="input-group">
                        <input type="text" class="form-control fw-bold fs-5 text-dark bg-white border-end-0" 
                               value="{{ $virtualAccount->accountNo }}" id="accountNo" readonly>
                        <button class="btn btn-outline-secondary border-start-0 bg-white" type="button" onclick="copyToClipboard('accountNo')">
                          <i class="bi bi-clipboard"></i>
                        </button>
                      </div>
                    </div>
                    <div class="col-12">
                      <label class="text-xs text-uppercase fw-bold text-muted mb-1">Account Name</label>
                      <div class="d-flex align-items-center justify-content-between bg-white p-2 rounded border">
                        <span class="fw-medium text-dark">{{ $virtualAccount->accountName }}</span>
                        <i class="bi bi-person-badge text-primary"></i>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="text-center mt-4">
                  <small class="text-muted d-block mb-2">
                    <i class="bi bi-info-circle me-1"></i> Transfers to this account are automatically credited to your wallet.
                  </small>
                </div>

              @else
                <!-- Create Account State -->
                <div class="text-center py-5">
                   <div class="mb-4">
                     <div class="icon-circle bg-primary-subtle text-primary mx-auto">
                        <i class="bi bi-wallet-fill fs-1"></i>
                     </div>
                   </div>
                   <h5 class="fw-bold">Activate Your Wallet</h5>
                   <p class="text-muted mb-4 px-3">Create a dedicated virtual account to easily fund your wallet via bank transfer.</p>
                   
                   <a href="#" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm hover-scale" data-bs-toggle="modal" data-bs-target="#virtualAccountModal">
                     <i class="bi bi-plus-lg me-2"></i> Create Virtual Account
                   </a>
                </div>
              @endif
            @else
              <!-- Disabled State -->
              <div class="alert alert-warning border-0 shadow-sm rounded-3">
                  <i class="bi bi-cone-striped me-2"></i> Wallet Funding is currently unavailable. Please check back later.
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Right Column: Professional System View (Desktop Only) -->
      <div class="col-lg-6 d-none d-lg-block">
        <!-- Digital Card Visual -->
        <div class="wallet-card-visual text-white p-4 rounded-4 mb-4 shadow-lg position-relative overflow-hidden">
           <div class="card-bg-pattern"></div>
           <div class="position-relative z-1">
             <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="badge bg-white-20 rounded-pill px-3 py-1 fw-light">Arewa Smart</span>
                <i class="bi bi-wifi fs-4"></i>
             </div>
             
             <div class="my-4">
                <small class="text-white-50 text-uppercase ls-1">Available Balance</small>
                <h1 class="display-5 fw-bold text-white mb-0">₦{{ number_format($walletData['wallet_balance'] ?? 0, 2) }}</h1>
             </div>

             <div class="d-flex justify-content-between align-items-end mt-4">
                <div>
                  <small class="text-white-50 text-uppercase d-block fs-10 mb-1">Account Holder</small>
                  <span class="fw-medium text-uppercase ls-1">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                </div>
                <div class="text-end">
                   <i class="bi bi-credit-card-2-front fs-2 text-white-50"></i>
                </div>
             </div>
           </div>
        </div>

        <!-- Funding Tips / Info -->
        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-body p-4">
             <h6 class="fw-bold mb-3 text-dark">Why use Automated Funding?</h6>
             <ul class="list-unstyled mb-0 v-stack gap-3">
               <li class="d-flex align-items-center">
                 <div class="icon-square bg-success-subtle text-success me-3 rounded-3">
                   <i class="bi bi-lightning-charge-fill"></i>
                 </div>
                 <div>
                   <span class="fw-bold d-block text-dark">Instant Credit</span>
                   <small class="text-muted">Your wallet is funded immediately payment is received.</small>
                 </div>
               </li>
               <li class="d-flex align-items-center">
                 <div class="icon-square bg-info-subtle text-info me-3 rounded-3">
                   <i class="bi bi-shield-lock-fill"></i>
                 </div>
                 <div>
                   <span class="fw-bold d-block text-dark">Secure Transactions</span>
                   <small class="text-muted">Bank-grade security for all your deposits.</small>
                 </div>
               </li>
               <li class="d-flex align-items-center">
                 <div class="icon-square bg-warning-subtle text-warning me-3 rounded-3">
                   <i class="bi bi-clock-history"></i>
                 </div>
                 <div>
                   <span class="fw-bold d-block text-dark">24/7 Availability</span>
                   <small class="text-muted">Fund your wallet anytime, day or night.</small>
                 </div>
               </li>
             </ul>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Virtual Account Modal -->
  <div class="modal fade" id="virtualAccountModal" tabindex="-1" aria-labelledby="virtualAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow rounded-4">
        <div class="modal-header bg-primary text-white border-0">
          <h5 class="modal-title fw-bold">Create Virtual Account</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4">
          <form method="POST" action="{{ route('wallet.create') }}" class="row g-3">
            @csrf
            
            <div class="col-12 text-center mb-2">
                <div class="avatar avatar-lg bg-light rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                    <span class="fs-2 text-primary fw-bold">{{ substr(auth()->user()->first_name, 0, 1) }}</span>
                </div>
                <h6 class="fw-bold mb-0">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h6>
                <p class="text-muted small">{{ auth()->user()->email }}</p>
            </div>

            <div class="col-12">
               <div class="alert alert-info border-0 rounded-3 small">
                 <i class="bi bi-check-circle-fill me-1"></i> A unique account number will be generated for you.
               </div>
            </div>

            <input type="hidden" name="name" value="{{ auth()->user()->first_name.' '.auth()->user()->last_name }}">
            
            <div class="col-12">
              <div class="form-check p-3 bg-light rounded-3 border">
                <input class="form-check-input" type="checkbox" id="confirmCheck" required>
                <label class="form-check-label small text-muted" for="confirmCheck">
                  I agree to generate a virtual account for my wallet funding.
                </label>
              </div>
            </div>
            
            <div class="col-12">
              <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold shadow-sm">
                Generate Account Now
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    function copyToClipboard(elementId) {
      var copyText = document.getElementById(elementId);
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      navigator.clipboard.writeText(copyText.value);
      
      // Optional: Show toast or tooltip styling here
      // alert("Copied: " + copyText.value);
    }
  </script>

  <style>
    .bg-gradient {
      background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
    }
    .hover-scale:hover {
      transform: translateY(-2px);
      transition: transform 0.2s;
    }
    .text-xs {
      font-size: 0.75rem;
    }
    .ls-1 {
      letter-spacing: 1px;
    }
    .fs-10 {
      font-size: 10px;
    }
    .bg-white-20 {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(5px);
    }
    
    /* Wallet Card Visual */
    .wallet-card-visual {
      background: linear-gradient(135deg, #1e1e1e 0%, #2c3e50 100%);
      min-height: 220px;
      border: 1px solid rgba(255,255,255,0.1);
    }
    .card-bg-pattern {
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      opacity: 0.1;
      background-image: radial-gradient(#fff 1px, transparent 1px);
      background-size: 20px 20px;
    }
    
    .icon-circle {
      width: 80px; height: 80px; 
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
    }
    .icon-square {
        width: 40px; height: 40px;
        display: flex; align-items: center; justify-content: center;
    }
    
    .account-details-card {
        background-color: #f8f9fa;
        border: 1px dashed #dee2e6;
    }
  </style>
</x-app-layout>
