@extends('documentation.layout')

@section('content')
    <h1>Introduction</h1>
    <p>Welcome to the Arewa Smart API Documentation. Our API allows you to integrate our robust identity verification, utility bill payments, and telecommunication services directly into your own applications seamlessly.</p>

    <div class="docs-alert docs-alert-info">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Base URL:</strong> <code>https://arewasmart.com.ng/api</code><br>
            All endpoints documented here should be prefixed with this base URL.
        </div>
    </div>

    <h2>Authentication</h2>
    <p>We use Bearer Token authentication to secure our endpoints. To access any API route, you must include your unique API Token in the <code>Authorization</code> header of your HTTP request.</p>

    <h3>Getting your API Token</h3>
    <ol>
        <li><a href="{{ route('register') }}">Create an account</a> on our platform.</li>
        <li>Log in to your dashboard.</li>
        <li>Navigate to your <strong>Profile Settings</strong> -> <strong>API Application</strong> to view or generate your API Token.</li>
    </ol>

    <div class="docs-alert docs-alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <div>
            <strong>Keep your token secure!</strong> Never share your API Token publicly, commit it to version control, or expose it in client-side code such as front-end JavaScript apps.
        </div>
    </div>

    <h3>Example Request</h3>
    <p>Here is an example of how to attach your API token in a standard HTTP request using cURL.</p>

    <div class="code-block">
        <div class="code-caption">
            <span>Authentication Header Example</span>
        </div>
<pre><code>curl -X GET "https://arewasmart.com.ng/api/endpoint" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"</code></pre>
    </div>

    <h2>Responses</h2>
    <p>Our API returns standardized JSON responses. A successful request typically yields a <code>200 OK</code> status code with a JSON payload containing the requested data and a status indicator.</p>

    <div class="code-block">
        <div class="code-caption">
            <span>Success Response (2xx)</span>
        </div>
<pre><code>{
    "status": true,
    "message": "Operation successful",
    "data": { ... }
}</code></pre>
    </div>

    <p>If an error occurs, you will receive an appropriate HTTP status code (e.g., 400 Bad Request, 401 Unauthorized, 422 Unprocessable Entity) alongside a JSON error message.</p>

    <div class="code-block">
        <div class="code-caption">
            <span>Error Response (4xx/5xx)</span>
        </div>
<pre><code>{
    "status": false,
    "message": "The provided token is invalid or expired."
}</code></pre>
    </div>

    <h2>Next Steps</h2>
    <p>Navigate through the sidebar to explore specific services like NIN Verification, BVN Checking, and Utility payments. Each service page provides detailed endpoint URLs, required parameters, and sample responses.</p>

@endsection
