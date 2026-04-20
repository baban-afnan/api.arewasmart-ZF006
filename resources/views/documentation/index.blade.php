@extends('documentation.layout')

@section('content')
    <div class="mb-5">
        <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fw-bold mb-3">V1.0.0</span>
        <h1 class="display-5 fw-bold mb-3">API Documentation</h1>
        <p class="lead">Welcome to the Arewa Smart API. Power your business with Nigeria's most reliable identity and utility service gateway.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5" style="background: linear-gradient(135deg, #1A2B4B, #1e293b);">
        <div class="card-body p-5 text-white position-relative">
            <div class="position-absolute top-0 end-0 p-4 opacity-10">
                <i class="ti ti-world-www" style="font-size: 8rem;"></i>
            </div>
            <h4 class="fw-bold mb-4 d-flex align-items-center">
                <i class="ti ti-link me-2 text-primary"></i> Base Endpoint
            </h4>
            <div class="bg-white bg-opacity-10 border border-white border-opacity-10 rounded-3 p-4 d-flex align-items-center justify-content-between">
                <code class="text-white fs-5 font-monospace">{{ url('/') }}/api/v1</code>
                <button class="btn btn-primary rounded-pill px-4" onclick="copyToClipboard('{{ url('/') }}/api/v1')">
                    <i class="ti ti-copy me-1"></i> Copy
                </button>
            </div>
            <div class="mt-4 text-warning-emphasis d-flex align-items-center small">
                <i class="ti ti-shield-lock me-2"></i>
                <span>All requests must be served over <strong>HTTPS</strong> for security.</span>
            </div>
        </div>
    </div>

    <h2 class="fw-bold mb-4 mt-5">Authentication</h2>
    <p>We use Bearer Token authentication to secure our endpoints. To access any API route, you must include your unique API Token in the <code>Authorization</code> header of your HTTP request.</p>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                    <i class="ti ti-user-plus fs-3"></i>
                </div>
                <h6 class="fw-bold">1. Create Account</h6>
                <p class="small text-muted mb-0">Sign up on our platform to get started.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="bg-soft-success text-success rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                    <i class="ti ti-layout-dashboard fs-3"></i>
                </div>
                <h6 class="fw-bold">2. Login to Dashboard</h6>
                <p class="small text-muted mb-0">Access your developer panel.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="bg-soft-warning text-warning rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                    <i class="ti ti-key fs-3"></i>
                </div>
                <h6 class="fw-bold">3. Get API Key</h6>
                <p class="small text-muted mb-0">Generate your token in Profile Settings.</p>
            </div>
        </div>
    </div>

    <div class="code-block bg-dark rounded-4 p-0 overflow-hidden shadow-lg mb-5">
        <div class="bg-white bg-opacity-10 px-4 py-3 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center">
            <span class="text-white small fw-bold opacity-75">Header Authorization Example</span>
            <span class="badge bg-primary text-white">cURL</span>
        </div>
        <pre class="m-0 p-4 font-monospace"><code class="text-white">curl -X GET "{{ url('/') }}/api/v1/user" \
  -H "<span class="text-info">Authorization:</span> <span class="text-warning">Bearer YOUR_API_TOKEN</span>" \
  -H "<span class="text-info">Accept:</span> <span class="text-warning">application/json</span>"</code></pre>
    </div>

    <div class="alert alert-danger border-0 rounded-4 d-flex align-items-center p-4 mb-5 shadow-sm">
        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-4" style="width: 50px; height: 50px; flex-shrink: 0;">
            <i class="ti ti-shield-x fs-3"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1">Security Warning</h6>
            <p class="small mb-0 opacity-75">Never share your API Token publicly. Avoid exposing it in client-side code such as front-end JavaScript components or mobile apps that can be decompiled.</p>
        </div>
    </div>

    <h2 class="fw-bold mb-4">Standardized Responses</h2>
    <p>Our API returns consistent JSON payloads to make integration predictable and easy to handle across different programming languages.</p>

    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                <div class="card-header border-bottom py-3">
                    <h6 class="fw-bold text-success mb-0 d-flex align-items-center">
                        <i class="ti ti-circle-check me-2"></i> Success Response (2xx)
                    </h6>
                </div>
                <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace small"><code>{
    "status": true,
    "message": "Operation successful",
    "data": { ... }
}</code></pre>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100 border shadow-sm rounded-4 overflow-hidden">
                <div class="card-header border-bottom py-3">
                    <h6 class="fw-bold text-danger mb-0 d-flex align-items-center">
                        <i class="ti ti-circle-x me-2"></i> Error Response (4xx/5xx)
                    </h6>
                </div>
                <div class="card-body p-0 bg-dark">
<pre class="m-0 p-4 text-white font-monospace small"><code>{
    "status": false,
    "message": "The provided token is invalid or expired."
}</code></pre>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center bg-soft-primary rounded-4 p-5 mb-5 border border-primary border-dashed">
        <h4 class="fw-bold mb-3">Ready to dive in?</h4>
        <p class="mb-4">Explore our comprehensive API endpoints to start building your solution today.</p>
        <a href="{{ route('docs.nin') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold">
            Explore Documentation <i class="ti ti-arrow-right ms-2"></i>
        </a>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const toast = document.createElement('div');
                toast.className = 'position-fixed top-0 end-0 m-4 alert alert-success shadow-lg border-0 animate__animated animate__fadeInDown';
                toast.style.zIndex = '9999';
                toast.innerHTML = '<i class="ti ti-check me-2"></i> Copied to clipboard!';
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            });
        }
    </script>
@endsection
