@extends('layouts.app')

@section('title', 'Register Visitor')

@section('content')

<div class="card shadow-sm">
    <div class="card-body">

        <form method="POST" action="{{ route('visitors.store') }}">
            @csrf

            <div class="mb-3">
                <label>Name</label>
                <input name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Phone</label>
                <input name="phone" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Purpose of Visit</label>
                <input name="purpose" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Staff to Visit</label>
                <div class="position-relative">
                    <input type="text" id="staffSearch" class="form-control form-control-lg rounded-3 shadow-sm" placeholder="Search and select staff..." autocomplete="off" required>
                    <input type="hidden" name="staff_to_visit" id="staffToVisit" required>
                    <div id="staffDropdown" class="dropdown-menu w-100 position-absolute" style="display: none; max-height: 200px; overflow-y: auto; z-index: 1000;">
                        @foreach($users as $u)
                            <button class="dropdown-item staff-option" type="button" data-id="{{ $u->id }}" data-name="{{ $u->name }}">
                                {{ $u->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <button class="btn btn-success">Check In</button>
        </form>

    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('staffSearch');
        const dropdown = document.getElementById('staffDropdown');
        const hiddenInput = document.getElementById('staffToVisit');
        const options = dropdown.querySelectorAll('.staff-option');

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            let hasVisible = false;

            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                if (text.includes(query)) {
                    option.style.display = '';
                    hasVisible = true;
                } else {
                    option.style.display = 'none';
                }
            });

            dropdown.style.display = hasVisible && query.length > 0 ? 'block' : 'none';
        });

        searchInput.addEventListener('focus', function() {
            if (this.value.length > 0) {
                dropdown.style.display = 'block';
            }
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        options.forEach(option => {
            option.addEventListener('click', function() {
                searchInput.value = this.dataset.name;
                hiddenInput.value = this.dataset.id;
                dropdown.style.display = 'none';
            });
        });
    });
</script>
@endsection
