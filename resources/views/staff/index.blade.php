@extends('layouts.app')

@section('title', 'Staff Management')

@section('content')

<style>
    /* Page Background */
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        min-height: 100vh;
    }

    .staff-page-wrapper {
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

    .page-header > div:first-child {
        flex: 1;
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

    /* Staff Cards - Modern Design */
    .staff-card {
        background: white;
        border-radius: 20px;
        padding: 28px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        height: 100%;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .staff-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 32px rgba(0, 0, 0, 0.12);
    }

    .staff-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
    }

    .staff-name {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        line-height: 1.2;
        flex: 1;
    }

    .staff-info {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #4b5563;
        font-size: 15px;
        margin-bottom: 16px;
        padding: 8px 0;
    }

    .staff-info i {
        color: #6b7280;
        font-size: 18px;
        width: 20px;
        text-align: center;
    }

    .staff-info span {
        flex: 1;
    }

    .staff-status {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid #f3f4f6;
        color: #10b981;
        font-size: 14px;
        font-weight: 500;
    }

    .staff-status i {
        font-size: 16px;
    }

    .staff-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid #f3f4f6;
    }

    /* Badges */
    .badge-position {
        font-size: 12px;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-manager { 
        background: #0d1b2a;
        color: white;
    }
    .badge-driver { 
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
    }
    .badge-reception { 
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        color: white;
    }

    /* Buttons */
    .btn-action {
        border-radius: 10px;
        padding: 8px 14px;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-edit {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        color: white;
    }

    .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.3);
        color: white;
    }

    .btn-delete {
        background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
        color: white;
    }

    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
        color: white;
    }

    .btn-icon-only {
        width: 36px;
        height: 36px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    .btn-add-staff {
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

    .btn-add-staff:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(13, 27, 42, 0.3);
        color: white;
    }

    /* Registered Staff Badge */
    .registered-badge {
        /* background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%); */
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
    }

    .badge-position {
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    /* Modals */
    .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        border-bottom: 2px solid #f3f4f6;
        padding: 24px;
        border-radius: 16px 16px 0 0;
    }

    .modal-title {
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
    }

    .modal-body {
        padding: 24px;
    }

    .modal-body label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        display: block;
        font-size: 14px;
    }

    .modal-body .form-control {
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        padding: 12px 16px;
        transition: all 0.3s ease;
    }

    .modal-body .form-control:focus {
        border-color: #0d1b2a;
        box-shadow: 0 0 0 4px rgba(13, 27, 42, 0.1);
        outline: none;
    }

    .modal-body .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        border: none;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .modal-body .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
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
    }
</style>

<div class="staff-page-wrapper">
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <p class="text-muted mb-0 mt-2">Manage your staff members and registered users</p>
            </div>
            <div class="search-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="searchStaff" class="form-control search-input" placeholder="Search staff by name, phone or position...">
            </div>
        </div>

        <!-- Staff Grid -->
        <div class="row g-4">
            @forelse($staff as $st)
            @php
                $badgeClass = 'badge-reception';
                if(str_contains(strtolower($st->position), 'manager')) $badgeClass = 'badge-manager';
                if(str_contains(strtolower($st->position), 'driver')) $badgeClass = 'badge-driver';
            @endphp

            <div class="col-md-6 col-lg-4 col-xl-3 staff-item">
                <div class="card staff-card">
                    <div class="staff-card-header">
                        <h5 class="staff-name">{{ $st->name }}</h5>
                        <span class="badge badge-position {{ $badgeClass }}">{{ $st->position }}</span>
                    </div>
                    <div class="staff-info">
                        <i class="bi bi-telephone-fill"></i>
                        <span>{{ $st->phone }}</span>
                    </div>
                    <div class="staff-actions">
                        <button class="btn btn-action btn-edit edit-staff-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#editStaffModal"
                            data-id="{{ $st->id }}"
                            data-name="{{ $st->name }}"
                            data-phone="{{ $st->phone }}"
                            data-position="{{ $st->position }}">
                            <i class="bi bi-pencil"></i>Edit
                        </button>

                        <form action="{{ route('staff.delete', $st->id) }}" method="GET" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                            <button type="submit" class="btn btn-action btn-delete">
                                <i class="bi bi-trash"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            @if(empty($users))
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-people"></i>
                    <h4>No staff members found</h4>
                    <p>Add your first staff member to get started</p>
                </div>
            </div>
            @endif
            @endforelse

            @foreach($users as $user)
            <div class="col-md-6 col-lg-4 col-xl-3 staff-item">
                <div class="card staff-card">
                    <div class="staff-card-header">
                        <h5 class="staff-name">{{ $user->name }}</h5>
                        <!-- <span class="registered-badge">Staff User</span> -->
                    </div>
                    <div class="staff-info">
                        <i class="bi bi-telephone-fill"></i>
                        <span>{{ $user->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="staff-info">
                        <i class="bi bi-envelope-fill"></i>
                        <span>{{ $user->email }}</span>
                    </div>
                    <div class="staff-status">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Registered</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Create Staff Modal --}}
<div class="modal fade" id="createStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus-fill me-2 text-primary"></i>Add Staff Member
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('staff.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label>Full Name</label>
                        <input name="name" class="form-control" placeholder="Enter staff name" required>
                    </div>
                    <div class="mb-4">
                        <label>Phone Number</label>
                        <input name="phone" type="tel" class="form-control" placeholder="Enter phone number" required>
                    </div>
                    <div class="mb-4">
                        <label>Position</label>
                        <input name="position" class="form-control" placeholder="e.g., Manager, Driver, Receptionist" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle me-2"></i>Save Staff Member
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Edit Staff Modal --}}
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-fill me-2 text-warning"></i>Edit Staff Member
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" id="editStaffForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label>Full Name</label>
                        <input name="name" id="editName" class="form-control" placeholder="Enter staff name" required>
                    </div>
                    <div class="mb-4">
                        <label>Phone Number</label>
                        <input name="phone" id="editPhone" type="tel" class="form-control" placeholder="Enter phone number" required>
                    </div>
                    <div class="mb-4">
                        <label>Position</label>
                        <input name="position" id="editPosition" class="form-control" placeholder="e.g., Manager, Driver, Receptionist" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle me-2"></i>Update Staff Member
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Enhanced search with smooth animations
    document.getElementById('searchStaff').addEventListener('keyup', function() {
        const query = this.value.toLowerCase().trim();
        const items = document.querySelectorAll('.staff-item');

        items.forEach((item, index) => {
            const card = item.querySelector('.staff-card');
            const text = card.textContent.toLowerCase();
            const matches = text.includes(query);
            
            if (matches) {
                item.style.display = '';
                item.style.animation = `fadeIn 0.3s ease ${index * 0.05}s both`;
            } else {
                item.style.display = 'none';
            }
        });

        // Show empty state if no results
        const visibleItems = Array.from(items).filter(item => item.style.display !== 'none');
        // You can add empty state logic here if needed
    });

    // Edit modal logic with better UX
    document.querySelectorAll('.edit-staff-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('editName').value = this.dataset.name;
            document.getElementById('editPhone').value = this.dataset.phone;
            document.getElementById('editPosition').value = this.dataset.position;
            document.getElementById('editStaffForm').action =
                '{{ route("staff.update", ":id") }}'.replace(':id', id);
        });
    });

    // Add smooth fade-in animation for cards on load
    document.addEventListener('DOMContentLoaded', function() {
        const items = document.querySelectorAll('.staff-item');
        items.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            setTimeout(() => {
                item.style.transition = 'all 0.4s ease';
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, index * 50);
        });
    });
</script>
@endsection
