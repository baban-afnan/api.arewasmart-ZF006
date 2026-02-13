<x-app-layout>
    <div class="container py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3">
                                    <i class="ti ti-report-money fs-1 text-primary"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-1">Service Prices & Rates</h4>
                                    <p class="text-muted mb-0">Transparent pricing for <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">{{ strtoupper($user->role ?? 'user') }}</span> account</p>
                                </div>
                            </div>
                            <div class="text-end d-none d-md-block">
                                <small class="text-muted d-block">Last Updated</small>
                                <span class="fw-semibold">{{ date('F d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs - Centered -->
        <div class="row mb-4">
            <div class="col-lg-10 mx-auto">
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-body p-2">
                        <ul class="nav nav-pills nav-fill gap-2" id="priceTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active py-3" id="airtime-tab" data-bs-toggle="pill" data-bs-target="#airtime" type="button" role="tab">
                                    <i class="ti ti-phone-call me-2"></i>
                                    <span class="d-none d-sm-inline">Airtime</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-3" id="data-tab" data-bs-toggle="pill" data-bs-target="#data" type="button" role="tab">
                                    <i class="ti ti-wifi me-2"></i>
                                    <span class="d-none d-sm-inline">Data Bundles</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-3" id="sme-tab" data-bs-toggle="pill" data-bs-target="#sme" type="button" role="tab">
                                    <i class="ti ti-database me-2"></i>
                                    <span class="d-none d-sm-inline">SME Data</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-3" id="verify-tab" data-bs-toggle="pill" data-bs-target="#verify" type="button" role="tab">
                                    <i class="ti ti-id me-2"></i>
                                    <span class="d-none d-sm-inline">Verification</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-3" id="modify-tab" data-bs-toggle="pill" data-bs-target="#modify" type="button" role="tab">
                                    <i class="ti ti-edit me-2"></i>
                                    <span class="d-none d-sm-inline">Modifications</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content - Centered -->
        <div class="row">
            <div class="col-12">
                <div class="tab-content" id="priceTabsContent">
                    
                    <!-- Airtime Services -->
                    <div class="tab-pane fade show active" id="airtime" role="tabpanel" tabindex="0">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3">S/N</th>
                                            <th class="py-3">Network</th>
                                            <th class="py-3">Status</th>
                                            <th class="py-3">Category</th>
                                            <th class="text-end pe-4 py-3">Cashback</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($airtimePaginator as $price)
                                        <tr>
                                            <td class="ps-4 py-3 fw-bold text-muted">{{ $loop->iteration + ($airtimePaginator->firstItem() - 1) }}</td>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        <span class="fw-bold text-primary">{{ substr($price['network'], 0, 2) }}</span>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-semibold mb-0">{{ $price['network'] }}</h6>
                                                        <small class="text-muted">VTU Airtime</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                @if($price['status'])
                                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                                    <span class="dot bg-success me-1"></span> Live
                                                </span>
                                                @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
                                                    <span class="dot bg-warning me-1"></span> Maintenance
                                                </span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Airtime</span>
                                            </td>
                                            <td class="text-end pe-4 py-3">
                                                <span class="fw-bold text-success">{{ number_format($price['commission'], 1) }}%</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">No airtime services available</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($airtimePaginator->hasPages())
                                    <div class="card-footer bg-white border-top py-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                            <small class="text-muted">
                                                Showing {{ $airtimePaginator->firstItem() }} to {{ $airtimePaginator->lastItem() }} of {{ $airtimePaginator->total() }} entries
                                            </small>
                                            {{ $airtimePaginator->appends(request()->except('airtime_page'))->links('vendor.pagination.custom') }}
                                        </div>
                                    </div>
                            @endif
                        </div>
                    </div>

                    <!-- Data Bundles -->
                    <div class="tab-pane fade" id="data" role="tabpanel" tabindex="0">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3">S/N</th>
                                            <th class="py-3">Network</th>
                                            <th class="py-3">Status</th>
                                            <th class="py-3">Plan</th>
                                            <th class="py-3">Cashback</th>
                                            <th class="text-end pe-4 py-3">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($dataPaginator as $plan)
                                        <tr>
                                            <td class="ps-4 py-3 fw-bold text-muted">{{ $loop->iteration + ($dataPaginator->firstItem() - 1) }}</td>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        <span class="fw-bold text-primary">{{ substr($plan->network_name, 0, 2) }}</span>
                                                    </div>
                                                    <span class="fw-semibold">{{ $plan->network_name }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                @if($plan->network_status)
                                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                                    <span class="dot bg-success me-1"></span> Live
                                                </span>
                                                @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
                                                    <span class="dot bg-warning me-1"></span> Offline
                                                </span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                <div class="fw-semibold">{{ $plan->name }}</div>
                                                <small class="text-muted">Code: {{ $plan->variation_code }}</small>
                                            </td>
                                            <td class="py-3">
                                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">{{ $plan->network_commission }}%</span>
                                            </td>
                                            <td class="text-end pe-4 py-3">
                                                <span class="fw-bold text-primary fs-15">₦{{ number_format($plan->variation_amount, 2) }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">No data bundles available</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($dataPaginator->hasPages())
                                    <div class="card-footer bg-white border-top py-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                            <small class="text-muted">
                                                Showing {{ $dataPaginator->firstItem() }} to {{ $dataPaginator->lastItem() }} of {{ $dataPaginator->total() }} entries
                                            </small>
                                            {{ $dataPaginator->appends(request()->except('data_page'))->links('vendor.pagination.custom') }}
                                        </div>
                                    </div>
                            @endif
                        </div>
                    </div>

                    <!-- SME Data -->
                    <div class="tab-pane fade" id="sme" role="tabpanel" tabindex="0">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3">S/N</th>
                                            <th class="py-3">Plan Size</th>
                                            <th class="py-3">Status</th>
                                            <th class="py-3">Network</th>
                                            <th class="py-3">Type</th>
                                            <th class="py-3">Validity</th>
                                            <th class="text-end pe-4 py-3">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($smePaginator as $plan)
                                        <tr>
                                            <td class="ps-4 py-3 fw-bold text-muted">{{ $loop->iteration + ($smePaginator->firstItem() - 1) }}</td>
                                            <td class="py-3">
                                                <span class="fw-semibold">{{ $plan->size }}</span>
                                            </td>
                                            <td class="py-3">
                                                @if($plan->status == 'enabled')
                                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                                    <span class="dot bg-success me-1"></span> Live
                                                </span>
                                                @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
                                                    <span class="dot bg-warning me-1"></span> Offline
                                                </span>
                                                @endif
                                            </td>
                                            <td class="py-3">{{ $plan->network_name }}</td>
                                            <td class="py-3">
                                                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill text-uppercase">{{ $plan->plan_type }}</span>
                                            </td>
                                            <td class="py-3">
                                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">{{ $plan->validity }}</span>
                                            </td>
                                            <td class="text-end pe-4 py-3">
                                                <span class="fw-bold text-primary fs-12">₦{{ number_format($plan->total_price, 2) }}</span>
                                                @if($plan->total_price < $plan->amount)
                                                <br><small class="text-muted text-decoration-line-through">₦{{ number_format($plan->amount, 2) }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">No SME data plans available</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($smePaginator->hasPages())
                                    <div class="card-footer bg-white border-top py-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                            <small class="text-muted">
                                                Showing {{ $smePaginator->firstItem() }} to {{ $smePaginator->lastItem() }} of {{ $smePaginator->total() }} entries
                                            </small>
                                            {{ $smePaginator->appends(request()->except('sme_page'))->links('vendor.pagination.custom') }}
                                        </div>
                                    </div>
                            @endif
                        </div>
                    </div>

                    <!-- Verification & Validation -->
                    <div class="tab-pane fade" id="verify" role="tabpanel" tabindex="0">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3">S/N</th>
                                            <th class="py-3">Service</th>
                                            <th class="py-3">Type</th>
                                            <th class="py-3">Status</th>
                                            <th class="text-end pe-4 py-3">Fee</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($verifyPaginator as $vprice)
                                        <tr>
                                            <td class="ps-4 py-3 fw-bold text-muted">{{ $loop->iteration + ($verifyPaginator->firstItem() - 1) }}</td>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        <i class="ti ti-shield-check text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-semibold">{{ $vprice['name'] }}</span>
                                                        <br><small class="text-muted">Code: {{ $vprice['code'] }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">{{ $vprice['type'] }}</span>
                                            </td>
                                            <td class="py-3">
                                                @if($vprice['status'])
                                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                                    <span class="dot bg-success me-1"></span> Live
                                                </span>
                                                @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
                                                    <span class="dot bg-warning me-1"></span> Offline
                                                </span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4 py-3">
                                                <span class="fw-bold text-primary fs-12">₦{{ number_format($vprice['price'], 2) }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">No verification services available</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($verifyPaginator->hasPages())
                                    <div class="card-footer bg-white border-top py-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                            <small class="text-muted">
                                                Showing {{ $verifyPaginator->firstItem() }} to {{ $verifyPaginator->lastItem() }} of {{ $verifyPaginator->total() }} entries
                                            </small>
                                            {{ $verifyPaginator->appends(request()->except('verify_page'))->links('vendor.pagination.custom') }}
                                        </div>
                                    </div>
                            @endif
                        </div>
                    </div>

                    <!-- Modifications -->
                    <div class="tab-pane fade" id="modify" role="tabpanel" tabindex="0">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3">S/N</th>
                                            <th class="py-3">Service</th>
                                            <th class="py-3">Status</th>
                                            <th class="py-3">Code</th>
                                            <th class="text-end pe-4 py-3">Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($modifyPaginator as $plan)
                                        <tr>
                                            <td class="ps-4 py-3 fw-bold text-muted">{{ $loop->iteration + ($modifyPaginator->firstItem() - 1) }}</td>
                                            <td class="py-3">
                                                <div class="fw-semibold">{{ $plan->name }}</div>
                                                <small class="text-muted">{{ $plan->category_name }}</small>
                                            </td>
                                            <td class="py-3">
                                                @if($plan->status)
                                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                                    <span class="dot bg-success me-1"></span> Live
                                                </span>
                                                @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
                                                    <span class="dot bg-warning me-1"></span> Maintenance
                                                </span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill font-monospace">{{ $plan->code }}</span>
                                            </td>
                                            <td class="text-end pe-4 py-3">
                                                <span class="fw-bold text-primary fs-12">₦{{ number_format($plan->price, 2) }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">No modification services available</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($modifyPaginator->hasPages())
                                    <div class="card-footer bg-white border-top py-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                            <small class="text-muted">
                                                Showing {{ $modifyPaginator->firstItem() }} to {{ $modifyPaginator->lastItem() }} of {{ $modifyPaginator->total() }} entries
                                            </small>
                                            {{ $modifyPaginator->appends(request()->except('modify_page'))->links('vendor.pagination.custom') }}
                                        </div>
                                    </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab persistence from URL
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || 'airtime';
            
            const tabElement = document.querySelector(`#${activeTab}-tab`);
            if (tabElement) {
                bootstrap.Tab.getOrCreateInstance(tabElement).show();
            }

            // Update URL when tab changes
            document.querySelectorAll('button[data-bs-toggle="pill"]').forEach(button => {
                button.addEventListener('shown.bs.tab', function(event) {
                    const tabId = event.target.id.replace('-tab', '');
                    const url = new URL(window.location);
                    url.searchParams.set('tab', tabId);
                    window.history.replaceState({}, '', url);
                });
            });
        });
    </script>
</x-app-layout>