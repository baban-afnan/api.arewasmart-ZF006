<x-guest-layout>
    <title>Arewa Smart - {{ $title ?? 'Confirm Password' }}</title>
    <form method="POST" action="{{ route('password.confirm') }}" class="vh-100 d-flex flex-column justify-content-between">
        @csrf

        <div class="d-flex flex-column justify-content-center align-items-center flex-grow-1 p-4">

            {{-- Logo + Heading --}}
            <img src="{{ asset('assets/img/logo/logo-smart.png') }}" alt="Arewa Smart Logo" class="img-fluid d-block mx-auto"  style="max-width: 150px; height: auto;" >
                <h2 class="mb-1">Confirm Password</h2>
                <p class="text-muted mb-4">
                    This is a secure area of the application. <br>
                    Please confirm your password before continuing.
                </p>
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

                {{-- Confirm Button --}}
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary w-100">Confirm</button>
                </div>

                {{-- Back to Login --}}
                <div class="text-center">
                    <p class="mb-0">
                        Need to log in again? 
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
