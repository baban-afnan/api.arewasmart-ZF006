<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
   
    <meta name="description" content="SmartHR - An advanced Bootstrap 5 admin dashboard template for HRM and CRM. Ideal for managing employee records, payroll, attendance, recruitment, and team performance with an intuitive and responsive design. Perfect for HR teams and business managers looking to streamline workforce management.">
    <meta name="keywords" content="HR dashboard template, HRM admin template, Bootstrap 5 HR dashboard, workforce management dashboard, employee management system, payroll dashboard, HR analytics, admin dashboard, CRM admin template, human resources management, HR admin template, team management dashboard, recruitment dashboard, employee attendance system, performance management, HR CRM, HR dashboard HTML, Bootstrap HR template, employee engagement, HR software, project management dashboard">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="index, follow">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/logo/logo.png') }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/logo/logo.png') }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ asset('assets/img/logo/logo.png') }}" type="image/x-icon" />

    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Feather CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/icons/feather/feather.css') }}">

    <!-- Tabler Icons -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">

    <!-- Datetimepicker -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">

    <!-- Bootstrap Tagsinput -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">

    <!-- Summernote -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/summernote/summernote-lite.min.css') }}">

    <!-- Daterangepicker -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Color Picker -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/@simonwep/pickr/themes/nano.min.css') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@200;300;400;500;600;700;800;900;1000&display=swap" rel="stylesheet">

    <!-- Custom App CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bokanturai.css') }}">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    @stack('styles')

    <!-- Theme Detection Script -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>

    <style>
    /* Dark Mode System - High Precedence */
    :root {
        --bg-body: #f4f7fe;
        --bg-card: #ffffff;
        --text-main: #334155;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --header-bg: #ffffff;
    }

    .dark-mode {
        --bg-body: #0f172a;
        --bg-card: #1e293b;
        --text-main: #f1f5f9;
        --text-muted: #94a3b8;
        --border-color: #334155;
        --header-bg: #1e293b;
    }

    .dark-mode, .dark-mode body {
        background-color: var(--bg-body) !important;
        color: var(--text-main) !important;
    }

    .dark-mode .card, 
    .dark-mode .card-body,
    .dark-mode .modal-content {
        background-color: var(--bg-card) !important;
        border-color: var(--border-color) !important;
        color: var(--text-main) !important;
    }

    .dark-mode .card-header, 
    .dark-mode .card-footer,
    .dark-mode .bg-white,
    .dark-mode .bg-light {
        background-color: var(--bg-card) !important;
        border-color: var(--border-color) !important;
        color: var(--text-main) !important;
    }

    /* Profile Dropdown Dark Mode */
    .dark-mode .profile-dropdown .dropdown-menu {
        background-color: var(--bg-card) !important;
        border-color: var(--border-color) !important;
    }
    .dark-mode .profile-dropdown .dropdown-item {
        color: var(--text-main) !important;
    }
    .dark-mode .profile-dropdown .dropdown-item:hover,
    .dark-mode .profile-dropdown .dropdown-item:focus {
        background-color: rgba(255, 255, 255, 0.05) !important;
        color: var(--text-main) !important;
    }
    .dark-mode .profile-dropdown .dropdown-item.text-danger {
        color: #f87171 !important;
    }
    .dark-mode .profile-dropdown .dropdown-item.text-danger:hover {
        background-color: rgba(239, 68, 68, 0.08) !important;
    }

    .dark-mode h1, .dark-mode h2, .dark-mode h3, .dark-mode h4, .dark-mode h5, .dark-mode h6,
    .dark-mode .text-dark, .dark-mode .fw-bold {
        color: var(--text-main) !important;
    }

    .dark-mode .text-muted:not(.badge):not(.stats-label),
    .dark-mode .text-secondary {
        color: var(--text-muted) !important;
    }

    .dark-mode .table {
        color: var(--text-main) !important;
        background-color: transparent !important;
    }

    .dark-mode thead.bg-light,
    .dark-mode .thead-light th {
        background-color: rgba(255, 255, 255, 0.05) !important;
        color: var(--text-main) !important;
        border-color: var(--border-color) !important;
    }

    .dark-mode .table td, .dark-mode .table th {
        border-color: var(--border-color) !important;
        color: var(--text-main) !important;
    }

    .dark-mode .header, .dark-mode .main-header {
        background-color: var(--header-bg) !important;
        border-bottom: 1px solid var(--border-color) !important;
    }

    .dark-mode .btn-light {
        background-color: rgba(255, 255, 255, 0.1) !important;
        border-color: transparent !important;
        color: var(--text-main) !important;
    }

    /* Universal Modal & Form Overrides for Dark Mode */
    .dark-mode .modal-header,
    .dark-mode .modal-footer {
        background-color: var(--bg-card) !important;
        border-color: var(--border-color) !important;
        color: var(--text-main) !important;
    }

    .dark-mode .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    .dark-mode .form-control,
    .dark-mode .form-select,
    .dark-mode .input-group-text {
        background-color: rgba(255, 255, 255, 0.05) !important;
        border-color: var(--border-color) !important;
        color: var(--text-main) !important;
    }

    .dark-mode .form-control:focus,
    .dark-mode .form-select:focus {
        background-color: rgba(255, 255, 255, 0.08) !important;
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 0.25rem rgba(242, 105, 34, 0.25);
    }

    .dark-mode .form-floating > label {
        color: var(--text-muted) !important;
    }

    .dark-mode .form-floating > .form-control:focus ~ label,
    .dark-mode .form-floating > .form-control:not(:placeholder-shown) ~ label,
    .dark-mode .form-floating > .form-select ~ label {
        color: var(--text-main) !important;
        background-color: transparent !important;
    }

    .dark-mode .form-check-input {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: var(--border-color);
    }

    .dark-mode .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .dark-mode .form-text {
        color: var(--text-muted) !important;
    }

    .dark-mode .select2-container--default .select2-selection--single {
        background-color: rgba(255, 255, 255, 0.05) !important;
        border-color: var(--border-color) !important;
        color: var(--text-main) !important;
    }

    .dark-mode .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--text-main) !important;
    }

    /* SweetAlert2 Dark Mode Overrides */
    .dark-mode .swal2-popup {
        background-color: var(--bg-card) !important;
        color: var(--text-main) !important;
        border: 1px solid var(--border-color) !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5) !important;
    }

    .dark-mode .swal2-title,
    .dark-mode .swal2-html-container,
    .dark-mode .swal2-content {
        color: var(--text-main) !important;
    }

    .dark-mode .swal2-footer {
        border-top: 1px solid var(--border-color) !important;
        color: var(--text-muted) !important;
    }

    .dark-mode .swal2-close:hover {
        color: var(--primary) !important;
    }

    .dark-mode .swal2-validation-message {
        background-color: rgba(255, 255, 255, 0.05) !important;
        color: var(--text-main) !important;
    }

    .dark-mode .swal2-timer-progress-bar {
        background: var(--primary) !important;
    }

    /* Premium Table Utilities */
    .table-premium {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .table-premium thead th {
        background: transparent !important;
        border-bottom: 1px solid var(--border-color) !important;
        padding: 1rem 1.25rem !important;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: var(--text-muted) !important;
    }
    .table-premium tbody td {
        padding: 1rem 1.25rem !important;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
        background: transparent !important;
    }
    .table-premium tbody tr:last-child td {
        border-bottom: none;
    }
    .table-premium.table-hover tbody tr:hover td {
        background-color: rgba(0, 0, 0, 0.02) !important;
    }
    .dark-mode .table-premium.table-hover tbody tr:hover td {
        background-color: rgba(255, 255, 255, 0.02) !important;
    }

    /* Adaptive Subtle Badges */
    .badge-subtle {
        padding: 0.4em 0.8em;
        font-weight: 600;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
    }
    .badge-subtle-success { background-color: rgba(34, 197, 94, 0.1) !important; color: #22c55e !important; }
    .badge-subtle-danger { background-color: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; }
    .badge-subtle-warning { background-color: rgba(245, 158, 11, 0.1) !important; color: #f59e0b !important; }
    .badge-subtle-info { background-color: rgba(6, 182, 212, 0.1) !important; color: #06b6d4 !important; }
    .badge-subtle-primary { background-color: rgba(242, 105, 34, 0.1) !important; color: #f26922 !important; }

    .dark-mode .badge-subtle-success { background-color: rgba(34, 197, 94, 0.2) !important; color: #4ade80 !important; }
    .dark-mode .badge-subtle-danger { background-color: rgba(239, 68, 68, 0.2) !important; color: #f87171 !important; }
    .dark-mode .badge-subtle-warning { background-color: rgba(245, 158, 11, 0.2) !important; color: #fbbf24 !important; }
    .dark-mode .badge-subtle-info { background-color: rgba(6, 182, 212, 0.2) !important; color: #22d3ee !important; }
    .dark-mode .badge-subtle-primary { background-color: rgba(242, 105, 34, 0.2) !important; color: #f26922 !important; }
    </style>
</head>

<body>
    <!-- Tap to top -->
    <div class="tap-top"><i class="iconly-Arrow-Up icli"></i></div>

    <!-- Loader -->
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>

    <!-- Page Wrapper -->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        @include('layouts.partials.header')

        <div class="page-body-wrapper">
            @include('layouts.partials.sidebar')

            <div class="page-body">
                <div class="container-fluid">
                    @isset($header)
                        <div class="page-title">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h2>{{ $header }}</h2>
                                </div>
                            </div>
                        </div>
                    @endisset

                    <main class="pb-5 mb-5">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </div>
<hr>
   <!-- ===== Footer Start ===== -->
<footer class="footer bg-primary text-light py-3 mt-auto">
  <div class="container-fluid">
    <div class="row align-items-center justify-content-between">

      <!-- Left Side: Copyright -->
      <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
        <p class="mb-0 small">
          © <span id="currentYear"></span> 
          <strong class="text-dark"> Arewa Smart Idea  </strong>. 
          All Rights Reserved.
        </p>
      </div>

      <!-- Right Side: Social & Quick Links -->
      <div class="col-md-6 text-center text-md-end">
        <div class="d-inline-flex align-items-center gap-3">
          <a href="https://www.facebook.com/arewasmartidea" target="_blank" class="text-light text-decoration-none footer-social">
            <i class="ti ti-brand-facebook fs-18"></i>
          </a>
          <a href="https://x.com/arewasmartidea" target="_blank" class="text-light text-decoration-none footer-social">
            <i class="ti ti-brand-twitter fs-18"></i>
          </a>
          <a href="https://chat.whatsapp.com/KoSu12yDO4A8b6AvYSkvIx" target="_blank" class="text-light text-decoration-none footer-social">
            <i class="ti ti-brand-whatsapp fs-18"></i>
          </a>
          <a href="mailto:arewasmart001@gmail.com" class="text-light text-decoration-none footer-social">
            <i class="ti ti-mail fs-18"></i>
          </a>
        </div>
      </div>

    </div>
  </div>
</footer>
<!-- ===== Footer End ===== -->

        <div class="row">
            @include('pages.dashboard.kyc')
        </div>

         <div class="row">
        @if(!auth()->user()->two_factor_enabled && !session('two_factor_verified'))
            @include('auth.two-factor')
        @endif
        </div>

<!-- ===== Footer Style ===== -->
<style>
  .footer {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    font-size: 14px;
    backdrop-filter: blur(8px);
  }
  .footer-social {
    transition: all 0.3s ease;
  }
  .footer-social:hover {
    color: #ffc107 !important;
    transform: translateY(-3px);
  }

  /* Responsive Card Rounding Utilities */
  @media (min-width: 768px) {
    .rounded-md-4 { border-radius: 1.5rem !important; }
    .rounded-top-md-4 { border-top-left-radius: 1.5rem !important; border-top-right-radius: 1.5rem !important; }
    .rounded-bottom-md-4 { border-bottom-left-radius: 1.5rem !important; border-bottom-right-radius: 1.5rem !important; }
  }

  /* Scrolling Container Utility */
  .scrollable-card-body {
    max-height: 500px;
    overflow-y: auto;
    scrollbar-width: thin;
  }
  .scrollable-card-body::-webkit-scrollbar {
    width: 6px;
  }
  .scrollable-card-body::-webkit-scrollbar-thumb {
    background: #e0e0e0;
    border-radius: 10px;
  }


  /* Premium Skeleton Shimmer Effect */
  .skeleton-shimmer {
    background: linear-gradient(90deg, transparent 25%, rgba(242, 105, 34, 0.05) 50%, transparent 75%);
    background-size: 200% 100%;
    animation: skeleton-load 1.5s infinite;
    border-radius: 4px;
    display: inline-block;
    width: 100%;
    min-height: 1em;
  }

  .dark-mode .skeleton-shimmer {
    background: linear-gradient(90deg, transparent 25%, rgba(255, 255, 255, 0.05) 50%, transparent 75%);
  }

  @keyframes skeleton-load {
    from { background-position: 200% 0; }
    to { background-position: -200% 0; }
  }

  /* Fixed size for skeletons in specific blocks */
  .sk-text { height: 12px; margin-bottom: 8px; }
  .sk-title { height: 24px; width: 60%; margin-bottom: 12px; }
  .sk-circle { width: 45px; height: 45px; border-radius: 50%; }
</style>

  <!-- Auto Year Script -->
  <script>
    document.getElementById("currentYear").textContent = new Date().getFullYear();

    /* Theme Switcher Helper */
    function toggleTheme() {
        const isDark = document.documentElement.classList.toggle('dark-mode');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        
        // Update Icons if needed
        const sunIcon = document.getElementById('theme-sun');
        const moonIcon = document.getElementById('theme-moon');
        if (sunIcon && moonIcon) {
            if (isDark) {
                sunIcon.classList.remove('d-none');
                moonIcon.classList.add('d-none');
            } else {
                sunIcon.classList.add('d-none');
                moonIcon.classList.remove('d-none');
            }
        }
    }

    /* Premium Toast Helper */
    function showToast(title, icon = 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        Toast.fire({
            icon: icon,
            title: title
        });
    }
  </script>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>

    <!-- Charts -->
    <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexchart/chart-data.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/chart-data.js') }}"></script>

    <!-- Date & Time -->
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>

    <!-- Editors -->
    <script src="{{ asset('assets/plugins/summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <!-- Color Picker -->
    <script src="{{ asset('assets/plugins/@simonwep/pickr/pickr.es5.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/todo.js') }}"></script>
    <script src="{{ asset('assets/js/theme-colorpicker.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/bokanturai.js') }}"></script>
    <script src="{{ asset('assets/js/data.js') }}"></script>
    <script src="{{ asset('assets/js/airtime.js') }}"></script>
    <script src="{{ asset('assets/js/pin.js') }}"></script>
    <script src="{{ asset('assets/js/bvnservices.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/sweetalert.js') }}"></script>

    @if(!Auth::user()->bvn || !Auth::user()->phone_no || !Auth::user()->lga || !Auth::user()->pin)
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var myModal = new bootstrap.Modal(document.getElementById('forceProfileModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                myModal.show();
            });
        </script>
    @endif

    <script>
        // Auto-dismiss alerts after 4 seconds
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(() => {
                document.querySelectorAll('.alert.alert-dismissible').forEach(alert => new bootstrap.Alert(alert).close());
            }, 4000);
        });
    </script>

    <x-ai-assistant />
    @stack('scripts')
</body>
</html>
