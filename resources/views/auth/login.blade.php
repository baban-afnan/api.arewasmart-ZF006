<x-guest-layout>
    <title>Arewa Smart - {{ $title ?? 'Login' }}</title>
    
    <form method="POST" action="{{ route('login') }}" class="vh-100 d-flex flex-column justify-content-between">
        @csrf

        <div class="d-flex flex-column justify-content-center align-items-center flex-grow-1 p-4">
            
            {{-- Logo + Heading --}}
            <div class="text-center mb-4">
                <div class="text-center mb-4">
             <img src="{{ asset('assets/img/logo/logo-smart.png') }}" alt="Arewa Smart Logo" class="img-fluid d-block mx-auto"  style="max-width: 150px; height: auto;" >
                 </div>
                <h2 class="mb-1">Sign In</h2>
                <p class="text-muted mb-4">Welcome back! Please log in to your account</p>
                
            </div>


            <!-- Session Status -->
           <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

            {{-- Form Fields --}}
            <div class="w-100" style="max-width: 400px;">
                
                {{-- Email Field --}}
                <div class="mb-3">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-group">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            autocomplete="username"
                            class="form-control border-end-0 @error('email') is-invalid @enderror">
                        <span class="input-group-text border-start-0">
                            <i class="ti ti-mail"></i>
                        </span>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Password Field --}}
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <div class="pass-group position-relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            class="form-control @error('password') is-invalid @enderror">
                        <span class="ti toggle-password ti-eye-off position-absolute end-0 top-50 translate-middle-y me-3 cursor-pointer"></span>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Remember Me + Forgot Password --}}
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                        <label class="form-check-label" for="remember_me">Remember me</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-primary small">Forgot Password?</a>
                    @endif
                </div>

                {{-- Submit Button --}}
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary w-100">Sign In</button>
                </div>

                {{-- Register Link --}}
                <div class="text-center">
                    <p class="mb-0">
                        Don’t have an account? 
                        <a href="{{ route('register') }}" class="text-primary fw-semibold">Sign Up</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center py-3">
            <p class="text-muted mb-0">&copy; {{ date('Y') }} Arewa Smart</p>
        </div>
    </form>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password Toggle
            const togglePassword = document.querySelector('.toggle-password');
            const password = document.getElementById('password');

            if (togglePassword && password) {
                togglePassword.addEventListener('click', function () {
                    // Toggle the type attribute
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    
                    // Toggle the eye icon
                    this.classList.toggle('ti-eye-off');
                    this.classList.toggle('ti-eye');
                });
            }

            // Form Processing State
            const loginForm = document.querySelector('form');
            const submitBtn = loginForm.querySelector('button[type="submit"]');

            if (loginForm && submitBtn) {
                loginForm.addEventListener('submit', function() {
                    // Change button content to processing state
                    // Preserving width to avoid layout jump could be nice, but simple text change is requested
                    const originalWidth = submitBtn.offsetWidth;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
                    // submitBtn.style.width = originalWidth + 'px'; // Optional: keep width fixed
                    submitBtn.disabled = true;
                    submitBtn.classList.add('disabled');
                });
            }
        });
    </script>
</x-guest-layout>
