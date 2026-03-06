@extends('documentation.layout')

@section('content')
    <h1>SME Data Purchasing</h1>
    <p>Purchase highly subsidized SME data bundles designed for small businesses and resellers.</p>

    <h2>Endpoint</h2>
    <div class="code-block" style="padding: 15px; margin-bottom: 30px;">
        <span class="code-method method-post">POST</span> <code>/sme-data</code>
    </div>

    <div class="docs-alert docs-alert-info">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Note:</strong> SME Data is currently largely applicable to MTN networks, but occasionally available for others. Make sure to fetch available plans from our pricing list before creating a transaction.
        </div>
    </div>

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
                <td>Network provider (e.g., MTN).</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
            <tr>
                <td><code>phone</code></td>
                <td><code>string</code></td>
                <td>The 11-digit phone number.</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
            <tr>
                <td><code>plan_id</code></td>
                <td><code>integer</code></td>
                <td>The SME plan ID.</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
            <tr>
                <td><code>reference</code></td>
                <td><code>string</code></td>
                <td>Your unique transaction reference.</td>
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
    "network": "MTN",
    "phone": "08123456789",
    "plan_id": 102,
    "reference": "SMED_999888"
}</code></pre>
    </div>

    <h2>Example Response</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>JSON</span>
        </div>
<pre><code>{
    "status": true,
    "message": "SME Data dispensed successfully",
    "data": {
        "transaction_id": "ASI-SME-0001",
        "phone": "08123456789",
        "plan": "MTN SME 1GB 30Days",
        "amount": 250,
        "token": "..."
    }
}</code></pre>
    </div>
@endsection
