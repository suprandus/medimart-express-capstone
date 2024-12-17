<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\SalesAdmin;
use App\Models\SalesByPharmacy;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function showSalesReport()
    {
        $overallDataMedimart = SalesAdmin::selectRaw('DATE(created_at) as date, SUM(sales) as total_sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        $overallLabelsMedimart = $overallDataMedimart->pluck('date')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'));
        $overallSalesMedimart = $overallDataMedimart->pluck('total_sales');
        $totalOverallSalesMedimart = $overallSalesMedimart->sum();
        
        $todayDataMedimart = SalesAdmin::whereDate('created_at', Carbon::today())
            ->selectRaw('DATE_FORMAT(created_at, "%h:%i %p") as time, SUM(sales) as total_sales')
            ->groupBy('time')
            ->orderBy('created_at')
            ->get();
        $todayLabelsMedimart = $todayDataMedimart->pluck('time');
        $todaySalesMedimart = $todayDataMedimart->pluck('total_sales');
        $totalTodaySalesMedimart = $todaySalesMedimart->sum();

        $weeklyDataMedimart = SalesAdmin::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->selectRaw('DATE(created_at) as date, SUM(sales) as total_sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        $weekLabelsMedimart = $weeklyDataMedimart->pluck('date')->map(fn($date) => Carbon::parse($date)->format('l (m/d)'));
        $weekSalesMedimart = $weeklyDataMedimart->pluck('total_sales');
        $totalWeekSalesMedimart = $weekSalesMedimart->sum();

        $monthDataMedimart = SalesAdmin::whereMonth('created_at', Carbon::now()->month)
            ->selectRaw('WEEK(created_at, 1) - WEEK(DATE_FORMAT(created_at, "%Y-%m-01"), 1) + 1 as week_of_month, SUM(sales) as total_sales')
            ->groupBy('week_of_month')
            ->orderBy('week_of_month')
            ->get();
        $monthLabelsMedimart = $monthDataMedimart->pluck('week_of_month')->map(fn($week) => "Week " . $week);
        $monthSalesMedimart = $monthDataMedimart->pluck('total_sales');
        $totalMonthSalesMedimart = $monthSalesMedimart->sum();

        $yearDataMedimart = SalesAdmin::whereYear('created_at', Carbon::now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(sales) as total_sales')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $yearLabelsMedimart = $yearDataMedimart->pluck('month')->map(fn($month) => Carbon::createFromDate(null, $month)->format('F'));
        $yearSalesMedimart = $yearDataMedimart->pluck('total_sales');
        $totalYearSalesMedimart = $yearSalesMedimart->sum();

        $overallDataPharmacy = SalesByPharmacy::selectRaw('vendor_id, name, DATE(created_at) as date, SUM(sales) as total_sales')
            ->groupBy('vendor_id')
            ->orderBy('date')
            ->get();
        $overallLabelsPharmacy = $overallDataPharmacy->pluck('date')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'));
        $overallSalesPharmacy = $overallDataPharmacy->pluck('total_sales');
        $totalOverallSalesPharmacy = $overallSalesPharmacy->sum();

        $todayDataPharmacy = SalesByPharmacy::whereDate('created_at', Carbon::today())
            ->selectRaw('vendor_id, name, DATE_FORMAT(created_at, "%h:%i %p") as time, SUM(sales) as total_sales')
            ->groupBy('vendor_id')
            ->orderBy('created_at')
            ->get();
        $todayLabelsPharmacy = $todayDataPharmacy->pluck('time');
        $todaySalesPharmacy = $todayDataPharmacy->pluck('total_sales');
        $totalTodaySalesPharmacy = $todaySalesPharmacy->sum();

        $weeklyDataPharmacy = SalesByPharmacy::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->selectRaw('vendor_id, name, DATE(created_at) as date, SUM(sales) as total_sales')
            ->groupBy('vendor_id')
            ->orderBy('date')
            ->get();
        $weekLabelsPharmacy = $weeklyDataPharmacy->pluck('date')->map(fn($date) => Carbon::parse($date)->format('l (m/d)'));
        $weekSalesPharmacy = $weeklyDataPharmacy->pluck('total_sales');
        $totalWeekSalesPharmacy = $weekSalesPharmacy->sum();

        $monthDataPharmacy = SalesByPharmacy::whereMonth('created_at', Carbon::now()->month)
            ->selectRaw('vendor_id, name, WEEK(created_at, 1) - WEEK(DATE_FORMAT(created_at, "%Y-%m-01"), 1) + 1 as week_of_month, SUM(sales) as total_sales')
            ->groupBy('vendor_id')
            ->orderBy('week_of_month')
            ->get();
        $monthLabelsPharmacy = $monthDataPharmacy->pluck('week_of_month')->map(fn($week) => "Week " . $week);
        $monthSalesPharmacy = $monthDataPharmacy->pluck('total_sales');
        $totalMonthSalesPharmacy = $monthSalesPharmacy->sum();

        $yearDataPharmacy = SalesByPharmacy::whereYear('created_at', Carbon::now()->year)
            ->selectRaw('vendor_id, name, MONTH(created_at) as month, SUM(sales) as total_sales')
            ->groupBy('vendor_id')
            ->orderBy('month')
            ->get();
        $yearLabelsPharmacy = $yearDataPharmacy->pluck('month')->map(fn($month) => Carbon::createFromDate(null, $month)->format('F'));
        $yearSalesPharmacy = $yearDataPharmacy->pluck('total_sales');
        $totalYearSalesPharmacy = $yearSalesPharmacy->sum();

        // Fetch sales data grouped by vendor_id
        $pharmacySale = SalesByPharmacy::selectRaw('vendor_id, name, SUM(sales) as total_sales')
            ->groupBy('vendor_id', 'name')
            ->orderBy('total_sales', 'DESC')
            ->get();
        //$vendorLabels = $vendorSales->pluck('vendor_id'); // Use vendor_id as labels
        $pharmacyNames = $pharmacySale->pluck('name'); // Use vendor_id as labels
        $pharmacySales = $pharmacySale->pluck('total_sales'); // Total sales per vendor

        return view('admin.reports.sales-reports', [
            'overallLabelsMedimart' => $overallLabelsMedimart,
            'overallSalesMedimart' => $overallSalesMedimart,
            'todayLabelsMedimart' => $todayLabelsMedimart,
            'todaySalesMedimart' => $todaySalesMedimart,
            'weekLabelsMedimart' => $weekLabelsMedimart,
            'weekSalesMedimart' => $weekSalesMedimart,
            'monthLabelsMedimart' => $monthLabelsMedimart,
            'monthSalesMedimart' => $monthSalesMedimart,
            'yearLabelsMedimart' => $yearLabelsMedimart,
            'yearSalesMedimart' => $yearSalesMedimart,
            'totalOverallSalesMedimart' => $totalOverallSalesMedimart,
            'totalTodaySalesMedimart' => $totalTodaySalesMedimart,
            'totalWeekSalesMedimart' => $totalWeekSalesMedimart,
            'totalMonthSalesMedimart' => $totalMonthSalesMedimart,
            'totalYearSalesMedimart' => $totalYearSalesMedimart,
            'overallLabelsPharmacy' => $overallLabelsPharmacy,
            'overallSalesPharmacy' => $overallSalesPharmacy,
            'todayLabelsPharmacy' => $todayLabelsPharmacy,
            'todaySalesPharmacy' => $todaySalesPharmacy,
            'weekLabelsPharmacy' => $weekLabelsPharmacy,
            'weekSalesPharmacy' => $weekSalesPharmacy,
            'monthLabelsPharmacy' => $monthLabelsPharmacy,
            'monthSalesPharmacy' => $monthSalesPharmacy,
            'yearLabelsPharmacy' => $yearLabelsPharmacy,
            'yearSalesPharmacy' => $yearSalesPharmacy,
            'totalOverallSalesPharmacy' => $totalOverallSalesPharmacy,
            'totalTodaySalesPharmacy' => $totalTodaySalesPharmacy,
            'totalWeekSalesPharmacy' => $totalWeekSalesPharmacy,
            'totalMonthSalesPharmacy' => $totalMonthSalesPharmacy,
            'totalYearSales' => $totalYearSalesPharmacy,
            'pharmacyNames' => $pharmacyNames,
            'pharmacySales' => $pharmacySales,
        ]);
    }
    public function showPharmacySales(){

        $vendorId = Auth::id();

        $overallData = SalesByPharmacy::selectRaw('DATE(created_at) as date, SUM(sales) as total_sales')
            ->where('vendor_id', $vendorId)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        $overallLabels = $overallData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'));
        $overallSales = $overallData->pluck('total_sales');
        $totalOverallSales = $overallSales->sum();

        $todayData = SalesByPharmacy::whereDate('created_at', Carbon::today())
            ->where('vendor_id', $vendorId)
            ->selectRaw('DATE_FORMAT(created_at, "%h:%i %p") as time, SUM(sales) as total_sales')
            ->groupBy('time')
            ->orderBy('created_at')
            ->get();
        $todayLabels = $todayData->pluck('time');
        $todaySales = $todayData->pluck('total_sales');
        $totalTodaySales = $todaySales->sum();

        $weeklyData = SalesAdmin::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->where('vendor_id', $vendorId)
            ->selectRaw('DATE(created_at) as date, SUM(sales) as total_sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        $weekLabels = $weeklyData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('l (m/d)'));
        $weekSales = $weeklyData->pluck('total_sales');
        $totalWeekSales = $weekSales->sum();

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

        $pharmacySales = SalesByPharmacy::selectRaw('vendor_id, name, SUM(sales) as total_sales')
            ->where('vendor_id', $vendorId)
            ->groupBy('vendor_id')
            ->orderBy('total_sales', 'DESC')
            ->get();
        $pharmacyNames = $pharmacySales->pluck('name');
        $pharmacySales = $pharmacySales->pluck('total_sales');

        return view('vendor.reports.sales-reports', [
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
            'pharmacyNames' => $pharmacyNames,
            'pharmacySales' => $pharmacySales,
        ]);
    }
    public function exportLineGraphToPDF($period){
        $validPeriods = ['overall', 'today', 'week', 'month', 'year'];
        if (!in_array($period, $validPeriods)) {
            return back()->with('error', 'Invalid period selected.');
        }
    
        $query = SalesAdmin::selectRaw('DATE(created_at) as date, SUM(sales) as total_sales')->groupBy('date');
    
        $fileName = "MediMart_Sales_Report_Medimart_Overall_" . Carbon::now()->format('Y-m-d') . '.pdf';
        $latestDate = null;
        $reportDate = 'As of ' . Carbon::now()->format('F j, Y');
        $reportTitle = 'Overall Sales';
    
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                $fileName = "MediMart_Sales_Report_Medimart_Today_" . Carbon::now()->format('Y-m-d') . '.pdf';
                $reportTitle = "Today's Sales";
                break;
    
            case 'week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                $fileName = "MediMart_Sales_Report_Medimart_This_Week_" . Carbon::now()->format('Y-m-d') . '.pdf';
                $reportTitle = "This Week's Sales";
                break;
    
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                $fileName = "MediMart_Sales_Report_Medimart_This_Month_" . Carbon::now()->format('Y-m-d') . '.pdf';
                $reportTitle = "This Month's Sales";
                break;
    
            case 'year':
                $query->whereYear('created_at', Carbon::now()->year);
                $fileName = "MediMart_Sales_Report_Medimart_This_Year_" . Carbon::now()->format('Y-m-d') . '.pdf';
                $reportTitle = "This Year's Sales";
                break;
        }
    
        $latestDateQuery = clone $query;
    
        $latestDate = $latestDateQuery->latest('created_at')->value('created_at');
        if ($latestDate) {
            $formattedLatestDate = Carbon::parse($latestDate)->format('F j, Y');
            $reportDate = 'As of ' . $formattedLatestDate;
        }
    
        $data = $query->orderBy('date')->get();
    
        if ($data->isEmpty()) {
            return back()->with('error', 'No sales data available for the selected period.');
        }
    
        $grandTotalSales = $data->sum('total_sales');
    
        $pdf = Pdf::loadView('admin.reports.sales-medimart-pdf', [
            'data' => $data,
            'grandTotalSales' => $grandTotalSales,
            'reportDate' => $reportDate,
            'reportTitle' => $reportTitle,
        ]);
    
        return $pdf->download($fileName);
    }     
    public function exportLineGraphToExcel($period){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('MediMart Sales Report Overall');
        $data = [];
        switch ($period) {
            case 'today':
                $data = SalesAdmin::whereDate('created_at', Carbon::today())
                    ->get(['product_name', 'product_price', 'product_order_quantity', 'sales as sale', 'created_at']);
                $fileName = "Medimart_Sales_Report_Today_" . now()->format('Y-m-d') . ".xlsx";
                $sheet->setTitle('MediMart Sales Report Today');
                break;

            case 'week':
                $data = SalesAdmin::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                    ->get(['product_name', 'product_price', 'product_order_quantity', 'sales as sale', 'created_at']);
                $fileName = "Medimart_Sales_Report_This_Week_" . now()->format('Y-m-d') . ".xlsx";
                $sheet->setTitle('MediMart Sales Report This Week');
                break;

            case 'month':
                $data = SalesAdmin::whereMonth('created_at', Carbon::now()->month)
                    ->get(['product_name', 'product_price', 'product_order_quantity', 'sales as sale', 'created_at']);
                $fileName = "Medimart_Sales_Report_This_Month_" . now()->format('Y-m-d') . ".xlsx";
                $sheet->setTitle('MediMart Sales Report This Month');
                break;

            case 'year':
                $data = SalesAdmin::whereYear('created_at', Carbon::now()->year)
                    ->get(['product_name', 'product_price', 'product_order_quantity', 'sales as sale', 'created_at']);
                $fileName = "Medimart_Sales_Report_This_Year_" . now()->format('Y-m-d') . ".xlsx";
                $sheet->setTitle('MediMart Sales Report This Year');
                break;

            case 'overall':
                $data = SalesAdmin::all(['product_name', 'product_price', 'product_order_quantity', 'sales as sale', 'created_at']);
                $fileName = "Medimart_Sales_Report_Overall_" . now()->format('Y-m-d') . ".xlsx";
                $sheet->setTitle('MediMart Sales Report Overall');
                break;

            default:
                return back()->with('error', 'Invalid period specified');
        }

        if ($data->isEmpty()) {
            return back()->with('error', 'No sales data available for the selected period.');
        }

        $totalSales = $data->sum('sale');
 
        $headers = [
            'Product Name', 'Product Price', 'Order Quantity', 'Order Cost', 'Sale', 'Date'
        ];
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        foreach (range('B', 'F') as $column) {
            $sheet->getStyle($column)->getAlignment()->setHorizontal('center');
        }
        $headerRange = 'A1:F1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal('center');

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue("{$column}1", $header);
            $column++;
        }

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

        $writer = new Xlsx($spreadsheet);

        return Response::streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
    public function exportBarGraphToPDF($period)
    {
        $validPeriods = ['overall', 'today', 'week', 'month', 'year'];
        if (!in_array($period, $validPeriods)) {
            return back()->with('error', 'Invalid period selected.');
        }
    
        $query = SalesByPharmacy::selectRaw(
            'vendor_id, name, email, 
            SUM(product_order_quantity) as total_orders, 
            SUM(order_cost) as total_cost, 
            SUM(sales) as total_sales'
        )->groupBy('vendor_id', 'name', 'email');
    
        $fileName = "MediMart_Sales_Report_by_Pharmacy_Overall_" . Carbon::now()->format('Y-m-d') . '.pdf';
        $latestDate = null;
        $reportDate = 'As of ' . Carbon::now()->format('F j, Y');
        $reportTitle = 'Overall Sales per Pharmacy';
    
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                $fileName = "MediMart_Sales_Report_by_Pharmacy_Today_" . Carbon::now()->format('Y-m-d') . '.pdf';
                $reportTitle = "Today's Sales per Pharmacy";
                break;
    
            case 'week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                $fileName = "MediMart_Sales_Report_by_Pharmacy_This_Week_" . Carbon::now()->format('Y-m-d') . '.pdf';
                $reportTitle = "This Week's Sales per Pharmacy";
                break;
    
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                $fileName = "MediMart_Sales_Report_by_Pharmacy_This_Month_" . Carbon::now()->format('Y-m-d') . '.pdf';
                $reportTitle = "This Month's Sales per Pharmacy";
                break;
    
            case 'year':
                $query->whereYear('created_at', Carbon::now()->year);
                $fileName = "MediMart_Sales_Report_by_Pharmacy_This_Year_" . Carbon::now()->format('Y-m-d') . '.pdf';
                $reportTitle = "This Year's Sales per Pharmacy";
                break;
        }
    
        $latestDateQuery = clone $query;
        $latestDate = $latestDateQuery->latest('created_at')->value('created_at');
        if ($latestDate) {
            $formattedLatestDate = Carbon::parse($latestDate)->format('F j, Y');
            $reportDate = 'As of ' . $formattedLatestDate;
        }
    
        $data = $query->orderByDesc('total_sales')->get();
    
        if ($data->isEmpty()) {
            return back()->with('error', 'No sales data available for the selected period.');
        }
    
        $grandTotalSales = $data->sum('total_sales');
    
        $pdf = Pdf::loadView('admin.reports.sales-by-pharmacy-pdf', [
            'data' => $data,
            'grandTotalSales' => $grandTotalSales,
            'reportDate' => $reportDate,
            'reportTitle' => $reportTitle,
        ]);
    
        return $pdf->download($fileName);
    }
    public function exportBarGraphToExcel($period){
        $validPeriods = ['overall', 'today', 'week', 'month', 'year'];
        if (!in_array($period, $validPeriods)) {
            return back()->with('error', 'Invalid period selected.');
        }
        
        $query = SalesByPharmacy::selectRaw(
            'vendor_id, name, email, SUM(product_order_quantity) as total_orders, SUM(order_cost) as total_cost, SUM(sales) as total_sales')
            ->groupBy('vendor_id', 'name', 'email');
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sales Report by Pharmacy');
        $fileName = "MediMart_Sales_Report_by_Pharmacy_Overall_" . now()->format('Y-m-d') . '.xlsx';

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                $sheet->setTitle('Sales Report by Pharmacy Today');
                $fileName = "MediMart_Sales_Report_by_Pharmacy_Today_" . now()->format('Y-m-d') . '.xlsx';
                break;
            case 'week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                $sheet->setTitle('Sales by Pharmacy This Week');
                $fileName = "MediMart_Sales_Report_by_Pharmacy_This_Week_" . now()->format('Y-m-d') . '.xlsx';

                break;
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
                $sheet->setTitle('Sales by Pharmacy This Month');
                $fileName = "MediMart_Sales_Report_by_Pharmacy__This_Month" . now()->format('Y-m-d') . '.xlsx';
                break;
            case 'year':
                $query->whereYear('created_at', Carbon::now()->year);
                $sheet->setTitle('Sales by Pharmacy This Year');
                $fileName = "MediMart_Sales_Report_by_Pharmacy_This_Year" . now()->format('Y-m-d') . '.xlsx';
                break;
        }

        $data = $query->orderByDesc('total_sales')->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'No sales data available for the selected period.');
        }

        $grandTotalSales = $data->sum('total_sales');

        $headers = [
            'Pharmacy Name', 
            'Pharmacy Email', 
            'Total Orders', 
            'Total Order Cost (₱)', 
            'Total Sales (₱)'
        ];

        $sheet->fromArray($headers, null, 'A1');

        $rowNumber = 2;
        foreach ($data as $record) {
            $sheet->setCellValue("A{$rowNumber}", $record->name);
            $sheet->setCellValue("B{$rowNumber}", $record->email);
            $sheet->setCellValue("C{$rowNumber}", $record->total_orders);
            $sheet->setCellValue("D{$rowNumber}", '₱' . number_format($record->total_cost, 2));
            $sheet->setCellValue("E{$rowNumber}", '₱' . number_format($record->total_sales, 2));
            $rowNumber++;
        }

        $sheet->setCellValue("D{$rowNumber}", 'Overall Total Sales');
        $sheet->setCellValue("E{$rowNumber}", '₱' . number_format($grandTotalSales, 2));

        $headerRange = 'A1:E1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal('center');

        $sheet->getStyle("D{$rowNumber}:E{$rowNumber}")->getFont()->setBold(true);

        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        foreach (range('C', 'E') as $column) {
            $sheet->getStyle($column)->getAlignment()->setHorizontal('center');
        }

        $writer = new Xlsx($spreadsheet);
        $tempFilePath = storage_path("app/{$fileName}");

        $writer->save($tempFilePath);

        return response()->download($tempFilePath)->deleteFileAfterSend(true);
    }
    public function pharmacyExportLineGraphToPDF($period){
        $validPeriods = ['overall', 'today', 'week', 'month', 'year'];
        if (!in_array($period, $validPeriods)) {
            return back()->with('error', 'Invalid period selected.');
        }
    
        $vendorId = Auth::id();

        $query = SalesByPharmacy::selectRaw('vendor_id, name, email, DATE(created_at) as date, SUM(sales) as total_sales')
            ->where('vendor_id', $vendorId)
            ->groupBy('date');
    
        $fileName = "Pharmacy_Sales_Report_Overall_" . Carbon::now()->format('Y-m-d') . '.pdf';
        $reportDate = 'As of ' . Carbon::now()->format('F j, Y');
        $reportTitle = 'Overall Sales';
    
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                $fileName = "Pharmacy_Sales_Report_Today_" . Carbon::now()->format('Y-m-d') . '.pdf';
                $reportTitle = "Today's Sales";
                break;
    
            case 'week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                $fileName = "Pharmacy_Sales_Report_This_Week_" . Carbon::now()->format('Y-m-d') . '.pdf';
                $reportTitle = "This Week's Sales";
                break;
    
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                $fileName = "Pharmacy_Sales_Report_This_Month_" . Carbon::now()->format('Y-m-d') . '.pdf';
                $reportTitle = "This Month's Sales";
                break;
    
            case 'year':
                $query->whereYear('created_at', Carbon::now()->year);
                $fileName = "Pharmacy_Sales_Report_This_Year_" . Carbon::now()->format('Y-m-d') . '.pdf';
                $reportTitle = "This Year's Sales";
                break;
        }
    
        $latestDateQuery = clone $query;
        $latestDate = $latestDateQuery->latest('created_at')->value('created_at');
        if ($latestDate) {
            $formattedLatestDate = Carbon::parse($latestDate)->format('F j, Y');
            $reportDate = 'As of ' . $formattedLatestDate;
        }
    
        $data = $query->orderBy('date')->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'No sales data available for the selected period.');
        }
    
        $grandTotalSales = $data->sum('total_sales');
    
        $pdf = Pdf::loadView('vendor.reports.sales-pharmacy-pdf', [
            'data' => $data,
            'grandTotalSales' => $grandTotalSales,
            'reportDate' => $reportDate,
            'reportTitle' => $reportTitle,
        ]);
    
        return $pdf->download($fileName);
    }
    public function pharmacyExportLineGraphToExcel($period)
    {
        $vendorId = Auth::id();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pharmacy Sales Report Overall');
    
        $data = [];
        switch ($period) {
            case 'today':
                $data = SalesByPharmacy::where('vendor_id', $vendorId)
                    ->whereDate('created_at', Carbon::today())
                    ->get(['product_name', 'product_price', 'product_order_quantity', 'sales as sale', 'created_at']);
                $fileName = "Pharmacy_Sales_Report_Today_" . now()->format('Y-m-d') . ".xlsx";
                $sheet->setTitle("Today's Sales Report");
                break;
    
            case 'week':
                $data = SalesByPharmacy::where('vendor_id', $vendorId)
                    ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                    ->get(['product_name', 'product_price', 'product_order_quantity', 'sales as sale', 'created_at']);
                $fileName = "Pharmacy_Sales_Report_This_Week_" . now()->format('Y-m-d') . ".xlsx";
                $sheet->setTitle("This Week's Sales Report");
                break;
    
            case 'month':
                $data = SalesByPharmacy::where('vendor_id', $vendorId)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->get(['product_name', 'product_price', 'product_order_quantity', 'sales as sale', 'created_at']);
                $fileName = "Pharmacy_Sales_Report_This_Month_" . now()->format('Y-m-d') . ".xlsx";
                $sheet->setTitle("This Month's Sales Report");
                break;
    
            case 'year':
                $data = SalesByPharmacy::where('vendor_id', $vendorId)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->get(['product_name', 'product_price', 'product_order_quantity', 'sales as sale', 'created_at']);
                $fileName = "Pharmacy_Sales_Report_This_Year_" . now()->format('Y-m-d') . ".xlsx";
                $sheet->setTitle("This Year's Sales Report");
                break;
    
            case 'overall':
                $data = SalesByPharmacy::where('vendor_id', $vendorId)
                    ->get(['product_name', 'product_price', 'product_order_quantity', 'sales as sale', 'created_at']);
                $fileName = "Pharmacy_Sales_Report_Overall_" . now()->format('Y-m-d') . ".xlsx";
                $sheet->setTitle("Overall Sales Report");
                break;
    
            default:
                return back()->with('error', 'Invalid period specified');
        }
    
        if ($data->isEmpty()) {
            return back()->with('error', 'No sales data available for the selected period.');
        }
    
        $totalSales = $data->sum('sale');
    
        $headers = ['Product Name', 'Product Price', 'Order Quantity', 'Order Cost', 'Sale', 'Date'];
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        foreach (range('B', 'F') as $column) {
            $sheet->getStyle($column)->getAlignment()->setHorizontal('center');
        }
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
    
        $formattedData = $data->map(function ($item) use ($period) {
            $dateFormat = match ($period) {
                'today' => 'h:i A',
                'week', 'month' => 'l, m/d/Y',
                'year', 'overall' => 'M Y',
                default => 'Y-m-d',
            };
    
            return [
                $item->product_name,
                '₱' . number_format($item->product_price, 2),
                $item->product_order_quantity,
                '₱' . number_format($item->product_price * $item->product_order_quantity, 2),
                '₱' . number_format($item->sale, 2),
                Carbon::parse($item->created_at)->format($dateFormat),
            ];
        });
    
        $sheet->fromArray($formattedData->toArray(), null, 'A2');
    
        $sheet->setCellValue('D' . ($formattedData->count() + 2), 'Total Sales');
        $sheet->setCellValue('E' . ($formattedData->count() + 2), '₱' . number_format($totalSales, 2));
        $sheet->getStyle('D' . ($formattedData->count() + 2) . ':E' . ($formattedData->count() + 2))->getFont()->setBold(true);
    
        $writer = new Xlsx($spreadsheet);
    
        return Response::streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}