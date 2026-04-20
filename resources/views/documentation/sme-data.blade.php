@extends('documentation.layout')

@section('content')
    <div class="mb-5">
        <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fw-bold mb-3">BILL PAYMENT</span>
        <h1 class="display-5 fw-bold  mb-3">SME Data Purchasing</h1>
        <p class="lead text-muted">Purchase highly subsidized SME data bundles designed for small businesses and resellers, primarily on the MTN network.</p>
    </div>

    <!-- Endpoint Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-body p-0">
            <div class="bg-soft-primary p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">POST</span>
                    <code class="text-primary fw-bold fs-5 font-monospace">/api/v1/sme-data</code>
                </div>
                <button class="btn btn-white btn-sm rounded-pill px-3 shadow-sm" onclick="copyToClipboard('{{ url('/') }}/api/v1/sme-data')">
                    <i class="ti ti-copy me-1 fs-15"></i> Copy URL
                </button>
            </div>
            <div class="p-4 bg-white">
                <h6 class="fw-bold  mb-3">Authentication & Headers</h6>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="bg-light rounded-3">
                            <tr class="text-muted small">
                                <th class="ps-3">HEADER</th>
                                <th>VALUE</th>
                                <th class="text-end pe-3">REQUIRED</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-3"><code class="text-primary">Authorization</code></td>
                                <td><code class="text-muted">Bearer YOUR_API_TOKEN</code></td>
                                <td class="text-end pe-3"><span class="badge bg-soft-danger text-danger px-2 py-1 rounded">Yes</span></td>
                            </tr>
                            <tr>
                                <td class="ps-3"><code class="text-primary">Content-Type</code></td>
                                <td><code class="text-muted">application/json</code></td>
                                <td class="text-end pe-3"><span class="badge bg-soft-danger text-danger px-2 py-1 rounded">Yes</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-warning border-0 rounded-4 d-flex align-items-center p-4 mb-5 shadow-sm">
        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-4" style="width: 50px; height: 50px; flex-shrink: 0;">
            <i class="ti ti-alert-triangle fs-15"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1">Availability Note</h6>
            <p class="small mb-0 opacity-75">SME Data is primarily provided for the <strong>MTN</strong> network. Availability for other networks is intermittent. Always verify available plans via the <a href="{{ route('docs.pricing') }}">Pricing List</a>.</p>
        </div>
    </div>

    <h4 class="fw-bold  mb-4 mt-5">Request Parameters</h4>
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small">
                        <th class="ps-4 py-3">PARAMETER</th>
                        <th class="py-3">TYPE</th>
                        <th class="py-3">DESCRIPTION</th>
                        <th class="text-end pe-4 py-3">REQUIRED</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4"><code class="text-primary fw-bold">network</code></td>
                        <td><span class="badge bg-light text-muted">string</span></td>
                        <td>Network provider (e.g., MTN).</td>
                        <td class="text-end pe-4"><span class="badge bg-danger text-white px-2 py-1 rounded">Required</span></td>
                    </tr>
                    <tr>
                        <td class="ps-4"><code class="text-primary fw-bold">phone</code></td>
                        <td><span class="badge bg-light text-muted">string</span></td>
                        <td>The 11-digit recipient phone number.</td>
                        <td class="text-end pe-4"><span class="badge bg-danger text-white px-2 py-1 rounded">Required</span></td>
                    </tr>
                    <tr>
                        <td class="ps-4"><code class="text-primary fw-bold">plan_id</code></td>
                        <td><span class="badge bg-light text-muted">integer</span></td>
                        <td>The unique SME data plan ID.</td>
                        <td class="text-end pe-4"><span class="badge bg-danger text-white px-2 py-1 rounded">Required</span></td>
                    </tr>
                    <tr>
                        <td class="ps-4"><code class="text-primary fw-bold">reference</code></td>
                        <td><span class="badge bg-light text-muted">string</span></td>
                        <td>A unique transaction reference for your system.</td>
                        <td class="text-end pe-4"><span class="badge bg-danger text-white px-2 py-1 rounded">Required</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-lg rounded-4 overflow-hidden bg-dark">
                <div class="card-header bg-white bg-opacity-10 border-bottom border-white border-opacity-10 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-white mb-0">JSON Payload</h6>
                    <span class="badge bg-primary">JSON</span>
                </div>
                <div class="card-body p-0">
<pre class="m-0 p-4 font-monospace"><code class="text-white">{
    "network": "MTN",
    "phone": "08123456789",
    "plan_id": 102,
    "reference": "SMED_999888"
}</code></pre>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-lg rounded-4 overflow-hidden bg-dark">
                <div class="card-header bg-white bg-opacity-10 border-bottom border-white border-opacity-10 py-3">
                    <h6 class="fw-bold text-success mb-0 d-flex align-items-center">
                        <i class="ti ti-circle-check me-2 fs-15"></i> Success Response
                    </h6>
                </div>
                <div class="card-body p-0">
<pre class="m-0 p-4 font-monospace"><code class="text-white">{
    "status": true,
    "message": "SME Data dispensed successfully",
    "data": {
        "transaction_id": "ASI-SME-0001",
        "phone": "08123456789",
        "plan": "MTN SME 1GB 30Days",
        "amount": 250,
        "token": "..."
    }
}</code></pre>
                </div>
            </div>
        </div>
    </div>
@endsection
