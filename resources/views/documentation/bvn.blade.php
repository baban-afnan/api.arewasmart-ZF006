@extends('documentation.layout')

@section('content')
    <h1>Bank Verification Number (BVN) Checking</h1>
    <p>The BVN endpoint enables you to verify the identity of users via their Bank Verification Number linked to their bank accounts.</p>

    <h2>Endpoint</h2>
    <div class="code-block" style="padding: 15px; margin-bottom: 30px;">
        <span class="code-method method-post">POST</span> <code>/bvn</code>
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
                <td><code>bvn</code></td>
                <td><code>string</code></td>
                <td>The 11-digit Bank Verification Number.</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
        </tbody>
    </table>

    <h2>Example Request</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>cURL</span>
        </div>
<pre><code>curl -X POST "https://arewasmart.com.ng/api/bvn" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json" \
  -d "bvn=01234567890"</code></pre>
    </div>

    <h2>Example Response</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>JSON</span>
        </div>
<pre><code>{
    "status": true,
    "message": "BVN Verified Successfully",
    "data": {
        "firstName": "John",
        "lastName": "Doe",
        "middleName": "Smith",
        "dateOfBirth": "01-Jan-1990",
        "phoneNumber": "08012345678"
    }
}</code></pre>
    </div>
@endsection
