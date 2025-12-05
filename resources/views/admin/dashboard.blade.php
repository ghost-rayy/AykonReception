@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        min-height: 100vh;
    }

    .admin-dashboard-wrapper {
        padding: 30px 0 50px;
        animation: fadeIn 0.6s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: none;
        border-radius: 16px;
        padding: 28px 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        height: 100%;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #0d1b2a;
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 28px rgba(13, 27, 42, 0.15);
    }

    .stat-card:hover::before {
        transform: scaleX(1);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        font-size: 24px;
        color: white;
        background: #0d1b2a;
    }

    .stat-label {
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-number {
        font-size: 32px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .section-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
    }

    .section-header {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-header i {
        color: #0d1b2a;
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .chart-container canvas {
        max-height: 300px !important;
    }

    .activity-item {
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f7ff;
        color: #0d1b2a;
    }

    .quick-action-btn {
        background: #0d1b2a;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(13, 27, 42, 0.3);
        color: white;
    }
</style>

<div class="admin-dashboard-wrapper">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1" style="font-size: 32px; font-weight: 700; color: #1f2937;">
                    <i class="bi bi-shield-check text-primary"></i> Admin Dashboard
                </h1>
                <p class="text-muted">Complete system overview and management</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users') }}" class="quick-action-btn">
                    <i class="bi bi-people"></i> Manage Users
                </a>
                <!-- <a href="{{ route('admin.settings') }}" class="quick-action-btn" style="background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);">
                    <i class="bi bi-gear"></i> Settings
                </a> -->
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="row g-4 mb-4">
            <!-- Users Statistics -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-number">{{ $stats['total_users'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%);">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div class="stat-label">Staff Members</div>
                    <div class="stat-number">{{ $stats['total_staff'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <div class="stat-label">Receptionists</div>
                    <div class="stat-number">{{ $stats['total_receptionists'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);">
                        <i class="bi bi-shield-fill"></i>
                    </div>
                    <div class="stat-label">Admins</div>
                    <div class="stat-number">{{ $stats['total_admins'] }}</div>
                </div>
            </div>

            <!-- Visitors Statistics -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div class="stat-label">Visitors Today</div>
                    <div class="stat-number">{{ $stats['visitors_today'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%);">
                        <i class="bi bi-door-open-fill"></i>
                    </div>
                    <div class="stat-label">Currently Inside</div>
                    <div class="stat-number">{{ $stats['checked_in_now'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%);">
                        <i class="bi bi-calendar-event-fill"></i>
                    </div>
                    <div class="stat-label">Appointments Today</div>
                    <div class="stat-number">{{ $stats['appointments_today'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #14b8a6 0%, #2dd4bf 100%);">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="stat-label">Pending Appointments</div>
                    <div class="stat-number">{{ $stats['appointments_pending'] }}</div>
                </div>
            </div>
        </div>

        <!-- Charts and Recent Activity Row -->
        <div class="row g-4">
            <!-- Visitor Trends Chart -->
            <div class="col-md-6">
                <div class="section-card">
                    <div class="section-header">
                        <i class="bi bi-graph-up"></i>
                        Visitor Trends (Last 7 Days)
                    </div>
                    <div class="chart-container">
                        <canvas id="visitorTrendsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Appointment Status Breakdown -->
            <div class="col-md-6">
                <div class="section-card">
                    <div class="section-header">
                        <i class="bi bi-pie-chart"></i>
                        Appointment Status Breakdown
                    </div>
                    <div class="chart-container">
                        <canvas id="appointmentStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <div class="section-card">
                    <div class="section-header">
                        <i class="bi bi-clock-history"></i>
                        Recent Visitors
                    </div>
                    <div>
                        @forelse($recentVisitors as $visitor)
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="bi bi-person"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $visitor->name }}</div>
                                <small class="text-muted">
                                    {{ $visitor->purpose }} • {{ $visitor->user->name ?? 'N/A' }}
                                </small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">{{ $visitor->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center py-3">No recent visitors</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="section-card">
                    <div class="section-header">
                        <i class="bi bi-calendar-check"></i>
                        Recent Appointments
                    </div>
                    <div>
                        @forelse($recentAppointments as $appointment)
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $appointment->visitor_name }}</div>
                                <small class="text-muted">
                                    {{ $appointment->purpose }} • {{ $appointment->user->name ?? 'N/A' }}
                                </small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_time)->diffForHumans() }}</small>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center py-3">No recent appointments</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-4 mt-2">
            <div class="col-md-12">
                <div class="section-card">
                    <div class="section-header">
                        <i class="bi bi-lightning-charge"></i>
                        Quick Actions
                    </div>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('admin.users') }}" class="btn btn-primary">
                            <i class="bi bi-people me-2"></i>Manage Users
                        </a>
                        <!-- <a href="{{ route('admin.settings') }}" class="btn btn-secondary">
                            <i class="bi bi-gear me-2"></i>System Settings
                        </a>
                        <a href="{{ route('admin.logs') }}" class="btn btn-info text-white">
                            <i class="bi bi-journal-text me-2"></i>View Logs
                        </a>
                        <a href="{{ route('admin.health') }}" class="btn btn-success">
                            <i class="bi bi-heart-pulse me-2"></i>System Health
                        </a> -->
                        <a href="{{ route('reports.index') }}" class="btn btn-warning text-white">
                            <i class="bi bi-graph-up me-2"></i>Reports
                        </a>
                        <a href="{{ route('admin.export', ['type' => 'visitors', 'format' => 'csv']) }}" class="btn btn-dark">
                            <i class="bi bi-download me-2"></i>Export Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Store chart instances to prevent recreation
    let visitorTrendsChartInstance = null;
    let appointmentStatusChartInstance = null;

    // Function to initialize charts only once
    function initializeCharts() {
        // Only create charts if they don't exist
        if (!visitorTrendsChartInstance && !appointmentStatusChartInstance) {
            // Visitor Trends Chart
            const visitorTrendsCtx = document.getElementById('visitorTrendsChart');
            if (visitorTrendsCtx) {
                const visitorTrendsData = @json($visitorTrends);
                
                visitorTrendsChartInstance = new Chart(visitorTrendsCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: visitorTrendsData.map(d => d.date),
                        datasets: [{
                            label: 'Visitors',
                            data: visitorTrendsData.map(d => d.count),
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
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        animation: {
                            duration: 1000
                        }
                    }
                });
            }

            // Appointment Status Chart
            const appointmentStatusCtx = document.getElementById('appointmentStatusChart');
            if (appointmentStatusCtx) {
                const appointmentStatusData = @json($appointmentStatusBreakdown);
                
                appointmentStatusChartInstance = new Chart(appointmentStatusCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending', 'Confirmed', 'Canceled'],
                        datasets: [{
                            data: [
                                appointmentStatusData.pending,
                                appointmentStatusData.confirmed,
                                appointmentStatusData.canceled
                            ],
                            backgroundColor: [
                                '#f59e0b',
                                '#10b981',
                                '#ef4444'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        animation: {
                            duration: 1000
                        }
                    }
                });
            }
        }
    }

    // Initialize charts when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeCharts);
    } else {
        initializeCharts();
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (visitorTrendsChartInstance) {
            visitorTrendsChartInstance.destroy();
            visitorTrendsChartInstance = null;
        }
        if (appointmentStatusChartInstance) {
            appointmentStatusChartInstance.destroy();
            appointmentStatusChartInstance = null;
        }
    });
</script>
@endsection

