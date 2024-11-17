<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\SalesAdmin;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function showSalesReport()
    {
        $overallData = SalesAdmin::selectRaw('DATE(created_at) as date, SUM(sales) as total_sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        $overallLabels = $overallData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'));
        $overallSales = $overallData->pluck('total_sales');
        $totalOverallSales = $overallSales->sum();
        
        // Fetch today’s data grouped by exact time (hour and minute), formatted with AM/PM
        $todayData = SalesAdmin::whereDate('created_at', Carbon::today())
            ->selectRaw('DATE_FORMAT(created_at, "%h:%i %p") as time, SUM(sales) as total_sales')
            ->groupBy('time')
            ->orderBy('created_at')
            ->get();
        $todayLabels = $todayData->pluck('time');
        $todaySales = $todayData->pluck('total_sales');
        $totalTodaySales = $todaySales->sum();

        $weeklyData = SalesAdmin::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->selectRaw('DATE(created_at) as date, SUM(sales) as total_sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        $weekLabels = $weeklyData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('l (m/d)'));
        $weekSales = $weeklyData->pluck('total_sales');
        $totalWeekSales = $weekSales->sum();

        // Monthly data grouped by week with relative week number within the month
        $monthData = SalesAdmin::whereMonth('created_at', Carbon::now()->month)
            ->selectRaw('WEEK(created_at, 1) - WEEK(DATE_FORMAT(created_at, "%Y-%m-01"), 1) + 1 as week_of_month, SUM(sales) as total_sales')
            ->groupBy('week_of_month')
            ->orderBy('week_of_month')
            ->get();
        $monthLabels = $monthData->pluck('week_of_month')->map(fn($week) => "Week " . $week);
        $monthSales = $monthData->pluck('total_sales');
        $totalMonthSales = $monthSales->sum();

        $yearData = SalesAdmin::whereYear('created_at', Carbon::now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(sales) as total_sales')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $yearLabels = $yearData->pluck('month')->map(fn($month) => Carbon::createFromDate(null, $month)->format('F'));
        $yearSales = $yearData->pluck('total_sales');
        $totalYearSales = $yearSales->sum();
        return view('admin.reports.sales-reports', [
            'overallLabels' => $overallLabels,
            'overallSales' => $overallSales,
            'todayLabels' => $todayLabels,
            'todaySales' => $todaySales,
            'weekLabels' => $weekLabels,
            'weekSales' => $weekSales,
            'monthLabels' => $monthLabels,
            'monthSales' => $monthSales,
            'yearLabels' => $yearLabels,
            'yearSales' => $yearSales,
            'totalOverallSales' => $totalOverallSales,
            'totalTodaySales' => $totalTodaySales,
            'totalWeekSales' => $totalWeekSales,
            'totalMonthSales' => $totalMonthSales,
            'totalYearSales' => $totalYearSales,
        ]);
    }
    public function exportSalesToExcel($period)
    {
        $data = [];
        switch ($period) {
            case 'today':
                $data = SalesAdmin::whereDate('created_at', Carbon::today())
                    ->get(['product_name', 'product_price', 'product_order_quantity', 'sales as sale', 'created_at']);
                break;

            case 'week':
                $data = SalesAdmin::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                    ->get(['product_name', 'product_price', 'product_order_quantity', 'sales as sale', 'created_at']);
                break;

            case 'month':
                $data = SalesAdmin::whereMonth('created_at', Carbon::now()->month)
                    ->get(['product_name', 'product_price', 'product_order_quantity', 'sales as sale', 'created_at']);
                break;

            case 'year':
                $data = SalesAdmin::whereYear('created_at', Carbon::now()->year)
                    ->get(['product_name', 'product_price', 'product_order_quantity', 'sales as sale', 'created_at']);
                break;

            case 'overall':
                $data = SalesAdmin::all(['product_name', 'product_price', 'product_order_quantity', 'sales as sale', 'created_at']);
                break;

            default:
                return back()->with('error', 'Invalid period specified');
        }

        if ($data->isEmpty()) {
            return back()->with('error', 'No sales data available for the selected period.');
        }

        $totalSales = $data->sum('sale');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sales Report');

        $headers = [
            'Product Name', 'Product Price', 'Order Quantity', 'Order Cost', 'Sale', 'Date'
        ];

        $formattedData = $data->map(function ($item) use ($period) {
            $dateFormat = match($period) {
                'today' => 'h:i A',
                'week', 'month' => 'l, m/d/Y',
                'year', 'overall' => 'M Y',
                default => 'Y-m-d',
            };

            return [
                'product_name' => $item->product_name,
                'product_price' => '₱' . number_format($item->product_price, 2),
                'product_order_quantity' => $item->product_order_quantity,
                'order_cost' => '₱' . number_format($item->product_price * $item->product_order_quantity, 2),  // Calculate order cost
                'sale' => '₱' . number_format($item->sale, 2),
                'date' => Carbon::parse($item->created_at)->format($dateFormat),
            ];
        });

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue("{$column}1", $header);
            $column++;
        }

        $rowNumber = 2;
        foreach ($formattedData as $row) {
            $sheet->setCellValue("A{$rowNumber}", $row['product_name']);
            $sheet->setCellValue("B{$rowNumber}", $row['product_price']);
            $sheet->setCellValue("C{$rowNumber}", $row['product_order_quantity']);
            $sheet->setCellValue("D{$rowNumber}", $row['order_cost']);
            $sheet->setCellValue("E{$rowNumber}", $row['sale']);
            $sheet->setCellValue("F{$rowNumber}", $row['date']);
            $rowNumber++;
        }

        $sheet->setCellValue("D{$rowNumber}", 'Total Sales');
        $sheet->setCellValue("E{$rowNumber}", '₱' . number_format($totalSales, 2));

        $fileName = "Sales_Report_{$period}_" . now()->format('Y-m-d') . ".xlsx";
        $writer = new Xlsx($spreadsheet);

        return Response::streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }   
}