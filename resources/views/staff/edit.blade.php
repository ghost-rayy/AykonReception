@extends('layouts.app')

@section('title', 'Edit Staff')

@section('content')

<div class="card shadow-sm">
    <div class="card-body">

        <form method="POST" action="{{ route('staff.update', $staff->id) }}">
            @csrf

            <div class="mb-3">
                <label>Name</label>
                <input name="name" class="form-control" value="{{ $staff->name }}" required>
            </div>

            <div class="mb-3">
                <label>Phone</label>
                <input name="phone" class="form-control" value="{{ $staff->phone }}" required>
            </div>

            <div class="mb-3">
                <label>Position</label>
                <input name="position" class="form-control" value="{{ $staff->position }}" required>
            </div>

            <button class="btn btn-success">Update</button>
        </form>

    </div>
</div>

@endsection
