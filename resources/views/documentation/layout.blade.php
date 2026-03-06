<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>API Documentation - Arewa Smart</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/logo/logo.png') }}">
    
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <style>
        :root {
            --primary-color: #D4AF37;
            --primary-dark: #B8860B;
            --sidebar-width: 280px;
            --bg-light: #f8f9fa;
        }
        body {
            font-family: 'Figtree', sans-serif;
            background-color: var(--bg-light);
            display: flex;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }
        /* Sidebar */
        .docs-sidebar {
            width: var(--sidebar-width);
            background: #fff;
            border-right: 1px solid #eaeaea;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        .docs-sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #eaeaea;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .docs-sidebar-header img {
            height: 40px;
        }
        .docs-nav {
            padding: 20px 0;
        }
        .docs-nav-title {
            padding: 0 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #888;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        .docs-nav ul {
            list-style: none;
            padding: 0;
            margin: 0 0 20px 0;
        }
        .docs-nav li a {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: #444;
            text-decoration: none;
            font-size: 0.95rem;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        .docs-nav li a:hover {
            background-color: #f5f5f5;
            color: var(--primary-dark);
        }
        .docs-nav li a.active {
            background-color: rgba(212, 175, 55, 0.1);
            color: var(--primary-dark);
            border-left-color: var(--primary-color);
            font-weight: 600;
        }
        .docs-nav li a i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
            color: #888;
        }
        .docs-nav li a.active i {
            color: var(--primary-color);
        }

        /* Main Content */
        .docs-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        
        /* Top Navigation */
        .docs-topbar {
            background: #fff;
            padding: 15px 30px;
            border-bottom: 1px solid #eaeaea;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 900;
        }
        .btn-mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #333;
            cursor: pointer;
        }

        /* Content Area */
        .docs-content {
            padding: 40px;
            flex: 1;
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
        }
        .docs-content h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #222;
        }
        .docs-content h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin: 40px 0 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eaeaea;
            color: #333;
        }
        .docs-content h3 {
            font-size: 1.4rem;
            font-weight: 600;
            margin: 30px 0 15px;
            color: #444;
        }
        .docs-content p {
            font-size: 1.05rem;
            line-height: 1.7;
            color: #555;
            margin-bottom: 20px;
        }
        
        /* Code Blocks */
        .code-block {
            background: #1e1e1e;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            position: relative;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .code-block pre {
            margin: 0;
            color: #d4d4d4;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.9rem;
            overflow-x: auto;
        }
        .code-caption {
            background: #333;
            color: #ccc;
            font-size: 0.8rem;
            padding: 5px 15px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            margin: -20px -20px 15px -20px;
            border-bottom: 1px solid #444;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .code-method {
            font-weight: bold;
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-right: 10px;
        }
        .method-get { background: #61affe; color: #fff; }
        .method-post { background: #49cc90; color: #fff; }

        /* Tables */
        .docs-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
            background: #fff;
        }
        .docs-table th, .docs-table td {
            padding: 12px 15px;
            border: 1px solid #eaeaea;
            text-align: left;
        }
        .docs-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        .docs-table td {
            color: #555;
            font-size: 0.95rem;
        }
        .docs-table code {
            background: #f1f1f1;
            padding: 2px 6px;
            border-radius: 4px;
            color: #e83e8c;
            font-size: 0.85rem;
        }

        /* Badges */
        .badge-req { background: #ffebee; color: #c62828; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
        .badge-opt { background: #e8f5e9; color: #2e7d32; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }

        /* Alerts */
        .docs-alert {
            padding: 15px 20px;
            border-radius: 8px;
            border-left: 4px solid;
            margin-bottom: 25px;
            display: flex;
        }
        .docs-alert i { margin-right: 15px; font-size: 1.2rem; margin-top: 2px; }
        .docs-alert-info { background: #e3f2fd; border-color: #2196f3; color: #0d47a1; }
        .docs-alert-warning { background: #fff3e0; border-color: #ff9800; color: #e65100; }

        /* Mobile Responsive */
        @media (max-width: 991px) {
            .docs-sidebar {
                transform: translateX(-100%);
            }
            .docs-sidebar.show {
                transform: translateX(0);
                box-shadow: 2px 0 15px rgba(0,0,0,0.1);
            }
            .docs-main {
                margin-left: 0;
            }
            .btn-mobile-toggle {
                display: block;
            }
            .docs-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="docs-sidebar" id="sidebar">
        <div class="docs-sidebar-header">
            <a href="{{ url('/') }}">
                <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Arewa Smart">
            </a>
            <button class="btn btn-mobile-toggle d-lg-none" id="closeSidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="docs-nav">
            <div class="docs-nav-title">Getting Started</div>
            <ul>
                <li>
                    <a href="{{ route('docs.index') }}" class="{{ request()->routeIs('docs.index') ? 'active' : '' }}">
                        <i class="fas fa-book"></i> Introduction
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.pricing') }}" class="{{ request()->routeIs('docs.pricing') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i> Pricing
                    </a>
                </li>
            </ul>

            <div class="docs-nav-title">Identity Verification</div>
            <ul>
                <li>
                    <a href="{{ route('docs.nin') }}" class="{{ request()->routeIs('docs.nin') ? 'active' : '' }}">
                        <i class="fas fa-id-card"></i> NIN Verification
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.nin-demo') }}" class="{{ request()->routeIs('docs.nin-demo') ? 'active' : '' }}">
                        <i class="fas fa-address-card"></i> NIN DEMO Docs
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.nin-phone') }}" class="{{ request()->routeIs('docs.nin-phone') ? 'active' : '' }}">
                        <i class="fas fa-phone-square-alt"></i> NIN Phone No Docs
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.nin-validation') }}" class="{{ request()->routeIs('docs.nin-validation') ? 'active' : '' }}">
                        <i class="fas fa-check-double"></i> NIN Validation
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.nin-modification') }}" class="{{ request()->routeIs('docs.nin-modification') ? 'active' : '' }}">
                        <i class="fas fa-user-edit"></i> NIN Modification
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.nin-ipe') }}" class="{{ request()->routeIs('docs.nin-ipe') ? 'active' : '' }}">
                        <i class="fas fa-file-signature"></i> NIN IPE
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.bvn') }}" class="{{ request()->routeIs('docs.bvn') ? 'active' : '' }}">
                        <i class="fas fa-fingerprint"></i> BVN Verification
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.tin') }}" class="{{ request()->routeIs('docs.tin') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar"></i> TIN Verification
                    </a>
                </li>
            </ul>

            <div class="docs-nav-title">Utility & Bills</div>
            <ul>
                <li>
                    <a href="{{ route('docs.airtime') }}" class="{{ request()->routeIs('docs.airtime') ? 'active' : '' }}">
                        <i class="fas fa-mobile-alt"></i> Airtime Topup
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.data') }}" class="{{ request()->routeIs('docs.data') ? 'active' : '' }}">
                        <i class="fas fa-wifi"></i> Data Subscription
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.sme-data') }}" class="{{ request()->routeIs('docs.sme-data') ? 'active' : '' }}">
                        <i class="fas fa-network-wired"></i> SME Data
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.electricity') }}" class="{{ request()->routeIs('docs.electricity') ? 'active' : '' }}">
                        <i class="fas fa-bolt"></i> Electricity Payment
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="docs-main">
        <header class="docs-topbar">
            <div class="d-flex align-items-center">
                <button class="btn-mobile-toggle me-3" id="openSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="m-0 d-none d-md-block text-muted">API Documentation</h5>
            </div>
            <div>
                <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm me-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-dark btn-sm" style="background: linear-gradient(45deg, var(--primary-color), var(--primary-dark)); border: none;">Get API Key</a>
            </div>
        </header>

        <div class="docs-content">
            @yield('content')
        </div>
        
        <footer style="padding: 20px 40px; border-top: 1px solid #eaeaea; color: #888; font-size: 0.9rem; margin-top: auto;">
            &copy; {{ date('Y') }} Arewa Smart Idea. All rights reserved.
        </footer>
    </main>

    <script>
        document.getElementById('openSidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('show');
        });
        document.getElementById('closeSidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('show');
        });
    </script>
</body>
</html>
