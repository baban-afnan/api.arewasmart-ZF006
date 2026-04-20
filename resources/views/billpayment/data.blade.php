<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'Data Bundle API' }}</title>
    <div class="content container-fluid">
           <!-- Page Header -->
        <div class="page-header mb-5">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title fw-bold text-primary display-6">
                        Data Bundle API
                    </h3>

                    <ul class="breadcrumb bg-transparent p-0 mt-2 mb-1">
                        <li class="breadcrumb-item active text-primary fw-semibold">
                            API Documentation
                        </li>
                    </ul>

                    <p class="text-muted mb-0">
                        Integrate Data Bundle purchases for MTN, Airtel, Glo, and 9mobile into your application.
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
                                        <i class="ti ti-list me-2 opacity-75 fs-15"></i> Data Plans
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#endpoint" onclick="switchTab('endpoint'); return false;">
                                        <i class="ti ti-server me-2 opacity-75 fs-15"></i> Purchase Endpoint
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#codes" onclick="switchTab('codes'); return false;">
                                        <i class="ti ti-list-numbers me-2 opacity-75 fs-15"></i> Commissions
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Support Card -->
                        <div class="card border-0 shadow-sm rounded-4 mt-4 support-card-custom text-white overflow-hidden position-relative">
                            <div class="position-absolute top-0 end-0 p-3 opacity-25">
                                <i class="ti ti-headset fs-15"></i>
                            </div>
                            <div class="card-body p-4 position-relative z-index-1">
                                <h6 class="fw-bold text-white mb-2">Need Help?</h6>
                                <p class="small text-white-50 mb-3">Our support team is available 24/7 to assist with integration.</p>
                                <a href="https://wa.me/2347037343660" target="_blank" class="btn btn-support w-100 btn-sm rounded-pill fw-bold shadow-sm">
                                    <i class="ti ti-brand-whatsapp me-1 fs-15"></i> Contact Support
                                </a>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>

            <!-- Mobile Offcanvas Sidebar -->
            <div class="offcanvas offcanvas-start border-0 shadow-lg" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
                <div class="offcanvas-header sidebar-nav-header border-bottom">
                    <h5 class="offcanvas-title fw-bold" id="mobileSidebarLabel">Documentation</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    <div class="list-group list-group-flush custom-sidebar-nav p-3">
                        <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center active" href="#overview" onclick="switchTab('overview'); closeOffcanvas(); return false;">
                            <i class="ti ti-info-circle me-2 fs-15"></i> Overview
                        </a>
                        <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#auth" onclick="switchTab('auth'); closeOffcanvas(); return false;">
                            <i class="ti ti-shield-lock me-2 fs-15"></i> Authentication
                        </a>
                        <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#variations" onclick="switchTab('variations'); closeOffcanvas(); return false;">
                            <i class="ti ti-list fs-15"></i> Data Plans
                        </a>
                        <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#endpoint" onclick="switchTab('endpoint'); closeOffcanvas(); return false;">
                            <i class="ti ti-server me-2 fs-15"></i> Purchase Endpoint
                        </a>
                        <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#codes" onclick="switchTab('codes'); closeOffcanvas(); return false;">
                            <i class="ti ti-list-numbers me-2 fs-15"></i> Commissions
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                
                <!-- Overview Section -->
                <div class="docs-section active-section" id="overview">
                    <div class="card border-0 shadow-sm rounded-4 mb-4 position-relative overflow-hidden docs-card">
                        <div class="card-body p-xl-5 p-4">
                            <div class="w-100 text-center py-2 mb-4 intro-badge fw-bold text-uppercase rounded-1">Introduction</div>
                            <h2 class="fw-bold mb-3 text-body">Data Bundle API Guide</h2>
                            <p class="text-muted lead mb-5 docs-text">
                                Purchase data bundles for MTN, Airtel, Glo, and 9mobile instantly. 
                                Our API allows you to fetch available plans and make purchases seamlessly.
                            </p>
                            
                            <!-- Endpoint Box -->
                            <div class="rounded-4 p-4 api-box position-relative overflow-hidden bg-dark">
                                <label class="text-white small text-uppercase ls-1 fw-bold mb-3 d-block fs-14">API Base URL</label>
                                <div class="d-flex align-items-center rounded-3 p-3 api-inner border border-secondary border-opacity-25 bg-black bg-opacity-25">
                                    <code class="text-white fs-16 font-monospace flex-grow-1">{{ url('/') }}/api/v1</code>
                                    <button class="btn btn-sm rounded-pill px-4 ms-3 copy-btn text-white fw-medium d-flex align-items-center shadow-sm" onclick="copyToClipboard('{{ url('/') }}/api/v1')">
                                        <i class="ti ti-copy me-2"></i> Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                         <button class="btn btn-orange btn-lg next-tab-btn px-4 py-3 rounded-3 shadow" data-next="auth">
                            Next: Authentication <i class="ti ti-arrow-right ms-2"></i>
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
                            Next: Fetching Data Plans <i class="ti ti-arrow-right ms-2 fs-15"></i>
                        </button>
                    </div>
                </div>

                <!-- Variations Section -->
                <div class="docs-section d-none fade-in" id="variations">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">2</span>
                                Fetching Data Plans
                            </h4>
                            <p class="text-muted mb-4">
                                Get a list of available data bundles and their <code>variation_code</code>.
                            </p>

                            <!-- Endpoint -->
                            <div class="card border-0 bg-soft-primary mb-4 overflow-hidden">
                                <div class="card-body d-flex align-items-center justify-content-between p-4 flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">GET</span>
                                        <code class="text-primary fw-bold fs-18 text-break">{{ url('/') }}/api/v1/data/variations</code>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <!-- Query Params -->
                                <div class="col-lg-12">
                                    <div class="table-responsive rounded-3 border custom-table-border mb-4">
                                        <table class="table table-premium table-hover align-middle mb-0">
                                            <thead>
                                                <tr class="text-uppercase small text-muted">
                                                    <th class="py-3 ps-4">Parameter</th>
                                                    <th class="py-3">Type</th>
                                                    <th class="py-3">Required</th>
                                                    <th class="py-3 pe-4">Description</th>
                                                </tr>
                                            </thead>
                                            <tbody class="border-top-0">
                                                <tr>
                                                    <td class="ps-4 fw-medium text-body">network</td>
                                                    <td class="text-muted">String</td>
                                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded px-2 py-1">Optional</span></td>
                                                    <td class="pe-4 text-muted">Filter by network (mtn-data, airtel-data, glo-data, etisalat-data)</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

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
      <span class="text-info">"service_name"</span>: <span class="text-warning">"MTN Data"</span>,
      <span class="text-info">"service_id"</span>: <span class="text-warning">"mtn-data"</span>,
      <span class="text-info">"variation_code"</span>: <span class="text-warning">"mtn-100mb-100"</span>,
      <span class="text-info">"name"</span>: <span class="text-warning">"MTN Data 100MB - 1 Day"</span>,
      <span class="text-info">"variation_amount"</span>: <span class="text-warning">"100.00"</span>,
      <span class="text-info">"fixedPrice"</span>: <span class="text-warning">"Yes"</span>,
      <span class="text-info">"status"</span>: <span class="text-warning">"enabled"</span>
    }
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
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">3</span>
                                Purchase Endpoint
                            </h4>

                            <!-- Endpoint -->
                            <div class="card border-0 bg-soft-primary mb-4 overflow-hidden">
                                <div class="card-body d-flex align-items-center justify-content-between p-4 flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">POST</span>
                                        <code class="text-primary fw-bold fs-18 text-break">{{ url('/') }}/api/v1/data/purchase</code>
                                    </div>
                                </div>
                            </div>

                            <p class="text-muted mb-4">
                                Note: The <code>network</code> and <code>bundle</code> fields must match the <code>service_id</code> and <code>variation_code</code> respectively as found in our <a href="#variations" onclick="switchTab('variations')">Data Plans</a> list.
                            </p>

                            <div class="row g-4">
                                <!-- Request Body -->
                                <div class="col-lg-6">
                                    <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header border-bottom py-3">
                                            <h6 class="fw-bold mb-0">Request Body</h6>
                                        </div>
                                        <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace"><code>{
  <span class="text-info">"network"</span>: <span class="text-warning">"mtn-data"</span>, // must match database service_id
  <span class="text-info">"mobileno"</span>: <span class="text-warning">"07037343660"</span>,
  <span class="text-info">"bundle"</span>: <span class="text-warning">"mtn-10mb-100"</span>, // must match database variation_code
  <span class="text-info">"request_id"</span>: <span class="text-warning">"DATA_REF_991051eff7"</span> 
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
  <span class="text-info">"message"</span>: <span class="text-warning">"Data purchase successful"</span>,
  <span class="text-info">"data"</span>: {
    <span class="text-info">"transaction_ref"</span>: <span class="text-warning">"202403210001234"</span>,
    <span class="text-info">"request_id"</span>: <span class="text-warning">"DATA_REF_991051eff7"</span>,
    <span class="text-info">"amount"</span>: <span class="text-warning">"100.00"</span>,
    <span class="text-info">"paid_amount"</span>: <span class="text-warning">"100.00"</span>,
    <span class="text-info">"commission_earned"</span>: <span class="text-warning">"3.00"</span>,
    <span class="text-info">"new_balance"</span>: <span class="text-warning">"4900.00"</span>,
    <span class="text-info">"status"</span>: <span class="text-warning">"completed"</span>
  }
}</code></pre>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold mt-4 mb-3">Error Response (400)</h6>
                                    <pre class="bg-dark text-white rounded-3 p-4 mb-0"><code class="language-json">{
  <span class="text-info">"status"</span>: <span class="text-warning">"error"</span>,
  <span class="text-info">"message"</span>: <span class="text-warning">"Data purchase failed."</span>,
  <span class="text-info">"upstream_response"</span>: {
    <span class="text-info">"code"</span>: <span class="text-warning">"016"</span>,
    <span class="text-info">"response_description"</span>: <span class="text-warning">"TRANSACTION FAILED"</span>,
    <span class="text-info">"requestId"</span>: <span class="text-warning">"DATA_REF_99099712"</span>,
    <span class="text-info">"amount"</span>: <span class="text-warning">100</span>,
    <span class="text-info">"transaction_date"</span>: <span class="text-warning">"2026-01-18T12:19:40.000Z"</span>
  }
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary d-inline-flex btn-lg align-items-center shadow-sm prev-tab-btn" data-prev="variations">
                            <i class="ti ti-arrow-left me-2 fs-15"></i> Previous
                        </button>
                        <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="codes">
                            Next: Commissions <i class="ti ti-arrow-right ms-2 fs-15"></i>
                        </button>
                    </div>
                </div>

                 <!-- Commissions Section -->
                 <div class="docs-section d-none fade-in" id="codes">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">4</span>
                                Commissions & Incentives
                            </h4>
                            
                            <p class="text-muted mb-4">
                                Below are the cashback rates applied to each network for your account type (<strong>{{ ucfirst($user->role ?? 'User') }}</strong>). These are fetched dynamically from our <code>Data</code> service configuration.
                            </p>

                            <div class="table-responsive rounded-3 border custom-table-border">
                                <table class="table table-premium table-hover align-middle mb-0">
                                    <thead>
                                        <tr class="text-uppercase small text-muted">
                                            <th class="py-3 ps-4">Network</th>
                                            <th class="py-3 text-center">Service ID</th>
                                            <th class="py-3 text-end pe-4">Cashback %</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @foreach($networks as $code => $name)
                                        <tr>
                                            <td class="ps-4 fw-medium text-body">{{ $name }}</td>
                                            <td class="text-center">
                                                <code class="text-primary border border-primary border-opacity-25 rounded px-2 py-1 fw-bold fs-14 bg-primary bg-opacity-10">{{ $code }}</code>
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                                                    {{ $commissions[$code] ?? 0 }}%
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-4">
                                <div class="alert alert-info border border-info border-opacity-25 bg-info bg-opacity-10 rounded-4 d-flex align-items-start p-4 mb-0" role="alert">
                                    <i class="ti ti-info-circle me-3 mt-1 text-info fs-15"></i>
                                    <div class="text-body text-opacity-75">
                                        <h6 class="fw-bold mb-2 text-info">How Commission Works:</h6>
                                        <ul class="mb-0 ps-3 text-white">
                                            <li class="mb-2">We debit the <strong>full amount</strong> of the data plan from your main balance.</li>
                                            <li class="mb-2">Immediately upon success, the <strong>commission percentage</strong> is credited back to your Bonus Wallet.</li>
                                            <li>Two transaction records are created: One for the <strong>Debit</strong> and one for the <strong>Cashback (Bonus)</strong>.</li>
                                        </ul>
                                    </div>
                                </div>
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
        
        /* New Premium Sidebar Header */
        .sidebar-nav-header { 
            background-color: #FFF5F2 !important; 
            border-bottom: 1px solid #f8e1da;
        }
        .dark-mode .sidebar-nav-header {
            background-color: rgba(229, 113, 94, 0.1) !important;
            border-bottom: 1px solid rgba(229, 113, 94, 0.2);
        }
        .sidebar-nav-header h6, .sidebar-nav-header h5 { color: #e5715e !important; }

        /* Sidebar Navigation Items as Individual Cards */
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
            background-color: #0d6efd !important; /* Vivid blue */
            border-color: #0d6efd !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
        }

        .dark-mode .custom-sidebar-nav .list-group-item {
            background-color: #1e2532 !important; 
            border-color: #2b3346 !important;
            color: #94a3b8 !important;
        }

        /* Support Card Redesign */
        .support-card-custom { background-color: #1e2532 !important; }
        .support-card-custom .btn-support { 
            background-color: #ffffff !important; 
            color: #3b82f6 !important; 
            font-weight: 700;
        }

        /* Unified Premium Dark Template Styles */
        .dark-mode .docs-card {
            background-color: #1e2532 !important;
            border: 1px solid #2b3346 !important;
        }
        
        .dark-mode .api-box {
            background-color: #242b3b !important;
            border: none;
        }

        .dark-mode .api-inner {
            background-color: #1a202c !important;
        }

        .dark-mode .intro-badge {
            background-color: rgba(26, 43, 75, 0.5) !important;
            color: #f97316 !important;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        .intro-badge {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .copy-btn {
            background-color: #297a81 !important; 
            border: none !important;
        }
        .copy-btn:hover { background-color: #1d5b62 !important; }

        .btn-orange {
            background-color: #ea580c !important;
            border-color: #ea580c !important;
            color: white !important;
        }
        .btn-orange:hover {
            background-color: #c2410c !important;
            color: white !important;
        }

        /* Badges */
        .bg-indigo-soft { background-color: rgba(102, 16, 242, 0.1); }
        .text-indigo { color: #6610f2; }
        .bg-teal-soft { background-color: rgba(32, 201, 151, 0.1); }
        .text-teal { color: #20c997; }

        .dark-mode .custom-table-border { border-color: rgba(255, 255, 255, 0.1) !important; }

        @media (max-width: 991.98px) { .sticky-top { position: relative !important; top: 0 !important; z-index: 1 !important; } }
    </style>
    @endpush
</x-app-layout>
