@extends('vendor.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Pharmacy Sales</h1>
    </div>

    <!-- LINE GRAPH -->
    <div class="row">
        <div class="col-lg-8 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Your Sales</h4>
                    <div class="card-header-action">
                        <div class="btn-group" role="group" aria-label="Time Periods">
                            <button type="button" class="btn btn-outline-primary" id="btn-overall">Overall</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-today">Today</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-week">Week</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-month">Month</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-year">Year</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="myChart" height="182"></canvas>
                    <div id="noSalesMessage">No sales for selected period</div>
                    <div class="statistic-details mt-sm-4">
                        <div class="statistic-details-item">
                            <div class="detail-value today-sales">₱{{ number_format($totalTodaySales, 2) }}</div>
                            <div class="detail-name">Today's Sales</div>
                        </div>
                        <div class="statistic-details-item">
                            <div class="detail-value week-sales">₱{{ number_format($totalWeekSales, 2) }}</div>
                            <div class="detail-name">This Week's Sales</div>
                        </div>
                        <div class="statistic-details-item">
                            <div class="detail-value month-sales">₱{{ number_format($totalMonthSales, 2) }}</div>
                            <div class="detail-name">This Month's Sales</div>
                        </div>
                        <div class="statistic-details-item">
                            <div class="detail-value month-sales">₱{{ number_format($totalYearSales, 2) }}</div>
                            <div class="detail-name">This Years's Sales</div>
                        </div>
                        <div class="statistic-details-item">
                            <div class="detail-value overall-sales">₱{{ number_format($totalOverallSales, 2) }}</div>
                            <div class="detail-name">Overall Sales</div>
                        </div>
                    </div>
                    <div class="text-right mt-3">
                        <button type="button" class="btn btn-warning" id="btn-export-pdf">Export to PDF</button>
                        <button type="button" class="btn btn-success" id="btn-export-excel">Export to Excel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1"></script>
<script>
    // Line Chart Initialization
    const ctx = document.getElementById('myChart').getContext('2d');
    const dataSets = {
        overall: {
            labels: @json($overallLabels),
            sales: @json($overallSales)
        },
        today: {
            labels: @json($todayLabels),
            sales: @json($todaySales)
        },
        week: {
            labels: @json($weekLabels),
            sales: @json($weekSales)
        },
        month: {
            labels: @json($monthLabels),
            sales: @json($monthSales)
        },
        year: {
            labels: @json($yearLabels),
            sales: @json($yearSales)
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

    function updateChart(period) {
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

        document.querySelectorAll('.btn-group .btn').forEach(button => button.classList.remove('active'));
        document.getElementById(`btn-${period}`).classList.add('active');
    }

    document.getElementById('btn-overall').addEventListener('click', () => updateChart('overall'));
    document.getElementById('btn-today').addEventListener('click', () => updateChart('today'));
    document.getElementById('btn-week').addEventListener('click', () => updateChart('week'));
    document.getElementById('btn-month').addEventListener('click', () => updateChart('month'));
    document.getElementById('btn-year').addEventListener('click', () => updateChart('year'));

    document.getElementById('btn-export-pdf').addEventListener('click', () => {
        const period = document.querySelector('.btn-group .btn.active').id.replace('btn-', '');
        window.location.href = `sales-pharmacy-pdf/${period}`;
    });
    document.getElementById('btn-export-excel').addEventListener('click', () => {
        const period = document.querySelector('.btn-group .btn.active').id.replace('btn-', '');
        window.location.href = `sales/export/${period}`;
    });

    updateChart('overall');
</script>
@endpush