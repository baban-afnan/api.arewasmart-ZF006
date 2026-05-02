<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'Education Pin API' }}</title>
    <div class="content container-fluid">
           <!-- Page Header -->
        <div class="page-header mb-5">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title fw-bold text-primary display-6">
                        Education Pin API
                    </h3>

                    <ul class="breadcrumb bg-transparent p-0 mt-2 mb-1">
                        <li class="breadcrumb-item active text-primary fw-semibold">
                            API Documentation
                        </li>
                    </ul>

                    <p class="text-muted mb-0">
                        Integrate Education Pins for JAMB & DE, WAEC, NECO, and NABTEB. 
                    </p>
                </div>
                <div class="col-auto">
                    <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fw-medium fs-14 border border-primary border-opacity-10">
                        <i class="ti ti-tag me-1 fs-15"></i> Version 1.0.0
                    </span>
                    <!-- Mobile Sidebar Toggle Button -->
                    <button class="btn btn-white shadow-sm d-lg-none ms-2 rounded-circle p-2" type="button" id="sidebarToggle" aria-label="Toggle Navigation">
                        <i class="ti ti-menu-2 text-primary fs-15"></i>
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
                                <div class="p-4 sidebar-nav-header">
                                    <h6 class="fw-bold mb-0 d-flex align-items-center">
                                        <i class="ti ti-menu-deep me-2 fs-15"></i> Navigation
                                    </h6>
                                </div>
                                <div class="list-group list-group-flush custom-sidebar-nav p-3">
                                    <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center active" href="#overview" onclick="switchTab('overview'); return false;">
                                        <i class="ti ti-info-circle me-2 opacity-75 fs-15"></i> Overview
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#auth" onclick="switchTab('auth'); return false;">
                                        <i class="ti ti-shield-lock me-2 opacity-75 fs-15"></i> Authentication
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#variations" onclick="switchTab('variations'); return false;">
                                        <i class="ti ti-list me-2 opacity-75 fs-15"></i> Pin Plans
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#verify" onclick="switchTab('verify'); return false;">
                                        <i class="ti ti-user-check me-2 opacity-75 fs-15"></i> Profile Verification
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#endpoint" onclick="switchTab('endpoint'); return false;">
                                        <i class="ti ti-server me-2 opacity-75 fs-15"></i> Purchase Endpoint
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#codes" onclick="switchTab('codes'); return false;">
                                        <i class="ti ti-list-numbers me-2 opacity-75 fs-15"></i> Pricing/Cashback
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Support Card -->
                        <div class="card border-0 shadow-lg rounded-4 mt-4 support-card-custom text-white overflow-hidden position-relative">
                            <div class="position-absolute top-0 end-0 p-3 opacity-25">
                                <i class="ti ti-headset fs-3"></i>
                            </div>
                            <div class="card-body p-4 position-relative z-index-1">
                                <h5 class="fw-bold text-white mb-2">Need Help?</h5>
                                <p class="small text-white-50 mb-3">Our support team is available 24/7.</p>
                                <a href="https://wa.me/2347037343660" target="_blank" class="btn btn-support w-100 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center py-2">
                                    <i class="ti ti-brand-whatsapp me-2 fs-5"></i> Contact Support
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
                            <i class="ti ti-info-circle me-2 fs-15"></i> Overview
                        </a>
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#auth" onclick="switchTab('auth'); closeOffcanvas(); return false;">
                            <i class="ti ti-shield-lock me-2 fs-15"></i> Authentication
                        </a>
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#variations" onclick="switchTab('variations'); closeOffcanvas(); return false;">
                            <i class="ti ti-list fs-15"></i> Pin Plans
                        </a>
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#verify" onclick="switchTab('verify'); closeOffcanvas(); return false;">
                            <i class="ti ti-user-check me-2 fs-15"></i> Verification
                        </a>
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#endpoint" onclick="switchTab('endpoint'); closeOffcanvas(); return false;">
                            <i class="ti ti-server me-2 fs-15"></i> Purchase Endpoint
                        </a>
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#codes" onclick="switchTab('codes'); closeOffcanvas(); return false;">
                            <i class="ti ti-list-numbers me-2 fs-15"></i> Pricing
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
                            <h2 class="fw-bold mb-3">Education Pin API Guide</h2>
                            <p class="text-muted lead mb-4">
                                Automate Education Pin purchases for JAMB & DE, WAEC, NECO, and NABTEB. 
                                Our API supports profile verification for JAMB to ensure correct profile ID usage.
                            </p>
                            
                            <!-- Endpoint Box -->
                            <div class="bg-dark rounded-4 p-4 text-white shadow-lg position-relative overflow-hidden">
                                <div class="position-absolute top-0 end-0 p-3 opacity-10">
                                    <i class="ti ti-world-www fs-15"></i>
                                </div>
                                <label class="text-white-50 small text-uppercase ls-1 fw-bold mb-2">Base URL</label>
                                <div class="d-flex align-items-center bg-white bg-opacity-10 rounded-3 p-3 border border-white border-opacity-10">
                                    <code class="text-white fs-16 font-monospace flex-grow-1">{{ url('/') }}/api/v1</code>
                                    <button class="btn btn-sm btn-secondary rounded-pill px-3 ms-3" onclick="copyToClipboard('{{ url('/') }}/api/v1')">
                                        <i class="ti ti-copy me-1 fs-15"></i> Copy
                                    </button>
                                </div>
                                <div class="mt-3 d-flex align-items-center small text-warning">
                                    <i class="ti ti-alert-triangle me-2 fs-15"></i>
                                    <span>Ensure all requests are made via <strong>HTTPS</strong>.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                         <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="auth">
                            Next: Authentication <i class="ti ti-arrow-right ms-2 fs-15"></i>
                        </button>
                    </div>
                </div>

                <!-- Authentication Section -->
                <div class="docs-section d-none fade-in" id="auth">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">1</span>
                                Authentication
                            </h4>
                            <p class="text-muted mb-4">
                                Use your unique API Token to authenticate requests.
                            </p>
                            
                            <div class="mb-5">
                                <label class="form-label fw-bold mb-2">Your API Token</label>
                                <div class="input-group input-group-lg shadow-sm">
                                    <span class="input-group-text border-end-0 text-muted ps-3">
                                        <i class="ti ti-key fs-15"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control font-monospace border-start-0 border-end-0" 
                                           value="{{ Auth::user()->api_token }}" 
                                           id="apiToken" 
                                           readonly>
                                    <button class="btn btn-primary px-4" type="button" onclick="copyToken()">
                                        <span id="copyBtnText">Copy</span> <i class="ti ti-copy ms-2 fs-15"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card bg-dark text-white border-0 shadow-lg overflow-hidden position-relative">
                                <div class="card-header bg-transparent border-white border-opacity-10 py-3">
                                     <h6 class="mb-0 fw-bold text-light"><i class="ti ti-code me-2 fs-15"></i>Header Authorization</h6>
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
                            <i class="ti ti-arrow-left me-2 fs-15"></i> Previous
                        </button>
                        <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="variations">
                            Next: Pin Plans <i class="ti ti-arrow-right ms-2 fs-15"></i>
                        </button>
                    </div>
                </div>

                <!-- Variations Section -->
                <div class="docs-section d-none fade-in" id="variations">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">2</span>
                                Fetching Pin Plans (Variations)
                            </h4>
                            <p class="text-muted mb-4">
                                Get a list of available plans for a specific service (e.g., <code>jamb</code>, <code>waec</code>, <code>neco</code>, <code>nabteb</code>).
                            </p>

                            <!-- Endpoint -->
                            <div class="card border-0 bg-soft-primary mb-4 overflow-hidden">
                                <div class="card-body d-flex align-items-center justify-content-between p-4 flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">GET</span>
                                        <code class="text-primary fw-bold fs-18 text-break">{{ url('/') }}/api/v1/education/variations</code>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <!-- Success Response -->
                                <div class="col-lg-12">
                                    <div class="card border shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header border-bottom py-3">
                                            <h6 class="fw-bold text-success mb-0">Success Response (200 OK)</h6>
                                        </div>
                                        <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace"><code>{
  <span class="text-info">"status"</span>: <span class="text-warning">"success"</span>,
  <span class="text-info">"data"</span>: [
    {
      <span class="text-info">"service_id"</span>: <span class="text-warning">"jamb"</span>,
      <span class="text-info">"code"</span>: <span class="text-warning">"utme"</span>,
      <span class="text-info">"name"</span>: <span class="text-warning">"UTME PIN"</span>,
      <span class="text-info">"amount"</span>: <span class="text-warning">"6200.00"</span>
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
                            <i class="ti ti-arrow-left me-2 fs-15"></i> Previous
                        </button>
                        <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="verify">
                            Next: Profile Verification <i class="ti ti-arrow-right ms-2 fs-15"></i>
                        </button>
                    </div>
                </div>

                <!-- Verify Section -->
                <div class="docs-section d-none fade-in" id="verify">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">3</span>
                                JAMB Profile Verification
                            </h4>

                            <!-- Endpoint -->
                            <div class="card border-0 bg-soft-primary mb-4 overflow-hidden">
                                <div class="card-body d-flex align-items-center justify-content-between p-4 flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">POST</span>
                                        <code class="text-primary fw-bold fs-18 text-break">{{ url('/') }}/api/v1/education/verify</code>
                                    </div>
                                </div>
                            </div>

                            <p class="text-muted mb-4">
                                Verify the candidate's JAMB Profile ID before making a payment.
                            </p>

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header bg-light border-bottom py-3">
                                            <h6 class="fw-bold  mb-0">Request Body</h6>
                                        </div>
                                        <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace"><code>{
  <span class="text-info">"serviceID"</span>: <span class="text-warning">"jamb"</span>,
  <span class="text-info">"billersCode"</span>: <span class="text-warning">"1234567890"</span>, // Profile ID
  <span class="text-info">"type"</span>: <span class="text-warning">"utme"</span> // or "direct-entry"
}</code></pre>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header border-bottom py-3">
                                            <h6 class="fw-bold text-success mb-0">Success Response</h6>
                                        </div>
                                        <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace"><code>{
  <span class="text-info">"status"</span>: <span class="text-warning">"success"</span>,
  <span class="text-info">"data"</span>: {
    <span class="text-info">"Customer_Name"</span>: <span class="text-warning">"ADAMU AREWA"</span>,
    <span class="text-info">"Profile_ID"</span>: <span class="text-warning">"1234567890"</span>
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
                            <i class="ti ti-arrow-left me-2 fs-15"></i> Previous
                        </button>
                        <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="endpoint">
                            Next: Purchase Endpoint <i class="ti ti-arrow-right ms-2 fs-15"></i>
                        </button>
                    </div>
                </div>

                <!-- Purchase Endpoint Section -->
                <div class="docs-section d-none fade-in" id="endpoint">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">4</span>
                                Purchase Endpoint
                            </h4>

                            <!-- Endpoint -->
                            <div class="card border-0 bg-soft-primary mb-4 overflow-hidden">
                                <div class="card-body d-flex align-items-center justify-content-between p-4 flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">POST</span>
                                        <code class="text-primary fw-bold fs-18 text-break">{{ url('/') }}/api/v1/education/purchase</code>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <!-- Request Body -->
                                <div class="col-lg-6">
                                    <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header border-bottom py-3">
                                            <h6 class="fw-bold mb-0">Request Body</h6>
                                        </div>
                                        <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace"><code>{
  <span class="text-info">"serviceID"</span>: <span class="text-warning">"jamb"</span>,
  <span class="text-info">"billersCode"</span>: <span class="text-warning">"1234567890"</span>,
  <span class="text-info">"variation_code"</span>: <span class="text-warning">"utme"</span>,
  <span class="text-info">"amount"</span>: <span class="text-warning">"6200"</span>,
  <span class="text-info">"phone"</span>: <span class="text-warning">"07037343660"</span>,
  <span class="text-info">"request_id"</span>: <span class="text-warning">"EDU_REF_12345"</span> 
}</code></pre>
                                        </div>
                                    </div>
                                </div>

                                <!-- Success Response -->
                                <div class="col-lg-6">
                                    <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header border-bottom py-3">
                                            <h6 class="fw-bold text-success mb-0">Success Response (200 OK)</h6>
                                        </div>
                                        <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace"><code>{
  <span class="text-info">"status"</span>: <span class="text-warning">"success"</span>,
  <span class="text-info">"message"</span>: <span class="text-warning">"JAMB purchase successful"</span>,
  <span class="text-info">"data"</span>: {
    <span class="text-info">"transaction_ref"</span>: <span class="text-warning">"202403210001235"</span>,
    <span class="text-info">"amount"</span>: <span class="text-warning">"6200.00"</span>,
    <span class="text-info">"pin"</span>: <span class="text-warning">"8372-9283-1293-1234"</span>
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
                            <i class="ti ti-arrow-left me-2 fs-15"></i> Previous
                        </button>
                        <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="codes">
                            Next: Pricing <i class="ti ti-arrow-right ms-2 fs-15"></i>
                        </button>
                    </div>
                </div>

                 <!-- Commissions Section -->
                 <div class="docs-section d-none fade-in" id="codes">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">5</span>
                                Pricing & Cashback
                            </h4>
                            
                            <p class="text-muted mb-4">
                                Below are the rates and cashback applied to Education Pins for your account type (<strong>{{ ucfirst($user->role ?? 'User') }}</strong>).
                            </p>

                            <div class="table-responsive rounded-3 border custom-table-border">
                                <table class="table table-premium table-hover align-middle mb-0">
                                    <thead>
                                        <tr class="text-uppercase small text-muted">
                                            <th class="py-3 ps-4">Exam Body</th>
                                            <th class="py-3 text-center">Service ID</th>
                                            <th class="py-3 text-end pe-4">Cashback / Price</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @foreach($providers as $code => $name)
                                        <tr>
                                            <td class="ps-4 fw-bold">{{ $name }}</td>
                                            <td class="text-center">
                                                <code class="text-primary bg-primary bg-opacity-10 px-2 py-1 rounded fw-bold">{{ $code }}</code>
                                            </td>
                                            <td class="text-end pe-4">
                                                @if(in_array($code, ['jamb', 'waec']))
                                                    <span class="badge bg-soft-success text-success">
                                                        {{ $commissions[$code] ?? 0 }}% Cashback
                                                    </span>
                                                @else
                                                    <span class="badge bg-soft-primary text-primary">
                                                        ₦{{ number_format($commissions[$code] ?? 0, 2) }} (Fixed)
                                                    </span>
                                                @endif
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
                            <i class="ti ti-arrow-left me-2 fs-15"></i> Previous
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
             btn.innerHTML = '<i class="ti ti-check me-1 fs-15"></i> Copied';
             setTimeout(() => btn.innerHTML = orig, 2000);
        }
    </script>
    <style>
        .docs-section { opacity: 0; transition: opacity 0.3s ease-in-out; }
        .docs-section.active-section { opacity: 1; }
        
        .sidebar-nav-header { 
            background-color: #FFF5F2 !important; 
            border-bottom: 1px solid #f8e1da;
        }
        .dark-mode .sidebar-nav-header {
            background-color: rgba(229, 113, 94, 0.1) !important;
            border-bottom: 1px solid rgba(229, 113, 94, 0.2);
        }
        .sidebar-nav-header h6, .sidebar-nav-header h5 { color: #e5715e !important; }
 
        .custom-sidebar-nav .list-group-item { 
            transition: all 0.2s ease; 
            font-weight: 500; 
            background: var(--bg-card, #ffffff) !important; 
            color: var(--text-muted, #64748b) !important; 
            border: 1px solid var(--border-color, #eef2f6) !important;
            border-radius: 12px !important;
            margin-bottom: 10px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .custom-sidebar-nav .list-group-item:hover { 
            background-color: var(--bg-body, #f8fafc) !important; 
            color: var(--bs-primary) !important; 
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .custom-sidebar-nav .list-group-item.active { 
            background-color: #1A2B4B !important; 
            color: #ffffff !important; 
            border-color: #1A2B4B !important;
            box-shadow: 0 4px 12px rgba(26, 43, 75, 0.2);
            font-weight: bold; 
        }
        .dark-mode .custom-sidebar-nav .list-group-item.active {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
        }

        /* Support Card Redesign */
        .support-card-custom { 
            background-color: #f26922 !important; 
            border-radius: 20px !important;
        }
        .support-card-custom .btn-support { 
            background-color: #ffffff !important; 
            color: #f26922 !important; 
            font-weight: 700;
            transition: all 0.3s ease;
        }
        .support-card-custom .btn-support:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
            background-color: #f8fafc !important;
        }
        .dark-mode .support-card-custom {
            background-color: #d95d1e !important;
        }

        @media (max-width: 991.98px) { .sticky-top { position: relative !important; top: 0 !important; z-index: 1 !important; } }
    </style>
    @endpush
</x-app-layout>
