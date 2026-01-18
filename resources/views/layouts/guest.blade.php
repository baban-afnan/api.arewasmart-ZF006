<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Arewa Smart - {{ $title ?? 'Authentication' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Add your custom assets for the registration page here if they aren't included by @vite --}}
        {{-- For this example, I'll assume your custom CSS is not managed by Vite and must be included separately --}}
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/logo/logo.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/icons/feather/feather.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    </head>

    <body class="bg-white">
        <div id="global-loader" style="display: none;">
            <div class="page-loader"></div>
        </div>
        <div class="main-wrapper">
            <div class="container-fuild">
                <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
                    <div class="row">
                        {{-- The left-side image/text column is static and placed here --}}
                        <div class="col-lg-5">
                            <div class="login-background position-relative d-lg-flex align-items-center justify-content-center d-none flex-wrap vh-100">
                                <div class="bg-overlay-img">
                                 </div>
                                <div class="authentication-card w-100">
                                    <div class="authen-overlay-item border w-100">
                                        <h1 class="text-white fs-20 fw-semibold text-center">Transform Your Business Journey with Arewa Smart API  </h1>
                                        <div class="my-4 mx-auto authen-overlay-img">
                                            <img src="{{ asset('assets/img/landing/api.png') }}" alt="Img">
                                        </div>
                                        <div>
                                            <p class="text-white fs-20 fw-semibold text-center">Efficiently manage your workforce, streamline <br> operations effortlessly.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- The right-side form column contains the slot for the actual form --}}
                        <div class="col-lg-7 col-md-12 col-sm-12">
                            <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap">
                                <div class="col-md-7 mx-auto vh-100">
                                    {{ $slot }} {{-- This is where your register form content will go --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/js/feather.min.js') }}"></script>
        <script src="{{ asset('assets/js/script.js') }}"></script>
    </body>
</html>

{{-- JavaScript Validation --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const email = document.getElementById('email');
            const emailError = document.getElementById('emailError');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');
            const passwordStrengthBar = document.getElementById('passwordStrengthBar');
            const passwordStrengthText = document.getElementById('passwordStrengthText');
            const passwordMatchError = document.getElementById('passwordMatchError');

            // Email Validation
            email.addEventListener('input', () => {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email.value.trim())) {
                    emailError.classList.remove('d-none');
                } else {
                    emailError.classList.add('d-none');
                }
            });

            // Password Strength
            password.addEventListener('input', () => {
                const val = password.value;
                let strength = 0;

                if (val.length >= 8) strength++;
                if (/[A-Z]/.test(val)) strength++;
                if (/[a-z]/.test(val)) strength++;  // Added lowercase check
                if (/[0-9]/.test(val)) strength++;
                if (/[^A-Za-z0-9]/.test(val)) strength++;
                if (val.length >= 12) strength++;   // Extra point for longer passwords

                let width = 0, color = '', label = '';

                switch (strength) {
                    case 0: 
                        width = 0; 
                        color = 'bg-secondary';
                        label = 'Time to create something special! 🎯'; 
                        break;
                    case 1: 
                        width = 20; 
                        color = 'bg-danger'; 
                        label = 'Getting started! Add some magic to make it stronger ✨'; 
                        break;
                    case 2: 
                        width = 40; 
                        color = 'bg-warning'; 
                        label = 'Nice progress! Mix it up a bit more! 🌟'; 
                        break;
                    case 3: 
                        width = 60; 
                        color = 'bg-info'; 
                        label = 'Looking good! Almost fortress-level security! 🏰'; 
                        break;
                    case 4: 
                        width = 80; 
                        color = 'bg-success'; 
                        label = 'Excellent! Your password is getting super strong! 💪'; 
                        break;
                    case 5: 
                        width = 100; 
                        color = 'bg-success'; 
                        label = 'Perfect! Your password is now fortress-level secure! 🔒'; 
                        break;
                }

                passwordStrengthBar.style.width = width + '%';
                passwordStrengthBar.className = 'progress-bar ' + color;
                passwordStrengthText.textContent = label;
            });

            // Password Match
            confirmPassword.addEventListener('input', () => {
                if (confirmPassword.value && confirmPassword.value !== password.value) {
                    passwordMatchError.classList.remove('d-none');
                } else {
                    passwordMatchError.classList.add('d-none');
                }
            });
        });
    </script>
