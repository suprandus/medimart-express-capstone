<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MediMart Express Sales Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 60px;
            height: 60px;
            margin-right: 15px;
        }

        .header-text h1 {
            margin: 0;
            font-size: 20px;
        }

        .header-text p {
            margin: 0;
            font-size: 14px;
            color: #555;
        }

        .pharmacy-details {
            margin-bottom: 20px;
            font-size: 14px;
        }

        .pharmacy-details p {
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
            font-size: 12px;
        }

        th {
            background-color: #f4f4f4;
        }

        .total-row {
            font-weight: bold;
        }

        .footer {
            text-align: right;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="MediMart Logo">
        <div class="header-text">
            <h1>MediMart Express</h1>
            <p>Sales Report</p>
        </div>
    </div>

    <div class="pharmacy-details">
        <p><strong>Pharmacy Name:</strong> {{ $pharmacy->name }}</p>
        <p><strong>Email:</strong> {{ $pharmacy->email }}</p>
        <p><strong>Phone:</strong> {{ $pharmacy->phone }}</p>
    </div>

    <div class="report-details">
        <h4>Sales Per Pharmacy</h4>
        <p>{{ $reportTitle }}</p>
        <p>{{ $reportDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Pharmacy Name</th>
                <th>Pharmacy Email</th>
                <th>Total Orders</th>
                <th>Total Cost (₱)</th>
                <th>Sales (₱)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $record)
                <tr>
                    <td style="text-align:left;">{{ $record->name }}</td>
                    <td style="text-align:left;">{{ $record->email }}</td>
                    <td>{{ $record->total_orders }}</td>
                    <td>₱{{ number_format($record->total_cost, 2) }}</td>
                    <td>₱{{ number_format($record->total_sales, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td></td><td></td><td></td>
                <td>Total Sales</td>
                <td>₱{{ number_format($grandTotalSales, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on: {{ now()->format('F j, Y, g:i a') }}</p>
    </div>
</body>
</html>