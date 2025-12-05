@extends('layouts.app')

@section('title', 'System Logs')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        min-height: 100vh;
    }

    .logs-container {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin: 30px auto;
    }

    .logs-header {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logs-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .log-item {
        padding: 16px;
        border-left: 4px solid #0d1b2a;
        background: #f9fafb;
        border-radius: 8px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
    }

    .log-item:hover {
        background: #f3f4f6;
        transform: translateX(4px);
    }

    .log-type-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .log-type-badge.user {
        background: #dbeafe;
        color: #1e40af;
    }

    .log-type-badge.visitor {
        background: #dcfce7;
        color: #166534;
    }

    .log-type-badge.appointment {
        background: #fef3c7;
        color: #92400e;
    }
</style>

<div class="container-fluid" style="padding: 30px 0 50px;">
    <div class="logs-container">
        <div class="logs-header">
            <h1>
                <i class="bi bi-journal-text text-primary"></i>
                System Activity Logs
            </h1>
        </div>

        <div>
            @forelse($activities as $activity)
            <div class="log-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="log-type-badge {{ $activity['type'] }}">
                                {{ ucfirst($activity['type']) }}
                            </span>
                            <span class="badge bg-secondary">{{ ucfirst($activity['action']) }}</span>
                        </div>
                        <div class="fw-semibold mb-1">{{ $activity['description'] }}</div>
                        <small class="text-muted">
                            <i class="bi bi-person me-1"></i>{{ $activity['user'] }}
                        </small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($activity['timestamp'])->format('M d, Y H:i') }}
                        </small>
                        <br>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($activity['timestamp'])->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 48px; color: #d1d5db;"></i>
                <p class="text-muted mt-3">No activity logs found</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection

