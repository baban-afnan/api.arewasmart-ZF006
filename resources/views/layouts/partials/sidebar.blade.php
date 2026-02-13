<!-- Main Wrapper -->
	<div class="main-wrapper">

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}" class="logo logo-normal">
            <img src="{{ asset('assets/img/logo/logo-small.png') }}" alt="Logo" width="120" height="70">
        </a>
        <a href="{{ route('dashboard') }}" class="logo-small">
            <img src="{{ asset('assets/img/logo/logo-small.png') }}" alt="Logo" width="120" height="70">
        </a>
        <a href="{{ route('dashboard') }}" class="dark-logo">
            <img src="{{ asset('assets/img/logo/logo-small.png') }}" alt="Logo" width="120" height="70">
        </a>
    </div>
    <!-- /Logo -->
    
    <div class="modern-profile p-3 pb-0">
        <div class="text-center rounded bg-light p-3 mb-4 user-profile">
            <div class="avatar avatar-lg online mb-3">
                <img src="{{ Auth::user()->photo ? asset(Auth::user()->photo) : asset('assets/img/profiles/avatar-02.jpg') }}" alt="Img" class="img-fluid rounded-circle">
            </div>
            <h6 class="fs-12 fw-normal mb-1">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h6>
            <p class="fs-10">{{ Auth::user()->email }}</p>
        </div>
        <div class="sidebar-nav mb-3">
            <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent" role="tablist">
                <li class="nav-item"><a class="nav-link active border-0" href="#">Menu</a></li>
                <li class="nav-item"><a class="nav-link border-0" href="chat.html">Chats</a></li>
                <li class="nav-item"><a class="nav-link border-0" href="email.html">Inbox</a></li>
            </ul>
        </div>
    </div>
    
    <div class="sidebar-header p-3 pb-0 pt-2">
        <div class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center">
            <div class="avatar avatar-md online">
                <img src="assets/img/profiles/avatar-02.jpg" alt="Img" class="img-fluid rounded-circle">
            </div>
            <div class="text-start sidebar-profile-info ms-2">
                <h6 class="fs-12 fw-normal mb-1">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h6>
                <p class="fs-10">{{ Auth::user()->email }}</p>
            </div>
        </div>
        
        <div class="input-group input-group-flat d-inline-flex mb-4">
            <span class="input-icon-addon">
                <i class="ti ti-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Search in HRMS">
            <span class="input-group-text">
                <kbd>CTRL + / </kbd>
            </span>
        </div>
        
        <div class="d-flex align-items-center justify-content-between menu-item mb-3">
            <div class="me-3">
                <a href="calendar.html" class="btn btn-menubar">
                    <i class="ti ti-layout-grid-remove"></i>
                </a>
            </div>
            <div class="me-3">
                <a href="chat.html" class="btn btn-menubar position-relative">
                    <i class="ti ti-brand-hipchat"></i>
                    <span class="badge bg-info rounded-pill d-flex align-items-center justify-content-center header-badge">5</span>
                </a>
            </div>
            <div class="me-3 notification-item">
                <a href="activity.html" class="btn btn-menubar position-relative me-1">
                    <i class="ti ti-bell"></i>
                    <span class="notification-status-dot"></span>
                </a>
            </div>
            <div class="me-0">
                <a href="email.html" class="btn btn-menubar">
                    <i class="ti ti-message"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <!-- Main Menu -->
                <li class="menu-title"><span>Main Menu</span></li>
                
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="ti ti-home"></i><span>Dashboard</span>
                    </a>
                </li>


                   <!-- wallet -->
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="ti ti-wallet"></i>
                        <span>Wallet</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="{{ route('wallet') }}" class="{{ request()->routeIs('wallet') ? 'active' : '' }}">Wallet</a></li>
                        <li><a href="{{ route('wallet.bonus') }}" class="{{ request()->routeIs('wallet.bonus') ? 'active' : '' }}">Bonus</a></li>
                    </ul>
                </li>
                <!-- /wallet-->
                

                <!-- Utility bill payment Services -->
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="ti ti-currency-naira"></i>
                        <span>Bill payment</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="{{ route('developer.airtime.docs') }}" class="{{ request()->routeIs('developer.airtime.docs') ? 'active' : '' }}">Airtime</a></li>
                        <li><a href="{{ route('developer.data.docs') }}" class="{{ request()->routeIs('developer.data.docs') ? 'active' : '' }}">Data</a></li>
                        <li><a href="{{ route('developer.sme-data.docs') }}">SME Data</a></li>
                        <li><a href="{{ route('developer.electricity.docs') }}" class="{{ request()->routeIs('developer.electricity.docs') ? 'active' : '' }}">Electricity</a></li>
                        <li><a href="#">TV</a></li>
                        <li><a href="#">Education Pin</a></li>
                    </ul>
                </li>
                <!-- /Utility bill payment Services-->

                <!-- BVN Services -->
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="ti ti-home"></i>
                        <span>BVN Services</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('developer.bvn.modification.docs') }}" class="{{ request()->routeIs('developer.bvn.modification.docs') ? 'active' : '' }}">
                                BVN Modification Docs
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                BVN Search Docs
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- /BVN Services -->

                <!-- NIN Services -->
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="ti ti-user-check"></i>
                        <span>NIN Services</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="{{ route('developer.nin.validation.docs') }}" class="{{ request()->routeIs('developer.nin.validation.docs') ? 'active' : '' }}">Validation API Docs</a></li>
                        <li><a href="{{ route('developer.nin.ipe.docs') }}" class="{{ request()->routeIs('developer.nin.ipe.docs') ? 'active' : '' }}">IPE Clearance API Docs</a></li>
                        <li><a href="{{ route('developer.nin.modification.docs') }}" class="{{ request()->routeIs('developer.nin.modification.docs') ? 'active' : '' }}">Modification</a></li>
                    </ul>
                </li>
                <!-- /NIN Services -->

                <!-- Verification -->
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="ti ti-fingerprint"></i>
                        <span>Verification</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="{{ route('developer.bvn.docs') }}" class="{{ request()->routeIs('developer.bvn.docs') ? 'active' : '' }}">BVN API Docs</a></li>
                        <li><a href="{{ route('developer.nin.docs') }}" class="{{ request()->routeIs('developer.nin.docs') ? 'active' : '' }}">NIN API Docs</a></li>
                        <li><a href="{{ route('developer.tin.docs') }}" class="{{ request()->routeIs('developer.tin.docs') ? 'active' : '' }}">TIN API Docs</a></li>
                        <li><a href="{{ route('developer.nin.demo.docs') }}" class="{{ request()->routeIs('developer.nin.demo.docs') ? 'active' : '' }}">NIN DEMO Docs</a></li>
                        <li><a href="{{ route('developer.nin.phone.docs') }}" class="{{ request()->routeIs('developer.nin.phone.docs') ? 'active' : '' }}">NIN Phone No Docs</a></li>
                    </ul>
                </li>
                <!-- /Verification -->
              
                <!-- Account Section -->
                <li class="menu-title"><span>Account</span></li>

                  <li>
                    <a href="{{ route('prices.index') }}" class="{{ request()->routeIs('prices.index') ? 'active' : '' }}">
                        <i class="ti ti-shopping-bag"></i><span>Prices</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <i class="ti ti-settings"></i><span>Settings</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.index') ? 'active' : '' }}">
                        <i class="ti ti-receipt"></i><span>Transactions</span>
                    </a>
                </li>
                
                <li>
                    <a href="javascript:void(0);" onclick="confirmSupport()">
                        <i class="ti ti-message"></i><span>Support</span>
                    </a>
                </li>
                
                <li>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="javascript:void(0);" onclick="confirmLogout(event)">
                            <i class="ti ti-logout"></i><span>Logout</span>
                        </a>
                    </form>
                    <script>
                        function confirmLogout(event) {
                            event.preventDefault();
                            Swal.fire({
                                title: 'Are you sure?',
                                text: "You will be logged out of your session.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, logout!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    document.getElementById('logout-form').submit();
                                }
                            })
                        }

                        function confirmSupport() {
                            Swal.fire({
                                title: 'Contact Support',
                                text: "You will be redirected to our WhatsApp support channel.",
                                icon: 'info',
                                showCancelButton: true,
                                confirmButtonColor: '#25D366',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Continue to WhatsApp'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.open('https://wa.me/2347037343660', '_blank');
                                }
                            })
                        }
                    </script>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->


 <style>

  /* Better icon and text spacing */
.sidebar-menu li a {
    display: flex;
    align-items: center;
    padding: 10px 15px;
}

.sidebar-menu li a i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

/* Submenu styling */
.sidebar-menu .submenu ul {
    background: rgba(0, 0, 0, 0.02);
}

.sidebar-menu .submenu ul li a {
    padding-left: 45px;
    font-size: 13px;
}

/* Active state */
.sidebar-menu li a.active {
    background: #f1d2aeff;
    color: white;
}

/* Menu title spacing */
.menu-title {
    padding: 15px 15px 5px 15px;
    font-size: 12px;
    text-transform: uppercase;
    color: #6c757d;
    font-weight: 600;
}

/* Special Wallet Button */
.special-wallet-entry a {
    background: linear-gradient(45deg, #1a1a1a, #2c3e50);
    color: #fff !important;
    border-radius: 8px;
    margin: 5px 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    border: 1px solid rgba(255,255,255,0.1);
    transition: all 0.3s ease;
}

.special-wallet-entry a:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    background: linear-gradient(45deg, #2c3e50, #1a1a1a);
}

.special-wallet-entry i {
    color: #ffd700 !important; /* Gold */
}

.pulse-badge {
    animation: pulse-animation 2s infinite;
}

@keyframes pulse-animation {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
    70% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}
 </style>