@extends('documentation.layout')

@section('content')
    <h1>Data Subscription</h1>
    <p>Purchase corporate and regular data bundles for your users across all networks.</p>

    <h2>Endpoint</h2>
    <div class="code-block" style="padding: 15px; margin-bottom: 30px;">
        <span class="code-method method-post">POST</span> <code>/data</code>
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
                <td><code>Content-Type</code></td>
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
                <td><code>network</code></td>
                <td><code>string</code></td>
                <td>Network provider (e.g., MTN, GLO, AIRTEL, 9MOBILE).</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
            <tr>
                <td><code>phone</code></td>
                <td><code>string</code></td>
                <td>The 11-digit phone number to receive the data.</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
            <tr>
                <td><code>plan_id</code></td>
                <td><code>integer</code></td>
                <td>The unique ID of the data plan. <em>Use the /prices endpoint to fetch valid plan IDs.</em></td>
                <td><span class="badge-req">Required</span></td>
            </tr>
            <tr>
                <td><code>reference</code></td>
                <td><code>string</code></td>
                <td>A custom unique transaction reference.</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
        </tbody>
    </table>

    <h2>Example Request</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>JSON</span>
        </div>
<pre><code>{
    "network": "AIRTEL",
    "phone": "08012345678",
    "plan_id": 25,
    "reference": "DATA_REF_001"
}</code></pre>
    </div>

    <h2>Example Response</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>JSON</span>
        </div>
<pre><code>{
    "status": true,
    "message": "Data subscription successful",
    "data": {
        "transaction_id": "ASI-987654321",
        "reference": "DATA_REF_001",
        "network": "AIRTEL",
        "phone": "08012345678",
        "plan": "1.5GB 30Days",
        "amount": 1000,
        "balance_after": 5000
    }
}</code></pre>
    </div>
@endsection
