<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ULC - System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/print.css') }}">
    <style>
        body {
            margin: 0px !important;
            padding: 0px !important;
        }

        .ledger-table {
            border: 2px solid #000 !important;
        }

        .ledger-table th,
        .ledger-table td {
            border: 1px solid #000 !important;
            padding: 4px 8px !important;
            vertical-align: middle;
            text-transform: uppercase;
        }

        .ledger-table thead th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }

        .label-cell {
            font-weight: bold;
            width: 15%;
        }

        .loan-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px 16px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .info-item {
            display: flex;
            border-bottom: 1px solid #000;
            padding: 4px 0;
        }

        .label {
            font-weight: bold;
            width: 80px;
        }

        .value {
            flex: 1;
        }
    </style>

</head>

<body>
    <div class="container-fluid bg-white">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h2 class="fw-bold mb-0">{{ strtoupper($loan->fullname) }}</h2>
            </div>
            <div class="text-end" style="font-size: 11px;">
                <p class="mb-0">For any concern, Please contact:</p>
                <p class="mb-0">Mobile No.: 0995-418-1658</p>
                <p class="mb-0 fw-bold">JESSA A. MISAJON - OIC</p>
            </div>
        </div>

        <div class="loan-info">
            <div class="info-item">
                <span class="label">NAME:</span>
                <span class="value">{{ $loan->fullname }}</span>
            </div>
            <div class="info-item">
                <span class="label">DATE:</span>
                <span class="value">{{ \Carbon\Carbon::parse($loan->created_at)->format('M d, Y') }}</span>
            </div>
            <div class="info-item">
                <span class="label">PN#:</span>
                <span class="value">{{ $loan->pn_number }}</span>
            </div>
            <div class="info-item">
                <span class="label">DURATION:</span>
                <span class="value">{{ \Carbon\Carbon::parse($loan->loan_from)->format('M d, Y') }} -
                    {{ \Carbon\Carbon::parse($loan->loan_to)->format('M d, Y') }}</span>
            </div>

            <div class="info-item">
                <span class="label">FC:</span>
                <span class="value">{{ $payments->first()->collected_by ?? 'N/A' }}</span>
            </div>

        </div>


        <table class="table table-bordered ledger-table mb-4 text-center" style="font-size: 12px;">
            <thead>
                <tr>
                    <th>PN Amount</th>
                    <th>Due Date</th>
                    <th>Terms</th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>₱{{ number_format($loan->loan_amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($loan->loan_to)->format('M d, Y') }}</td>
                    <td>{{ $loan->loan_terms }}</td>
                </tr>
            </tbody>
        </table>

        <div class="table-responsive">
            <table class="table table-bordered ledger-table" style="font-size: 11px;">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Date</th>
                        <th>Daily Payment</th>
                        <th>Total Payment</th>
                        <th>Lapsed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $index => $payment)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') }}</td>
                            <td class="text-center">₱{{ number_format($loan->daily, 2) }}</td>
                            <td class="text-center">₱{{ number_format($payment->collection ?? 0, 2) }}</td>
                            <td class="text-center">
                                {{ $payment->is_lapsed == 1 ? 'Yes' : 'No' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        window.addEventListener("load", () => {
            window.print();
        });
    </script>

</body>

</html>
