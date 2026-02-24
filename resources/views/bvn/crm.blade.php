<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'BVN CRM API' }}</title>
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-5">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title fw-bold text-primary display-6">
                        BVN CRM API
                    </h3>

                    <ul class="breadcrumb bg-transparent p-0 mt-2 mb-1">
                        <li class="breadcrumb-item active text-primary fw-semibold">
                            API Documentation
                        </li>
                    </ul>

                    <p class="text-muted mb-0">
                        Submit BVN CRM modification requests programmatically using your secure API token.
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
                                    <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#errors" onclick="switchTab('errors'); return false;">
                                        <i class="ti ti-alert-triangle me-2 fs-5 opacity-75"></i> Error Codes
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
                                <p class="small text-white-50 mb-3">Our support team is available 24/7.</p>
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
                        <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#errors" onclick="switchTab('errors'); closeOffcanvas(); return false;">
                            <i class="ti ti-alert-triangle me-2"></i> Error Codes
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
                            <h2 class="fw-bold text-dark mb-3">BVN CRM Guide</h2>
                            <p class="text-muted lead mb-4">
                                Submit BVN CRM (Customer Relationship Management) modification requests using our simple REST API.
                                Every request is wallet-charged, instantly tracked, and returns a unique reference number.
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

                            <div class="alert alert-info border border-info border-opacity-25 rounded-3 d-flex align-items-start p-4 mb-4" role="alert">
                                <i class="ti ti-info-circle fs-4 me-3 mt-1"></i>
                                <div>
                                    <h5 class="alert-heading fw-bold mb-2">How to use Field Codes</h5>
                                    <p class="mb-0 text-dark">
                                        Each CRM service has a unique <strong>field_code</strong>.
                                        Pass the correct code in the <code>field_code</code> parameter of your request.
                                        Pricing varies by your account tier.
                                    </p>
                                </div>
                            </div>

                            <div class="table-responsive rounded-4 border-0 shadow-sm mb-4">
                                <table class="table table-hover align-middle mb-0 bg-white">
                                    <thead class="bg-primary text-white">
                                        <tr class="small text-uppercase tracking-wide">
                                            <th class="py-4 ps-4 fw-semibold rounded-top-start">Service Name</th>
                                            <th class="py-4 fw-semibold">Field Code</th>
                                            <th class="py-4 fw-semibold text-center">API (₦)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($availableServices as $svc)
                                            <tr class="border-bottom hover-bg-light transition-all">
                                                <td class="ps-4 fw-bold text-dark py-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-soft-primary text-primary rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="ti ti-id-badge-2 fs-5"></i>
                                                        </div>
                                                        <div>
                                                            {{ $svc->name }}
                                                            <span class="d-block text-muted small fw-normal mt-1"><i class="ti ti-building-bank me-1"></i>{{ $svc->bank }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge bg-light text-primary border border-primary border-opacity-25 px-3 py-2 fs-13 rounded-pill shadow-sm">
                                                        <i class="ti ti-code me-1"></i> {{ $svc->code }}
                                                    </span>
                                                </td>                         
                                                <td class="text-center py-3">
                                                    <div class="fw-bold text-info fs-15">₦{{ number_format(\App\Models\ServiceField::find($svc->id)->getPriceForUserType('api'), 2) }}</div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <div class="p-4 bg-light rounded-4 d-inline-block border border-dashed text-muted">
                                                        <i class="ti ti-database-off fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                                        <h6 class="fw-bold mb-1">No Services Available</h6>
                                                        <span class="small">CRM services are currently inactive. Contact support.</span>
                                                    </div>
                                                </td>
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
                                Authenticate all requests using your secret API Bearer Token.
                            </p>

                            <div class="mb-5">
                                <label class="form-label fw-bold text-dark mb-2">Your Personal API Token</label>
                                <div class="input-group input-group-lg shadow-sm">
                                    <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                                        <i class="ti ti-key fs-4"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control font-monospace border-start-0 border-end-0 bg-white text-dark"
                                           value="{{ Auth::user()->api_token ?? 'No token available' }}"
                                           id="apiToken"
                                           readonly>
                                    <button class="btn btn-primary px-4" type="button" onclick="copyToken()">
                                        <span id="copyBtnText">Copy</span> <i class="ti ti-copy ms-2"></i>
                                    </button>
                                </div>
                                <div class="form-text mt-2"><i class="ti ti-lock me-1"></i> Keep this token safe. Never expose it publicly.</div>
                            </div>

                            <div class="card bg-dark text-white border-0 shadow-lg overflow-hidden position-relative">
                                <div class="card-header bg-transparent border-white border-opacity-10 py-3">
                                    <h6 class="mb-0 fw-bold text-light"><i class="ti ti-code me-2"></i>Header Authorization Example</h6>
                                </div>
                                <div class="card-body bg-black bg-opacity-25 font-monospace p-4">
                                    <div class="d-flex">
                                        <span class="text-info me-3">Authorization:</span>
                                        <span class="text-warning">Bearer <span class="text-white-50">
                                            @if(Auth::user()->api_token ?? false)
                                                {{ substr(Auth::user()->api_token, 0, 15) }}...
                                            @else
                                                YOUR_API_TOKEN
                                            @endif
                                        </span></span>
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
                                Submit a CRM Request
                            </h4>

                            <!-- Endpoint -->
                            <div class="card border-0 bg-soft-primary mb-4 overflow-hidden">
                                <div class="card-body d-flex align-items-center justify-content-between p-4 flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">POST</span>
                                        <code class="text-primary fw-bold fs-18 text-break">{{ url('/') }}/api/v1/bvn/crm</code>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm rounded-pill" onclick="copyToClipboard('{{ url('/') }}/api/v1/bvn/crm')">
                                        <i class="ti ti-copy me-1"></i> Copy URL
                                    </button>
                                </div>
                            </div>

                            <div class="row g-4">
                                <!-- Request Parameters -->
                                <div class="col-lg-6">
                                    <h5 class="fw-bold text-dark mb-3">Request Parameters</h5>
                                    <ul class="list-group list-group-flush mb-3">
                                        <li class="list-group-item px-0">
                                            <span class="fw-bold">field_code</span> <span class="text-danger">*</span>: (string) CRM field code from the Services tab.
                                        </li>
                                        <li class="list-group-item px-0">
                                            <span class="fw-bold">ticket_id</span> <span class="text-danger">*</span>: (string) 8-digit numeric ticket ID.
                                        </li>
                                        <li class="list-group-item px-0">
                                            <span class="fw-bold">batch_id</span> <span class="text-danger">*</span>: (string) 7-digit numeric batch ID.
                                        </li>
                                    </ul>
                                </div>

                                <!-- Payload Examples -->
                                <div class="col-12 mt-2">
                                    <h5 class="fw-bold text-dark mb-3">Payload Examples</h5>
                                    <ul class="nav nav-tabs mb-3" id="payload-tabs">
                                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#payload-request">Request</a></li>
                                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#payload-response">Success Response</a></li>
                                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#payload-error">Error Response</a></li>
                                    </ul>

                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="payload-request">
                                            <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                                <div class="card-header bg-light border-bottom py-3 d-flex justify-content-between">
                                                    <h6 class="fw-bold text-dark mb-0">CRM Request Body</h6>
                                                    <button class="btn btn-sm btn-outline-primary copy-btn" data-clipboard-target="#code-request"><i class="ti ti-clipboard"></i> Copy</button>
                                                </div>
                                                <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace" id="code-request"><code>{
    "field_code": "021",
    "ticket_id": "12345678",
    "batch_id": "1234567"
}</code></pre>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="payload-response">
                                            <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                                <div class="card-header bg-light border-bottom py-3 d-flex justify-content-between">
                                                    <h6 class="fw-bold text-dark mb-0">Success Response (HTTP 200)</h6>
                                                    <button class="btn btn-sm btn-outline-primary copy-btn" data-clipboard-target="#code-response"><i class="ti ti-clipboard"></i> Copy</button>
                                                </div>
                                                <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace" id="code-response"><code>{
    "success": true,
    "message": "CRM request submitted successfully.",
    "data": {
        "reference": "CRMA1B2C3D4E5",
        "trx_ref":   "CRMA1B2C3D4E5",
        "status":    "pending",
        "service":   "CRM Name Correction",
        "field_code": "021",
        "ticket_id": "12345678",
        "batch_id":  "1234567",
        "amount_charged": "500.00",
        "submitted_at": "2026-02-24 12:00:00"
    }
}</code></pre>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="payload-error">
                                            <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                                                <div class="card-header bg-light border-bottom py-3 d-flex justify-content-between">
                                                    <h6 class="fw-bold text-dark mb-0">Error Response (e.g. HTTP 402)</h6>
                                                    <button class="btn btn-sm btn-outline-primary copy-btn" data-clipboard-target="#code-error"><i class="ti ti-clipboard"></i> Copy</button>
                                                </div>
                                                <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace" id="code-error"><code>{
    "success": false,
    "message": "Insufficient wallet balance.",
    "required":  "500.00",
    "balance":   "200.00",
    "shortfall": "300.00"
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
                                Check Submission Status
                            </h4>

                            <!-- Endpoint -->
                            <div class="card border-0 bg-soft-success mb-4 overflow-hidden">
                                <div class="card-body d-flex align-items-center justify-content-between p-4 flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">GET</span>
                                        <code class="text-success fw-bold fs-18 text-break">{{ url('/') }}/api/v1/bvn/crm</code>
                                    </div>
                                    <button class="btn btn-outline-success btn-sm rounded-pill" onclick="copyToClipboard('{{ url('/') }}/api/v1/bvn/crm')">
                                        <i class="ti ti-copy me-1"></i> Copy URL
                                    </button>
                                </div>
                            </div>

                            <p class="text-muted mb-4">
                                Poll the current status of a previously submitted CRM request using any one query parameter below.
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
                                                    <strong>?reference=</strong><span class="text-muted">CRMABC...</span>
                                                </li>
                                                <li class="mb-3">
                                                    <span class="badge bg-light text-dark border me-2">OPTION 2</span>
                                                    <strong>?batch_id=</strong><span class="text-muted">1234567</span>
                                                </li>
                                                <li>
                                                    <span class="badge bg-light text-dark border me-2">OPTION 3</span>
                                                    <strong>?ticket_id=</strong><span class="text-muted">12345678</span>
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
    "success": true,
    "data": {
        "reference":   "CRMA1B2C3D4E5",
        "ticket_id":   "12345678",
        "batch_id":    "1234567",
        "field_code":  "021",
        "service":     "CRM Name Correction",
        "status":      "pending",
        "comment":      null,
        "file_url":     null,
        "amount_charged": "500.00",
        "submission_date": "2026-02-24T12:00:00Z"
    }
}</code></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Descriptions -->
                            <div class="mt-4 p-4 bg-light rounded-3 border">
                                <h6 class="fw-bold text-dark mb-3"><i class="ti ti-info-circle me-1 text-primary"></i> Status Descriptions</h6>
                                <div class="row g-2">
                                    <div class="col-sm-6 col-lg-3">
                                        <span class="badge bg-warning text-dark w-100 py-2">pending</span>
                                        <div class="small text-muted mt-1">Submitted, awaiting processing</div>
                                    </div>
                                    <div class="col-sm-6 col-lg-3">
                                        <span class="badge bg-info text-white w-100 py-2">processing</span>
                                        <div class="small text-muted mt-1">Being reviewed by our team</div>
                                    </div>
                                    <div class="col-sm-6 col-lg-3">
                                        <span class="badge bg-success w-100 py-2">successful</span>
                                        <div class="small text-muted mt-1">Request completed</div>
                                    </div>
                                    <div class="col-sm-6 col-lg-3">
                                        <span class="badge bg-danger w-100 py-2">query</span>
                                        <div class="small text-muted mt-1">Clarification needed</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary d-inline-flex btn-lg align-items-center shadow-sm prev-tab-btn" data-prev="submit">
                            <i class="ti ti-arrow-left me-2"></i> Previous
                        </button>
                        <button class="btn btn-primary d-inline-flex btn-lg align-items-center shadow-sm next-tab-btn" data-next="errors">
                            Next: Error Codes <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Error Codes Section -->
                <div class="docs-section d-none fade-in" id="errors">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold text-dark mb-4 d-flex align-items-center">
                                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">5</span>
                                HTTP Error Codes
                            </h4>
                            <div class="table-responsive rounded-3 border">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr class="text-uppercase small text-muted">
                                            <th class="py-3 ps-4">Code</th>
                                            <th class="py-3">Status</th>
                                            <th class="py-3">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        <tr>
                                            <td class="ps-4"><code class="fw-bold text-success">200</code></td>
                                            <td><span class="badge bg-success text-white">OK</span></td>
                                            <td class="text-muted small">Request accepted and queued successfully.</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4"><code class="fw-bold text-warning">400</code></td>
                                            <td><span class="badge bg-warning text-dark">Bad Request</span></td>
                                            <td class="text-muted small">Validation failed, invalid field_code, or missing required parameters.</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4"><code class="fw-bold text-danger">401</code></td>
                                            <td><span class="badge bg-danger">Unauthorized</span></td>
                                            <td class="text-muted small">Missing or invalid API Bearer token.</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4"><code class="fw-bold text-danger">402</code></td>
                                            <td><span class="badge bg-danger">Payment Required</span></td>
                                            <td class="text-muted small">Insufficient wallet balance to cover the service charge.</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4"><code class="fw-bold text-danger">403</code></td>
                                            <td><span class="badge bg-danger">Forbidden</span></td>
                                            <td class="text-muted small">Account is suspended or wallet is inactive.</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4"><code class="fw-bold text-danger">404</code></td>
                                            <td><span class="badge bg-secondary">Not Found</span></td>
                                            <td class="text-muted small">No record found for the given reference, batch_id, or ticket_id.</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4"><code class="fw-bold text-dark">500</code></td>
                                            <td><span class="badge bg-dark">Server Error</span></td>
                                            <td class="text-muted small">Internal error. Please retry or contact support.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary d-inline-flex btn-lg align-items-center shadow-sm prev-tab-btn" data-prev="status">
                            <i class="ti ti-arrow-left me-2"></i> Previous
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.10/clipboard.min.js"></script>
    <script>
        // Initialize Clipboard.js for all copy buttons
        new ClipboardJS('.copy-btn');

        // Tab switching logic
        function switchTab(tabId) {
            document.querySelectorAll('.docs-section').forEach(section => {
                section.classList.add('d-none');
                section.classList.remove('active-section');
            });

            const targetSection = document.getElementById(tabId);
            if (targetSection) {
                targetSection.classList.remove('d-none');
                setTimeout(() => targetSection.classList.add('active-section'), 10);
            }

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
            if (hash && document.getElementById(hash)) {
                switchTab(hash);
            } else {
                document.getElementById('overview').classList.add('active-section');
            }
        });

        document.querySelectorAll('.next-tab-btn, .prev-tab-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const targetTab = this.getAttribute('data-next') || this.getAttribute('data-prev');
                switchTab(targetTab);
            });
        });

        const mobileSidebar = document.getElementById('mobileSidebar');
        const bsOffcanvas   = new bootstrap.Offcanvas(mobileSidebar);

        document.getElementById('sidebarToggle').addEventListener('click', function () {
            bsOffcanvas.show();
        });

        function closeOffcanvas() {
            bsOffcanvas.hide();
        }

        function copyToken() {
            const tokenInput = document.getElementById('apiToken');
            tokenInput.select();
            tokenInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(tokenInput.value).then(() => {
                const btnText     = document.getElementById('copyBtnText');
                const originalText = btnText.innerText;
                btnText.innerText = 'Copied!';

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
        .docs-section {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .docs-section.active-section {
            opacity: 1;
        }
        .custom-sidebar-nav .list-group-item {
            transition: all 0.2s ease;
            font-weight: 500;
        }
        .custom-sidebar-nav .list-group-item:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.05);
            color: var(--bs-primary) !important;
            transform: translateX(5px);
        }
        .custom-sidebar-nav .list-group-item.active {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
            color: var(--bs-primary);
            border-left: 3px solid var(--bs-primary);
        }
        .font-monospace {
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        }
    </style>
    @endpush
</x-app-layout>
