<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3 text-primary">
                                    <i class="ti ti-report-money fs-1"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold text-dark mb-1">Service Prices & Rates</h3>
                                    <p class="text-muted mb-0">Transparent pricing for <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fw-bold">{{ strtoupper($user->role ?? 'user') }}</span> account</p>
                                </div>
                            </div>
                            <div class="d-none d-md-block">
                                <div class="text-end">
                                    <p class="text-muted small mb-1">Last Updated</p>
                                    <h6 class="fw-bold mb-0 text-dark">{{ date('F d, Y') }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-pill bg-white overflow-hidden p-1">
                    <ul class="nav nav-pills nav-justified" id="priceTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active rounded-pill py-3" id="basic-tab" data-bs-toggle="pill" data-bs-target="#basic" type="button" role="tab">
                                <i class="ti ti-wifi-2 me-2"></i> Airtime & Data
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill py-3" id="sme-tab" data-bs-toggle="pill" data-bs-target="#sme" type="button" role="tab">
                                <i class="ti ti-database me-2"></i> SME Data
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill py-3" id="verify-tab" data-bs-toggle="pill" data-bs-target="#verify" type="button" role="tab">
                                <i class="ti ti-id-badge me-2"></i> Verification & Validation
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill py-3" id="modify-tab" data-bs-toggle="pill" data-bs-target="#modify" type="button" role="tab">
                                <i class="ti ti-edit-circle me-2"></i> Modifications
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="priceTabsContent">
            
            <!-- Basic Services (Airtime/Data) -->
            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                <!-- Airtime Cards Row -->
                <div class="row mb-4 g-3">
                    @foreach($airtimePrices as $price)
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100 highlight-on-hover">
                            <div class="card-body p-3 p-md-4 text-center">
                                <div class="avatar avatar-md bg-light p-1 rounded-circle mb-3 mx-auto border overflow-hidden">
                                     <img src="{{ asset('assets/img/networks/'.strtolower($price['network']).'.png') }}" 
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ $price['network'] }}&background=6366f1&color=fff'" 
                                         class="img-fluid rounded-circle">
                                </div>
                                <h6 class="fw-bold text-dark mb-1">{{ $price['network'] }}</h6>
                                <p class="text-muted small mb-3">Airtime Reward</p>
                                <div class="bg-success bg-opacity-10 text-success rounded-pill py-2 px-3 d-inline-block fw-bold fs-12">
                                    {{ $price['commission'] }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Data Bundles Table -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <h5 class="fw-bold text-dark mb-0">General Data Bundles</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="small fw-bold text-secondary text-uppercase border-bottom-0">
                                    <th class="ps-4">NETWORK & SERVICE</th>
                                    <th>CASHBACK</th>
                                    <th>PLAN CODE</th>
                                    <th class="text-end pe-4">UNIT PRICE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataGroups as $group)
                                    @foreach($group['plans'] as $plan)
                                    <tr class="border-bottom-light">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary bg-opacity-10 p-1 me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <span class="text-primary small fw-bold">{{ substr($group['network'], 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-0 text-dark">{{ $plan->name }}</h6>
                                                    <small class="text-muted opacity-75">{{ $group['network'] }} {{ $plan->service_name }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-lg bg-soft-success text-success rounded-pill px-3">{{ $group['commission'] }}%</span>
                                        </td>
                                        <td><code>{{ $plan->variation_code }}</code></td>
                                        <td class="text-end pe-4">
                                            <span class="fw-bold text-dark fs-15">₦{{ number_format($plan->variation_amount, 2) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SME Data Section -->
            <div class="tab-pane fade" id="sme" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="small fw-bold text-secondary text-uppercase">
                                    <th class="ps-4 py-3">PLAN SIZE</th>
                                    <th>NETWORK</th>
                                    <th>PLAN TYPE</th>
                                    <th>VALIDITY</th>
                                    <th class="text-end pe-4">TOTAL PRICE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($smeGroups as $group)
                                    @foreach($group['plans'] as $plan)
                                    <tr class="border-bottom-light">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xs bg-indigo-600 rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm">
                                                    <i class="ti ti-database fs-12 text-white"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-0">{{ $plan->size }}</h6>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $group['network'] }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted small text-uppercase">{{ $plan->plan_type }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-warning text-warning rounded-pill px-3">{{ $plan->validity }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex flex-column align-items-end">
                                                <span class="fw-bold text-primary fs-15">₦{{ number_format($plan->total_price, 2) }}</span>
                                                @if($plan->total_price < $plan->amount)
                                                <small class="text-muted text-decoration-line-through opacity-75">₦{{ number_format($plan->amount, 2) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Verification & Validation Section -->
            <div class="tab-pane fade" id="verify" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="small fw-bold text-secondary text-uppercase">
                                    <th class="ps-4 py-3">SERVICE NAME</th>
                                    <th>SERVICE TYPE</th>
                                    <th>STATUS</th>
                                    <th class="text-end pe-4">SERVICE FEE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($verificationPrices as $vprice)
                                <tr class="border-bottom-light">
                                    <td class="ps-4 py-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-soft-primary text-primary rounded-circle me-3 d-flex align-items-center justify-content-center border border-primary border-opacity-10">
                                                <i class="ti {{ $vprice['type'] == 'Verification' ? 'ti-circle-check-filled' : 'ti-shield-check' }} fs-12"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1">{{ $vprice['name'] }}</h6>
                                                <small class="text-muted">Code: {{ $vprice['code'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-medium fs-13">{{ $vprice['type'] }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded-circle me-2 animate-pulse" style="width: 8px; height: 8px;"></div>
                                            <span class="text-success small fw-bold">Live</span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <span class="fw-bold text-dark fs-16">₦{{ number_format($vprice['price'], 2) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modifications Section -->
            <div class="tab-pane fade" id="modify" role="tabpanel">
                <div class="row g-4">
                    @foreach($modificationGroups as $group)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white py-3 border-bottom border-light d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                                    <span class="bg-primary bg-opacity-10 p-2 rounded-3 me-2 text-primary">
                                        <i class="ti ti-briefcase fs-12"></i>
                                    </span>
                                    {{ $group['category'] }}
                                </h6>
                                <span class="badge bg-light text-secondary rounded-pill fw-normal px-3">{{ count($group['plans']) }} Services</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr class="small fw-bold text-secondary text-uppercase">
                                            <th class="ps-4 py-2">SPECIFIC SERVICE</th>
                                            <th>SERVICE CODE</th>
                                            <th class="text-end pe-4">RATE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group['plans'] as $plan)
                                        <tr class="border-bottom-light">
                                            <td class="ps-4 py-3">
                                                <span class="fw-semibold text-dark">{{ $plan->name }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark font-monospace fw-normal">{{ $plan->code }}</span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="fw-bold text-indigo-600 fs-15">₦{{ number_format($plan->price, 2) }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        :root {
            --primary: #6366f1;
            --indigo-600: #4f46e5;
        }
        .bg-soft-primary { background-color: rgba(99, 102, 241, 0.1); }
        .bg-soft-success { background-color: rgba(34, 197, 94, 0.1); }
        .bg-soft-warning { background-color: rgba(245, 158, 11, 0.1); }
        .bg-indigo-600 { background-color: var(--indigo-600); }
        .text-indigo-600 { color: var(--indigo-600); }
        
        .card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .highlight-on-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 1rem 3rem rgba(0,0,0,0.08) !important;
        }
        
        .nav-pills .nav-link { 
            color: #64748b; 
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }
        .nav-pills .nav-link i { font-size: 1.1rem; }
        .nav-pills .nav-link.active {
            background-color: #f8fafc;
            color: var(--primary);
            border-color: #e2e8f0;
            box-shadow: none;
        }
        
        .table > thead { background-color: #f9fafb !important; }
        .table > :not(caption) > * > * { 
            border-bottom: 0.8px solid #f1f5f9;
        }
        .border-bottom-light { border-bottom: 0.8px solid #f8fafc; }
        
        .fs-12 { font-size: 12px; }
        .fs-13 { font-size: 13px; }
        .fs-15 { font-size: 15px; }
        .fs-16 { font-size: 16px; }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        
        code {
            padding: 0.2rem 0.4rem;
            font-size: 0.85rem;
            color: var(--primary);
            background-color: rgba(99, 102, 241, 0.05);
            border-radius: 0.25rem;
        }
    </style>
    @endpush
</x-app-layout>
