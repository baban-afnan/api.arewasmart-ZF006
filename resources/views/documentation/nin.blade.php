@extends('documentation.layout')

@section('content')
    <h1>National Identity Number (NIN) Verification</h1>
    <p>The NIN Verification endpoint allows you to verify the identity of individuals using their National Identity Number.</p>

    <h2>Endpoint</h2>
    <div class="code-block" style="padding: 15px; margin-bottom: 30px;">
        <span class="code-method method-post">POST</span> <code>/nin</code>
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
            <span>PHP (cURL)</span>
        </div>
<pre><code>&lt;?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://arewasmart.com.ng/api/nin',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('nin' => '12345678901'),
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer YOUR_API_TOKEN',
    'Accept: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
</code></pre>
    </div>

    <h2>Example Response</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>JSON</span>
        </div>
<pre><code>{
    "status": true,
    "message": "NIN Verified Successfully",
    "data": {
        "firstname": "John",
        "surname": "Doe",
        "middlename": "Smith",
        "birthdate": "1990-01-01",
        "gender": "m",
        "photo": "base64_encoded_string..."
    }
}</code></pre>
    </div>
@endsection
