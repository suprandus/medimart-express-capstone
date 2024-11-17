<!-- sales-reports.blade.php -->
@extends('admin.layouts.master')

@section('content')
<br><br>
<div class="detail-value">${{$todaySales}}</div>
<div class="detail-value">${{$weekSales}}</div>
<div class="detail-value">${{$monthSales}}</div>
<div class="detail-value">${{$yearSales}}</div>
<div class="row">
    <div class="col-lg-8 col-md-12 col-12 col-sm-12">
      <div class="card">
        <div class="card-header">
          <h4>Statistics</h4>
          <div class="card-header-action">
            <div class="btn-group">
                <a href="#" id="btn-overall" class="btn active">Overall</a>
                <a href="#" id="btn-today" class="btn">Today</a>
                <a href="#" id="btn-week" class="btn">Week</a>
                <a href="#" id="btn-month" class="btn">Month</a>
                <a href="#" id="btn-year" class="btn">Year</a>
            </div>            
          </div>
        </div>
        <div class="card-body">
          <canvas id="myChart" height="182"></canvas>
          <div class="statistic-details mt-sm-4">
            <div class="statistic-details-item">
                <div class="detail-value today-sales">${{$todaySales}}</div>
                <div class="detail-name">Today's Sales</div>
              </div>
              <div class="statistic-details-item">
                <div class="detail-value week-sales">${{$weekSales}}</div>
                <div class="detail-name">This Week's Sales</div>
              </div>
              <div class="statistic-details-item">
                <div class="detail-value month-sales">${{$monthSales}}</div>
                <div class="detail-name">This Month's Sales</div>
              </div>
              <div class="statistic-details-item">
                <div class="detail-value year-sales">${{$yearSales}}</div>
                <div class="detail-name">This Year's Sales</div>
              </div>
          </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('myChart').getContext('2d');
    const todaySalesElement = document.querySelector(".today-sales");
    const weekSalesElement = document.querySelector(".week-sales");
    const monthSalesElement = document.querySelector(".month-sales");
    const yearSalesElement = document.querySelector(".year-sales");

    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Sales Over Time',
                data: @json($sales),
                backgroundColor: 'rgba(58, 123, 213, 0.1)',
                borderColor: '#3a7bd5',
                borderWidth: 2,
                tension: 0.4
            }]
        },
        options: {
            scales: {
                x: { beginAtZero: true },
                y: { beginAtZero: true }
            }
        }
    });

    async function fetchSalesData(period) {
        try {
            const response = await fetch(`/sales-data?period=${period}`);
            if (!response.ok) {
                console.error(`Failed to fetch data: ${response.statusText}`);
                return { labels: [], sales: [], todaySales: 0, weekSales: 0, monthSales: 0, yearSales: 0 };
            }
            const data = await response.json();
            return data;

        } catch (error) {
            console.error("Error fetching data:", error);
            return { labels: [], sales: [], todaySales: 0, weekSales: 0, monthSales: 0, yearSales: 0 };
        }
    }

    async function updateChart(period) {
        const data = await fetchSalesData(period);

        if (data && data.labels && data.sales) {
            myChart.data.labels = data.labels;
            myChart.data.datasets[0].data = data.sales;
            myChart.update();
        } else {
            console.error("No data found to plot on the chart.");
        }

        todaySalesElement.textContent = `$${data.todaySales || 0}`;
        weekSalesElement.textContent = `$${data.weekSales || 0}`;
        monthSalesElement.textContent = `$${data.monthSales || 0}`;
        yearSalesElement.textContent = `$${data.yearSales || 0}`;
    }

    function handleButtonClick(period) {
        updateChart(period);

        document.querySelectorAll('.btn-group .btn').forEach(button => button.classList.remove('active'));
        const selectedButton = document.getElementById(`btn-${period}`);
        if (selectedButton) {
            selectedButton.classList.add('active');
        }
    }

    document.getElementById('btn-overall').addEventListener('click', (e) => {
        e.preventDefault();
        handleButtonClick('overall');
    });
    document.getElementById('btn-today').addEventListener('click', (e) => {
        e.preventDefault();
        handleButtonClick('day');
    });
    document.getElementById('btn-week').addEventListener('click', (e) => {
        e.preventDefault();
        handleButtonClick('week');
    });
    document.getElementById('btn-month').addEventListener('click', (e) => {
        e.preventDefault();
        handleButtonClick('month');
    });
    document.getElementById('btn-year').addEventListener('click', (e) => {
        e.preventDefault();
        handleButtonClick('year');
    });

    handleButtonClick('overall');
</script>
@endsection