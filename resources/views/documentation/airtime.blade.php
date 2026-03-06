@extends('documentation.layout')

@section('content')
    <h1>Airtime Topup</h1>
    <p>Recharge airtime seamlessly across all major Nigerian telecommunication networks.</p>

    <h2>Endpoint</h2>
    <div class="code-block" style="padding: 15px; margin-bottom: 30px;">
        <span class="code-method method-post">POST</span> <code>/airtime</code>
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
                <td>Network provider ID or name (e.g., MTN, GLO, AIRTEL, 9MOBILE).</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
            <tr>
                <td><code>phone</code></td>
                <td><code>string</code></td>
                <td>The 11-digit phone number to recharge.</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
            <tr>
                <td><code>amount</code></td>
                <td><code>numeric</code></td>
                <td>The amount in Naira (NGN) to recharge.</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
            <tr>
                <td><code>reference</code></td>
                <td><code>string</code></td>
                <td>A unique transaction reference for tracking.</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
        </tbody>
    </table>

    <h2>Example Request</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>JSON Payload</span>
        </div>
<pre><code>{
    "network": "MTN",
    "phone": "08012345678",
    "amount": 500,
    "reference": "TXN_123456789"
}</code></pre>
    </div>

    <h2>Example Response</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>JSON</span>
        </div>
<pre><code>{
    "status": true,
    "message": "Airtime purchased successfully",
    "data": {
        "transaction_id": "ASI-123456789",
        "reference": "TXN_123456789",
        "network": "MTN",
        "phone": "08012345678",
        "amount": 500,
        "balance_before": 1500,
        "balance_after": 1000
    }
}</code></pre>
    </div>
@endsection
