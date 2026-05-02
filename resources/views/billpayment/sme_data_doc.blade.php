<x-app-layout>
    @push('styles')
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
            background-color: #f26922 !important; 
            border-color: #f26922 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(242, 105, 34, 0.4);
        }

        .dark-mode .custom-sidebar-nav .list-group-item {
            background-color: #1e2532 !important; 
            border-color: #2b3346 !important;
            color: #94a3b8 !important;
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

        .bg-soft-primary { background-color: rgba(var(--bs-primary-rgb), 0.1); }
        .text-white-50 { color: rgba(255, 255, 255, 0.5) !important; }
        .ls-1 { letter-spacing: 1px; }
        .fs-14 { font-size: 14px; }
        .fs-16 { font-size: 16px; }
        .z-index-1 { z-index: 1; }

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
            background-color: rgba(242, 105, 34, 0.1);
            color: #f26922;
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

        @media (max-width: 991.98px) { .sticky-top { position: relative !important; top: 0 !important; z-index: 1 !important; } }
    </style>
    @endpush

    <title>Arewa Smart - SME Data API Guide</title>
    
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-5">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title fw-bold text-primary display-6">
                        SME Data API
                    </h3>

                    <ul class="breadcrumb bg-transparent p-0 mt-2 mb-1">
                        <li class="breadcrumb-item active text-primary fw-semibold">
                            API Documentation
                        </li>
                    </ul>

                    <p class="text-muted mb-0">
                        Integrate SME Data purchases for MTN, Airtel, Glo, and 9mobile into your application.
                    </p>
                </div>
                <div class="col-auto">
                    <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fw-medium fs-14 border border-primary border-opacity-10">
                        <i class="ti ti-tag me-1"></i> Version 1.1.0
                    </span>
                    <button class="btn btn-white shadow-sm d-lg-none ms-2 rounded-circle p-2" type="button" id="sidebarToggle" aria-label="Toggle Navigation">
                        <i class="ti ti-menu-2 fs-15 text-primary"></i>
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
                                        <i class="ti ti-menu-deep me-2"></i> Navigation
                                    </h6>
                                </div>
                                <div class="list-group list-group-flush custom-sidebar-nav p-3">
                                    <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center active" href="#overview" onclick="switchTab('overview'); return false;">
                                        <i class="ti ti-info-circle me-2 fs-5 opacity-75"></i> Overview
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#auth" onclick="switchTab('auth'); return false;">
                                        <i class="ti ti-shield-lock me-2 fs-5 opacity-75"></i> Authentication
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#variations" onclick="switchTab('variations'); return false;">
                                        <i class="ti ti-list me-2 fs-5 opacity-75"></i> Data Plans
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#endpoint" onclick="switchTab('endpoint'); return false;">
                                        <i class="ti ti-server me-2 fs-5 opacity-75"></i> Purchase Endpoint
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
            <div class="offcanvas offcanvas-start border-0 shadow-lg" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
                <div class="offcanvas-header sidebar-nav-header border-bottom">
                    <h5 class="offcanvas-title fw-bold" id="mobileSidebarLabel">Documentation</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    <div class="list-group list-group-flush custom-sidebar-nav p-3">
                        <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center active" href="#overview" onclick="switchTab('overview'); closeOffcanvas(); return false;">
                            <i class="ti ti-info-circle me-2"></i> Overview
                        </a>
                        <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#auth" onclick="switchTab('auth'); closeOffcanvas(); return false;">
                            <i class="ti ti-shield-lock me-2"></i> Authentication
                        </a>
                        <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#variations" onclick="switchTab('variations'); closeOffcanvas(); return false;">
                            <i class="ti ti-list"></i> Data Plans
                        </a>
                        <a class="list-group-item list-group-item-action border-0 mb-2 px-3 py-2 d-flex align-items-center" href="#endpoint" onclick="switchTab('endpoint'); closeOffcanvas(); return false;">
                            <i class="ti ti-server me-2"></i> Purchase Endpoint
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
                            <h2 class="fw-bold mb-3 text-body">SME Data Bundle Guide</h2>
                            <p class="text-muted lead mb-5 docs-text">
                                Automate SME data purchases with our robust API. 
                                This documentation covers fetching plans and executing transactions.
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
                <div class="docs-section d-none" id="auth">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold mb-4">Authentication</h4>
                            <p class="text-muted mb-4">Bearer Token authentication is required for all requests.</p>
                            
                            <div class="mb-5">
                                <label class="form-label fw-bold mb-2">Your API Token</label>
                                <div class="input-group input-group-lg shadow-sm">
                                    <input type="text" class="form-control font-monospace" value="{{ Auth::user()->api_token ?? 'No token available' }}" id="apiToken" readonly>
                                    <button class="btn btn-primary" type="button" onclick="copyToken()">Copy</button>
                                </div>
                            </div>

                            <div class="card bg-dark text-white border-0 shadow-lg">
                                <div class="card-body font-monospace p-4">
                                    <span class="text-info">Authorization:</span>
                                    <span class="text-warning">Bearer {{ Auth::user()->api_token ?? 'your-api-token' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary btn-lg prev-tab-btn" data-prev="overview">Previous</button>
                        <button class="btn btn-primary btn-lg next-tab-btn" data-next="variations">Next</button>
                    </div>
                </div>

                <!-- Variations Section -->
                <div class="docs-section d-none" id="variations">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold mb-4">Fetching SME Data Plans</h4>
                            
                            <div class="card border-0 bg-soft-primary mb-4 p-4">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-3">GET</span>
                                    <code class="text-primary fw-bold">{{ url('/') }}/api/v1/sme-data/variations</code>
                                </div>
                            </div>

                            <div class="table-responsive rounded-3 border custom-table-border mb-4">
                                <table class="table table-premium table-hover mb-0">
                                    <thead>
                                        <tr class="small text-muted">
                                            <th>Parameter</th>
                                            <th>Type</th>
                                            <th>Required</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>network</td>
                                            <td>String</td>
                                            <td>Optional</td>
                                            <td>MTN, AIRTEL, GLO, 9MOBILE</td>
                                        </tr>
                                        <tr>
                                            <td>type</td>
                                            <td>String</td>
                                            <td>Optional</td>
                                            <td>SME, GIFTING, etc.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="card bg-dark overflow-hidden">
                                <div class="card-header text-white-50 fw-bold">Success Response</div>
                                <div class="card-body p-0">
                                    <pre class="m-0 p-4 text-white"><code>{
  "status": "success",
  "data": [
    {
      "data_id": "215",
      "network": "MTN",
      "plan_type": "SME",
      "amount": "650",
      "size": "1.0 GB",
      "validity": "1 Days"
    }
  ]
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary btn-lg prev-tab-btn" data-prev="auth">Previous</button>
                        <button class="btn btn-primary btn-lg next-tab-btn" data-next="endpoint">Next</button>
                    </div>
                </div>

                <!-- Purchase Endpoint Section -->
                <div class="docs-section d-none" id="endpoint">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold mb-4">Purchase SME Data</h4>

                            <div class="card border-0 bg-soft-primary mb-4 p-4">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-3">POST</span>
                                    <code class="text-primary fw-bold">{{ url('/') }}/api/v1/sme-data/purchase</code>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <h6 class="fw-bold mb-3">Request Body</h6>
                                    <pre class="bg-dark text-white rounded p-4"><code>{
  "network": "MTN",
  "mobileno": "08064333983",
  "plan_id": "215",
  "request_id": "REF_12345"
}</code></pre>
                                </div>
                                <div class="col-lg-6">
                                    <h6 class="fw-bold mb-3">Success Response</h6>
                                    <pre class="bg-dark text-white rounded p-4"><code>{
    "status": "success",
    "message": "SME Data purchase successful",
    "data": {
        "transaction_ref": "20260213130035271",
        "request_id": "REF_123451",
        "amount": 70,
        "phone": "08064333983",
        "plan": "75.0 MB MTN SME BOSS",
        "status": "completed"
    }
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary btn-lg prev-tab-btn" data-prev="variations">Previous</button>
                    </div>
                </div>


            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.docs-section').forEach(section => {
                section.classList.add('d-none');
                section.classList.remove('active-section');
            });
            const target = document.getElementById(tabId);
            if (target) {
                target.classList.remove('d-none');
                void target.offsetWidth; 
                setTimeout(() => target.classList.add('active-section'), 10);
            }

            document.querySelectorAll('.custom-sidebar-nav a').forEach(link => {
                link.classList.remove('active', 'bg-soft-primary', 'text-primary', 'fw-bold');
                if (link.getAttribute('href') === '#' + tabId) {
                    link.classList.add('active');
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
        document.getElementById('sidebarToggle')?.addEventListener('click', () => bsOffcanvas.show());
        function closeOffcanvas() { bsOffcanvas.hide(); }

        function copyToken() {
            const el = document.getElementById('apiToken');
            el.select(); navigator.clipboard.writeText(el.value);
            alert('Token copied to clipboard!');
        }
        function copyToClipboard(text) {
             navigator.clipboard.writeText(text);
             alert('URL copied to clipboard!');
        }
    </script>
    @endpush
</x-app-layout>