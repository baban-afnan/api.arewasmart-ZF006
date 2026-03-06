@extends('documentation.layout')

@section('content')
    <h1>National Identity Number (NIN) Modification</h1>
    <p>Submit requests to modify specific details linked to a National Identity Number, such as changing a name, date of birth, or address. Modification requests undergo an approval process.</p>

    <h2>Endpoint</h2>
    <div class="code-block" style="padding: 15px; margin-bottom: 30px;">
        <span class="code-method method-post">POST</span> <code>/nin-modification</code>
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
                <td><code>multipart/form-data</code></td>
                <td><span class="badge-req">Required</span></td>
            </tr>
            <tr>
                <td><code>Accept</code></td>
                <td><code>application/json</code></td>
                <td><span class="badge-req">Required</span></td>
            </tr>
        </tbody>
    </table>

    <div class="docs-alert docs-alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <div>
            This request requires <code>multipart/form-data</code> formatting rather than standard JSON because file uploads (documents supporting the modification) are mandatory.
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
                <td><code>nin</code></td>
                <td><code>string</code></td>
                <td>The 11-digit NIN being modified.</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
            <tr>
                <td><code>tracking_id</code></td>
                <td><code>string</code></td>
                <td>The tracking ID associated with the original verifiable record.</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
             <tr>
                <td><code>modification_type</code></td>
                <td><code>string</code></td>
                <td>The type of modification (e.g., <code>name</code>, <code>dob</code>, <code>address</code>).</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
             <tr>
                <td><code>new_value</code></td>
                <td><code>string</code></td>
                <td>The new value representing the modification.</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
            <tr>
                <td><code>supporting_document</code></td>
                <td><code>file</code></td>
                <td>A PDF or image (JPEG/PNG) document proving the validity of the modification (e.g., marriage certificate for name change, court affidavit).</td>
                <td><span class="badge-req">Required</span></td>
            </tr>
        </tbody>
    </table>

    <h2>Example Response</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>JSON</span>
        </div>
<pre><code>{
    "status": true,
    "message": "NIN Modification Request Submitted Successfully",
    "data": {
        "reference": "MOD-100200300",
        "nin": "12345678901",
        "modification_type": "name",
        "status": "pending_approval",
        "submitted_at": "2024-03-05T16:00:22Z"
    }
}</code></pre>
    </div>
@endsection
