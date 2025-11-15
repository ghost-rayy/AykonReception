@extends('layouts.app')

@section('title', 'Add Staff')

@section('content')

<div class="card shadow-sm">
    <div class="card-body">

        <form method="POST" action="{{ route('staff.store') }}">
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
                <label>Position</label>
                <input name="position" class="form-control" required>
            </div>

            <button class="btn btn-success">Save</button>
        </form>

    </div>
</div>

@endsection
