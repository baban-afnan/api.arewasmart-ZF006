<x-guest-layout>
    <title>Arewa Smart - {{ $title ?? 'forgot password' }}</title>
    <form method="POST" action="{{ route('password.email') }}" class="vh-100 d-flex flex-column justify-content-between">
        @csrf

        <div class="d-flex flex-column justify-content-center align-items-center flex-grow-1 p-4">

            {{-- Logo + Heading --}}
            <div class="text-center mb-4">
               <img src="{{ asset('assets/img/logo/logo-smart.png') }}" alt="Arewa Smart Logo" class="img-fluid d-block mx-auto"  style="max-width: 150px; height: auto;" >
               <h2 class="mb-1">Forgot Password</h2>
                <p class="text-muted mb-4">
                    No problem — enter your email, and we’ll send you a link to reset your password.
                </p>
            </div>

            {{-- Session Status --}}
            <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

            {{-- Email Input --}}
            <div class="w-100" style="max-width: 400px;">
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
                            class="form-control border-end-0 @error('email') is-invalid @enderror">
                        <span class="input-group-text border-start-0">
                            <i class="ti ti-mail"></i>
                        </span>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary w-100">
                        Email Password Reset Link
                    </button>
                </div>

                {{-- Back to Login --}}
                <div class="text-center">
                    <p class="mb-0">
                        Remembered your password? 
                        <a href="{{ route('login') }}" class="text-primary fw-semibold">Sign In</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center py-3">
            <p class="text-muted mb-0">&copy; {{ date('Y') }} Arewa Smart</p>
        </div>
    </form>
</x-guest-layout>
