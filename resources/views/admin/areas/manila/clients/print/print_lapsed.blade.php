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
                    LIST OF LAPSED ACCOUNT FOR THE MONTH OF {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                </div>

                <div class="mt-2" style="font-size:12px;">
                    <strong>Area:</strong> Manila [{{ $area->area_name }}]<br>
                </div>

            </div>


            <!-- TABLE HEADER -->
            <table class="table table-borderless summary-table">
                <tr class="summary-header text-left">
                    <td>Client Name</td>
                    <td>R.Date</td>
                    <td>PN Amount</td>
                    <td>Collection</td>
                    <td>Maturity</td>
                    <td>Current Balance</td>
                    <td>Payments for
                        {{ \Carbon\Carbon::parse($month)->format('F') }}</td>
                    <td>Closed</td>
                </tr>

                @forelse ($clients as $client)
                    <tr>
                        <td>{{ strtoupper($client->fullname) }}</td>
                        <td>{{ \Carbon\Carbon::parse($client->release_date)->format('m/d/Y') }}</td>
                        <td>{{ number_format($client->loan_amount, 2) }}</td>
                        <td>{{ number_format($client->total_collection, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($client->loan_to)->format('m/d/Y') }}</td>
                        <td>₱{{ number_format($client->balance, 2) }}</td>
                        <td>₱{{ number_format($client->total_collection, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($client->updated_at)->format('m/d/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No lapsed accounts found for selected month
                        </td>
                    </tr>
                @endforelse

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
