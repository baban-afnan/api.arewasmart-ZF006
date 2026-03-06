@extends('documentation.layout')

@section('content')
    <h1>National Identity Number (NIN) IPE</h1>
    <p>Perform an Identity Pattern Extraction (IPE) to fetch deep metadata and historical tracking information associated with a given National Identity Number.</p>

    <h2>Endpoint</h2>
    <div class="code-block" style="padding: 15px; margin-bottom: 30px;">
        <span class="code-method method-post">POST</span> <code>/nin-ipe</code>
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
            <tr>
                <td><code>Accept</code></td>
                <td><code>application/json</code></td>
                <td><span class="badge-req">Required</span></td>
            </tr>
        </tbody>
    </table>

    <h2>Parameters</h2>
    <table class="docs-table">
        <thead>
            <tr>
                <th>Parameter</th>
                <th>Type</th>
                <th>Description</th>
                <th>Required</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>nin</code></td>
                <td><code>string</code></td>
                <td>The 11-digit National Identity Number.</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
        </tbody>
    </table>

    <h2>Example Request</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>cURL</span>
        </div>
<pre><code>curl -X POST "https://arewasmart.com.ng/api/nin-ipe" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json" \
  -d "nin=12345678901"</code></pre>
    </div>

    <h2>Example Response</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>JSON</span>
        </div>
<pre><code>{
    "status": true,
    "message": "NIN IPE Successful",
    "data": {
        "nin": "12345678901",
        "tracking_id": "ABC123XYZ890",
        "extracted_patterns": {
            "registration_center": "Lagos HQ",
            "registration_date": "2015-08-21",
            "fingerprint_status": "Complete",
            "signature_status": "Captured"
        },
        "verification_history": [
            {
                "date": "2023-01-15T10:00:00Z",
                "purpose": "Bank Account Opening"
            }
        ]
    }
}</code></pre>
    </div>
@endsection
