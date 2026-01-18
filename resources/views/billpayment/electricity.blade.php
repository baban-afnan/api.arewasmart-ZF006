<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'Electricity API' }}</title>
    <div class="content container-fluid">
           <!-- Page Header -->
        <div class="page-header mb-5">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title fw-bold text-primary display-6">
                        Electricity Bill Payment API
                    </h3>

                    <ul class="breadcrumb bg-transparent p-0 mt-2 mb-1">
                        <li class="breadcrumb-item active text-primary fw-semibold">
                            API Documentation
                        </li>
                    </ul>

                    <p class="text-muted mb-0">
                        Integrate Electricity bill payments for various distribution companies including IKEDC, EKEDC, AEDC, and more.
                    </p>
                </div>
                <div class="col-auto">
                    <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fw-medium fs-14 border border-primary border-opacity-10">
                        <i class="ti ti-tag me-1"></i> Version 1.0.0
                    </span>
                    <!-- Mobile Sidebar Toggle Button -->
                    <button class="btn btn-white shadow-sm d-lg-none ms-2 rounded-circle p-2" type="button" id="sidebarToggle" aria-label="Toggle Navigation">
                        <i class="ti ti-menu-2 fs-4 text-primary"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="sticky-top" style="top: 100px; z-index: 10;">
                    <nav id="navbar-example3" class="h-100 flex-column align-items-stretch border-0">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-0">
                                <div class="p-4 bg-soft-primary border-bottom border-light">
                                    <h6 class="fw-bold text-primary mb-0 d-flex align-items-center">
                                        <i class="ti ti-menu-deep me-2"></i> Navigation
                                    </h6>
                                </div>
                                <div class="list-group list-group-flush custom-sidebar-nav p-2">
                                    <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center active" href="#overview" onclick="switchTab('overview'); return false;">
                                        <i class="ti ti-info-circle me-2 fs-5 opacity-75"></i> Overview
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#auth" onclick="switchTab('auth'); return false;">
                                        <i class="ti ti-shield-lock me-2 fs-5 opacity-75"></i> Authentication
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#variations" onclick="switchTab('variations'); return false;">
                                        <i class="ti ti-list me-2 fs-5 opacity-75"></i> Electricity Companies
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#verify" onclick="switchTab('verify'); return false;">
                                        <i class="ti ti-user-check me-2 fs-5 opacity-75"></i> Meter Verification
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#endpoint" onclick="switchTab('endpoint'); return false;">
                                        <i class="ti ti-server me-2 fs-5 opacity-75"></i> Payment Endpoint
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#codes" onclick="switchTab('codes'); return false;">
                                        <i class="ti ti-list-numbers me-2 fs-5 opacity-75"></i> Commissions
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Support Card -->
                        <div class="card border-0 shadow-sm rounded-4 mt-4 bg-primary text-white overflow-hidden position-relative">
                            <div class="position-absolute top-0 end-0 p-3 opacity-25">
                                <i class="ti ti-headset fs-1"></i>
                            </div>
                            <div class="card-body p-4 position-relative z-index-1">
                                <h6 class="fw-bold text-white mb-2">Need Help?</h6>
                                <p class="small text-white-50 mb-3">Our support team is available 24/7 to assist with integration.</p>
                                <a href="https://wa.me/2347037343660" target="_blank" class="btn btn-white text-primary w-100 btn-sm rounded-pill fw-bold shadow-sm">
                                    <i class="ti ti-brand-whatsapp me-1"></i> Contact Support
                                </a>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>

            <!-- Mobile Offcanvas Sidebar -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
                <div class="offcanvas-header bg-white border-bottom">
                    <h5 class="offcanvas-title fw-bold text-primary" id="mobileSidebarLabel">Documentation</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    <div class="list-group list-group-flush custom-sidebar-nav p-3">
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center active" href="#overview" onclick="switchTab('overview'); closeOffcanvas(); return false;">
                            <i class="ti ti-info-circle me-2"></i> Overview
                        </a>
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#auth" onclick="switchTab('auth'); closeOffcanvas(); return false;">
                            <i class="ti ti-shield-lock me-2"></i> Authentication
                        </a>
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#variations" onclick="switchTab('variations'); closeOffcanvas(); return false;">
                            <i class="ti ti-list"></i> Companies
                        </a>
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#verify" onclick="switchTab('verify'); closeOffcanvas(); return false;">
                            <i class="ti ti-user-check me-2"></i> Verification
                        </a>
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#endpoint" onclick="switchTab('endpoint'); closeOffcanvas(); return false;">
                            <i class="ti ti-server me-2"></i> Payment Endpoint
                        </a>
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#codes" onclick="switchTab('codes'); closeOffcanvas(); return false;">
                            <i class="ti ti-list-numbers me-2"></i> Commissions
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                
                <!-- Overview Section -->
                <div class="docs-section fade-in" id="overview">
                    <div class="card border-0 shadow-sm rounded-4 mb-4 position-relative overflow-hidden">
                        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-5" style="background: url('https://cdn.svgporn.com/logos/laravel.svg') no-repeat right bottom; background-size: 30%;"></div>
                        <div class="card-body p-5 position-relative">
                            <span class="badge bg-soft-primary text-primary mb-3">Introduction</span>
                            <h2 class="fw-bold text-dark mb-3">Electricity Bill Payment API Guide</h2>
                            <p class="text-muted lead mb-4">
                                Pay electricity bills for all major distribution companies in Nigeria. 
                                Our API supports meter verification to ensure you're paying for the correct account.
                            </p>
                            
                            <!-- Endpoint Box -->
                            <div class="bg-dark rounded-4 p-4 text-white shadow-lg position-relative overflow-hidden">
                                <div class="position-absolute top-0 end-0 p-3 opacity-10">
                                    <i class="ti ti-world-www fs-1"></i>
                                </div>
                                <label class="text-white-50 small text-uppercase ls-1 fw-bold mb-2">Base URL</label>
                                <div class="d-flex align-items-center bg-white bg-opacity-10 rounded-3 p-3 border border-white border-opacity-10">
                                    <code class="text-white fs-16 font-monospace flex-grow-1">{{ url('/') }}/api/v1</code>
                                    <button class="btn btn-sm btn-secondary rounded-pill px-3 ms-3" onclick="copyToClipboard('{{ url('/') }}/api/v1')">
                                        <i class="ti ti-copy me-1"></i> Copy
                                    </button>
                                </div>
                                <div class="mt-3 d-flex align-items-center small text-warning">
                                    <i class="ti ti-alert-triangle me-2"></i>
                                    <span>Ensure all requests are made via <strong>HTTPS</strong>.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                         <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="auth">
                            Next: Authentication <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Authentication Section -->
                <div class="docs-section d-none fade-in" id="auth">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold text-dark mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">1</span>
                                Authentication
                            </h4>
                            <p class="text-muted mb-4">
                                Use your unique API Token to authenticate requests.
                            </p>
                            
                            <div class="mb-5">
                                <label class="form-label fw-bold text-dark mb-2">Your API Token</label>
                                <div class="input-group input-group-lg shadow-sm">
                                    <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                                        <i class="ti ti-key fs-4"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control font-monospace border-start-0 border-end-0 bg-white text-dark" 
                                           value="{{ Auth::user()->api_token }}" 
                                           id="apiToken" 
                                           readonly>
                                    <button class="btn btn-primary px-4" type="button" onclick="copyToken()">
                                        <span id="copyBtnText">Copy</span> <i class="ti ti-copy ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card bg-dark text-white border-0 shadow-lg overflow-hidden position-relative">
                                <div class="card-header bg-transparent border-white border-opacity-10 py-3">
                                     <h6 class="mb-0 fw-bold text-light"><i class="ti ti-code me-2"></i>Header Authorization</h6>
                                </div>
                                <div class="card-body bg-black bg-opacity-25 font-monospace p-4">
                                    <div class="d-flex">
                                        <span class="text-info me-3">Authorization:</span>
                                        <span class="text-warning">Bearer <span class="text-white-50">{{ substr(Auth::user()->api_token, 0, 15) }}...</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary d-inline-flex btn-lg align-items-center shadow-sm prev-tab-btn" data-prev="overview">
                            <i class="ti ti-arrow-left me-2"></i> Previous
                        </button>
                        <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="variations">
                            Next: Fetching Companies <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Variations Section -->
                <div class="docs-section d-none fade-in" id="variations">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold text-dark mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">2</span>
                                Fetching Electricity Companies
                            </h4>
                            <p class="text-muted mb-4">
                                Get a list of available electricity distribution companies and their <code>service_id</code>.
                            </p>

                            <!-- Endpoint -->
                            <div class="card border-0 bg-soft-primary mb-4 overflow-hidden">
                                <div class="card-body d-flex align-items-center justify-content-between p-4 flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">GET</span>
                                        <code class="text-primary fw-bold fs-18 text-break">{{ url('/') }}/api/v1/electricity/variations</code>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <!-- Success Response -->
                                <div class="col-lg-12">
                                    <div class="card border shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header bg-light border-bottom py-3">
                                            <h6 class="fw-bold text-success mb-0">Success Response (200 OK)</h6>
                                        </div>
                                        <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace"><code>{
  <span class="text-info">"status"</span>: <span class="text-warning">"success"</span>,
  <span class="text-info">"data"</span>: [
    {
      <span class="text-info">"service_name"</span>: <span class="text-warning">"Ikeja Electric (IKEDC)"</span>,
      <span class="text-info">"service_id"</span>: <span class="text-warning">"ikeja-electric"</span>,
      <span class="text-info">"variation_code"</span>: <span class="text-warning">"ikeja-electric-prepaid"</span>,
      <span class="text-info">"name"</span>: <span class="text-warning">"Ikeja Electric Prepaid"</span>,
      <span class="text-info">"variation_amount"</span>: <span class="text-warning">"0.00"</span>,
      <span class="text-info">"fixedPrice"</span>: <span class="text-warning">"No"</span>,
      <span class="text-info">"status"</span>: <span class="text-warning">"enabled"</span>
    },
    ...
  ]
}</code></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary d-inline-flex btn-lg align-items-center shadow-sm prev-tab-btn" data-prev="auth">
                            <i class="ti ti-arrow-left me-2"></i> Previous
                        </button>
                        <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="verify">
                            Next: Meter Verification <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Verify Section -->
                <div class="docs-section d-none fade-in" id="verify">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold text-dark mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">3</span>
                                Meter Verification
                            </h4>

                            <!-- Endpoint -->
                            <div class="card border-0 bg-soft-primary mb-4 overflow-hidden">
                                <div class="card-body d-flex align-items-center justify-content-between p-4 flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">POST</span>
                                        <code class="text-primary fw-bold fs-18 text-break">{{ url('/') }}/api/v1/electricity/verify</code>
                                    </div>
                                </div>
                            </div>

                            <p class="text-muted mb-4">
                                Verify the customer's meter number before making a payment.
                            </p>

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header bg-light border-bottom py-3">
                                            <h6 class="fw-bold text-dark mb-0">Request Body</h6>
                                        </div>
                                        <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace"><code>{
  <span class="text-info">"serviceID"</span>: <span class="text-warning">"ikeja-electric"</span>,
  <span class="text-info">"billersCode"</span>: <span class="text-warning">"01010101010"</span>, // Meter Number
  <span class="text-info">"variation_code"</span>: <span class="text-warning">"ikeja-electric-prepaid"</span> // or "ikeja-electric-postpaid"
}</code></pre>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header bg-light border-bottom py-3">
                                            <h6 class="fw-bold text-success mb-0">Success Response</h6>
                                        </div>
                                        <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace"><code>{
  <span class="text-info">"status"</span>: <span class="text-warning">"success"</span>,
  <span class="text-info">"data"</span>: {
    <span class="text-info">"Customer_Name"</span>: <span class="text-warning">"JOHN DOE"</span>,
    <span class="text-info">"Meter_Number"</span>: <span class="text-warning">"01010101010"</span>,
    <span class="text-info">"Address"</span>: <span class="text-warning">"123 LAGOS WAY"</span>
    ...
  }
}</code></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary d-inline-flex btn-lg align-items-center shadow-sm prev-tab-btn" data-prev="variations">
                            <i class="ti ti-arrow-left me-2"></i> Previous
                        </button>
                        <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="endpoint">
                            Next: Payment Endpoint <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Purchase Endpoint Section -->
                <div class="docs-section d-none fade-in" id="endpoint">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold text-dark mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">4</span>
                                Payment Endpoint
                            </h4>

                            <!-- Endpoint -->
                            <div class="card border-0 bg-soft-primary mb-4 overflow-hidden">
                                <div class="card-body d-flex align-items-center justify-content-between p-4 flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">POST</span>
                                        <code class="text-primary fw-bold fs-18 text-break">{{ url('/') }}/api/v1/electricity/purchase</code>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <!-- Request Body -->
                                <div class="col-lg-6">
                                    <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header bg-light border-bottom py-3">
                                            <h6 class="fw-bold text-dark mb-0">Request Body</h6>
                                        </div>
                                        <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace"><code>{
  <span class="text-info">"serviceID"</span>: <span class="text-warning">"ikeja-electric"</span>,
  <span class="text-info">"billersCode"</span>: <span class="text-warning">"01010101010"</span>,
  <span class="text-info">"variation_code"</span>: <span class="text-warning">"ikeja-electric-prepaid"</span>,
  <span class="text-info">"amount"</span>: <span class="text-warning">"1000"</span>,
  <span class="text-info">"phone"</span>: <span class="text-warning">"07037343660"</span>,
  <span class="text-info">"request_id"</span>: <span class="text-warning">"ELEC_REF_99105"</span> 
}</code></pre>
                                        </div>
                                    </div>
                                </div>

                                <!-- Success Response -->
                                <div class="col-lg-6">
                                    <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header bg-light border-bottom py-3">
                                            <h6 class="fw-bold text-success mb-0">Success Response (200 OK)</h6>
                                        </div>
                                        <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace"><code>{
  <span class="text-info">"status"</span>: <span class="text-warning">"success"</span>,
  <span class="text-info">"message"</span>: <span class="text-warning">"Electricity payment successful"</span>,
  <span class="text-info">"data"</span>: {
    <span class="text-info">"transaction_ref"</span>: <span class="text-warning">"202403210001235"</span>,
    <span class="text-info">"purchased_code"</span>: <span class="text-warning">"1234-5678-9012-3456"</span>, // Token
    <span class="text-info">"amount"</span>: <span class="text-warning">"1000.00"</span>,
    <span class="text-info">"status"</span>: <span class="text-warning">"completed"</span>
    ...
  }
}</code></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary d-inline-flex btn-lg align-items-center shadow-sm prev-tab-btn" data-prev="verify">
                            <i class="ti ti-arrow-left me-2"></i> Previous
                        </button>
                        <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="codes">
                            Next: Commissions <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                 <!-- Commissions Section -->
                 <div class="docs-section d-none fade-in" id="codes">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold text-dark mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">5</span>
                                Commissions & Incentives
                            </h4>
                            
                            <p class="text-muted mb-4">
                                Below are the cashback rates applied to each company for your account type (<strong>{{ ucfirst($user->role ?? 'User') }}</strong>).
                            </p>

                            <div class="table-responsive rounded-3 border">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr class="text-uppercase small text-muted">
                                            <th class="py-3 ps-4">Company</th>
                                            <th class="py-3 text-center">Field Code</th>
                                            <th class="py-3 text-end pe-4">Cashback %</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @foreach($companies as $code => $name)
                                        <tr>
                                            <td class="ps-4 fw-bold">{{ $name }}</td>
                                            <td class="text-center">
                                                <code class="text-primary bg-primary bg-opacity-10 px-2 py-1 rounded fw-bold">{{ $code }}</code>
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="badge bg-soft-success text-success">
                                                    {{ $commissions[$code] ?? 0 }}%
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                     <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary d-inline-flex btn-lg align-items-center shadow-sm prev-tab-btn" data-prev="endpoint">
                            <i class="ti ti-arrow-left me-2"></i> Previous
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.docs-section').forEach(section => {
                section.style.display = 'none'; 
                section.classList.remove('active-section');
            });
            const target = document.getElementById(tabId);
            if (target) {
                target.style.display = 'block';
                void target.offsetWidth; 
                setTimeout(() => target.classList.add('active-section'), 10);
            }
            
             document.querySelectorAll('.docs-section').forEach(section => {
                 if (section.id !== tabId) section.classList.add('d-none');
                 else section.classList.remove('d-none');
            });

            document.querySelectorAll('.custom-sidebar-nav a').forEach(link => {
                link.classList.remove('active', 'bg-soft-primary', 'text-primary', 'fw-bold');
                link.classList.add('text-muted');
                if (link.getAttribute('href') === '#' + tabId) {
                    link.classList.remove('text-muted');
                    link.classList.add('active', 'bg-soft-primary', 'text-primary', 'fw-bold');
                }
            });
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        document.addEventListener('DOMContentLoaded', () => {
             const hash = window.location.hash.substring(1);
             if (hash && document.getElementById(hash)) switchTab(hash);
             else switchTab('overview');
        });
        document.querySelectorAll('.next-tab-btn, .prev-tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                switchTab(this.getAttribute('data-next') || this.getAttribute('data-prev'));
            });
        });
        
        const bsOffcanvas = new bootstrap.Offcanvas(document.getElementById('mobileSidebar'));
        document.getElementById('sidebarToggle').addEventListener('click', () => bsOffcanvas.show());
        function closeOffcanvas() { bsOffcanvas.hide(); }

        function copyToken() {
            const el = document.getElementById('apiToken');
            el.select(); navigator.clipboard.writeText(el.value);
            document.getElementById('copyBtnText').innerText = 'Copied!';
            setTimeout(() => document.getElementById('copyBtnText').innerText = 'Copy', 2000);
        }
        function copyToClipboard(text) {
             navigator.clipboard.writeText(text);
             const btn = event.currentTarget;
             const orig = btn.innerHTML;
             btn.innerHTML = '<i class="ti ti-check me-1"></i> Copied';
             setTimeout(() => btn.innerHTML = orig, 2000);
        }
    </script>
    <style>
        .docs-section { opacity: 0; transition: opacity 0.3s ease-in-out; }
        .docs-section.active-section { opacity: 1; }
        .custom-sidebar-nav .list-group-item { transition: all 0.2s ease; font-weight: 500; }
        .custom-sidebar-nav .list-group-item:hover { background-color: rgba(var(--bs-primary-rgb), 0.05); color: var(--bs-primary) !important; transform: translateX(5px); }
        .custom-sidebar-nav .list-group-item.active { background-color: rgba(var(--bs-primary-rgb), 0.1); color: var(--bs-primary); border-left: 3px solid var(--bs-primary); }
        @media (max-width: 991.98px) { .sticky-top { position: relative !important; top: 0 !important; z-index: 1 !important; } }
    </style>
    @endpush
</x-app-layout>
