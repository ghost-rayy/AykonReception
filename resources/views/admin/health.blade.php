@extends('layouts.app')

@section('title', 'System Health')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        min-height: 100vh;
    }

    .health-container {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin: 30px auto;
        max-width: 1000px;
    }

    .health-header {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }

    .health-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .health-item {
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        border-left: 4px solid;
    }

    .health-item.healthy {
        background: #f0fdf4;
        border-color: #10b981;
    }

    .health-item.warning {
        background: #fffbeb;
        border-color: #f59e0b;
    }

    .health-item.error {
        background: #fef2f2;
        border-color: #ef4444;
    }

    .health-item.caution {
        background: #fef3c7;
        border-color: #f59e0b;
    }

    .health-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .health-status.healthy {
        background: #10b981;
        color: white;
    }

    .health-status.warning {
        background: #f59e0b;
        color: white;
    }

    .health-status.error {
        background: #ef4444;
        color: white;
    }

    .health-status.caution {
        background: #f59e0b;
        color: white;
    }

    .progress {
        height: 24px;
        border-radius: 12px;
    }
</style>

<div class="container-fluid" style="padding: 30px 0 50px;">
    <div class="health-container">
        <div class="health-header">
            <h1>
                <i class="bi bi-heart-pulse text-primary"></i>
                System Health Check
            </h1>
        </div>

        <!-- Database Health -->
        <div class="health-item {{ $health['database']['status'] }}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h5 class="mb-1">
                        <i class="bi bi-database me-2"></i>Database Connection
                    </h5>
                    <p class="mb-0 text-muted">{{ $health['database']['message'] }}</p>
                </div>
                <span class="health-status {{ $health['database']['status'] }}">
                    {{ ucfirst($health['database']['status']) }}
                </span>
            </div>
        </div>

        <!-- Storage Health -->
        <div class="health-item {{ $health['storage']['status'] }}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h5 class="mb-1">
                        <i class="bi bi-hdd me-2"></i>Storage
                    </h5>
                    <p class="mb-0 text-muted">{{ $health['storage']['message'] }}</p>
                </div>
                <span class="health-status {{ $health['storage']['status'] }}">
                    {{ ucfirst($health['storage']['status']) }}
                </span>
            </div>
        </div>

        <!-- Cache Health -->
        <div class="health-item {{ $health['cache']['status'] }}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h5 class="mb-1">
                        <i class="bi bi-lightning-charge me-2"></i>Cache System
                    </h5>
                    <p class="mb-0 text-muted">{{ $health['cache']['message'] }}</p>
                </div>
                <span class="health-status {{ $health['cache']['status'] }}">
                    {{ ucfirst($health['cache']['status']) }}
                </span>
            </div>
        </div>

        <!-- Disk Space -->
        <div class="health-item {{ $health['disk_space']['status'] }}">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-1">
                        <i class="bi bi-hdd-stack me-2"></i>Disk Space
                    </h5>
                    <span class="health-status {{ $health['disk_space']['status'] }}">
                        {{ $health['disk_space']['percent'] }}% Used
                    </span>
                </div>
                <div class="mb-2">
                    <div class="progress">
                        <div class="progress-bar 
                            @if($health['disk_space']['percent'] > 90) bg-danger
                            @elseif($health['disk_space']['percent'] > 80) bg-warning
                            @else bg-success
                            @endif" 
                            role="progressbar" 
                            style="width: {{ $health['disk_space']['percent'] }}%">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Free: {{ $health['disk_space']['free'] }}</span>
                    <span>Used: {{ $health['disk_space']['used'] }}</span>
                    <span>Total: {{ $health['disk_space']['total'] }}</span>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>

@endsection

