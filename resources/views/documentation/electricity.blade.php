@extends('documentation.layout')

@section('content')
    <div class="mb-5">
        <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fw-bold mb-3">BILL PAYMENT</span>
        <h1 class="display-5 fw-bold  mb-3">Electricity Bill Payment</h1>
        <p class="lead text-muted">Purchase electricity tokens or pay postpaid bills for all major distribution companies (DisCos) in Nigeria.</p>
    </div>

    <!-- Step 1: Validation -->
    <h4 class="fw-bold  mb-4 mt-5">Step 1: Validate Meter Number</h4>
    <p class="text-muted mb-4">Before making a payment, you must validate the meter number to retrieve the customer's name and ensure the account is active.</p>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-body p-0">
            <div class="bg-soft-primary p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">POST</span>
                    <code class="text-primary fw-bold fs-5 font-monospace">/api/v1/electricity/validate</code>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted small">
                            <th class="ps-4 py-3">PARAMETER</th>
                            <th class="py-3">TYPE</th>
                            <th class="py-3">DESCRIPTION</th>
                            <th class="text-end pe-4 py-3">REQUIRED</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4"><code class="text-primary fw-bold">disco</code></td>
                            <td><span class="badge bg-light text-muted">string</span></td>
                            <td>e.g., KEDCO, IKEDC, AEDC, IBEDC.</td>
                            <td class="text-end pe-4"><span class="badge bg-danger text-white px-2 py-1 rounded">Required</span></td>
                        </tr>
                        <tr>
                            <td class="ps-4"><code class="text-primary fw-bold">meter_number</code></td>
                            <td><span class="badge bg-light text-muted">string</span></td>
                            <td>The customer's meter number.</td>
                            <td class="text-end pe-4"><span class="badge bg-danger text-white px-2 py-1 rounded">Required</span></td>
                        </tr>
                        <tr>
                            <td class="ps-4"><code class="text-primary fw-bold">meter_type</code></td>
                            <td><span class="badge bg-light text-muted">string</span></td>
                            <td><code>prepaid</code> or <code>postpaid</code>.</td>
                            <td class="text-end pe-4"><span class="badge bg-danger text-white px-2 py-1 rounded">Required</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Step 2: Payment -->
    <h4 class="fw-bold  mb-4 mt-5">Step 2: Payment Endpoint</h4>
    <p class="text-muted mb-4">Once validated, use this endpoint to finalize the transaction and receive a token (for prepaid) or payment confirmation.</p>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-body p-0">
            <div class="bg-soft-primary p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary px-3 py-2 rounded-2 fw-bold fs-14 shadow-sm me-3">POST</span>
                    <code class="text-primary fw-bold fs-5 font-monospace">/api/v1/electricity/pay</code>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted small">
                            <th class="ps-4 py-3">PARAMETER</th>
                            <th class="py-3">TYPE</th>
                            <th class="py-3">DESCRIPTION</th>
                            <th class="text-end pe-4 py-3">REQUIRED</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4"><code class="text-primary fw-bold">disco</code></td>
                            <td><span class="badge bg-light text-muted">string</span></td>
                            <td>Distribution company identifier.</td>
                            <td class="text-end pe-4"><span class="badge bg-danger text-white px-2 py-1 rounded">Required</span></td>
                        </tr>
                        <tr>
                            <td class="ps-4"><code class="text-primary fw-bold">meter_number</code></td>
                            <td><span class="badge bg-light text-muted">string</span></td>
                            <td>The 10 to 13-digit meter number.</td>
                            <td class="text-end pe-4"><span class="badge bg-danger text-white px-2 py-1 rounded">Required</span></td>
                        </tr>
                        <tr>
                            <td class="ps-4"><code class="text-primary fw-bold">amount</code></td>
                            <td><span class="badge bg-light text-muted">numeric</span></td>
                            <td>Amount in Naira.</td>
                            <td class="text-end pe-4"><span class="badge bg-danger text-white px-2 py-1 rounded">Required</span></td>
                        </tr>
                        <tr>
                            <td class="ps-4"><code class="text-primary fw-bold">phone</code></td>
                            <td><span class="badge bg-light text-muted">string</span></td>
                            <td>Customer's phone number.</td>
                            <td class="text-end pe-4"><span class="badge bg-danger text-white px-2 py-1 rounded">Required</span></td>
                        </tr>
                        <tr>
                            <td class="ps-4"><code class="text-primary fw-bold">reference</code></td>
                            <td><span class="badge bg-light text-muted">string</span></td>
                            <td>Your unique transaction reference.</td>
                            <td class="text-end pe-4"><span class="badge bg-danger text-white px-2 py-1 rounded">Required</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-12">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-dark">
                <div class="card-header bg-white bg-opacity-10 border-bottom border-white border-opacity-10 py-3">
                    <h6 class="fw-bold text-success mb-0 d-flex align-items-center">
                        <i class="ti ti-circle-check me-2 fs-15"></i> Example Response (Prepaid Token)
                    </h6>
                </div>
                <div class="card-body p-0">
<pre class="m-0 p-4 font-monospace small"><code class="text-white">{
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
            </div>
        </div>
    </div>
@endsection
