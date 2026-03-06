@extends('documentation.layout')

@section('content')
    <h1>NIN Validation</h1>
    <p>Perform an advanced validation of a NIN or tracking ID. This endpoint utilizes smart caching logic to return saved information if it exists in our system, reducing your wait time, otherwise, it fetches the data directly from the national registry.</p>

    <h2>Endpoint</h2>
    <div class="code-block" style="padding: 15px; margin-bottom: 30px;">
        <span class="code-method method-post">POST</span> <code>/nin-validation</code>
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
                <td><code>tracking_id</code></td>
                <td><code>string</code></td>
                <td>The Tracking ID or NIN to validate.</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
        </tbody>
    </table>

    <h2>Example Request</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>cURL</span>
        </div>
<pre><code>curl -X POST "https://arewasmart.com.ng/api/nin-validation" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json" \
  -d "tracking_id=12345678901"</code></pre>
    </div>

    <h2>Example Response</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>JSON</span>
        </div>
<pre><code>{
    "status": true,
    "message": "NIN Validation Successful",
    "source": "cached",
    "data": {
        "nin": "12345678901",
        "tracking_id": "12345678901",
        "firstname": "Fatima",
        "surname": "Umar",
        "validation_status": "Valid",
        "verified_at": "2024-03-05T15:20:00Z"
    }
}</code></pre>
    </div>
@endsection
