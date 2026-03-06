@extends('documentation.layout')

@section('content')
    <h1>Electricity Bill Payment</h1>
    <p>Purchase electricity tokens or pay postpaid bills for all major distribution companies (DisCos) in Nigeria.</p>

    <h2>Step 1: Validate Meter Number</h2>
    <p>Before making a payment, you should validate the meter number to retrieve the customer's name.</p>
    
    <div class="code-block" style="padding: 15px; margin-bottom: 30px;">
        <span class="code-method method-post">POST</span> <code>/electricity/validate</code>
    </div>

    <table class="docs-table">
        <thead>
            <tr>
                <th>Parameter</th>
                <th>Type</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>disco</code></td>
                <td><code>string</code></td>
                <td>e.g., KEDCO, IKEDC, AEDC, IBEDC.</td>
            </tr>
            <tr>
                <td><code>meter_number</code></td>
                <td><code>string</code></td>
                <td>The customer's meter number.</td>
            </tr>
            <tr>
                <td><code>meter_type</code></td>
                <td><code>string</code></td>
                <td><code>prepaid</code> or <code>postpaid</code>.</td>
            </tr>
        </tbody>
    </table>

    <h2>Step 2: Payment Endpoint</h2>
    <div class="code-block" style="padding: 15px; margin-bottom: 30px;">
        <span class="code-method method-post">POST</span> <code>/electricity/pay</code>
    </div>

    <h2>Parameters</h2>
    <table class="docs-table">
        <thead>
            <tr>
                <th>Parameter</th>
                <th>Type</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>disco</code></td>
                <td><code>string</code></td>
                <td>Distribution company identifier.</td>
            </tr>
            <tr>
                <td><code>meter_number</code></td>
                <td><code>string</code></td>
                <td>The 10 to 13-digit meter number.</td>
            </tr>
            <tr>
                <td><code>meter_type</code></td>
                <td><code>string</code></td>
                <td><code>prepaid</code> or <code>postpaid</code>.</td>
            </tr>
            <tr>
                <td><code>amount</code></td>
                <td><code>numeric</code></td>
                <td>Amount in Naira.</td>
            </tr>
            <tr>
                <td><code>phone</code></td>
                <td><code>string</code></td>
                <td>Customer's phone number.</td>
            </tr>
            <tr>
                <td><code>reference</code></td>
                <td><code>string</code></td>
                <td>Your unique transaction reference.</td>
            </tr>
        </tbody>
    </table>

    <h2>Example Response (Prepaid Token)</h2>
    <div class="code-block">
        <div class="code-caption">
            <span>JSON</span>
        </div>
<pre><code>{
    "status": true,
    "message": "Electricity token generated successfully",
    "data": {
        "transaction_id": "ELEC-999000",
        "customer_name": "JOHN DOE",
        "meter": "0123456789",
        "amount": 2000,
        "token": "4521 8905 1234 5678 9012",
        "units": "34.5 kWh"
    }
}</code></pre>
    </div>
@endsection
