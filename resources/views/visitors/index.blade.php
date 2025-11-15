@extends('layouts.app')

@section('title', '👤 Visitors')

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

    .visitor-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px;
        cursor: pointer;
        padding: 15px;
        background: white;
    }
    .visitor-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    }

    .badge-purpose {
        font-size: 12px;
        font-weight: 600;
        padding: 6px 10px;
        border-radius: 8px;
    }

    .btn-primary, .btn-success, .btn-danger, .btn-secondary {
        transition: 0.2s;
    }
    .btn-primary:hover { transform: translateY(-2px); }
    .btn-success:hover { transform: translateY(-2px); }
    .btn-danger:hover { transform: translateY(-2px); }
    .btn-secondary:hover { transform: translateY(-2px); }

    .visitor-photo {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <h4 class="page-title"></h4>
        <div class="position-relative">
            <i class="bi bi-search" style="position:absolute; top:12px; left:15px; color:#6c757d;"></i>
            <input type="text" id="searchVisitors" class="form-control search-input" placeholder="Search visitors by name, phone or purpose..." style="width: 350px;">
        </div>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createVisitorModal">
        + Register Visitor
    </button>
</div>

<div class="row g-3">
    @foreach($visitors as $v)
    <div class="col-md-4">
        <div class="visitor-card shadow-sm">
            <div class="d-flex align-items-center gap-3 mb-2">
                @if($v->photo_path)
                    <img src="{{ asset('storage/' . $v->photo_path) }}" alt="Visitor Photo" class="visitor-photo">
                @else
                    <div class="visitor-photo d-flex align-items-center justify-content-center text-muted small">No Photo</div>
                @endif
                <div>
                    <h5 class="mb-1">{{ $v->name }}</h5>
                    <small class="text-muted"><i class="bi bi-telephone"></i> {{ $v->phone }}</small>
                </div>
            </div>
            <p class="mb-2"><span class="badge badge-purpose bg-info text-white">{{ $v->purpose }}</span></p>
            <p class="mb-2"><strong>Staff to Visit:</strong> {{ $v->user->name ?? '—' }}</p>
            <p class="mb-2"><strong>Check-in:</strong> {{ $v->check_in_time ? $v->check_in_time->format('Y-m-d H:i') : '—' }}</p>
            <p class="mb-3"><strong>Check-out:</strong> {{ $v->check_out_time ? $v->check_out_time->format('Y-m-d H:i') : '—' }}</p>

            <div class="d-flex gap-2 flex-wrap justify-content-end">
                @if(!$v->check_out_time)
                <a href="{{ route('visitors.checkout', $v->id) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Check-Out
                </a>
                @endif
                <form action="{{ route('visitors.delete', $v->id) }}" method="GET" style="display:inline;" onsubmit="return confirm('Delete this visitor?');">
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Create Visitor Modal --}}
<div class="modal fade" id="createVisitorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow-lg border-0" style="background: #fefefe;">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold">Register Visitor</h5>
                    <small class="text-muted">Fill in visitor details and capture photo</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body pt-3">
                <form method="POST" action="{{ route('visitors.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Name</label>
                            <input name="name" class="form-control form-control-lg rounded-3 shadow-sm" placeholder="Enter name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Phone</label>
                            <input name="phone" class="form-control form-control-lg rounded-3 shadow-sm" placeholder="Enter phone" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Purpose of Visit</label>
                        <input name="purpose" class="form-control form-control-lg rounded-3 shadow-sm" placeholder="Enter purpose" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Staff to Visit</label>
                        <select name="staff_to_visit" class="form-select form-select-lg rounded-3 shadow-sm" required>
                            <option value="">Select Staff</option>
                            @php
                                $staffUsers = \App\Models\User::where('role', 'staff')->get();
                            @endphp
                            @foreach($staffUsers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Camera Section --}}
                    <div class="mb-4">
                        <label class="form-label fw-medium">Visitor Photo</label>
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="startCameraBtn">
                                <i class="bi bi-camera"></i> Start Camera
                            </button>
                            <button type="button" class="btn btn-success btn-sm d-none" id="capturePhotoBtn">
                                <i class="bi bi-camera-fill"></i> Capture
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm d-none" id="stopCameraBtn">
                                <i class="bi bi-stop-circle"></i> Stop
                            </button>
                        </div>

                        <div id="cameraContainer" class="mt-2" style="display: none;">
                            <video id="cameraVideo" autoplay playsinline class="rounded-3 shadow-sm border" style="width:100%; max-width:400px;"></video>
                            <canvas id="photoCanvas" style="display:none;"></canvas>
                        </div>

                        <div id="photoPreview" class="mt-2" style="display: none;">
                            <img id="photoPreviewImg" src="" alt="Photo Preview" class="rounded-3 shadow-sm border" style="width:100%; max-width:300px;">
                            <button type="button" class="btn btn-sm btn-danger mt-2" id="removePhotoBtn">
                                <i class="bi bi-x-circle"></i> Remove
                            </button>
                        </div>

                        <input type="hidden" name="photo_data" id="photoDataInput">
                        <small class="text-muted d-block mt-1">Start the camera to capture visitor’s photo in real-time.</small>
                    </div>

                    <button class="btn btn-primary w-100 py-2 shadow-sm" type="submit">
                        <i class="bi bi-check-circle me-2"></i> Check In Visitor
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Live search
    document.getElementById('searchVisitors').addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        const cards = document.querySelectorAll('.visitor-card');
        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            card.parentElement.style.display = text.includes(query) ? '' : 'none';
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
            startCameraBtn.classList.add('d-none');
            capturePhotoBtn.classList.remove('d-none');
            stopCameraBtn.classList.remove('d-none');
        } catch (error) {
            console.error(error);
            alert('Unable to access camera. Check permissions.');
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
        startCameraBtn.classList.remove('d-none');
        capturePhotoBtn.classList.add('d-none');
        stopCameraBtn.classList.add('d-none');
    }
</script>
@endsection
