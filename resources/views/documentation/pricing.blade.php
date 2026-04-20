@extends('documentation.layout')

@section('content')
    <div class="mb-5">
        <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fw-bold mb-3">API REFERENCE</span>
        <h1 class="display-5 fw-bold mb-3">Service Pricing</h1>
        <p class="lead">Fetch the latest pricing for all available services, including data plans, electricity DISCOs, and identity verification services.</p>
    </div>

    <!-- Endpoint Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-body p-0">
            <div class="bg-soft-primary p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">GET</span>
                    <code class="text-primary fw-bold fs-5 font-monospace">/api/v1/prices</code>
                </div>
                <button class="btn btn-white btn-sm rounded-pill px-3 shadow-sm" onclick="copyToClipboard('{{ url('/') }}/api/v1/prices')">
                    <i class="ti ti-copy me-1"></i> Copy URL
                </button>
            </div>
            <div class="p-4 rounded-bottom-4">
                <h6 class="fw-bold mb-3">Authorization</h6>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead>
                            <tr class="small opacity-75">
                                <th class="ps-3">HEADER</th>
                                <th>VALUE</th>
                                <th class="text-end pe-3">REQUIRED</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-3"><code class="text-primary">Authorization</code></td>
                                <td><code class="">Bearer YOUR_API_TOKEN</code></td>
                                <td class="text-end pe-3"><span class="badge bg-soft-danger text-danger px-2 py-1 rounded">Yes</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info border-0 rounded-4 d-flex align-items-center p-4 mb-5 shadow-sm">
        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-4" style="width: 50px; height: 50px; flex-shrink: 0;">
            <i class="ti ti-info-circle fs-3"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1">Developer Tip</h6>
            <p class="small mb-0 opacity-75">This is a public endpoint but requires authentication. We highly recommend caching the response for at least <strong>15 minutes</strong> to reduce latency in your application.</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Request Body -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-dark">
                <div class="card-header bg-white bg-opacity-10 border-bottom border-white border-opacity-10 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-white mb-0">Example Request</h6>
                    <span class="badge bg-primary">cURL</span>
                </div>
                <div class="card-body p-0">
<pre class="m-0 p-4 font-monospace small"><code class="text-white">curl -X GET "{{ url('/') }}/api/v1/prices" \
  -H "<span class="text-info">Authorization:</span> <span class="text-warning">Bearer YOUR_API_TOKEN</span>" \
  -H "<span class="text-info">Accept:</span> <span class="text-warning">application/json</span>"</code></pre>
                </div>
            </div>
        </div>

        <!-- Success Response -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-dark">
                <div class="card-header bg-white bg-opacity-10 border-bottom border-white border-opacity-10 py-3">
                    <h6 class="fw-bold text-success mb-0 d-flex align-items-center">
                        <i class="ti ti-circle-check me-2"></i> Success Response (200 OK)
                    </h6>
                </div>
                <div class="card-body p-0">
<pre class="m-0 p-4 font-monospace small"><code class="text-white">{
    "status": true,
    "message": "Prices fetched successfully",
    "data": {
        "services": [
            {
                "id": 1,
                "name": "NIN Verification",
                "price": 100.00
            }
        ],
        "data_plans": [
            {
                "id": 102,
                "network": "MTN",
                "plan_name": "1GB SME 30 Days",
                "price": 250.00
            }
        ]
    }
}</code></pre>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const toast = document.createElement('div');
                toast.className = 'position-fixed top-0 end-0 m-4 alert alert-success shadow-lg border-0';
                toast.style.zIndex = '9999';
                toast.innerHTML = '<i class="ti ti-check me-2"></i> Copied to clipboard!';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            });
        }
    </script>
@endsection
