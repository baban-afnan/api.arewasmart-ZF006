@extends('documentation.layout')

@section('content')
    <h1>Service Pricing</h1>
    <p>Fetch the latest pricing for all available services, including data plans, electricity DISCOs, identity verification services (NIN, BVN), and more.</p>

    <h2>Endpoint</h2>
    <div class="code-block" style="padding: 15px; margin-bottom: 30px;">
        <span class="code-method method-get">GET</span> <code>/prices</code>
    </div>

    <h2>Headers</h2>
    <table class="docs-table">
        <thead>
            <tr>
                <th>Header</th>
                <th>Value</th>
                <th>Required</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>Authorization</code></td>
                <td><code>Bearer YOUR_API_TOKEN</code></td>
                <td><span class="badge-req">Required</span></td>
            </tr>
        </tbody>
    </table>

    <div class="docs-alert docs-alert-info">
        <i class="fas fa-info-circle"></i>
        <div>
            This is a GET request, so no body parameters are required. We highly recommend caching the response of this endpoint on your end for at least 15 minutes to improve performance.
        </div>
    </div>

    <h2>Example Request</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>cURL</span>
        </div>
<pre><code>curl -X GET "https://arewasmart.com.ng/api/prices" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"</code></pre>
    </div>

    <h2>Example Response</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>JSON</span>
        </div>
<pre><code>{
    "status": true,
    "message": "Prices fetched successfully",
    "data": {
        "services": [
            {
                "id": 1,
                "name": "NIN Verification",
                "price": 100.00
            },
            {
                "id": 2,
                "name": "BVN Checking",
                "price": 50.00
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
@endsection
