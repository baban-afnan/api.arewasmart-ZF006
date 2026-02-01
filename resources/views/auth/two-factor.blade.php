 <!-- 2FA Verification Modal -->
    <div class="modal fade" id="twoFactorModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-3">
                <div class="modal-header bg-primary text-white justify-content-center pt-4 border-bottom-0 pb-0">
                    <div class="text-center w-100">
                        <div class="avatar avatar-lg bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 60px; height: 60px;">
                            <i class="ti ti-shield-lock text-primary fs-2"></i>
                        </div>
                        <h5 class="modal-title fw-bold">Two-Factor Authentication</h5>
                        <p class="text-white-50 small mb-0">Verify your identity to continue</p>
                    </div>
                </div>
                
                <div class="modal-body p-4 pt-3">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                           {{ $errors->first() }}
                           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verify.store') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label text-muted fw-semibold small text-uppercase" for="two_factor_code">One-Time Password (OTP)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="ti ti-key text-muted"></i>
                                </span>
                                <input 
                                    type="text" 
                                    id="two_factor_code" 
                                    name="two_factor_code" 
                                    required 
                                    autofocus 
                                    autocomplete="one-time-code" 
                                    inputmode="numeric" 
                                    class="form-control border-start-0 py-2" 
                                    placeholder="Enter 6-digit code">
                            </div>
                            <div class="form-text mt-2">
                                <i class="ti ti-info-circle me-1"></i> A code has been sent to your email address.
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary py-2 fw-bold" type="submit">
                                Verify & Login
                            </button>
                        </div>
                    </form>

                    <div class="d-flex align-items-center justify-content-between mt-4 border-top pt-3">
                        <button type="button" class="btn btn-link link-secondary text-decoration-none px-0 fs-12" onclick="document.getElementById('logout-form').submit();">
                            <i class="ti ti-logout me-10"></i> Log Out
                        </button>

                        <a href="{{ route('verify.resend') }}" class="btn btn-link link-primary text-decoration-none px-0 fs-12">
                            Resend Code
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none; px-0p fs-12">
        @csrf
    </form>

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var myModal = new bootstrap.Modal(document.getElementById('twoFactorModal'));
            myModal.show();
        });
    </script>
    @endpush
