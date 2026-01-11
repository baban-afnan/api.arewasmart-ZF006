<x-app-layout>
    <div class="content container-fluid">
         <!-- Page Header -->
        <div class="page-header mb-5">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title fw-bold text-primary display-6">
                        NIN IPE AND Validation  API
                    </h3>

                    <ul class="breadcrumb bg-transparent p-0 mt-2 mb-1">
                        <li class="breadcrumb-item active text-primary fw-semibold">
                            API Documentation
                        </li>
                    </ul>

                    <p class="text-muted mb-0">
                        NIN IPE AND Validation  API requests
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
                                    <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#services" onclick="switchTab('services'); return false;">
                                        <i class="ti ti-currency-naira me-2 fs-5 opacity-75"></i> Services & Pricing
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#auth" onclick="switchTab('auth'); return false;">
                                        <i class="ti ti-shield-lock me-2 fs-5 opacity-75"></i> Authentication
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#submit" onclick="switchTab('submit'); return false;">
                                        <i class="ti ti-server me-2 fs-5 opacity-75"></i> Submit Request
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#status" onclick="switchTab('status'); return false;">
                                        <i class="ti ti-activity me-2 fs-5 opacity-75"></i> Check Status
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
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#services" onclick="switchTab('services'); closeOffcanvas(); return false;">
                            <i class="ti ti-currency-naira me-2"></i> Services & Pricing
                        </a>
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#auth" onclick="switchTab('auth'); closeOffcanvas(); return false;">
                            <i class="ti ti-shield-lock me-2"></i> Authentication
                        </a>
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#submit" onclick="switchTab('submit'); closeOffcanvas(); return false;">
                            <i class="ti ti-server me-2"></i> Submit Request
                        </a>
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#status" onclick="switchTab('status'); closeOffcanvas(); return false;">
                            <i class="ti ti-activity me-2"></i> Check Status
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
                            <h2 class="fw-bold text-dark mb-3">Integration Guide</h2>
                            <p class="text-muted lead mb-4">
                                Seamlessly integrate our robust NIN Validation and IPE Clearance services into your application. 
                                Our RESTful API ensures secure, fast, and reliable identity verification.
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
                         <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="services">
                            Next: Services <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Services Section -->
                <div class="docs-section d-none fade-in" id="services">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold text-dark mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">1</span>
                                Services & Pricing
                            </h4>
                            
                            <!-- Refund Policy Alert - Requested Item -->
                            <div class="alert alert-warning border border-warning border-opacity-25 rounded-3 d-flex align-items-start p-4 mb-4" role="alert">
                                <i class="ti ti-info-circle fs-4 me-3 mt-1"></i>
                                <div>
                                    <h5 class="alert-heading fw-bold mb-2">Refund Policy</h5>
                                    <p class="mb-0 text-dark">
                                        Please note our refund policy regarding failed transaction attempts:
                                    </p>
                                    <ul class="mb-0 mt-2">
                                        <li class="mb-1"><strong>IPE Clearance (002):</strong> <span class="badge bg-success bg-opacity-10 text-success ms-1">Auto-Refund</span> We provide instant automatic refunds for failed or rejected IPE requests.</li>
                                        <li><strong>NIN Validation (015):</strong> <span class="badge bg-danger bg-opacity-10 text-danger ms-1">No Refund</span> There are <strong>no refunds</strong> for failed or rejected validation requests. Ensure data is accurate before submission.</li>
                                    </ul>
                                </div>
                            </div>

                            <p class="text-muted mb-4">
                                Use the corresponding <strong>Service Field Code</strong> when initializing a transaction.
                            </p>
                            
                            <div class="table-responsive rounded-3 border">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr class="text-uppercase small text-muted">
                                            <th class="py-3 ps-4">Service Name</th>
                                            <th class="py-3">Type</th>
                                            <th class="py-3">Field Code (Required)</th>
                                            <th class="py-3 text-end pe-4">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($services as $service)
                                            <tr>
                                                <td class="ps-4 fw-medium text-dark">{{ $service->name }}</td>
                                                <td>
                                                    @if($service->type == 'Validation')
                                                        <span class="badge bg-indigo-soft text-indigo border border-indigo border-opacity-10 py-2 px-3 rounded-pill">Validation</span>
                                                    @else
                                                        <span class="badge bg-teal-soft text-teal border border-teal border-opacity-10 py-2 px-3 rounded-pill">IPE Clearance</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <code class="bg-light text-primary border rounded px-2 py-1 fw-bold fs-14">{{ $service->code }}</code>
                                                </td>
                                                <td class="text-end pe-4 fw-bold text-dark">₦{{ number_format($service->price, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">No active services found used by this account.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary d-inline-flex btn-lg align-items-center shadow-sm prev-tab-btn" data-prev="overview">
                            <i class="ti ti-arrow-left me-2"></i> Previous
                        </button>
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
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">2</span>
                                Authentication
                            </h4>
                            <p class="text-muted mb-4">
                                Security is paramount. All requests must be authenticated using your unique API Bearer Token.
                            </p>
                            
                            <div class="mb-5">
                                <label class="form-label fw-bold text-dark mb-2">Your Personal API Token</label>
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
                                <div class="form-text mt-2"><i class="ti ti-lock me-1"></i> Keep this token secret. Do not share it publicly.</div>
                            </div>

                            <div class="card bg-dark text-white border-0 shadow-lg overflow-hidden position-relative">
                                <div class="card-header bg-transparent border-white border-opacity-10 py-3">
                                     <h6 class="mb-0 fw-bold text-light"><i class="ti ti-code me-2"></i>Header Authorization Example</h6>
                                </div>
                                <div class="card-body bg-black bg-opacity-25 font-monospace p-4">
                                    <div class="d-flex">
                                        <span class="text-info me-3">Authorization:</span>
                                        <span class="text-warning">Bearer <span class="text-white-50">{{ substr(Auth::user()->api_token, 0, 15) }}...</span></span>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <span class="text-info me-3">Accept:</span>
                                        <span class="text-warning">application/json</span>
                                    </div>
                                     <div class="d-flex mt-2">
                                        <span class="text-info me-3">Content-Type:</span>
                                        <span class="text-warning">application/json</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary d-inline-flex btn-lg align-items-center shadow-sm prev-tab-btn" data-prev="services">
                            <i class="ti ti-arrow-left me-2"></i> Previous
                        </button>
                        <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="submit">
                            Next: Submit Request <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Request Section -->
                <div class="docs-section d-none fade-in" id="submit">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold text-dark mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">3</span>
                                Submit a Request
                            </h4>

                            <!-- Endpoint -->
                            <div class="card border-0 bg-soft-primary mb-4 overflow-hidden">
                                <div class="card-body d-flex align-items-center justify-content-between p-4 flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">POST</span>
                                        <code class="text-primary fw-bold fs-18 text-break">{{ url('/') }}/api/v1/nin/validation</code>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <!-- Request Body -->
                                <div class="col-lg-6">
                                    <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header bg-light border-bottom py-3">
                                            <h6 class="fw-bold text-dark mb-0">Request Payload</h6>
                                        </div>
                                        <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace"><code>{
  <span class="text-muted">// Required. 015 for Validation, 002 for IPE</span>
  <span class="text-info">"field_code"</span>: <span class="text-warning">"015"</span>, 

  <span class="text-muted">// Required if field_code is 015</span>
  <span class="text-info">"nin"</span>: <span class="text-warning">"12345678901"</span>,
  
  <span class="text-muted">// Required if field_code is 002</span>
  <span class="text-info">"tracking_id"</span>: <span class="text-warning">"IPE-1234..."</span>,
  
  <span class="text-info">"description"</span>: <span class="text-warning">"My Reference"</span>
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
  <span class="text-info">"success"</span>: <span class="text-warning">true</span>,
  <span class="text-info">"message"</span>: <span class="text-warning">"Request submitted..."</span>,
  <span class="text-info">"data"</span>: {
    <span class="text-info">"reference"</span>: <span class="text-warning">"REF-123..."</span>,
    <span class="text-info">"trx_ref"</span>: <span class="text-warning">"val123..."</span>,
    <span class="text-info">"status"</span>: <span class="text-warning">"processing"</span>,
    <span class="text-info">"response"</span>: <span class="text-warning">"..."</span>
  }
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
                        <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="status">
                            Next: Check Status <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Status Section -->
                <div class="docs-section d-none fade-in" id="status">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold text-dark mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">4</span>
                                Check Transaction Status
                            </h4>

                            <!-- Endpoint -->
                             <div class="card border-0 bg-soft-success mb-4 overflow-hidden">
                                <div class="card-body d-flex align-items-center justify-content-between p-4 flex-wrap gap-2">
                                     <div class="d-flex align-items-center">
                                        <span class="badge bg-success px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">GET</span>
                                        <code class="text-success fw-bold fs-18 text-break">{{ url('/') }}/api/v1/nin/validation</code>
                                    </div>
                                </div>
                            </div>
                            
                            <p class="text-muted mb-4">
                                Retrieve the current status using a query parameter.
                            </p>

                            <div class="row g-4">
                                <div class="col-lg-6">
                                     <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header bg-light border-bottom py-3">
                                            <h6 class="fw-bold text-dark mb-0">Query Parameters</h6>
                                        </div>
                                        <div class="card-body p-4 bg-white">
                                            <ul class="list-unstyled mb-0 font-monospace text-dark">
                                                <li class="mb-3">
                                                    <span class="badge bg-light text-dark border me-2">OPTION 1</span>
                                                    <strong>?nin=</strong><span class="text-muted">12345678901</span>
                                                </li>
                                                <li>
                                                    <span class="badge bg-light text-dark border me-2">OPTION 2</span>
                                                    <strong>?nin=</strong><span class="text-muted">TRACKING-ID</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                     <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header bg-light border-bottom py-3">
                                            <h6 class="fw-bold text-success mb-0">Response</h6>
                                        </div>
                                        <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace"><code>{
  <span class="text-info">"success"</span>: <span class="text-warning">true</span>,
  <span class="text-info">"nin"</span>: <span class="text-warning">"12345678901"</span>,
  <span class="text-info">"status"</span>: <span class="text-warning">"successful"</span>,
  <span class="text-info">"comment"</span>: <span class="text-warning">"{ JSON Data Here }"</span>
}</code></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary d-inline-flex btn-lg align-items-center shadow-sm prev-tab-btn" data-prev="submit">
                            <i class="ti ti-arrow-left me-2"></i> Previous
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Tab switching logic
        function switchTab(tabId) {
            // Hide all sections with fade
            document.querySelectorAll('.docs-section').forEach(section => {
                section.classList.add('d-none');
                section.classList.remove('active-section');
            });
            
            // Show target section
            const targetSection = document.getElementById(tabId);
            if (targetSection) {
                targetSection.classList.remove('d-none');
                // Small timeout to allow d-none removal to register before opacity transition
                setTimeout(() => targetSection.classList.add('active-section'), 10);
            }

            // Update sidebar active state
            document.querySelectorAll('.custom-sidebar-nav a').forEach(link => {
                link.classList.remove('active', 'bg-soft-primary', 'text-primary', 'fw-bold');
                link.classList.add('text-muted');
                
                // Check if this link corresponds to the tabId
                if (link.getAttribute('href') === '#' + tabId) {
                    link.classList.remove('text-muted');
                    link.classList.add('active', 'bg-soft-primary', 'text-primary', 'fw-bold');
                }
            });

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Initialize active tab logic on load
        document.addEventListener('DOMContentLoaded', () => {
             const hash = window.location.hash.substring(1);
             if (hash && document.getElementById(hash)) {
                 switchTab(hash);
             } else {
                 // Ensure Overview is active by default visuals
                 document.getElementById('overview').classList.add('active-section');
             }
        });

        // Navigation Buttons
        document.querySelectorAll('.next-tab-btn, .prev-tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-next') || this.getAttribute('data-prev');
                switchTab(targetTab);
            });
        });

        // Mobile Sidebar Logic
        const mobileSidebar = document.getElementById('mobileSidebar');
        const bsOffcanvas = new bootstrap.Offcanvas(mobileSidebar);
        
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            bsOffcanvas.show();
        });

        function closeOffcanvas() {
            bsOffcanvas.hide();
        }

        // Copy Token Logic
        function copyToken() {
            const tokenInput = document.getElementById('apiToken');
            tokenInput.select();
            tokenInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(tokenInput.value).then(() => {
                const btnText = document.getElementById('copyBtnText');
                const originalText = btnText.innerText;
                btnText.innerText = 'Copied!';
                
                // Show floating alert
                const notif = document.createElement('div');
                notif.className = 'alert alert-success position-fixed top-0 end-0 m-3 shadow-lg fw-bold';
                notif.style.zIndex = '9999';
                notif.innerHTML = '<i class="ti ti-check me-2"></i> Token copied!';
                document.body.appendChild(notif);
                
                setTimeout(() => {
                    notif.remove();
                    btnText.innerText = originalText;
                }, 2000);
            });
        }
        
        function copyToClipboard(text) {
             navigator.clipboard.writeText(text).then(() => {
                const notif = document.createElement('div');
                notif.className = 'alert alert-info position-fixed top-0 end-0 m-3 shadow-lg fw-bold';
                notif.style.zIndex = '9999';
                notif.innerHTML = '<i class="ti ti-check me-2"></i> Copied to clipboard!';
                document.body.appendChild(notif);
                setTimeout(() => notif.remove(), 2000);
            });
        }
    </script>
    <style>
        /* Smooth Fade Transitions */
        .docs-section { opacity: 0; transition: opacity 0.3s ease-in-out; }
        .docs-section.active-section { opacity: 1; }
        
        /* Sidebar Styling */
        .custom-sidebar-nav .list-group-item { transition: all 0.2s ease; font-weight: 500; }
        .custom-sidebar-nav .list-group-item:hover { background-color: rgba(var(--bs-primary-rgb), 0.05); color: var(--bs-primary) !important; transform: translateX(5px); }
        .custom-sidebar-nav .list-group-item.active { background-color: rgba(var(--bs-primary-rgb), 0.1); color: var(--bs-primary); border-left: 3px solid var(--bs-primary); }
        
        /* Badges */
        .bg-indigo-soft { background-color: rgba(102, 16, 242, 0.1); }
        .text-indigo { color: #6610f2; }
        .bg-teal-soft { background-color: rgba(32, 201, 151, 0.1); }
        .text-teal { color: #20c997; }

        /* Mobile Adjustments */
        @media (max-width: 991.98px) {
            .sticky-top { position: relative !important; top: 0 !important; z-index: 1 !important; }
        }
    </style>
    @endpush
</x-app-layout>
