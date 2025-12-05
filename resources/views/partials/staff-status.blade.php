@forelse($thirtyMinutesAgo as $staff)
    <div class="staff-item {{ $staff->status === 'busy' ? 'inactive-staff' : 'active-staff' }}">
        <i class="bi {{ $staff->status === 'busy' ? 'bi-calendar-x-fill' : 'bi-check-circle-fill' }}"></i>
        <span>{{ $staff->name }}</span>
    </div>
@empty
    <div class="text-muted text-center py-4">
        <i class="bi bi-people me-2"></i>No staff found
    </div>
@endforelse
