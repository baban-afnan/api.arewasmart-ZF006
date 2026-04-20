@extends('documentation.layout')

@section('content')
    <div class="mb-5">
        <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fw-bold mb-3">IDENTITY VERIFICATION</span>
        <h1 class="display-5 fw-bold  mb-3">TIN Verification</h1>
        <p class="lead text-muted">Verify business entities and individuals via their Tax Identification Number or CAC Registration Number.</p>
    </div>

    <!-- Endpoint Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-body p-0">
            <div class="bg-soft-primary p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">POST</span>
                    <code class="text-primary fw-bold fs-5 font-monospace">/api/v1/tin</code>
                </div>
                <button class="btn btn-white btn-sm rounded-pill px-3 shadow-sm" onclick="copyToClipboard('{{ url('/') }}/api/v1/tin')">
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
                        </tbody>
                    </table>
                </div>
            </div>
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
                        <td class="ps-4"><code class="text-primary fw-bold">tin</code></td>
                        <td><span class="badge bg-light text-muted">string</span></td>
                        <td>The Tax Identification Number or CAC Registration Number.</td>
                        <td class="text-end pe-4"><span class="badge bg-danger text-white px-2 py-1 rounded">Required</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-12">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-dark">
                <div class="card-header bg-white bg-opacity-10 border-bottom border-white border-opacity-10 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-white mb-0">Example Request</h6>
                    <span class="badge bg-primary">cURL</span>
                </div>
                <div class="card-body p-0">
<pre class="m-0 p-4 font-monospace small"><code class="text-white">curl -X POST "{{ url('/') }}/api/v1/tin" \
  -H "<span class="text-info">Authorization:</span> <span class="text-warning">Bearer YOUR_API_TOKEN</span>" \
  -H "<span class="text-info">Accept:</span> <span class="text-warning">application/json</span>" \
  -d "<span class="text-info">tin</span>=<span class="text-warning">12345678-0001</span>"</code></pre>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-dark">
                <div class="card-header bg-white bg-opacity-10 border-bottom border-white border-opacity-10 py-3">
                    <h6 class="fw-bold text-success mb-0 d-flex align-items-center">
                        <i class="ti ti-circle-check me-2 fs-15"></i> Success Response (200 OK)
                    </h6>
                </div>
                <div class="card-body p-0">
<pre class="m-0 p-4 font-monospace small"><code class="text-white">{
    "status": true,
    "message": "TIN Verified",
    "data": {
        "taxpayer_name": "Arewa Smart Idea",
        "taxpayer_type": "Corporate",
        "tax_office": "Lagos"
    }
}</code></pre>
                </div>
            </div>
        </div>
    </div>
@endsection
