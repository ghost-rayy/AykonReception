<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Check-In - Aykon Reception</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, #0d1b2a 0%, #1a2f47 50%, #0d1b2a 100%);
        font-family: 'Inter', sans-serif;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        position: relative;
        overflow-x: hidden;
    }

    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .check-in-container {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 1200px;
    }

    .check-in-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr;
        min-height: 700px;
    }

    /* Left Section - Form */
    .form-section {
        padding: 40px;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }

    .header-section {
        margin-bottom: 32px;
    }

    .header-section .logo-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #0d1b2a, #1a2f47);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .header-section h1 {
        font-size: 32px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
        line-height: 1.2;
    }

    .header-section p {
        font-size: 15px;
        color: #64748b;
        margin: 0;
    }

    .form-group {
        margin-bottom: 20px;
        position: relative;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-label i {
        font-size: 14px;
        color: #0d1b2a;
    }

    .form-control,
    .form-select {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
        font-size: 15px;
        color: #1e293b;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control:focus,
    .form-select:focus {
        background: #ffffff;
        border-color: #0d1b2a;
        box-shadow: 0 0 0 4px rgba(13, 27, 42, 0.1);
        outline: none;
    }

    .form-control::placeholder {
        color: #94a3b8;
    }

    /* Right Section - Camera */
    .camera-section {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .camera-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%230d1b2a" fill-opacity="0.03"><circle cx="30" cy="30" r="30"/></g></svg>');
        opacity: 0.5;
    }

    .camera-header {
        text-align: center;
        margin-bottom: 32px;
        position: relative;
        z-index: 1;
    }

    .camera-header h3 {
        font-size: 20px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .camera-header p {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }

    .video-wrapper {
        position: relative;
        width: 320px;
        height: 320px;
        margin: 0 auto 24px;
        z-index: 1;
    }

    .video-wrapper::before {
        content: '';
        position: absolute;
        top: -8px;
        left: -8px;
        right: -8px;
        bottom: -8px;
        background: linear-gradient(135deg, #0d1b2a, #1a2f47);
        border-radius: 50%;
        z-index: -1;
        animation: pulse-ring 2s ease-in-out infinite;
    }

    @keyframes pulse-ring {
        0%, 100% {
            transform: scale(1);
            opacity: 0.8;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.6;
        }
    }

    #video {
        width: 320px;
        height: 320px;
        object-fit: cover;
        border-radius: 50%;
        border: 6px solid #ffffff;
        box-shadow: 0 10px 40px rgba(13, 27, 42, 0.3);
        background: #e2e8f0;
    }

    #canvas {
        width: 320px;
        height: 320px;
        object-fit: cover;
        border-radius: 50%;
        border: 6px solid #ffffff;
        box-shadow: 0 10px 40px rgba(13, 27, 42, 0.3);
        display: none;
    }

    .video-controls {
        display: flex;
        justify-content: center;
        gap: 12px;
        position: relative;
        z-index: 1;
    }

    /* Buttons */
    .btn {
        border-radius: 12px;
        padding: 14px 28px;
        font-weight: 600;
        font-size: 15px;
        border: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0d1b2a, #1a2f47);
        color: white;
        box-shadow: 0 4px 15px rgba(13, 27, 42, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(13, 27, 42, 0.5);
        background: linear-gradient(135deg, #0088ee, #00c4ef);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    #captureBtn {
        background: linear-gradient(135deg, #facc15, #eab308);
        color: #1e293b;
        box-shadow: 0 4px 15px rgba(250, 204, 21, 0.4);
    }

    #captureBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(250, 204, 21, 0.5);
    }

    .btn-outline-secondary {
        border: 2px solid #cbd5e1;
        color: #64748b;
        background: white;
    }

    .btn-outline-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #475569;
    }

    .button-section {
        margin-top: 32px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .button-section .btn {
        flex: 1;
        min-width: 140px;
        justify-content: center;
    }

    /* Alerts */
    .alert {
        border-radius: 12px;
        padding: 14px 18px;
        font-size: 14px;
        border: none;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .alert ul {
        margin: 0;
        padding-left: 20px;
    }

    /* Loading State */
    .camera-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #0d1b2a;
        font-size: 24px;
        z-index: 2;
    }

    /* Mobile Responsive */
    @media (max-width: 968px) {
        .check-in-card {
            grid-template-columns: 1fr;
            min-height: auto;
        }

        .form-section,
        .camera-section {
            padding: 30px 24px;
        }

        .header-section h1 {
            font-size: 28px;
        }

        .video-wrapper {
            width: 260px;
            height: 260px;
        }

        #video,
        #canvas {
            width: 260px;
            height: 260px;
        }

        .button-section {
            flex-direction: column;
        }

        .button-section .btn {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        body {
            padding: 12px;
        }

        .form-section,
        .camera-section {
            padding: 24px 20px;
        }

        .header-section h1 {
            font-size: 24px;
        }

        .video-wrapper {
            width: 220px;
            height: 220px;
        }

        #video,
        #canvas {
            width: 220px;
            height: 220px;
        }
    }

    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .check-in-card {
        animation: fadeIn 0.6s ease-out;
    }

    .form-group {
        animation: fadeIn 0.6s ease-out;
        animation-fill-mode: both;
    }

    .form-group:nth-child(1) { animation-delay: 0.1s; }
    .form-group:nth-child(2) { animation-delay: 0.2s; }
    .form-group:nth-child(3) { animation-delay: 0.3s; }
    .form-group:nth-child(4) { animation-delay: 0.4s; }
    .form-group:nth-child(5) { animation-delay: 0.5s; }
    .form-group:nth-child(6) { animation-delay: 0.6s; }
    </style>
</head>
<body>

    <div class="check-in-container">
        <div class="check-in-card">
            <!-- Left Section - Form -->
            <div class="form-section">
                <div class="header-section">
                    <div class="logo-badge">
                        <i class="bi bi-building"></i>
                        <span>AYKON RECEPTION</span>
                    </div>
                    <h1>Visitor Check-In</h1>
                    <p>Please fill in your details to complete the check-in process</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('check-in.store') }}" id="checkInForm">
                    @csrf

                    <div class="form-group">
                        <label for="name" class="form-label">
                            <i class="bi bi-person"></i>
                            Full Name
                        </label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i>
                            Email Address
                        </label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="your.email@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">
                            <i class="bi bi-telephone"></i>
                            Phone Number
                        </label>
                        <input type="tel" name="phone" id="phone" class="form-control" placeholder="0244556677" required>
                    </div>

                    <div class="form-group">
                        <label for="purpose" class="form-label">
                            <i class="bi bi-briefcase"></i>
                            Purpose of Visit
                        </label>
                        <input type="text" name="purpose" id="purpose" class="form-control" placeholder="e.g., Business Meeting, Interview" required>
                    </div>

                    <div class="form-group">
                        <label for="staff_to_visit" class="form-label">
                            <i class="bi bi-person-badge"></i>
                            Staff to Visit
                        </label>
                        <select name="staff_to_visit" id="staff_to_visit" class="form-select" required>
                            <option value="">Select a staff member</option>
                            @php
                                $staffUsers = \App\Models\User::where('role', 'staff')->get();
                            @endphp
                            @foreach($staffUsers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="button-section">
                        <button type="button" id="captureBtn" class="btn btn-primary">
                            <i class="bi bi-camera-fill"></i>
                            Capture Photo
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-circle"></i>
                            Complete Check-In
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Section - Camera -->
            <div class="camera-section">
                <div class="camera-header">
                    <h3><i class="bi bi-camera-video me-2"></i>Photo Capture</h3>
                    <p>Position yourself in the center of the frame</p>
                </div>

                <div class="video-wrapper">
                    <div class="camera-loading" id="cameraLoading" style="display: none;">
                        <i class="bi bi-camera-video"></i>
                    </div>
                    <video id="video" autoplay playsinline></video>
                    <canvas id="canvas"></canvas>
                </div>

                <div class="video-controls">
                    <button type="button" id="retakeBtn" class="btn btn-outline-secondary" style="display: none;">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Retake Photo
                    </button>
                </div>

                <input type="hidden" name="photo_data" id="photoData" required>
            </div>
        </div>
    </div>

    <!-- Camera Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const captureBtn = document.getElementById('captureBtn');
            const retakeBtn = document.getElementById('retakeBtn');
            const photoData = document.getElementById('photoData');
            const submitBtn = document.getElementById('submitBtn');
            const cameraLoading = document.getElementById('cameraLoading');
            const context = canvas.getContext('2d');
            let stream = null;

            // Initialize camera
            function initCamera() {
                cameraLoading.style.display = 'block';
                navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'user',
                        width: { ideal: 640 },
                        height: { ideal: 640 }
                    } 
                })
                .then(mediaStream => {
                    stream = mediaStream;
                    video.srcObject = stream;
                    cameraLoading.style.display = 'none';
                })
                .catch(error => {
                    cameraLoading.style.display = 'none';
                    alert("Camera access is required. Please allow camera permissions and refresh the page.");
                    console.error('Camera error:', error);
                });
            }

            initCamera();

            // Capture photo
            captureBtn.addEventListener('click', () => {
                const size = 320;
                canvas.width = size;
                canvas.height = size;
                
                // Draw circular crop from video
                context.save();
                context.beginPath();
                context.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2);
                context.clip();
                
                // Calculate scaling to fill circle
                const videoAspect = video.videoWidth / video.videoHeight;
                let drawWidth = size;
                let drawHeight = size;
                let offsetX = 0;
                let offsetY = 0;
                
                if (videoAspect > 1) {
                    drawHeight = size / videoAspect;
                    offsetY = (size - drawHeight) / 2;
                } else {
                    drawWidth = size * videoAspect;
                    offsetX = (size - drawWidth) / 2;
                }
                
                context.drawImage(video, offsetX, offsetY, drawWidth, drawHeight);
                context.restore();

                photoData.value = canvas.toDataURL('image/jpeg', 0.9);

                // Update UI
                video.style.display = 'none';
                canvas.style.display = 'block';
                captureBtn.style.display = 'none';
                retakeBtn.style.display = 'inline-flex';
                
                // Stop camera stream
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
            });

            // Retake photo
            retakeBtn.addEventListener('click', () => {
                video.style.display = 'block';
                canvas.style.display = 'none';
                captureBtn.style.display = 'inline-flex';
                retakeBtn.style.display = 'none';
                photoData.value = '';
                
                // Restart camera
                initCamera();
            });

            // Form validation
            document.getElementById('checkInForm').addEventListener('submit', function(e) {
                if (!photoData.value) {
                    e.preventDefault();
                    alert('Please capture your photo before submitting.');
                    captureBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }
            });

            // Cleanup on page unload
            window.addEventListener('beforeunload', () => {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
            });
        });
    </script>

</body>
</html>
