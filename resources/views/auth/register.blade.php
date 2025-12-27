<x-guest-layout>
    <title>Arewa Smart - {{ $title ?? 'Register' }}</title>
    <form method="POST" action="{{ route('register') }}" class="vh-100 d-flex flex-column justify-content-between">
        @csrf

        {{-- Main Section --}}
        <div class="d-flex flex-column justify-content-center align-items-center flex-grow-1 p-4">
            
            {{-- Logo + Heading --}}
            <div class="text-center mb-4">
               <img src="{{ asset('assets/img/logo/logo-smart.png') }}" alt="Arewa Smart Logo" class="img-fluid d-block mx-auto"  style="max-width: 150px; height: auto;" >
                <h2 class="mb-1">Create Account</h2>
                <p class="text-muted mb-4">Please enter your details to sign up</p>
            </div>
           

               <!-- Session Status -->
             <x-auth-session-status class="mb-4 text-center" :status="session('status')" />
    
            {{-- Email Field --}}
            <div class="mb-3 w-100" style="max-width: 400px;">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-group">
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="username"
                        class="form-control border-end-0 @error('email') is-invalid @enderror">
                    <span class="input-group-text border-start-0">
                        <i class="ti ti-mail"></i>
                    </span>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <small id="emailError" class="text-danger d-none">Please enter a valid email address.</small>
            </div>

            {{-- Password Field --}}
            <div class="mb-3 w-100" style="max-width: 400px;">
                <label class="form-label" for="password">Password</label>
                <div class="pass-group position-relative">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        autocomplete="new-password"
                        class="form-control @error('password') is-invalid @enderror">
                    <span class="ti toggle-password ti-eye-off position-absolute end-0 top-50 translate-middle-y me-3 cursor-pointer"></span>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password Strength Bar --}}
                <div class="progress mt-2" style="height: 6px;">
                    <div id="passwordStrengthBar" class="progress-bar" role="progressbar"></div>
                </div>
                <small id="passwordStrengthText" class="text-muted"></small>
            </div>

            {{-- Confirm Password Field --}}
            <div class="mb-3 w-100" style="max-width: 400px;">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <div class="pass-group position-relative">
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password"
                        class="form-control @error('password_confirmation') is-invalid @enderror">
                    <span class="ti toggle-passwords ti-eye-off position-absolute end-0 top-50 translate-middle-y me-3 cursor-pointer"></span>
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <small id="passwordMatchError" class="text-danger d-none">Passwords do not match.</small>
            </div>

            {{-- Terms & Conditions --}}
            <div class="mb-3 w-100" style="max-width: 400px;">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    id="terms" 
                    name="terms" 
                    required>
                <label class="form-check-label" for="terms">
                    Agree to <a href="#" class="text-primary">Terms & Privacy</a>
                </label>
            </div>

            {{-- Submit Button --}}
            <div class="mb-3 w-100" style="max-width: 400px;">
                <button type="submit" class="btn btn-primary w-100">Sign Up</button>
            </div>

            {{-- Already have an account --}}
            <div class="text-center">
                <p class="mb-0">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-primary fw-semibold">Sign In</a>
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center py-3">
            <p class="text-muted mb-0">&copy; {{ date('Y') }} Arewa Smart</p>
        </div>
    </form>

   </x-guest-layout>
