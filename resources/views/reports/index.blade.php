@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        min-height: 100vh;
    }

    .reports-wrapper {
        padding: 30px 0 50px;
        animation: fadeIn 0.6s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .page-header {
        background: white;
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .page-title {
        font-size: 32px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .report-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .report-card:hover {
        box-shadow: 0 8px 20px rgba(13, 27, 42, 0.15);
    }

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .report-title {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .report-title i {
        color: #0d1b2a;
    }

    .filter-section {
        background: #f9fafb;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .chart-container {
        position: relative;
        height: 300px;
        margin-top: 20px;
    }

    .btn-export {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        color: white;
    }

    .stat-box {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 16px;
        text-align: center;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin: 8px 0;
    }

    .stat-label {
        font-size: 13px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<div class="reports-wrapper">
    <div class="container-fluid">
        <!-- Page Header -->
        <!-- <div class="page-header">
            <h1 class="page-title">
                <i class="bi bi-graph-up-arrow"></i>
                Reports & Analytics
            </h1>
            <p class="text-muted mb-0">Comprehensive insights into your reception system</p>
        </div> -->

        <!-- Visitor Reports -->
        <div class="report-card">
            <div class="report-header">
                <div class="report-title">
                    <i class="bi bi-people-fill"></i>
                    Visitor Reports
                </div>
                <a href="{{ route('reports.export', ['type' => 'visitor', 'format' => 'csv']) }}" class="btn-export">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            </div>

            <div class="filter-section">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Period</label>
                        <select id="visitorPeriod" class="form-select">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Start Date</label>
                        <input type="date" id="visitorStartDate" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">End Date</label>
                        <input type="date" id="visitorEndDate" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-primary w-100" onclick="loadVisitorReports()">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="stat-box">
                        <div class="stat-label">Total Visitors</div>
                        <div class="stat-number" id="visitorTotal">0</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <div class="stat-label">Checked In</div>
                        <div class="stat-number" id="visitorCheckedIn">0</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <div class="stat-label">Checked Out</div>
                        <div class="stat-number" id="visitorCheckedOut">0</div>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <canvas id="visitorChart"></canvas>
            </div>
        </div>

        <!-- Staff Performance -->
        <div class="report-card">
            <div class="report-header">
                <div class="report-title">
                    <i class="bi bi-person-badge-fill"></i>
                    Staff Performance
                </div>
                <a href="{{ route('reports.export', ['type' => 'staff', 'format' => 'csv']) }}" class="btn-export">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            </div>

            <div class="filter-section">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Start Date</label>
                        <input type="date" id="staffStartDate" class="form-control" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">End Date</label>
                        <input type="date" id="staffEndDate" class="form-control" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-primary w-100" onclick="loadStaffPerformance()">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <canvas id="staffChart"></canvas>
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-hover" id="staffTable">
                    <thead>
                        <tr>
                            <th>Staff Name</th>
                            <th>Total Visitors</th>
                            <th>Total Appointments</th>
                            <th>Confirmed</th>
                            <th>Pending</th>
                            <th>Canceled</th>
                        </tr>
                    </thead>
                    <tbody id="staffTableBody">
                        <tr>
                            <td colspan="6" class="text-center text-muted">Click search to load data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Appointment Statistics -->
        <div class="report-card">
            <div class="report-header">
                <div class="report-title">
                    <i class="bi bi-calendar-event-fill"></i>
                    Appointment Statistics
                </div>
                <a href="{{ route('reports.export', ['type' => 'appointment', 'format' => 'csv']) }}" class="btn-export">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            </div>

            <div class="filter-section">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Start Date</label>
                        <input type="date" id="appointmentStartDate" class="form-control" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">End Date</label>
                        <input type="date" id="appointmentEndDate" class="form-control" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-primary w-100" onclick="loadAppointmentStats()">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-label">Total Appointments</div>
                        <div class="stat-number" id="appointmentTotal">0</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                        <div class="stat-label">Pending</div>
                        <div class="stat-number" id="appointmentPending">0</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);">
                        <div class="stat-label">Confirmed</div>
                        <div class="stat-number" id="appointmentConfirmed">0</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);">
                        <div class="stat-label">Canceled</div>
                        <div class="stat-number" id="appointmentCanceled">0</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-3">Status Breakdown</h6>
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="appointmentStatusChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-3">Hourly Distribution</h6>
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="appointmentHourlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visitor Trends -->
        <div class="report-card">
            <div class="report-header">
                <div class="report-title">
                    <i class="bi bi-graph-up"></i>
                    Visitor Trends
                </div>
            </div>

            <div class="filter-section">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Days</label>
                        <select id="trendDays" class="form-select">
                            <option value="7">Last 7 Days</option>
                            <option value="14">Last 14 Days</option>
                            <option value="30" selected>Last 30 Days</option>
                            <option value="60">Last 60 Days</option>
                            <option value="90">Last 90 Days</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-primary w-100" onclick="loadVisitorTrends()">
                            <i class="bi bi-search"></i> Load
                        </button>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <canvas id="trendsChart"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    let visitorChart, staffChart, appointmentStatusChart, appointmentHourlyChart, trendsChart;

    // Visitor Reports
    function loadVisitorReports() {
        const period = document.getElementById('visitorPeriod').value;
        const startDate = document.getElementById('visitorStartDate').value;
        const endDate = document.getElementById('visitorEndDate').value;

        let url = `/reports/visitors?period=${period}`;
        if (startDate && endDate) {
            url += `&start_date=${startDate}&end_date=${endDate}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                document.getElementById('visitorTotal').textContent = data.total;
                document.getElementById('visitorCheckedIn').textContent = data.checked_in;
                document.getElementById('visitorCheckedOut').textContent = data.checked_out;

                if (visitorChart) visitorChart.destroy();
                const ctx = document.getElementById('visitorChart').getContext('2d');
                visitorChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Visitors',
                            data: data.values,
                            backgroundColor: 'rgba(13, 27, 42, 0.6)',
                            borderColor: '#0d1b2a',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
            })
            .catch(err => console.error('Error loading visitor reports:', err));
    }

    // Staff Performance
    function loadStaffPerformance() {
        const startDate = document.getElementById('staffStartDate').value;
        const endDate = document.getElementById('staffEndDate').value;

        fetch(`/reports/staff?start_date=${startDate}&end_date=${endDate}`)
            .then(res => res.json())
            .then(data => {
                if (staffChart) staffChart.destroy();
                const ctx = document.getElementById('staffChart').getContext('2d');
                staffChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Visitors',
                                data: data.visitor_counts,
                                backgroundColor: 'rgba(13, 27, 42, 0.6)',
                                borderColor: '#0d1b2a',
                                borderWidth: 2
                            },
                            {
                                label: 'Appointments',
                                data: data.appointment_counts,
                                backgroundColor: 'rgba(16, 185, 129, 0.6)',
                                borderColor: '#10b981',
                                borderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });

                // Update table
                const tbody = document.getElementById('staffTableBody');
                tbody.innerHTML = data.staff.map(s => `
                    <tr>
                        <td>${s.name}</td>
                        <td>${s.total_visitors}</td>
                        <td>${s.total_appointments}</td>
                        <td>${s.confirmed_appointments}</td>
                        <td>${s.pending_appointments}</td>
                        <td>${s.canceled_appointments}</td>
                    </tr>
                `).join('');
            })
            .catch(err => console.error('Error loading staff performance:', err));
    }

    // Appointment Statistics
    function loadAppointmentStats() {
        const startDate = document.getElementById('appointmentStartDate').value;
        const endDate = document.getElementById('appointmentEndDate').value;

        fetch(`/reports/appointments?start_date=${startDate}&end_date=${endDate}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('appointmentTotal').textContent = data.total;
                document.getElementById('appointmentPending').textContent = data.status_breakdown.pending;
                document.getElementById('appointmentConfirmed').textContent = data.status_breakdown.confirmed;
                document.getElementById('appointmentCanceled').textContent = data.status_breakdown.canceled;

                // Status Chart
                if (appointmentStatusChart) appointmentStatusChart.destroy();
                const statusCtx = document.getElementById('appointmentStatusChart').getContext('2d');
                appointmentStatusChart = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending', 'Confirmed', 'Canceled'],
                        datasets: [{
                            data: [
                                data.status_breakdown.pending,
                                data.status_breakdown.confirmed,
                                data.status_breakdown.canceled
                            ],
                            backgroundColor: ['#f59e0b', '#10b981', '#ef4444']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });

                // Hourly Chart
                if (appointmentHourlyChart) appointmentHourlyChart.destroy();
                const hourlyCtx = document.getElementById('appointmentHourlyChart').getContext('2d');
                appointmentHourlyChart = new Chart(hourlyCtx, {
                    type: 'line',
                    data: {
                        labels: data.hourly_distribution.labels,
                        datasets: [{
                            label: 'Appointments',
                            data: data.hourly_distribution.values,
                            borderColor: '#0d1b2a',
                            backgroundColor: 'rgba(13, 27, 42, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
            })
            .catch(err => console.error('Error loading appointment stats:', err));
    }

    // Visitor Trends
    function loadVisitorTrends() {
        const days = document.getElementById('trendDays').value;

        fetch(`/reports/trends?days=${days}`)
            .then(res => res.json())
            .then(data => {
                if (trendsChart) trendsChart.destroy();
                const ctx = document.getElementById('trendsChart').getContext('2d');
                trendsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Visitors',
                                data: data.visitors,
                                borderColor: '#0d1b2a',
                                backgroundColor: 'rgba(13, 27, 42, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Appointments',
                                data: data.appointments,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
            })
            .catch(err => console.error('Error loading trends:', err));
    }

    // Load initial data
    document.addEventListener('DOMContentLoaded', function() {
        loadVisitorReports();
        loadStaffPerformance();
        loadAppointmentStats();
        loadVisitorTrends();
    });
</script>
@endsection

