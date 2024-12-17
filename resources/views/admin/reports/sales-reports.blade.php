@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Sales</h1>
    </div>

    <!-- LINE CHART -->
    <div class="row">
        <div class="col-lg-8 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>MediMart Express Sales</h4>
                    <div class="card-header-action">
                        <div class="btn-group btn-group-line btn-medimart" role="group" aria-label="Time Periods">
                            <button type="button" class="btn btn-outline-primary active"
                                id="btn-medimart-overall">Overall</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-medimart-today">Today</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-medimart-week">This
                                Week</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-medimart-month">This
                                Month</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-medimart-year">This
                                Year</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="salesLineChart" height="182"></canvas>
                    <div id="noSalesMessage" style="display: none; text-align: center; color: gray; font-size: 1.5em;">
                        No sales for this period</div>
                    <div class="statistic-details mt-sm-4">
                        <div class="statistic-details-item">
                            <div class="detail-value today-sales">₱{{ number_format($totalTodaySalesMedimart, 2) }}
                            </div>
                            <div class="detail-name">Today's Sales</div>
                        </div>
                        <div class="statistic-details-item">
                            <div class="detail-value week-sales">₱{{ number_format($totalWeekSalesMedimart, 2) }}</div>
                            <div class="detail-name">This Week's Sales</div>
                        </div>
                        <div class="statistic-details-item">
                            <div class="detail-value month-sales">₱{{ number_format($totalMonthSalesMedimart, 2) }}
                            </div>
                            <div class="detail-name">This Month's Sales</div>
                        </div>
                        <div class="statistic-details-item">
                            <div class="detail-value month-sales">₱{{ number_format($totalYearSalesMedimart, 2) }}</div>
                            <div class="detail-name">This Years's Sales</div>
                        </div>
                        <div class="statistic-details-item">
                            <div class="detail-value overall-sales">₱{{ number_format($totalOverallSalesMedimart, 2) }}
                            </div>
                            <div class="detail-name">Overall Sales</div>
                        </div>
                    </div>
                    <div class="text-right mt-3">
                        <button type="button" class="btn btn-warning" id="btn-line-export-pdf">Export To PDF</button>
                        <button type="button" class="btn btn-success" id="btn-line-export-excel">Export to
                            Excel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BAR CHART -->
    <div class="row">
        <div class="col-lg-8 col-md-12 col-12 col-sm-12 mt-4">
            <div class="card">
                <div class="card-header">
                    <h4>MediMart Express Sales by Pharmacy</h4>
                    <div class="card-header-action">
                        <div class="btn-group btn-group-bar btn-pharmacy" role="group" aria-label="Time Periods">
                            <button type="button" class="btn btn-outline-primary"
                                id="btn-pharmacy-overall">Overall</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-pharmacy-today">Today</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-pharmacy-week">This
                                Week</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-pharmacy-month">This
                                Month</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-pharmacy-year">This
                                Year</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="salesBarChart" height="182"></canvas>
                    <div id="noSalesMessageBar"
                        style="display: none; text-align: center; color: gray; font-size: 1.5em;">No sales for this
                        period</div>
                </div>
                <div class="text-right mt-3" style="padding-right: 25px; padding-bottom: 25px;">
                    <button type="button" class="btn btn-warning" id="btn-bar-export-pdf">Export to PDF</button>
                    <button type="button" class="btn btn-success" id="btn-bar-export-excel">Export to Excel</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1"></script>
    <script>
        //MEDIMART SALES LINE CHART
        const ctx = document.getElementById('salesLineChart').getContext('2d');
        const dataSets = {
            overall: {
                labels: @json($overallLabelsMedimart),
                sales: @json($overallSalesMedimart)
            },
            today: {
                labels: @json($todayLabelsMedimart),
                sales: @json($todaySalesMedimart)
            },
            week: {
                labels: @json($weekLabelsMedimart),
                sales: @json($weekSalesMedimart)
            },
            month: {
                labels: @json($monthLabelsMedimart),
                sales: @json($monthSalesMedimart)
            },
            year: {
                labels: @json($yearLabelsMedimart),
                sales: @json($yearSalesMedimart)
            }
        };

        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dataSets.overall.labels,
                datasets: [{
                    label: 'Sales Over Time',
                    data: dataSets.overall.sales,
                    backgroundColor: 'rgba(58, 123, 213, 0.1)',
                    borderColor: '#3a7bd5',
                    borderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        function updateLineChart(period) {
            const salesData = dataSets[period].sales;
            const labels = dataSets[period].labels;

            if (salesData.length === 0) {
                document.getElementById('noSalesMessage').style.display = 'block';
                myChart.data.labels = [];
                myChart.data.datasets[0].data = [];
            } else {
                document.getElementById('noSalesMessage').style.display = 'none';
                myChart.data.labels = labels;
                myChart.data.datasets[0].data = salesData;
            }

            myChart.update();

            document.querySelectorAll('.btn-group-line .btn').forEach(button => button.classList.remove('active'));
            document.getElementById(`btn-medimart-${period}`).classList.add('active');
        }

        document.getElementById('btn-medimart-overall').addEventListener('click', () => updateLineChart('overall'));
        document.getElementById('btn-medimart-today').addEventListener('click', () => updateLineChart('today'));
        document.getElementById('btn-medimart-week').addEventListener('click', () => updateLineChart('week'));
        document.getElementById('btn-medimart-month').addEventListener('click', () => updateLineChart('month'));
        document.getElementById('btn-medimart-year').addEventListener('click', () => updateLineChart('year'));

        document.getElementById('btn-line-export-pdf').addEventListener('click', () => {
            const period = document.querySelector('.btn-group-line .btn.active').id.replace('btn-medimart-', '');
            window.location.href = `/admin/reports/export-sales-medimart-pdf/${period}`;
        });
        document.getElementById('btn-line-export-excel').addEventListener('click', () => {
            const period = document.querySelector('.btn-group-line .btn.active').id.replace('btn-medimart-', '');
            window.location.href = `/admin/reports/sales/export-line-graph/${period}`;
        });
        updateLineChart('overall');

        //MEDIMART SALES BY PHARMACY BAR CHART
        const barCtx = document.getElementById('salesBarChart').getContext('2d');
        const barDataSets = {
            overall: {
                labels: @json($pharmacyNames), 
                periodLabels: @json($overallLabelsPharmacy),
                sales: @json($overallSalesPharmacy),
            },
            today: {
                labels: @json($pharmacyNames), 
                periodLabels: @json($todayLabelsPharmacy),
                sales: @json($todaySalesPharmacy),
            },
            week: {
                labels: @json($pharmacyNames), 
                periodLabels: @json($weekLabelsPharmacy),
                sales: @json($weekSalesPharmacy),
            },
            month: {
                labels: @json($pharmacyNames), 
                periodLabels: @json($monthLabelsPharmacy),
                sales: @json($monthSalesPharmacy),
            },
            year: {
                labels: @json($pharmacyNames), 
                periodLabels: @json($yearLabelsPharmacy),
                sales: @json($yearSalesPharmacy),
            }
        };

        const salesBarChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: barDataSets.overall.labels,
                datasets: [{
                    label: 'Total Sales',
                    data: barDataSets.overall.sales,
                    backgroundColor: 'rgba(58, 123, 213, 0.5)',
                    borderColor: '#3a7bd5',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Pharmacy Name'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales (₱)'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const periodLabel = barDataSets[context.chart.activePeriod].periodLabels[context.dataIndex];
                                const sales = context.raw.toLocaleString();
                                return `${periodLabel}: ₱${sales}`;
                            },
                            title: function(context) {
                                return context[0].label;
                            }
                        }
                    }
                }
            }
        });

        function updateBarChart(period) {
            const labels = barDataSets[period].labels;
            const periodLabels = barDataSets[period].periodLabels;
            const salesData = barDataSets[period].sales;

            const filteredLabels = [];
            const filteredSales = [];
            const filteredPeriodLabels = [];
            salesData.forEach((sale, index) => {
                if (sale > 0) {
                    filteredLabels.push(labels[index]);
                    filteredSales.push(sale);
                    filteredPeriodLabels.push(periodLabels[index]);
                }
            });
            salesBarChart.activePeriod = period;

            if (filteredSales.length === 0) {
                document.getElementById('noSalesMessageBar').style.display = 'block';
                salesBarChart.data.labels = [];
                salesBarChart.data.datasets[0].data = [];
            } else {
                document.getElementById('noSalesMessageBar').style.display = 'none';
                salesBarChart.data.labels = filteredLabels;
                salesBarChart.data.datasets[0].data = filteredSales;
            }
            barDataSets[period].filteredPeriodLabels = filteredPeriodLabels;
            salesBarChart.update();

            document.querySelectorAll('.btn-group-bar .btn').forEach(button => button.classList.remove('active'));
            document.getElementById(`btn-pharmacy-${period}`).classList.add('active');
        }

        document.getElementById('btn-pharmacy-overall').addEventListener('click', () => updateBarChart('overall'));
        document.getElementById('btn-pharmacy-today').addEventListener('click', () => updateBarChart('today'));
        document.getElementById('btn-pharmacy-week').addEventListener('click', () => updateBarChart('week'));
        document.getElementById('btn-pharmacy-month').addEventListener('click', () => updateBarChart('month'));
        document.getElementById('btn-pharmacy-year').addEventListener('click', () => updateBarChart('year'));

        document.getElementById('btn-bar-export-pdf').addEventListener('click', () => {
            const period = document.querySelector('.btn-group-bar .btn.active').id.replace('btn-pharmacy-', '');
            window.location.href = `/admin/reports/export-sales-by-pharmacy-pdf/${period}`;
        });
        document.getElementById('btn-bar-export-excel').addEventListener('click', () => {
            const period = document.querySelector('.btn-group-bar .btn.active').id.replace('btn-pharmacy-', '');
            window.location.href = `/admin/reports/sales/export-bar-graph/${period}`;
        });
        
        updateBarChart('overall');
    </script>
</section>
@endsection