<x-app-layout>
     <title>Arewa Smart - {{ $title ?? 'settings' }}</title>

    <!-- Custom CSS for Profile Page -->
    <style>
        .profile-header-gradient {
            background: linear-gradient(135deg, #c24343ff 0%, #c5e02bff 100%);
            position: relative;
            overflow: hidden;
        }
        .profile-header-gradient::before {
            content: '';
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .profile-avatar-container {
            position: relative;
            display: inline-block;
            margin-top: -75px;
        }
        .profile-avatar-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            background: #fff;
            overflow: hidden;
        }
        .profile-avatar-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .profile-avatar-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .profile-avatar-wrapper:hover .profile-avatar-overlay {
            opacity: 1;
        }
        .profile-avatar-wrapper:hover img {
            transform: scale(1.1);
        }
        .info-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
            border-color: rgba(0,123,255,0.2);
        }
        .icon-box {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 15px;
        }
        .animate-up {
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }
        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
    </style>

    <div class="container-fluid py-4">
        
        <!-- Alerts -->
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-check-circle fs-4 me-2"></i>
                    <div>{{ session('status') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-circle fs-4 me-2"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center mb-2">
                    <i class="ti ti-alert-triangle fs-4 me-2"></i>
                    <strong>Please check the form below for errors:</strong>
                </div>
                <ul class="mb-0 small ps-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- Left Column: Profile Card -->
            <div class="col-lg-4 animate-up">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <!-- Stylish Header -->
                    <div class="profile-header-gradient" style="height: 150px;">
                         <div class="d-flex justify-content-end p-3">
                             <span class="badge bg-white/20 backdrop-blur text-white border border-white/30 rounded-full px-3 py-1">
                                <i class="ti ti-shield-check me-1"></i> {{ ucfirst($user->role) }}
                             </span>
                         </div>
                    </div>

                    <div class="card-body text-center pt-0 px-4 pb-5">
                        <!-- Profile Photo -->
                        <div class="profile-avatar-container mb-4">
                            <div class="profile-avatar-wrapper mx-auto" data-bs-toggle="modal" data-bs-target="#photoModal">
                                <img src="{{ $user->photo ? asset($user->photo) : asset('assets/img/profiles/avatar-01.jpg') }}" alt="Profile Photo">
                                <div class="profile-avatar-overlay">
                                    <i class="ti ti-camera text-white fs-2"></i>
                                </div>
                            </div>
                        </div>

                        <h4 class="fw-bold text-dark mb-1">{{ $user->first_name }} {{ $user->last_name }}</h4>
                        <p class="text-muted mb-3">{{ $user->email }}</p>
                        
                         <div class="d-inline-flex align-items-center bg-light rounded-pill px-4 py-2 border mb-4">
                             <div class="d-flex flex-column align-items-start me-4 border-end pe-4">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Status</small>
                                <span class="text-success fw-bold"><i class="ti ti-circle-filled fs-10 me-1"></i>Active</span>
                             </div>
                             <div class="d-flex flex-column align-items-start">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Limit</small>
                                <span class="text-dark fw-bold">{{ $user->limit ?? 'Level 1' }}</span>
                             </div>
                         </div>

                        <!-- Action Buttons -->
                        <div class="d-flex flex-column gap-2 mt-2">
                             <button class="btn btn-outline-primary rounded-pill py-2 fw-semibold shadow-sm d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#passwordModal">
                                <i class="ti ti-lock me-2"></i> Change Password
                            </button>
                            <button class="btn btn-outline-danger rounded-pill py-2 fw-semibold shadow-sm d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#pinModal">
                                <i class="ti ti-key me-2"></i> Reset Transaction PIN
                            </button>
                            <button class="btn btn-outline-info rounded-pill py-2 fw-semibold shadow-sm d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#apiTokenModal">
                                <i class="ti ti-code me-2"></i> Show API Token
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: User Details -->
            <div class="col-lg-8 animate-up delay-1">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom border-light py-4 px-4">
                        <div class="d-flex align-items-center justify-content-between">
                             <h5 class="fw-bold mb-0 text-dark">
                                <i class="ti ti-user-circle me-2 text-primary"></i>Personal Information
                            </h5>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <tbody>
                                    <tr class="border-bottom">
                                        <th class="text-muted small text-uppercase py-3" style="width: 35%">First Name</th>
                                        <td class="fw-semibold py-3">{{ $user->first_name }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="text-muted small text-uppercase py-3">Last Name</th>
                                        <td class="fw-semibold py-3">{{ $user->last_name }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="text-muted small text-uppercase py-3">Middle Name</th>
                                        <td class="fw-semibold py-3">{{ $user->middle_name ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="text-muted small text-uppercase py-3">Email</th>
                                        <td class="fw-semibold py-3">{{ $user->email }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="text-muted small text-uppercase py-3">Phone Number</th>
                                        <td class="fw-semibold py-3">{{ $user->phone_no }}</td>
                                    </tr>
                                   
                                    <tr class="border-bottom">
                                        <th class="text-muted small text-uppercase py-3">Business Name</th>
                                        <td class="fw-semibold py-3">{{ $user->business_name ?? 'Not Provided' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="text-muted small text-uppercase py-3">State</th>
                                        <td class="fw-semibold py-3">{{ $user->state ?? 'Not Provided' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="text-muted small text-uppercase py-3">LGA</th>
                                        <td class="fw-semibold py-3">{{ $user->lga ?? 'Not Provided' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small text-uppercase py-3">Address</th>
                                        <td class="fw-semibold py-3">{{ $user->address ?? 'Not Provided' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALS -->

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg border-0 overflow-hidden">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="ti ti-camera me-2"></i>Update Profile Photo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4 text-center">
                        <div class="d-inline-flex bg-primary-subtle rounded-circle p-4 mb-4">
                             <i class="ti ti-cloud-upload text-primary display-4"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Upload New Picture</h5>
                        <p class="text-muted small mb-4">Select a JPG, PNG or WEBP file. Max size: 2MB.</p>
                        
                        <div class="input-group mb-3">
                            <input type="file" name="photo" class="form-control form-control-lg" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light justify-content-center py-3">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">Upload Photo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg border-0 overflow-hidden">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="ti ti-lock me-2"></i>Change Password</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="form-floating mb-3">
                            <input type="password" name="current_password" class="form-control" id="currentPass" placeholder="Current Password" required>
                            <label for="currentPass">Current Password</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" name="password" class="form-control" id="newPass" placeholder="New Password" required>
                            <label for="newPass">New Password</label>
                        </div>
                        <div class="form-floating">
                            <input type="password" name="password_confirmation" class="form-control" id="confirmPass" placeholder="Confirm Password" required>
                            <label for="confirmPass">Confirm Password</label>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light justify-content-end py-3">
                        <button type="button" class="btn btn-outline-primary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PIN Modal -->
    <div class="modal fade" id="pinModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg border-0 overflow-hidden">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="ti ti-shield-lock me-2"></i>Reset Transaction PIN</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('profile.pin') }}">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-warning border-0 bg-warning-subtle text-warning-emphasis d-flex align-items-center rounded-3 mb-4" role="alert">
                            <i class="ti ti-info-circle me-2 fs-4"></i>
                            <div class="small fw-semibold">This PIN authorizes your financial transactions.</div>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="password" name="current_password" class="form-control" id="pinLoginPass" placeholder="Login Password" required>
                            <label for="pinLoginPass">Login Password</label>
                        </div>

                        <div class="row g-2">
                             <div class="col-6">
                                <div class="form-floating">
                                    <input type="password" name="pin" maxlength="5" pattern="\d{5}" class="form-control" id="newPin" placeholder="New PIN" required>
                                    <label for="newPin">New 5-Digit PIN</label>
                                </div>
                             </div>
                             <div class="col-6">
                                <div class="form-floating">
                                    <input type="password" name="pin_confirmation" maxlength="5" pattern="\d{5}" class="form-control" id="confirmPin" placeholder="Confirm PIN" required>
                                    <label for="confirmPin">Confirm PIN</label>
                                </div>
                             </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light justify-content-end py-3">
                        <button type="button" class="btn btn-outline-primary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">Update PIN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- API Token Modal -->
    <div class="modal fade" id="apiTokenModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg border-0 overflow-hidden">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="ti ti-code me-2"></i>Your API Token</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis d-flex align-items-center rounded-3 mb-4" role="alert">
                        <i class="ti ti-info-circle me-2 fs-4"></i>
                        <div class="small fw-semibold">Keep this token secret. It allows access to your wallet API.</div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">API Token</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="apiTokenInput" value="{{ $user->api_token ?? 'Not Generated' }}" readonly>
                            <button class="btn btn-outline-primary" type="button" onclick="copyApiToken()">
                                <i class="ti ti-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    
                    @if(!$user->api_token)
                    <div class="text-center mt-3">
                        <small class="text-danger">You have not generated an API token yet. Please contact support or run the generation command.</small>
                    </div>
                    @endif

                    <div class="d-flex justify-content-center mt-4">
                        <form action="{{ route('profile.api-token.regenerate') }}" method="POST" id="regenerateTokenForm">
                            @csrf
                            <button type="button" class="btn btn-outline-danger rounded-pill px-4" onclick="confirmRegenerate()">
                                <i class="ti ti-refresh me-2"></i> Regenerate Token
                            </button>
                        </form>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light justify-content-center py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-5" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyApiToken() {
            var copyText = document.getElementById("apiTokenInput");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            navigator.clipboard.writeText(copyText.value).then(function() {
                // Optional: Show a toast or change button text temporarily
                var btn = event.currentTarget;
                var originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="ti ti-check"></i> Copied!';
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-success');
                setTimeout(function() {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-primary');
                }, 2000);
            }, function(err) {
                console.error('Async: Could not copy text: ', err);
            });
        }

        function confirmRegenerate() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Regenerating your API token will invalidate the old one immediately. Any applications using the old token will stop working.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, regenerate it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('regenerateTokenForm').submit();
                }
            })
        }
    </script>


</x-app-layout>
