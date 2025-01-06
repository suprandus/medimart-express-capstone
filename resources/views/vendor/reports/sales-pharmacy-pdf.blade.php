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
            text-align: center;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header img {
            width: 70px;
            height: 70px;
        }

        .pharmacy-info {
            text-align: left;
            margin: 20px 0;
            font-size: 14px;
        }

        .pharmacy-info img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            float: left;
            margin-right: 20px;
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
    <!-- Header -->
    <div class="header">
        <img src="{{ storage_path('app/public/uploads/medimart_logo.png') }}" style=" border-radius: 5px; border: 1px solid rgb(66, 108, 246); width: 70px; height: 70px;" alt="MediMart Logo.png">
        <h2 style="color: rgb(66, 108, 246);">MediMart Express</h2>
    </div>

    <!-- Pharmacy Information -->
    <div class="pharmacy-info">
        {{-- @if (!$pharmacy->image)
            <img src="{{storage_path('app/public/uploads/rose_pharmacy.png') }}" alt="Pharmacy Profile.png">
        @else
            <img src="{{ asset($pharmacy->image) }}" style="width: 80px; height: 80px; border-radius: 50%; float: left; margin-right: 20px;" alt="Pharmacy Profile.png">
        @endif --}}
        <p><strong>Pharmacy Name:</strong> {{ $pharmacy->name }}</p>
        <p><strong>Email:</strong> {{ $pharmacy->email }}</p>
        <p><strong>Phone:</strong> {{ $pharmacy->phone ? $pharmacy->phone : '09179529331'}}</p>
    </div>

    <!-- Sales Details -->
    <div class="report-details">
        <h4 style="text-align:center;">{{ $reportTitle }}</h4>
        <p style="text-align:center;">{{ $reportDate }}</p>
    </div>

    <!-- Sales Data Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 100px; background-color: rgb(105, 140, 255); border: 1px solid rgb(105, 140, 255);">Date</th>
                <th style="width: 100px; background-color: rgb(143, 169, 255); border: 1px solid rgb(105, 140, 255);">Sales (₱)</th>
                <th style="background-color: rgb(105, 140, 255); border: 1px solid rgb(105, 140, 255);">Products Sold</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $record)
                @if($record->total_sales != 0){
                    <tr>
                        <td style="border: 1px solid rgb(105, 140, 255);">{{ $record->date }}</td>
                        <td style="border: 1px solid rgb(105, 140, 255);">₱{{ number_format($record->total_sales, 2) }}</td>
                        <td style="border: 1px solid rgb(105, 140, 255);">{{ implode(', ', $record->products) }}</td>
                    </tr>
                }
                @endif
            @endforeach
            <tr class="total-row">
                <td style="border: 1px solid rgb(105, 140, 255);">Total Sales</td>
                <td style="border: 1px solid rgb(105, 140, 255);">₱{{ number_format($grandTotalSales, 2) }}</td>
                <td style="border: 1px solid rgb(105, 140, 255);"></td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Generated on: {{ now()->format('F j, Y, g:i a') }}</p>
    </div>
</body>
</html>