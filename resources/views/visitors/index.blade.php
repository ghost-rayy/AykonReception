@extends('layouts.app')

@section('title', 'Visitors')

@section('content')

<style>
    /* Page Background */
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        min-height: 100vh;
    }

    .visitors-page-wrapper {
        padding: 30px 0 50px;
        animation: fadeIn 0.6s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Page Header */
    .page-header {
        background: white;
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
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

    .page-title i {
        color: #0d1b2a;
        font-size: 36px;
    }

    /* Search Bar */
    .search-container {
        position: relative;
        flex: 1;
        max-width: 500px;
    }

    .search-input {
        border-radius: 50px;
        padding: 14px 20px 14px 50px;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
        font-size: 15px;
        background: white;
    }

    .search-input:focus {
        border-color: #0d1b2a;
        box-shadow: 0 0 0 4px rgba(13, 27, 42, 0.1);
        outline: none;
    }

    .search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 18px;
        z-index: 10;
    }

    /* Table Container */
    .table-container {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    /* Modern Table */
    .modern-table {
        border-collapse: separate !important;
        border-spacing: 0 12px !important;
        margin: 0;
    }

    .modern-table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .modern-table thead th {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        color: #374151;
        background: transparent;
        border-bottom: 2px solid #e5e7eb;
        padding: 16px 12px;
        letter-spacing: 0.5px;
    }

    .modern-table thead th:first-child {
        border-top-left-radius: 12px;
    }

    .modern-table thead th:last-child {
        border-top-right-radius: 12px;
    }

    .modern-table tbody tr {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .modern-table tbody tr:hover {
        background: #f9fafb;
        box-shadow: 0 4px 12px rgba(13, 27, 42, 0.1);
        transform: translateY(-2px);
    }

    .modern-table tbody tr td {
        padding: 20px 12px;
        border-top: none;
        border-bottom: none;
        vertical-align: middle;
    }

    /* Avatar */
    .avatar-img {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .avatar-placeholder {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #0d1b2a;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .visitor-name {
        font-weight: 600;
        color: #1f2937;
        font-size: 15px;
    }

    .visitor-phone {
        font-size: 13px;
        color: #6b7280;
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-active {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }

    .status-checked-out {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    /* Buttons */
    .btn-register {
        background: #0d1b2a;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(13, 27, 42, 0.3);
        color: white;
    }

    .btn-checkout {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        color: white;
    }

    .btn-delete {
        background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        color: white;
    }

    /* Purpose Badge */
    .purpose-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        color: #374151;
    }

    /* Modals */
    .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }

    .modal-header {
        background: #0d1b2a;
        color: white;
        border: none;
        padding: 28px 30px;
        position: relative;
    }

    .modal-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: rgba(255, 255, 255, 0.2);
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.9;
        transition: all 0.3s ease;
    }

    .modal-header .btn-close:hover {
        opacity: 1;
        transform: rotate(90deg);
    }

    .modal-title {
        font-size: 24px;
        font-weight: 700;
        color: white;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-title i {
        font-size: 28px;
    }

    .modal-header small {
        color: rgba(255, 255, 255, 0.9);
        font-size: 13px;
        margin-top: 4px;
        display: block;
    }

    .modal-body {
        padding: 30px;
        background: #fafbfc;
    }

    .form-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #f0f0f0;
    }

    .form-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #0d1b2a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-section-title i {
        font-size: 16px;
    }

    .modal-body label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .modal-body label i {
        color: #0d1b2a;
        font-size: 16px;
    }

    .input-group-icon {
        position: relative;
    }

    .input-group-icon i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 18px;
        z-index: 10;
        pointer-events: none;
        width: 20px;
        text-align: center;
    }

    .input-group-icon .form-control,
    .input-group-icon .form-select {
        padding-left: 65px;
        padding-right: 16px;
    }

    .modal-body .form-control,
    .modal-body .form-select {
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        padding: 14px 16px;
        transition: all 0.3s ease;
        font-size: 15px;
        background: white;
    }

    .modal-body .form-control:focus,
    .modal-body .form-select:focus {
        border-color: #0d1b2a;
        box-shadow: 0 0 0 4px rgba(13, 27, 42, 0.1);
        outline: none;
        background: #fafbfc;
    }

    .modal-body .form-control::placeholder {
        color: #9ca3af;
    }

    /* Camera Section */
    .camera-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 20px;
        border: 2px dashed #d1d5db;
        transition: all 0.3s ease;
    }

    .camera-section.active {
        border-color: #0d1b2a;
        background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
    }

    .camera-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .btn-camera {
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        border: 2px solid;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-camera-start {
        background: #0d1b2a;
        color: white;
        border-color: transparent;
    }

    .btn-camera-start:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(13, 27, 42, 0.3);
        color: white;
    }

    .btn-camera-capture {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        border-color: transparent;
    }

    .btn-camera-capture:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
        color: white;
    }

    .btn-camera-stop {
        background: white;
        color: #6b7280;
        border-color: #d1d5db;
    }

    .btn-camera-stop:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
        color: #374151;
    }

    #cameraContainer {
        position: relative;
        display: inline-block;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 3px solid white;
        margin: 16px auto;
    }

    #cameraVideo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    #photoPreviewImg {
        width: 100%;
        max-width: 300px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 3px solid white;
    }

    .photo-preview-container {
        position: relative;
        display: inline-block;
    }

    .btn-remove-photo {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(239, 68, 68, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .btn-remove-photo:hover {
        background: #ef4444;
        transform: scale(1.1);
        color: white;
    }

    .camera-info {
        background: white;
        border-radius: 8px;
        padding: 12px 16px;
        margin-top: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #6b7280;
        border-left: 3px solid #0d1b2a;
    }

    .camera-info i {
        color: #0d1b2a;
        font-size: 16px;
    }

    .modal-body .btn-primary {
        background: #0d1b2a;
        border: none;
        border-radius: 12px;
        padding: 16px;
        font-weight: 700;
        font-size: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(13, 27, 42, 0.3);
        margin-top: 10px;
    }

    .modal-body .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(13, 27, 42, 0.4);
    }

    .modal-body .btn-primary:active {
        transform: translateY(0);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .search-container {
            max-width: 100%;
        }

        .page-title {
            font-size: 24px;
        }

        .modern-table {
            font-size: 13px;
        }
    }
</style>

<div class="visitors-page-wrapper">
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <!-- <i class="bi bi-people-fill"></i>
                Visitors Management -->
            </h1>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="search-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="searchVisitors" class="form-control search-input" placeholder="Search visitors by name, phone or purpose...">
                </div>
                <button class="btn btn-register" data-bs-toggle="modal" data-bs-target="#createVisitorModal">
                    <i class="bi bi-person-plus"></i>Register Visitor
                </button>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container">
    <div class="table-responsive">
        <table class="table modern-table align-middle">
            <thead>
                <tr>
                    <th>Visitor</th>
                    <th>Purpose</th>
                    <th>Staff to Visit</th>
                    <th>Status</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($visitors as $v)
                <tr>
                    <!-- Avatar + Name -->
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            @if($v->photo_path)
                                <img src="{{ asset('storage/' . $v->photo_path) }}" class="avatar-img" alt="{{ $v->name }}">
                            @else
                                <div class="avatar-placeholder">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                            @endif

                            <div>
                                <div class="visitor-name">{{ $v->name }}</div>
                                <div class="visitor-phone">
                                    <i class="bi bi-telephone-fill me-1"></i>{{ $v->phone }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Purpose -->
                    <td>
                        <span class="purpose-badge">{{ $v->purpose }}</span>
                    </td>

                    <!-- Staff -->
                    <td>
                        @if($v->user)
                            <span class="text-dark fw-semibold">{{ $v->user->name }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <!-- Status -->
                    <td>
                        @if(!$v->check_out_time)
                            <span class="status-badge status-active">
                                <span class="status-dot bg-success"></span>
                                Active
                            </span>
                        @else
                            <span class="status-badge status-checked-out">
                                <span class="status-dot bg-danger"></span>
                                Checked-out
                            </span>
                        @endif
                    </td>

                    <!-- Check-in -->
                    <td>
                        @if($v->check_in_time)
                            <div class="text-dark">{{ $v->check_in_time->format('M d, Y') }}</div>
                            <div class="text-muted small">{{ $v->check_in_time->format('H:i') }}</div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <!-- Check-out -->
                    <td>
                        @if($v->check_out_time)
                            <div class="text-dark">{{ $v->check_out_time->format('M d, Y') }}</div>
                            <div class="text-muted small">{{ $v->check_out_time->format('H:i') }}</div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <!-- Actions -->
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            @if(!$v->check_out_time)
                                <a href="{{ route('visitors.checkout', $v->id) }}" class="btn btn-checkout">
                                    <i class="bi bi-box-arrow-right me-1"></i>Check-out
                                </a>
                            @endif

                            <form action="{{ route('visitors.delete', $v->id) }}" method="GET" class="d-inline"
                                onsubmit="return confirm('Are you sure you want to delete this visitor?');">
                                <button type="submit" class="btn btn-delete">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h5 class="mt-3">No visitors found</h5>
                            <p class="text-muted">Register your first visitor to get started</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
        </div>
    </div>
</div>


{{-- Create Visitor Modal --}}
<div class="modal fade" id="createVisitorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus-fill"></i>Register Visitor
                    </h5>
                    <small>Fill in visitor details and capture photo</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form method="POST" action="{{ route('visitors.store') }}" enctype="multipart/form-data" id="visitorForm">
                    @csrf

                    {{-- Personal Information Section --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-person-badge"></i>Personal Information
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-person"></i>Full Name
                                </label>
                                <div class="input-group-icon">
                                    <input name="name" class="form-control" placeholder="Enter visitor's full name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-telephone"></i>Phone Number
                                </label>
                                <div class="input-group-icon">
                                    <input name="phone" type="tel" class="form-control" placeholder="Enter phone number" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Visit Details Section --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-calendar-event"></i>Visit Details
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-briefcase"></i>Purpose of Visit
                            </label>
                            <div class="input-group-icon">
                                <input name="purpose" class="form-control" placeholder="e.g., Meeting, Delivery, Interview" required>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">
                                <i class="bi bi-person-check"></i>Staff to Visit
                            </label>
                            <div class="input-group-icon">
                                <select name="staff_to_visit" class="form-select" required>
                                    <option value="">Select Staff Member</option>
                                    @php
                                        $staffUsers = \App\Models\User::where('role', 'staff')->get();
                                    @endphp
                                    @foreach($staffUsers as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Camera Section --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-camera"></i>Visitor Photo
                        </div>
                        <div class="camera-section" id="cameraSection">
                            <div class="camera-controls">
                                <button type="button" class="btn btn-camera btn-camera-start" id="startCameraBtn">
                                    <i class="bi bi-camera"></i>Start Camera
                                </button>
                                <button type="button" class="btn btn-camera btn-camera-capture d-none" id="capturePhotoBtn">
                                    <i class="bi bi-camera-fill"></i>Capture Photo
                                </button>
                                <button type="button" class="btn btn-camera btn-camera-stop d-none" id="stopCameraBtn">
                                    <i class="bi bi-stop-circle"></i>Stop Camera
                                </button>
                            </div>

                            <div id="cameraContainer" style="display: none;">
                                <video id="cameraVideo" autoplay playsinline></video>
                                <canvas id="photoCanvas" style="display:none;"></canvas>
                            </div>

                            <div id="photoPreview" style="display: none; text-align: center; margin: 16px 0;">
                                <div class="photo-preview-container">
                                    <img id="photoPreviewImg" src="" alt="Photo Preview">
                                    <button type="button" class="btn-remove-photo" id="removePhotoBtn" title="Remove Photo">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="photo_data" id="photoDataInput">
                            <div class="camera-info">
                                <i class="bi bi-info-circle"></i>
                                <span>Start the camera to capture visitor's photo in real-time. Ensure good lighting for best results.</span>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100" type="submit">
                        <i class="bi bi-check-circle me-2"></i>Check In Visitor
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Enhanced live search with smooth animations
    document.getElementById('searchVisitors').addEventListener('keyup', function() {
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.modern-table tbody tr');
        let visibleCount = 0;

        rows.forEach((row, index) => {
            const text = row.textContent.toLowerCase();
            const matches = text.includes(query);
            
            if (matches) {
                row.style.display = '';
                row.style.animation = `fadeIn 0.3s ease ${index * 0.05}s both`;
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Add smooth fade-in animation for rows on load
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.modern-table tbody tr');
        rows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(10px)';
            setTimeout(() => {
                row.style.transition = 'all 0.4s ease';
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, index * 50);
        });
    });

    // Camera logic
    let stream = null;
    const startCameraBtn = document.getElementById('startCameraBtn');
    const capturePhotoBtn = document.getElementById('capturePhotoBtn');
    const stopCameraBtn = document.getElementById('stopCameraBtn');
    const cameraContainer = document.getElementById('cameraContainer');
    const cameraVideo = document.getElementById('cameraVideo');
    const photoCanvas = document.getElementById('photoCanvas');
    const photoPreview = document.getElementById('photoPreview');
    const photoPreviewImg = document.getElementById('photoPreviewImg');
    const removePhotoBtn = document.getElementById('removePhotoBtn');
    const photoDataInput = document.getElementById('photoDataInput');

    startCameraBtn.addEventListener('click', async function() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
            cameraVideo.srcObject = stream;
            cameraContainer.style.display = 'block';
            document.getElementById('cameraSection').classList.add('active');
            startCameraBtn.classList.add('d-none');
            capturePhotoBtn.classList.remove('d-none');
            stopCameraBtn.classList.remove('d-none');
        } catch (error) {
            console.error(error);
            alert('Unable to access camera. Please check your camera permissions and try again.');
        }
    });

    capturePhotoBtn.addEventListener('click', function() {
        const ctx = photoCanvas.getContext('2d');
        photoCanvas.width = cameraVideo.videoWidth;
        photoCanvas.height = cameraVideo.videoHeight;
        ctx.drawImage(cameraVideo, 0, 0);

        const imageData = photoCanvas.toDataURL('image/jpeg', 0.8);
        photoPreviewImg.src = imageData;
        photoPreview.style.display = 'block';
        photoDataInput.value = imageData;

        stopCamera();
    });

    stopCameraBtn.addEventListener('click', stopCamera);
    removePhotoBtn.addEventListener('click', () => {
        photoPreview.style.display = 'none';
        photoPreviewImg.src = '';
        photoDataInput.value = '';
    });

    function stopCamera() {
        if (stream) stream.getTracks().forEach(track => track.stop());
        stream = null;
        cameraContainer.style.display = 'none';
        cameraVideo.srcObject = null;
        document.getElementById('cameraSection').classList.remove('active');
        startCameraBtn.classList.remove('d-none');
        capturePhotoBtn.classList.add('d-none');
        stopCameraBtn.classList.add('d-none');
    }

    // Reset modal when closed
    document.getElementById('createVisitorModal').addEventListener('hidden.bs.modal', function() {
        stopCamera();
        document.getElementById('visitorForm').reset();
        photoPreview.style.display = 'none';
        photoPreviewImg.src = '';
        photoDataInput.value = '';
        document.getElementById('cameraSection').classList.remove('active');
    });
</script>
@endsection
