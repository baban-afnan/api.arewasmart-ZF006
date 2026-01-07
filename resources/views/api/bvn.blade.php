<x-app-layout>
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title fw-bold text-primary">BVN Verification API</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary">BVN API Documentation</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fw-medium">
                        <i class="ti ti-tag me-1"></i> v1.0.0
                    </span>
                    <!-- Mobile Sidebar Toggle Button -->
                    <button class="btn btn-primary d-lg-none ms-2" type="button" id="sidebarToggle">
                        <i class="ti ti-menu-2"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Mobile Overlay -->
            <div class="sidebar-overlay d-lg-none" id="sidebarOverlay"></div>

            <!-- Sidebar Navigation -->
            <div class="col-lg-3 col-md-4 mb-4 mb-md-0" id="sidebarColumn">
                <div class="card border-0 shadow-sm rounded-4 sidebar-card" style="top: 100px; z-index: 1020;">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center d-lg-none">
                        <h6 class="mb-0">Navigation</h6>
                        <button type="button" class="btn-close" id="closeSidebar"></button>
                    </div>
                    <div class="card-body p-3">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3 ms-2 ls-1 d-none d-lg-block">On This Page</h6>
                        <nav class="nav flex-column nav-pills custom-sidebar-nav">
                            <a class="nav-link bg-soft-primary text-secondary fw-bold mb-2 rounded-3 d-flex align-items-center active" href="#overview" data-tab="overview">
                                <i class="ti ti-info-circle me-2"></i> Overview
                            </a>
                            <a class="nav-link text-secondary fw-medium mb-2 rounded-3 d-flex align-items-center" href="#auth" data-tab="auth">
                                <i class="ti ti-lock me-2"></i> Authentication
                            </a>
                            <a class="nav-link text-secondary fw-medium mb-2 rounded-3 d-flex align-items-center" href="#endpoint" data-tab="endpoint">
                                <i class="ti ti-link me-2"></i> Verify Endpoint
                            </a>
                            <a class="nav-link text-secondary fw-medium mb-2 rounded-3 d-flex align-items-center" href="#responses" data-tab="responses">
                                <i class="ti ti-message-code me-2"></i> Responses
                            </a>
                        </nav>
                        
                        <div class="mt-4 p-3 bg-light rounded-3 border border-light">
                            <h6 class="fw-bold text-secondary mb-2">Need Support?</h6>
                            <p class="small text-secondary mb-3">Contact our developer support team for assistance.</p>
                            <a href="#" class="btn btn-primary w-100 btn-sm rounded-pill">Contact Support</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-md-12" id="mainContent">
                
                <!-- Alert Messages -->
                @if(session('status'))
                    <div class="alert alert-{{ session('status') == 'success' ? 'success' : 'danger' }} alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-{{ session('status') == 'success' ? 'check' : 'alert-circle' }} me-2 fs-4"></i>
                            <div class="flex-grow-1">
                                <strong>{{ ucfirst(session('status')) }}!</strong> {{ session('message') }}
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif

                <!-- Overview Section -->
                <div class="docs-section" id="overview">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-white border-bottom py-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                    <i class="ti ti-api text-primary fs-20"></i>
                                </div>
                                <div>
                                    <h4 class="card-title mb-0 fw-bold text-primary">Introduction</h4>
                                    <p class="card-subtitle mb-0 text-secondary">BVN Verification Service Integration</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body p-4">
                            <!-- Base URL Information -->
                            <div class="alert alert-soft-primary border-start border-4 border-primary p-4 rounded-3 mb-0">
                                <div class="d-flex align-items-start">
                                    <i class="ti ti-world text-primary me-3 mt-1 fs-4"></i>
                                    <div class="flex-grow-1">
                                        <h5 class="alert-heading fw-bold mb-2 text-primary">
                                            <i class="ti ti-link me-1"></i> API Endpoint
                                        </h5>
                                        <p class="mb-2 text-dark">All API requests should be sent to the following base URL:</p>
                                        <div class="bg-white border rounded p-3 mt-2 shadow-sm">
                                            <code class="text-dark fw-bold fs-10">{{ url('/') }}/api/v1</code>
                                        </div>
                                        <p class="mt-3 mb-0 small text-muted">
                                            <i class="ti ti-info-circle me-1"></i> Ensure all requests use HTTPS for secure communication.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button class="btn btn-primary d-inline-flex align-items-center next-tab-btn" data-next="auth">
                            Next: Authentication <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Authentication Section -->
                <div class="docs-section d-none" id="auth">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="fw-bold text-dark mb-0"><span class="badge bg-dark rounded-circle me-2">1</span>Authentication</h5>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted mb-4">
                                <i class="ti ti-shield-lock me-1"></i> 
                                All API requests require authentication using your personal API token. Include this token in the Authorization header for every request.
                            </p>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark mb-2 text-uppercase small ls-1">
                                    Your API Token
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white border-end-0 text-primary">
                                        <i class="ti ti-key"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control font-monospace border-start-0 bg-white text-dark" 
                                           value="{{ Auth::user()->api_token }}" 
                                           id="apiToken" 
                                           readonly>
                                    <button class="btn btn-primary px-4" type="button" onclick="copyToken()">
                                        <i class="ti ti-copy me-2"></i> Copy
                                    </button>
                                </div>
                                <div class="form-text text-danger mt-2">
                                    <i class="ti ti-alert-triangle me-1"></i>
                                    <strong>Important:</strong> Keep this token secure. Never expose it in client-side code or public repositories.
                                </div>
                            </div>

                            <div class="bg-dark text-white p-3 rounded-3 shadow-inner">
                                <h6 class="fw-bold mb-2 text-primary small text-uppercase">Example Header:</h6>
                                <pre class="mb-0 text-light"><code>Authorization: Bearer {{ substr(Auth::user()->api_token, 0, 20) }}...</code></pre>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-secondary d-inline-flex align-items-center prev-tab-btn" data-prev="overview">
                            <i class="ti ti-arrow-left me-2"></i> Previous
                        </button>
                        <button class="btn btn-primary d-inline-flex align-items-center next-tab-btn" data-next="endpoint">
                            Next: Verify Endpoint <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Verify Endpoint Section -->
                <div class="docs-section d-none" id="endpoint">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="fw-bold text-dark mb-0"><span class="badge bg-dark rounded-circle me-2">2</span>BVN Verification Endpoint</h5>
                        </div>
                        <div class="card-body p-4">
                             <div class="card border-0 shadow-none bg-soft-primary mb-4">
                                <div class="card-body d-flex align-items-center p-3">
                                    <span class="badge bg-primary me-3 px-3 py-2 fw-bold">POST</span>
                                    <code class="text-dark fs-10 fw-bold">{{ url('/') }}/api/v1/bvn/verify</code>
                                </div>
                            </div>

                            <p class="text-muted mb-4">
                                <i class="ti ti-user-check me-1"></i>
                                Verify a Bank Verification Number (BVN) and retrieve associated details.
                            </p>

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="card border bg-white h-100 rounded-3">
                                        <div class="card-header bg-white border-bottom py-3">
                                            <h6 class="fw-bold mb-0 text-primary">
                                                <i class="ti ti-settings me-2"></i> Request Headers
                                            </h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-hover mb-0">
                                                <tbody>
                                                    <tr class="border-bottom">
                                                        <td class="fw-bold text-dark ps-4">Authorization</td>
                                                        <td class="pe-4"><code>Bearer &lt;your_api_token&gt;</code></td>
                                                    </tr>
                                                    <tr class="border-bottom">
                                                        <td class="fw-bold text-dark ps-4">Content-Type</td>
                                                        <td class="pe-4"><code>application/json</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold text-dark ps-4">Accept</td>
                                                        <td class="pe-4"><code>application/json</code></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <div class="card border bg-white h-100 rounded-3">
                                        <div class="card-header bg-white border-bottom py-3">
                                            <h6 class="fw-bold mb-0 text-primary">
                                                <i class="ti ti-input-check me-2"></i> Request Body
                                            </h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <pre class="bg-light p-3 m-0 h-100"><code class="language-json">{
    "bvn": "12345678901"
}</code></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-secondary d-inline-flex align-items-center prev-tab-btn" data-prev="auth">
                            <i class="ti ti-arrow-left me-2"></i> Previous
                        </button>
                        <button class="btn btn-primary d-inline-flex align-items-center next-tab-btn" data-next="responses">
                            Next: Responses <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Responses Section -->
                <div class="docs-section d-none" id="responses">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="fw-bold text-dark mb-0"><span class="badge bg-dark rounded-circle me-2">3</span>API Responses</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <!-- Success Response -->
                                <div class="col-lg-12">
                                    <div class="card border-0 shadow-sm rounded-3 mb-3">
                                        <div class="card-header bg-soft-success border-bottom-0 py-2">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-circle-check text-success me-2 fs-5"></i>
                                                <h6 class="mb-0 fw-bold text-success">Success Response (200)</h6>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <pre class="bg-dark text-light p-3 mb-0 shadow-inner custom-scrollbar" style="max-height: 400px; overflow-y: auto;"><code class="language-json">{
    "status": "success",
    "message": "BVN verification successful",
    "data": {
        "bvn": "12345678901",
        "firstName": "ABDULLAHI",
        "middleName": "MUSA",
        "lastName": "GARBA",
        "gender": "Male",
        "dateOfBirth": "1990-01-01",
        "phoneNumber": "08012345678",
        "registrationDate": "2014-05-15",
        "enrollmentBank": "Guaranty Trust Bank",
        "enrollmentBranch": "Lagos Main",
        "nationality": "Nigerian",
        "photo": "base64_encoded_string...",
        "watchListed": false
    },
    "meta": {
        "transaction_ref": "BVN-{{ date('YmdHis') }}",
        "charge": {{ number_format($verificationPrice ?: 50, 2) }},
        "timestamp": "{{ date('Y-m-d H:i:s') }}"
    }
}</code></pre>
                                        </div>
                                        <div class="card-footer bg-white border-top-0">
                                        </div>
                                    </div>
                                </div>

                                <!-- Error Response -->
                                <div class="col-lg-12">
                                    <div class="card border-0 shadow-sm rounded-3">
                                        <div class="card-header bg-soft-danger border-bottom-0 py-2">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-circle-x text-danger me-2 fs-5"></i>
                                                <h6 class="mb-0 fw-bold text-danger">Error Response (4xx)</h6>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <pre class="bg-dark text-light p-3 mb-0 shadow-inner custom-scrollbar" style="max-height: 300px; overflow-y: auto;"><code class="language-json">{
    "status": "error",
    "message": "Invalid BVN format. Must be 11 digits.",
    "errors": {
        "bvn": ["The bvn must be 11 digits."]
    },
    "meta": {
        "code": "VALIDATION_ERROR",
        "timestamp": "{{ date('Y-m-d H:i:s') }}"
    }
}</code></pre>
                                        </div>
                                    </div>
                                </div>

                                <!-- Charging Logic Table -->
                                <div class="col-lg-12">
                                    <div class="card border-0 shadow-sm rounded-3">
                                        <div class="card-header bg-white border-bottom py-3">
                                            <h6 class="fw-bold text-primary mb-0">
                                                <i class="ti ti-coins me-2"></i> Charging Policy
                                            </h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th class="ps-4">Response Code</th>
                                                            <th>Charging Status</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="ps-4"><code>00000000</code></td>
                                                            <td><span class="badge bg-success">Charge</span></td>
                                                            <td>Successful Verification</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="ps-4"><code>99120020</code></td>
                                                            <td><span class="badge bg-success">Charge</span></td>
                                                            <td>BVN does not exist</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="ps-4"><code>99120012</code></td>
                                                            <td><span class="badge bg-secondary">Not Charge</span></td>
                                                            <td>Parameters wrong</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="ps-4"><code>99120023</code></td>
                                                            <td><span class="badge bg-secondary">Not Charge</span></td>
                                                            <td>System error</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="ps-4"><code>99120024</code></td>
                                                            <td><span class="badge bg-success">Charge</span></td>
                                                            <td>BVN suspended</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="ps-4"><code>99120025</code></td>
                                                            <td><span class="badge bg-secondary">Not Charge</span></td>
                                                            <td>BVN_PARAMETER_INVALID</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="ps-4"><code>99120026</code></td>
                                                            <td><span class="badge bg-success">Charge</span></td>
                                                            <td>BIRTH_DATE_INVALID</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="ps-4"><code>99120027</code></td>
                                                            <td><span class="badge bg-success">Charge</span></td>
                                                            <td>NAME_INVALID</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="ps-4"><code>99120028</code></td>
                                                            <td><span class="badge bg-success">Charge</span></td>
                                                            <td>GENDER_NULL</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="ps-4"><code>99120029</code></td>
                                                            <td><span class="badge bg-success">Charge</span></td>
                                                            <td>PHOTO_INVALID</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="d-flex justify-content-start mt-4">
                        <button class="btn btn-secondary d-inline-flex align-items-center prev-tab-btn" data-prev="endpoint">
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
            // Hide all sections
            document.querySelectorAll('.docs-section').forEach(section => {
                section.classList.add('d-none');
            });
            
            // Show target section
            const targetSection = document.getElementById(tabId);
            if (targetSection) {
                targetSection.classList.remove('d-none');
            }

            // Update sidebar active state
            document.querySelectorAll('.custom-sidebar-nav .nav-link').forEach(link => {
                link.classList.remove('bg-soft-primary', 'text-primary', 'fw-bold', 'active');
                link.classList.add('text-muted', 'fw-medium');
                
                if (link.getAttribute('href') === '#' + tabId) {
                    link.classList.remove('text-muted', 'fw-medium');
                    link.classList.add('bg-soft-primary', 'text-primary', 'fw-bold', 'active');
                }
            });

            // Scroll to top of content
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Sidebar click events
        document.querySelectorAll('.custom-sidebar-nav .nav-link').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const tabId = this.getAttribute('href').substring(1);
                switchTab(tabId);
                
                // Mobile: Close sidebar after selection
                if (window.innerWidth < 992) {
                    closeSidebarFunction();
                }
            });
        });

        // Next/Prev button click events
        document.querySelectorAll('.next-tab-btn, .prev-tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-next') || this.getAttribute('data-prev');
                switchTab(targetTab);
            });
        });

        // Toggle sidebar on mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarColumn = document.getElementById('sidebarColumn');
        const closeSidebar = document.getElementById('closeSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (sidebarToggle && sidebarColumn) {
            sidebarToggle.addEventListener('click', function() {
                sidebarColumn.classList.add('sidebar-visible');
                sidebarOverlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            });

            function closeSidebarFunction() {
                sidebarColumn.classList.remove('sidebar-visible');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            }

            closeSidebar.addEventListener('click', closeSidebarFunction);
            sidebarOverlay.addEventListener('click', closeSidebarFunction);

            // Close sidebar on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSidebarFunction();
                }
            });
        }

        function copyToken() {
            const tokenInput = document.getElementById('apiToken');
            tokenInput.select();
            tokenInput.setSelectionRange(0, 99999);
            
            navigator.clipboard.writeText(tokenInput.value).then(() => {
                showNotification('API token copied to clipboard!', 'success');
            }).catch(err => {
                console.error('Failed to copy: ', err);
                showNotification('Failed to copy token', 'error');
            });
        }

        function showNotification(message, type) {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.custom-notification');
            existingNotifications.forEach(notification => notification.remove());

            // Create notification element
            const notification = document.createElement('div');
            notification.className = `custom-notification alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow-lg`;
            notification.style.zIndex = '1060';
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="ti ti-${type === 'success' ? 'check' : 'alert-circle'} me-2 fs-4"></i>
                    <span class="flex-grow-1">${message}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 3000);
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    <style>
        .custom-sidebar-nav .nav-link {
            transition: all 0.3s ease;
        }
        .custom-sidebar-nav .nav-link:hover {
            background-color: rgba(242, 101, 34, 0.05); /* Very light primary */
            color: #F26522; /* Primary color */
        }
        .custom-sidebar-nav .nav-link.active, 
        .custom-sidebar-nav .nav-link.bg-soft-primary {
            border-left: 3px solid #F26522;
        }

         /* Mobile Sidebar Styles */
         .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1019;
            display: none;
        }

        .sidebar-overlay.show {
            display: block;
        }

        @media (max-width: 991.98px) {
            #sidebarColumn {
                position: fixed;
                top: 0;
                left: -100%;
                width: 300px;
                height: 100vh;
                z-index: 1020;
                transition: left 0.3s ease;
                overflow-y: auto;
                background: white;
            }

            #sidebarColumn.sidebar-visible {
                left: 0;
            }

            .sidebar-card {
                position: relative;
                top: 0;
                height: 100%;
                border-radius: 0;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }
        }

        @media (min-width: 992px) {
            .sidebar-card {
                position: sticky;
            }
        }
    </style>
    @endpush
    
</x-app-layout>