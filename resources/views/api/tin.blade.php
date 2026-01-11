<x-app-layout>
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-5">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title fw-bold text-primary display-6">
                        TIN Registration API
                    </h3>

                    <ul class="breadcrumb bg-transparent p-0 mt-2 mb-1">
                        <li class="breadcrumb-item active text-primary fw-semibold">
                            API Documentation
                        </li>
                    </ul>

                    <p class="text-muted mb-0">
                        View and manage TIN Registration API requests
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
                                    <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#endpoint" onclick="switchTab('endpoint'); return false;">
                                        <i class="ti ti-server me-2 fs-5 opacity-75"></i> Register Endpoint
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#responses" onclick="switchTab('responses'); return false;">
                                        <i class="ti ti-message-code me-2 fs-5 opacity-75"></i> Responses & Billing
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
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#endpoint" onclick="switchTab('endpoint'); closeOffcanvas(); return false;">
                            <i class="ti ti-server me-2"></i> Register Endpoint
                        </a>
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#responses" onclick="switchTab('responses'); closeOffcanvas(); return false;">
                            <i class="ti ti-message-code me-2"></i> Responses & Billing
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
                                Register and Retrieve Tax Identification Numbers (TIN) for both Individuals (JTB) and Corporates (CAC) via our unified API.
                            </p>
                            
                            <!-- Endpoint Box -->
                            <div class="bg-dark rounded-4 p-4 text-white shadow-lg position-relative overflow-hidden">
                                <div class="position-absolute top-0 end-0 p-3 opacity-10">
                                    <i class="ti ti-world-www fs-1"></i>
                                </div>
                                <label class="text-white-50 small text-uppercase ls-1 fw-bold mb-2">Base URL</label>
                                <div class="d-flex align-items-center bg-white bg-opacity-10 rounded-3 p-3 border border-white border-opacity-10">
                                    <code class="text-white fs-16 font-monospace flex-grow-1">{{ url('/') }}/api/v1</code>
                                    <button class="btn btn-sm btn-secondary rounded-pill px-3 ms-3" onclick="copyToClipboard('https://arewasmart.com.ng/api/v1')">
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

                     <!-- Service Fee Information -->
                     <div class="alert alert-success border border-success border-opacity-25 rounded-3 d-flex align-items-start p-4 mb-4" role="alert">
                        <i class="ti ti-wallet fs-4 me-3 mt-1 text-success"></i>
                        <div>
                            <h5 class="alert-heading fw-bold mb-2">Service Fee</h5>
                            <p class="mb-0 text-dark">
                                You will be charged <span class="fw-bold">₦{{ number_format($verificationPrice ?? 0, 2) }}</span> per successful registration found.
                                <span class="badge bg-success bg-opacity-10 text-success ms-1 border border-success border-opacity-25">{{ ucfirst($user->role ?? 'user') }} Rate</span>
                            </p>
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
                        <button class="btn btn-secondary d-inline-flex btn-lg align-items-center shadow-sm prev-tab-btn" data-prev="overview">
                            <i class="ti ti-arrow-left me-2"></i> Previous
                        </button>
                        <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="endpoint">
                            Next: Register Endpoint <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Verify Endpoint Section -->
                <div class="docs-section d-none fade-in" id="endpoint">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold text-dark mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">2</span>
                                Register Endpoint
                            </h4>

                            <!-- Endpoint -->
                            <div class="card border-0 bg-soft-primary mb-4 overflow-hidden">
                                <div class="card-body d-flex align-items-center justify-content-between p-4 flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">POST</span>
                                        <code class="text-primary fw-bold fs-18 text-break">{{ url('/') }}/api/v1/tin/verify</code>
                                    </div>
                                </div>
                            </div>

                             <p class="text-muted mb-4">
                                This endpoint supports two types of requests: <strong>Individual (JTB)</strong> and <strong>Corporate (CAC)</strong>.
                            </p>
                            
                            <!-- Tabs for Request Types -->
                            <ul class="nav nav-pills nav-fill mb-4 p-1 rounded-3 bg-light" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active rounded-3 small fw-bold py-2" id="pills-individual-tab" data-bs-toggle="pill" data-bs-target="#pills-individual" type="button" role="tab" aria-selected="true">Individual (JTB)</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-3 small fw-bold py-2" id="pills-corporate-tab" data-bs-toggle="pill" data-bs-target="#pills-corporate" type="button" role="tab" aria-selected="false">Corporate (CAC)</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="pills-tabContent">
                                <!-- Individual Tab -->
                                <div class="tab-pane fade show active" id="pills-individual" role="tabpanel">
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                                <div class="card-header bg-light border-bottom py-3">
                                                    <h6 class="fw-bold text-dark mb-0">Individual Payload (JTB)</h6>
                                                </div>
                                                <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace"><code>{
  <span class="text-info">"nin"</span>: <span class="text-warning">"12345678901"</span>,
  <span class="text-info">"firstName"</span>: <span class="text-warning">"ABDULLAHI"</span>,
  <span class="text-info">"lastName"</span>: <span class="text-warning">"GARBA"</span>,
  <span class="text-info">"dateOfBirth"</span>: <span class="text-warning">"1990-01-01"</span> <span class="text-muted">// YYYY-MM-DD</span>
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
  <span class="text-info">"message"</span>: <span class="text-warning">"Verification Successful"</span>,
  <span class="text-info">"data"</span>: { <span class="text-muted">/* TIN Details */</span> }
}</code></pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Corporate Tab -->
                                <div class="tab-pane fade" id="pills-corporate" role="tabpanel">
                                     <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                                <div class="card-header bg-light border-bottom py-3">
                                                    <h6 class="fw-bold text-dark mb-0">Corporate Payload (CAC)</h6>
                                                </div>
                                                <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace"><code>{
  <span class="text-info">"rc"</span>: <span class="text-warning">"RC123456"</span>,
  <span class="text-info">"type"</span>: <span class="text-warning">"2"</span> <span class="text-muted">// CAC Type ID</span>
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
  <span class="text-info">"message"</span>: <span class="text-warning">"Verification Successful"</span>,
  <span class="text-info">"data"</span>: { <span class="text-muted">/* TIN Details */</span> }
}</code></pre>
                                                </div>
                                            </div>
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
                        <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="responses">
                            Next: Responses & Billing <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Responses Section -->
                <div class="docs-section d-none fade-in" id="responses">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold text-dark mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">3</span>
                                Responses & Billing
                            </h4>

                             <!-- Billing Policy Alert -->
                            <div class="alert alert-warning border border-warning border-opacity-25 rounded-3 d-flex align-items-start p-4 mb-4" role="alert">
                                <i class="ti ti-info-circle fs-4 me-3 mt-1"></i>
                                <div>
                                    <h5 class="alert-heading fw-bold mb-2">Billing Policy & Response Codes</h5>
                                    <p class="mb-0 text-dark">
                                        Please note that some responses are chargeable even if the TIN is not found (Verification Attempted).
                                    </p>
                                </div>
                            </div>
                            
                            <div class="table-responsive rounded-3 border">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr class="text-uppercase small text-muted">
                                            <th class="py-3 ps-4">Code</th>
                                            <th class="py-3">Description</th>
                                            <th class="py-3">Status</th>
                                            <th class="py-3 text-end pe-4">Billing</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        <tr>
                                            <td class="ps-4"><code class="text-success fw-bold">111111</code></td>
                                            <td class="text-dark fw-medium">Successful Verification</td>
                                            <td><span class="badge bg-soft-success text-success">Success</span></td>
                                            <td class="text-end pe-4"><span class="badge bg-danger">Charged</span></td>
                                        </tr>
                                         <tr>
                                            <td class="ps-4"><code class="text-warning fw-bold">222222</code></td>
                                            <td class="text-dark fw-medium">TIN does not exist</td>
                                            <td><span class="badge bg-soft-warning text-warning">Not Found</span></td>
                                            <td class="text-end pe-4"><span class="badge bg-danger">Charged</span></td>
                                        </tr>
                                         <tr>
                                            <td class="ps-4"><code class="text-secondary fw-bold">333333</code></td>
                                            <td class="text-dark fw-medium">Parameter Error</td>
                                            <td><span class="badge bg-light text-dark">Error</span></td>
                                            <td class="text-end pe-4"><span class="badge bg-success">Free</span></td>
                                        </tr>
                                         <tr>
                                            <td class="ps-4"><code class="text-secondary fw-bold">444444</code></td>
                                            <td class="text-dark fw-medium">System Error</td>
                                            <td><span class="badge bg-light text-dark">Error</span></td>
                                            <td class="text-end pe-4"><span class="badge bg-success">Free</span></td>
                                        </tr>
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
        
        /* Mobile Adjustments */
        @media (max-width: 991.98px) {
            .sticky-top { position: relative !important; top: 0 !important; z-index: 1 !important; }
        }
    </style>
    @endpush
</x-app-layout>
