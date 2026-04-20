<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>API Documentation - Arewa Smart</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/logo/logo.png') }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #1d4ed8;
            --sidebar-width: 300px;
            --peach-soft: #FFF5F2;
            --peach-text: #e5715e;
            --navy-dark: #1A2B4B;

            /* Light Theme Variables */
            --bg-body: #f8fafc;
            --bg-sidebar: #ffffff;
            --bg-card: #ffffff;
            --bg-topbar: rgba(255, 255, 255, 0.8);
            --text-main: #334155;
            --text-muted: #64748b;
            --text-heading: #1e293b;
            --border-color: #eef2f6;
            --sidebar-link: #64748b;
            --sidebar-link-hover: #3b82f6;
            --sidebar-link-bg: #fff;
            --sidebar-link-hover-bg: #f8fafc;
            --bg-soft-primary: #eff6ff;
            --footer-bg: #ffffff;
        }

        [data-theme="dark"] {
            --bg-body: #0f172a;
            --bg-sidebar: #1e293b;
            --bg-card: #1e293b;
            --bg-topbar: rgba(15, 23, 42, 0.8);
            --text-main: #94a3b8;
            --text-muted: #64748b;
            --text-heading: #f1f5f9;
            --border-color: #334155;
            --sidebar-link: #94a3b8;
            --sidebar-link-hover: #3b82f6;
            --sidebar-link-bg: rgba(255, 255, 255, 0.02);
            --sidebar-link-hover-bg: rgba(255, 255, 255, 0.05);
            --bg-soft-primary: rgba(59, 130, 246, 0.1);
            --footer-bg: #1e293b;
            --peach-soft: #2a1f1c; /* Dimm peach for dark mode */
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            display: flex;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            color: var(--text-main);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--text-heading);
        }

        /* Sidebar */
        .docs-sidebar {
            width: var(--sidebar-width);
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .docs-sidebar-header {
            padding: 24px;
            background-color: var(--peach-soft);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .docs-sidebar-header h5 {
            color: var(--peach-text);
            font-weight: 700;
            margin: 0;
            font-size: 1.1rem;
        }
        .docs-nav {
            padding: 24px;
        }
        .docs-nav-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 800;
            color: var(--text-muted);
            margin: 24px 0 12px 12px;
            letter-spacing: 0.1em;
        }
        .docs-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .docs-nav li {
            margin-bottom: 8px;
        }
        .docs-nav li a {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: var(--sidebar-link);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 12px;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            background: var(--sidebar-link-bg);
        }
        .docs-nav li a:hover {
            background-color: var(--sidebar-link-hover-bg);
            color: var(--sidebar-link-hover);
            transform: translateY(-1px);
            border-color: var(--border-color);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
        }
        .support-card-custom .btn-support { 
            background-color: #ffffff !important; 
            color: #3b82f6 !important; 
            font-weight: 700;
            border-radius: 50px;
        }

        /* Documentation Theme Helper Overrides */
        [data-theme="dark"] .text-dark {
            color: var(--text-heading) !important;
        }
        [data-theme="dark"] .text-muted {
            color: var(--text-muted) !important;
        }
        [data-theme="dark"] .bg-white {
            background-color: var(--bg-card) !important;
        }
        [data-theme="dark"] .bg-light {
            background-color: var(--bg-sidebar) !important;
        }
        [data-theme="dark"] .lead {
            color: var(--text-main) !important;
        }
        [data-theme="dark"] .badge.bg-light {
            background-color: var(--border-color) !important;
            color: var(--text-main) !important;
        }
        [data-theme="dark"] .table-borderless thead.bg-light {
            background-color: var(--border-color) !important;
        }
        [data-theme="dark"] .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }
        .docs-nav li a.active {
            background-color: var(--navy-dark) !important;
            color: #3b82f6 !important;
            font-weight: 700;
            box-shadow: 0 10px 15px -3px rgba(26, 43, 75, 0.2);
            border-color: var(--navy-dark);
        }
        .docs-nav li a i {
            margin-right: 12px;
            font-size: 1.25rem;
            opacity: 0.7;
        }

        /* Main Content */
        .docs-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        /* Top Navigation */
        .docs-topbar {
            background: var(--bg-topbar);
            backdrop-filter: blur(10px);
            padding: 16px 32px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 900;
        }
        .btn-mobile-toggle {
            display: none;
            background: var(--bg-soft-primary);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            color: var(--text-main);
            cursor: pointer;
            transition: all 0.2s;
        }
        
        /* Theme Toggle Button */
        .theme-toggle-btn {
            background: var(--bg-soft-primary);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: var(--text-main);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            margin-right: 10px;
        }
        .theme-toggle-btn:hover {
            transform: scale(1.1);
            background: var(--primary-color);
            color: white;
        }

        /* Content Area Customizations */
        .docs-content {
            padding: 48px;
            flex: 1;
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }

        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color) !important;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
        }
        
        .alert-info, .bg-soft-primary {
            background-color: var(--bg-soft-primary) !important;
            color: var(--text-main) !important;
            border: none;
        }

        /* Table adjustments for dark mode */
        .table {
            color: var(--text-main);
        }
        .table thead th {
            background-color: var(--border-color);
            color: var(--text-heading);
            border: none;
        }
        .table-hover tbody tr:hover {
            background-color: var(--sidebar-link-hover-bg);
            color: var(--primary-color);
        }
        
        /* Code blocks often don't need changes if they are already dark */
        .bg-dark {
            background-color: #1e293b !important; /* Keep code areas consistent */
        }

        /* Mobile Responsive */
        @media (max-width: 991px) {
            .docs-sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }
            .docs-sidebar.show {
                transform: translateX(0);
                box-shadow: 20px 0 25px -5px rgb(0 0 0 / 0.1);
            }
            .docs-main {
                margin-left: 0;
            }
            .btn-mobile-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .docs-content {
                padding: 24px;
            }
        }

        /* Footer */
        .docs-footer {
            padding: 32px 48px;
            border-top: 1px solid var(--border-color);
            background: var(--footer-bg);
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
    
    <script>
        // Critical script to prevent flash of unstyled content (FOUC)
        (function() {
            const savedTheme = localStorage.getItem('docs-theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>

    <!-- Sidebar -->
    <aside class="docs-sidebar" id="sidebar">
        <div class="docs-sidebar-header">
            <div class="d-flex align-items-center">
                <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Arewa Smart" class="me-2">
                <h5 class="mb-0">Docs</h5>
            </div>
            <button class="btn-mobile-toggle d-lg-none" id="closeSidebar">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <nav class="docs-nav">
            <div class="docs-nav-title" style="margin-top: 0;">Getting Started</div>
            <ul>
                <li>
                    <a href="{{ route('docs.index') }}" class="{{ request()->routeIs('docs.index') ? 'active' : '' }}">
                        <i class="ti ti-book-2"></i> Introduction
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.pricing') }}" class="{{ request()->routeIs('docs.pricing') ? 'active' : '' }}">
                        <i class="ti ti-receipt-2"></i> Pricing
                    </a>
                </li>
            </ul>

            <div class="docs-nav-title">Identity Verification</div>
            <ul>
                <li>
                    <a href="{{ route('docs.nin') }}" class="{{ request()->routeIs('docs.nin') ? 'active' : '' }}">
                        <i class="ti ti-id"></i> NIN Verification
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.nin-demo') }}" class="{{ request()->routeIs('docs.nin-demo') ? 'active' : '' }}">
                        <i class="ti ti-id-badge-2"></i> NIN DEMO Docs
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.nin-phone') }}" class="{{ request()->routeIs('docs.nin-phone') ? 'active' : '' }}">
                        <i class="ti ti-phone-check"></i> NIN Phone No Docs
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.nin-validation') }}" class="{{ request()->routeIs('docs.nin-validation') ? 'active' : '' }}">
                        <i class="ti ti-user-check"></i> NIN Validation
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.nin-modification') }}" class="{{ request()->routeIs('docs.nin-modification') ? 'active' : '' }}">
                        <i class="ti ti-user-edit"></i> NIN Modification
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.nin-ipe') }}" class="{{ request()->routeIs('docs.nin-ipe') ? 'active' : '' }}">
                        <i class="ti ti-file-certificate"></i> NIN IPE
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.bvn') }}" class="{{ request()->routeIs('docs.bvn') ? 'active' : '' }}">
                        <i class="ti ti-fingerprint"></i> BVN Verification
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.tin') }}" class="{{ request()->routeIs('docs.tin') ? 'active' : '' }}">
                        <i class="ti ti-file-analytics"></i> TIN Verification
                    </a>
                </li>
            </ul>

            <div class="docs-nav-title">Utility & Bills</div>
            <ul>
                <li>
                    <a href="{{ route('docs.airtime') }}" class="{{ request()->routeIs('docs.airtime') ? 'active' : '' }}">
                        <i class="ti ti-device-mobile"></i> Airtime Topup
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.data') }}" class="{{ request()->routeIs('docs.data') ? 'active' : '' }}">
                        <i class="ti ti-wifi"></i> Data Subscription
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.sme-data') }}" class="{{ request()->routeIs('docs.sme-data') ? 'active' : '' }}">
                        <i class="ti ti-broadcast"></i> SME Data
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.electricity') }}" class="{{ request()->routeIs('docs.electricity') ? 'active' : '' }}">
                        <i class="ti ti-bolt"></i> Electricity Payment
                    </a>
                </li>
            </ul>

            <!-- Support Card -->
            <div class="card border-0 shadow-sm support-card-custom text-white overflow-hidden position-relative mt-5">
                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                    <i class="ti ti-headset fs-1"></i>
                </div>
                <div class="card-body p-4 position-relative z-index-1">
                    <h6 class="fw-bold text-white mb-2">Need Help?</h6>
                    <p class="small text-white-50 mb-3" style="font-size: 0.8rem;">Our support team is available 24/7 to assist with integration.</p>
                    <a href="https://chat.whatsapp.com/KoSu12yDO4A8b6AvYSkvIx" target="_blank" class="btn btn-support w-100 btn-sm shadow-sm">
                        <i class="ti ti-brand-whatsapp me-1"></i> Contact Support
                    </a>
                </div>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="docs-main">
        <header class="docs-topbar">
            <div class="d-flex align-items-center">
                <button class="btn-mobile-toggle me-3 d-lg-none" id="openSidebar">
                    <i class="ti ti-menu-2"></i>
                </button>
                <div class="d-none d-md-flex align-items-center text-muted fw-semibold">
                    <i class="ti ti-book me-2"></i>
                    <span>Developer Documentation</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="theme-toggle-btn" id="themeToggle" title="Toggle Dark Mode">
                    <i class="ti ti-moon" id="themeIcon"></i>
                </button>
                <a href="{{ route('login') }}" class="btn btn-ghost-dark btn-sm rounded-pill px-3">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-bold">Get API Key</a>
            </div>
        </header>

        <div class="docs-content">
            @yield('content')
        </div>
        
        <footer class="docs-footer">
            <div>&copy; {{ date('Y') }} Arewa Smart Idea. <span class="d-none d-sm-inline">All rights reserved.</span></div>
            <div class="d-flex gap-3">
                <a href="#" class="text-muted"><i class="ti ti-brand-github"></i></a>
                <a href="#" class="text-muted"><i class="ti ti-brand-twitter"></i></a>
            </div>
        </footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle
        document.getElementById('openSidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('show');
        });
        document.getElementById('closeSidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const openBtn = document.getElementById('openSidebar');
            if (window.innerWidth < 992 && 
                sidebar.classList.contains('show') && 
                !sidebar.contains(event.target) && 
                !openBtn.contains(event.target)) {
                sidebar.classList.remove('show');
            }
        });

        // Theme Management
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        
        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.replace('ti-moon', 'ti-sun');
            } else {
                themeIcon.classList.replace('ti-sun', 'ti-moon');
            }
        }

        // Initialize icon on load
        updateThemeIcon(document.documentElement.getAttribute('data-theme'));

        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('docs-theme', newTheme);
            updateThemeIcon(newTheme);
        });
    </script>
</body>
</html>
