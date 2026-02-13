<x-app-layout>
    @push('styles')
    <style>
        .docs-section {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .docs-section.active-section {
            opacity: 1;
        }
        .custom-sidebar-nav .list-group-item.active {
            border-left: 3px solid var(--bs-primary);
            background-color: rgba(var(--bs-primary-rgb), 0.05) !important;
        }
        .bg-soft-primary {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }
        .text-white-50 {
            color: rgba(255, 255, 255, 0.5) !important;
        }
        .ls-1 {
            letter-spacing: 1px;
        }
        .fs-14 {
            font-size: 14px;
        }
        .fs-16 {
            font-size: 16px;
        }
        .z-index-1 {
            z-index: 1;
        }
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
                                        <i class="ti ti-list me-2 fs-5 opacity-75"></i> Data Plans
                                    </a>
                                    <a class="list-group-item list-group-item-action border-0 rounded-3 mb-1 px-3 py-2 d-flex align-items-center" href="#endpoint" onclick="switchTab('endpoint'); return false;">
                                        <i class="ti ti-server me-2 fs-5 opacity-75"></i> Purchase Endpoint
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

            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Overview Section -->
                <div class="docs-section active-section" id="overview">
                    <div class="card border-0 shadow-sm rounded-4 mb-4 position-relative overflow-hidden">
                        <div class="card-body p-5">
                            <span class="badge bg-soft-primary text-primary mb-3">Introduction</span>
                            <h2 class="fw-bold text-dark mb-3">SME Data Bundle Guide</h2>
                            <p class="text-muted lead mb-4">
                                Automate SME data purchases with our robust API. 
                                This documentation covers fetching plans and executing transactions.
                            </p>
                            
                            <!-- Endpoint Box -->
                            <div class="bg-dark rounded-4 p-4 text-white shadow-lg">
                                <label class="text-white-50 small text-uppercase ls-1 fw-bold mb-2">API Base URL</label>
                                <div class="d-flex align-items-center bg-white bg-opacity-10 rounded-3 p-3 border border-white border-opacity-10">
                                    <code class="text-white fs-16 font-monospace flex-grow-1">{{ url('/') }}/api/v1</code>
                                    <button class="btn btn-sm btn-secondary rounded-pill px-3 ms-3" onclick="copyToClipboard('{{ url('/') }}/api/v1')">
                                        <i class="ti ti-copy me-1"></i> Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button class="btn btn-primary btn-lg next-tab-btn" data-next="auth">
                            Next: Authentication <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Authentication Section -->
                <div class="docs-section d-none" id="auth">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold text-dark mb-4">Authentication</h4>
                            <p class="text-muted mb-4">Bearer Token authentication is required for all requests.</p>
                            
                            <div class="mb-5">
                                <label class="form-label fw-bold text-dark mb-2">Your API Token</label>
                                <div class="input-group input-group-lg shadow-sm">
                                    <input type="text" class="form-control font-monospace bg-white" value="{{ Auth::user()->api_token ?? 'No token available' }}" id="apiToken" readonly>
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
                            <h4 class="fw-bold text-dark mb-4">Fetching SME Data Plans</h4>
                            
                            <div class="card border-0 bg-soft-primary mb-4 p-4">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-3">GET</span>
                                    <code class="text-primary fw-bold">{{ url('/') }}/api/v1/sme-data/variations</code>
                                </div>
                            </div>

                            <div class="table-responsive rounded-3 border mb-4">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
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
                                <div class="card-header bg-light text-dark fw-bold">Success Response</div>
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
                            <h4 class="fw-bold text-dark mb-4">Purchase SME Data</h4>

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
            // Hide all sections
            document.querySelectorAll('.docs-section').forEach(section => {
                section.classList.add('d-none');
                section.classList.remove('active-section');
            });
            
            // Show target section
            const target = document.getElementById(tabId);
            if (target) {
                target.classList.remove('d-none');
                setTimeout(() => target.classList.add('active-section'), 10);
            }

            // Update active state in sidebar
            document.querySelectorAll('.custom-sidebar-nav a').forEach(link => {
                link.classList.remove('active', 'bg-soft-primary', 'text-primary', 'fw-bold');
                if (link.getAttribute('href') === '#' + tabId) {
                    link.classList.add('active', 'bg-soft-primary', 'text-primary', 'fw-bold');
                }
            });
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Navigation buttons
        document.querySelectorAll('.next-tab-btn, .prev-tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const nextTab = this.getAttribute('data-next');
                const prevTab = this.getAttribute('data-prev');
                switchTab(nextTab || prevTab);
            });
        });

        // Copy token function
        function copyToken() {
            const el = document.getElementById('apiToken');
            if (el) {
                el.select();
                navigator.clipboard.writeText(el.value).then(() => {
                    alert('Token copied to clipboard!');
                }).catch(() => {
                    alert('Failed to copy token');
                });
            }
        }

        // Copy URL function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('URL copied to clipboard!');
            }).catch(() => {
                alert('Failed to copy URL');
            });
        }

        // Mobile sidebar toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            const sidebar = document.querySelector('.col-lg-3');
            sidebar?.classList.toggle('d-none');
            sidebar?.classList.toggle('d-block');
        });

        // Initialize first section if none active
        document.addEventListener('DOMContentLoaded', function() {
            const activeSection = document.querySelector('.docs-section:not(.d-none)');
            if (!activeSection) {
                switchTab('overview');
            }
        });
    </script>
    @endpush
</x-app-layout>