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
            font-family: 'Inter', sans-serif;
            font-size: 12px;
        }

        .summary-table td {
            padding: 6px 8px;
            vertical-align: middle;
        }

        .summary-header td {
            font-weight: 600;
            border-bottom: 2px solid #000;
        }
    </style>
</head>

<body>

    @php
        $totalCollectibles = $payments->sum('daily');
        $totalCollected = $payments->sum(fn($p) => is_numeric($p->collection) ? $p->collection : 0);
        $clientsPaid = $payments->filter(fn($p) => $p->collection > 0 && $p->type != 'NO PAYMENT')->count();
        $clientsNotPaid = $payments->filter(fn($p) => $p->type == 'NO PAYMENT')->count();
    @endphp

    <div class="wrapper">
        <section class="invoice">

            <div class="text-center mb-4">
                <div class="fw-bold" style="font-size:16px;">
                    ULTRARITZ LENDING CORPORATION
                </div>

                <div class="fw-bold" style="font-size:14px;">
                    QUEZON CITY
                </div>

                <div class="fw-bold mt-2" style="font-size:15px;">
                    Summary Collection for {{ \Carbon\Carbon::parse($payments->first()->due_date)->format('F j Y') }}
                </div>

                <div class="mt-2" style="font-size:12px;">
                    <strong>Area:</strong> Caloocan [{{ $area->areas_name }}]
                </div>
            </div>
            <div class="m-2">
                <h5>#{{ $referenceNumber }}</h5>
            </div>

            <table class="table table-borderless mb-0 w-100">
                <tr>
                    <td width="15%"><strong>Collected By</strong></td>
                    <td width="35%">{{ $payments->first()->collected_by ?? 'N/A' }}</td>

                    <td width="25%"><strong>Total Collectibles</strong></td>
                    <td width="25%" class="text-end">
                        ₱{{ number_format($totalCollectibles, 2) }}
                    </td>
                </tr>

                <tr>
                    <td><strong># of Paid</strong></td>
                    <td>{{ $clientsPaid }}</td>

                    <td><strong>Total Collected</strong></td>
                    <td class="text-end">
                        ₱{{ number_format($totalCollected, 2) }}
                    </td>
                </tr>

                <tr>
                    <td><strong># of No Payment</strong></td>
                    <td>{{ $clientsNotPaid }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>


            <table class="table table-borderless summary-table">
                <thead>
                    <tr class="summary-header">
                        <td>Client Name</td>
                        <td>Loan Amount</td>
                        <td>Old Balance</td>
                        <td>Balance</td>
                        <td>Daily</td>
                        <td>Collection</td>
                        <td>Type</td>
                        <td>Lapsed</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>{{ $payment->fullname }}</td>
                            <td>₱{{ number_format($payment->loan_amount, 2) }}</td>
                            <td>₱{{ number_format($payment->old_balance, 2) }}</td>
                            <td>₱{{ number_format($payment->balance, 2) }}</td>
                            <td>₱{{ number_format($payment->daily, 2) }}</td>
                            <td>
                                @if (is_numeric($payment->collection))
                                    ₱{{ number_format($payment->collection, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $payment->type ?? '-' }}</td>
                            <td>{{ $payment->is_lapsed ? 'Yes' : 'No' }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </section>
    </div>



    <script>
        window.addEventListener("load", () => {
            window.print();
        });

        window.onafterprint = function() {
            window.close();
        };
    </script>

</body>

</html>
