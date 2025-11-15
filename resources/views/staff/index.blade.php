@extends('layouts.app')

@section('title', '👥 Staff Management')

@section('content')

<style>
    body {
        background: #f8f9fa;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 25px;
        color: #0d6efd;
    }

    .search-input {
        border-radius: 50px;
        padding-left: 40px;
        transition: 0.3s;
    }

    .search-input:focus {
        box-shadow: 0 0 15px rgba(0, 123, 255, 0.3);
    }

    .staff-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px;
        cursor: pointer;
    }

    .staff-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    }

    .badge-position {
        font-size: 12px;
        font-weight: 600;
        padding: 6px 10px;
        border-radius: 8px;
    }

    .badge-manager { background: #d4edda; color: #155724; }
    .badge-driver { background: #d1ecf1; color: #0c5460; }
    .badge-reception { background: #fff3cd; color: #856404; }

    .btn-primary, .btn-warning, .btn-danger {
        transition: 0.2s;
    }
    .btn-primary:hover {
        background: #0b5ed7;
        transform: translateY(-2px);
    }
    .btn-warning:hover {
        background: #ffc107;
        transform: translateY(-2px);
    }
    .btn-danger:hover {
        background: #dc3545;
        transform: translateY(-2px);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <h4 class="page-title"></h4>
        <div class="position-relative">
            <i class="bi bi-search" style="position:absolute; top:12px; left:15px; color:#6c757d;"></i>
            <input type="text" id="searchStaff" class="form-control search-input" placeholder="Search staff by name, phone or position..." style="width: 300px;">
        </div>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStaffModal">
        + Add Staff
    </button>
</div>

<div class="row g-3">
    @foreach($staff as $st)
    @php
        $badgeClass = 'badge-reception';
        if(str_contains(strtolower($st->position), 'manager')) $badgeClass = 'badge-manager';
        if(str_contains(strtolower($st->position), 'driver')) $badgeClass = 'badge-driver';
    @endphp

    <div class="col-md-4">
        <div class="card staff-card shadow-sm p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">{{ $st->name }}</h5>
                <span class="badge badge-position {{ $badgeClass }}">{{ $st->position }}</span>
            </div>
            <p class="text-muted mb-3"><i class="bi bi-telephone"></i> {{ $st->phone }}</p>
            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-sm btn-warning edit-staff-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#editStaffModal"
                    data-id="{{ $st->id }}"
                    data-name="{{ $st->name }}"
                    data-phone="{{ $st->phone }}"
                    data-position="{{ $st->position }}">
                    Edit
                </button>

                <form action="{{ route('staff.delete', $st->id) }}" method="GET" style="display:inline;"
                    onsubmit="return confirm('Delete this staff?');">
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    @foreach($users as $user)
    <div class="col-md-4">
        <div class="card staff-card shadow-sm p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">{{ $user->name }}</h5>
                <span class="badge badge-position badge-manager">Staff User</span>
            </div>
            <p class="text-muted mb-3"><i class="bi bi-telephone"></i> {{ $user->phone }}</p>
            <p class="text-muted mb-3"><i class="bi bi-envelope"></i> {{ $user->email }}</p>
            <div class="d-flex justify-content-end gap-2">
                <span class="text-muted small">Registered Staff</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Create Staff Modal --}}
<div class="modal fade" id="createStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title">Add Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('staff.store') }}">
                    @csrf
                    <div class="mb-3"><label>Name</label><input name="name" class="form-control" required></div>
                    <div class="mb-3"><label>Phone</label><input name="phone" class="form-control" required></div>
                    <div class="mb-3"><label>Position</label><input name="position" class="form-control" required></div>
                    <button class="btn btn-success w-100">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Edit Staff Modal --}}
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title">Edit Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" id="editStaffForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-3"><label>Name</label><input name="name" id="editName" class="form-control" required></div>
                    <div class="mb-3"><label>Phone</label><input name="phone" id="editPhone" class="form-control" required></div>
                    <div class="mb-3"><label>Position</label><input name="position" id="editPosition" class="form-control" required></div>
                    <button class="btn btn-success w-100">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Search logic (unchanged)
    document.getElementById('searchStaff').addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        const cards = document.querySelectorAll('.staff-card');

        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            card.parentElement.style.display = text.includes(query) ? '' : 'none';
        });
    });

    // Edit modal logic (unchanged)
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
</script>
@endsection
