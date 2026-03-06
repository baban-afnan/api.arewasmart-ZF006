@extends('documentation.layout')

@section('content')
    <h1>Tax Identification Number (TIN) Verification</h1>
    <p>Verify business entities and individuals via their Tax Identification Number.</p>

    <h2>Endpoint</h2>
    <div class="code-block" style="padding: 15px; margin-bottom: 30px;">
        <span class="code-method method-post">POST</span> <code>/tin</code>
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
                <td><code>tin</code></td>
                <td><code>string</code></td>
                <td>The Tax Identification Number or CAC Registration Number.</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
        </tbody>
    </table>

    <h2>Example Request</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>cURL</span>
        </div>
<pre><code>curl -X POST "https://arewasmart.com.ng/api/tin" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json" \
  -d "tin=12345678-0001"</code></pre>
    </div>

    <h2>Example Response</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>JSON</span>
        </div>
<pre><code>{
    "status": true,
    "message": "TIN Verified",
    "data": {
        "taxpayer_name": "Arewa Smart Idea",
        "taxpayer_type": "Corporate",
        "tax_office": "Lagos"
    }
}</code></pre>
    </div>
@endsection
