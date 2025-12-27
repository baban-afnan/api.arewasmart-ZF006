<x-guest-layout>
    <title>Arewa Smart - {{ $title ?? 'Verify Email' }}</title>
    <div class="vh-100 d-flex flex-column justify-content-between">

        {{-- Main Content --}}
        <div class="d-flex flex-column justify-content-center align-items-center flex-grow-1 p-4">
            {{-- Logo + Heading --}}
            <div class="text-center mb-4">
                 <img src="{{ asset('assets/img/logo/logo-smart.png') }}" alt="Arewa Smart Logo" class="img-fluid d-block mx-auto"  style="max-width: 150px; height: auto;" >
                <h2 class="mb-1">Verify Your Email Address</h2>
                <p class="text-muted mb-4">
                    Thanks for signing up! Please verify your email address by clicking the link we just sent to your inbox.  
                    If you didn’t receive the email, you can request another below.
                </p>
            </div>

            {{-- Status Message --}}
            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success text-center w-100" style="max-width: 400px;">
                    <i class="ti ti-mail me-1"></i>
                    A new verification link has been sent to your registered email address.
                </div>
            @endif

            {{-- Actions --}}
            <div class="d-flex flex-column align-items-center mt-4 w-100" style="max-width: 400px;">
                
                {{-- Resend Verification Link --}}
                <form method="POST" action="{{ route('verification.send') }}" class="w-100 mb-3">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-refresh me-1"></i> Resend Verification Email
                    </button>
                </form>

                {{-- Log Out --}}
                <form method="POST" action="{{ route('logout') }}" class="w-100">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary w-100">
                        <i class="ti ti-logout me-1"></i> Log Out
                    </button>
                </form>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center py-3">
            <p class="text-muted mb-0">&copy; {{ date('Y') }} Arewa Smart</p>
        </div>
    </div>
</x-guest-layout>
