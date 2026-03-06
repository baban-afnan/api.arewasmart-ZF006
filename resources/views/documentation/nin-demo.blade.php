@extends('documentation.layout')

@section('content')
    <h1>National Identity Number (NIN) DEMO Verification</h1>
    <p>Test the basic NIN verification process using our DEMO endpoint without incurring real charges or needing real NIN records during development.</p>

    <h2>Endpoint</h2>
    <div class="code-block" style="padding: 15px; margin-bottom: 30px;">
        <span class="code-method method-post">POST</span> <code>/nin-demo</code>
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
                <td>A sample or demo 11-digit NIN (e.g., <code>11111111111</code>).</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
        </tbody>
    </table>

    <h2>Example Request</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>cURL</span>
        </div>
<pre><code>curl -X POST "https://arewasmart.com.ng/api/nin-demo" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json" \
  -d "nin=11111111111"</code></pre>
    </div>

    <h2>Example Response</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>JSON</span>
        </div>
<pre><code>{
    "status": true,
    "message": "Demo NIN Verified Successfully",
    "data": {
        "nin": "11111111111",
        "firstname": "Demo",
        "surname": "User",
        "middlename": "Test",
        "birthdate": "1990-10-10",
        "gender": "m",
        "photo": "base64_encoded_string..."
    }
}</code></pre>
    </div>
@endsection
