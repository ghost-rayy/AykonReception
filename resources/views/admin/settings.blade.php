@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        min-height: 100vh;
    }

    .settings-container {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        max-width: 900px;
        margin: 30px auto;
    }

    .settings-header {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }

    .settings-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .settings-section {
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 1px solid #f0f0f0;
    }

    .settings-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>

<div class="container-fluid" style="padding: 30px 0 50px;">
    <div class="settings-container">
        <div class="settings-header">
            <h1>
                <i class="bi bi-gear-fill text-primary"></i>
                System Settings
            </h1>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            <!-- General Settings -->
            <div class="settings-section">
                <div class="section-title">
                    <i class="bi bi-sliders text-primary"></i>
                    General Settings
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Site Name</label>
                        <input type="text" name="site_name" class="form-control" 
                               value="{{ $settings['site_name'] }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Timezone</label>
                        <select name="timezone" class="form-select" required>
                            <option value="UTC" {{ $settings['timezone'] == 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ $settings['timezone'] == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                            <option value="America/Chicago" {{ $settings['timezone'] == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                            <option value="America/Denver" {{ $settings['timezone'] == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                            <option value="America/Los_Angeles" {{ $settings['timezone'] == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date Format</label>
                        <select name="date_format" class="form-select" required>
                            <option value="Y-m-d" {{ $settings['date_format'] == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            <option value="m/d/Y" {{ $settings['date_format'] == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                            <option value="d/m/Y" {{ $settings['date_format'] == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Time Format</label>
                        <select name="time_format" class="form-select" required>
                            <option value="H:i" {{ $settings['time_format'] == 'H:i' ? 'selected' : '' }}>24 Hour (HH:MM)</option>
                            <option value="h:i A" {{ $settings['time_format'] == 'h:i A' ? 'selected' : '' }}>12 Hour (HH:MM AM/PM)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Visitor Settings -->
            <div class="settings-section">
                <div class="section-title">
                    <i class="bi bi-person-check text-primary"></i>
                    Visitor Settings
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="visitor_photo_required" 
                                   id="visitor_photo_required" value="1" 
                                   {{ $settings['visitor_photo_required'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="visitor_photo_required">
                                Require Visitor Photo
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Auto Checkout (Hours)</label>
                        <input type="number" name="auto_checkout_hours" class="form-control" 
                               value="{{ $settings['auto_checkout_hours'] }}" 
                               min="1" max="24" placeholder="Leave empty to disable">
                        <small class="text-muted">Automatically check out visitors after X hours</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Max Visitors Per Day</label>
                        <input type="number" name="max_visitors_per_day" class="form-control" 
                               value="{{ $settings['max_visitors_per_day'] ?? '' }}" 
                               min="1" placeholder="Leave empty for unlimited">
                    </div>
                </div>
            </div>

            <!-- Appointment Settings -->
            <div class="settings-section">
                <div class="section-title">
                    <i class="bi bi-calendar-event text-primary"></i>
                    Appointment Settings
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Reminder Hours Before Appointment</label>
                        <input type="number" name="appointment_reminder_hours" class="form-control" 
                               value="{{ $settings['appointment_reminder_hours'] }}" 
                               min="1" max="168" placeholder="24">
                        <small class="text-muted">Send reminder X hours before appointment</small>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-2"></i>Save Settings
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary px-4">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

