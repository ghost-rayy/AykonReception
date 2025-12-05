<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Aykon Reception</title>
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

    .register-container {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 1000px;
    }

    .register-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr;
        min-height: 700px;
        animation: fadeIn 0.6s ease-out;
    }

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

    /* Left Section - Form */
    .form-section {
        padding: 50px 45px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #ffffff;
        overflow-y: auto;
        max-height: 90vh;
    }

    .header-section {
        margin-bottom: 32px;
    }

    .logo-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #0d1b2a, #1a2f47);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 0px;
        letter-spacing: 0.5px;
    }

    .header-section h1 {
        font-size: 36px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 10px;
        line-height: 1.2;
    }

    .header-section .aykon {
        background: linear-gradient(135deg, #0d1b2a, #1a2f47);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .header-section p {
        font-size: 15px;
        color: #64748b;
        margin: 0;
    }

    .input-box {
        margin-bottom: 20px;
        position: relative;
        animation: slideIn 0.6s ease-out;
        animation-fill-mode: both;
    }

    .input-box:nth-child(1) { animation-delay: 0.1s; }
    .input-box:nth-child(2) { animation-delay: 0.15s; }
    .input-box:nth-child(3) { animation-delay: 0.2s; }
    .input-box:nth-child(4) { animation-delay: 0.25s; }
    .input-box:nth-child(5) { animation-delay: 0.3s; }
    .input-box:nth-child(6) { animation-delay: 0.35s; }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .input-box i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 18px;
        z-index: 2;
        transition: color 0.3s ease;
    }

    .input-box input {
        width: 100%;
        padding: 16px 16px 16px 50px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        outline: none;
        font-size: 15px;
        color: #1e293b;
        background: #f8fafc;
        transition: all 0.3s ease;
        font-family: 'Inter', sans-serif;
    }

    .input-box input:focus {
        background: #ffffff;
        border-color: #0d1b2a;
        box-shadow: 0 0 0 4px rgba(13, 27, 42, 0.1);
    }

    .input-box input:focus + i {
        color: #0d1b2a;
    }

    .input-box input::placeholder {
        color: #94a3b8;
    }

    .error-message {
        color: #dc2626;
        font-size: 13px;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-register {
        background: linear-gradient(135deg, #0d1b2a, #1a2f47);
        color: white;
        border: none;
        padding: 16px 28px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(13, 27, 42, 0.4);
        animation: slideIn 0.6s ease-out;
        animation-delay: 0.4s;
        animation-fill-mode: both;
        font-family: 'Inter', sans-serif;
        margin-top: 8px;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(13, 27, 42, 0.5);
        background: linear-gradient(135deg, #0088ee, #00c4ef);
    }

    .btn-register:active {
        transform: translateY(0);
    }

    /* Right Section - Branding */
    .branding-section {
        background: linear-gradient(135deg, #0d1b2a 0%, #1a2f47 100%);
        padding: 50px 45px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .branding-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="30"/></g></svg>');
        opacity: 0.5;
    }

    .branding-content {
        position: relative;
        z-index: 1;
        text-align: center;
        width: 100%;
    }

    .logo-container {
        margin-bottom: 30px;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    .logo-container img {
        width: 200px;
        height: 80px;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        background: white;
        padding: 10px;
    }

    .branding-title {
        font-size: 32px;
        font-weight: 700;
        color: white;
        margin-bottom: 12px;
    }

    .branding-subtitle {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 24px;
        line-height: 1.6;
    }

    .features-list {
        list-style: none;
        padding: 0;
        margin: 0 0 40px 0;
        text-align: left;
        max-width: 280px;
        margin-left: auto;
        margin-right: auto;
    }

    .features-list li {
        color: rgba(255, 255, 255, 0.95);
        font-size: 14px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
    }

    .features-list li i {
        font-size: 18px;
        color: white;
        background: rgba(255, 255, 255, 0.2);
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 12px;
        width: 100%;
        max-width: 280px;
        margin: 0 auto;
    }

    .btn-secondary-action {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
        padding: 14px 24px;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-family: 'Inter', sans-serif;
    }

    .btn-secondary-action:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        color: white;
        text-decoration: none;
    }

    /* Alert Messages */
    .alert {
        border-radius: 12px;
        padding: 14px 18px;
        font-size: 14px;
        margin-bottom: 20px;
        border: none;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
    }

    /* Mobile Responsive */
    @media (max-width: 968px) {
        .register-card {
            grid-template-columns: 1fr;
            min-height: auto;
        }

        .form-section,
        .branding-section {
            padding: 40px 30px;
        }

        .branding-section {
            order: -1;
            padding: 30px;
        }

        .logo-container img {
            width: 100px;
            height: 100px;
        }

        .branding-title {
            font-size: 28px;
        }

        .form-section {
            max-height: none;
        }
    }

    @media (max-width: 576px) {
        body {
            padding: 12px;
        }

        .form-section,
        .branding-section {
            padding: 30px 24px;
        }

        .header-section h1 {
            font-size: 28px;
        }

        .branding-title {
            font-size: 24px;
        }

        .action-buttons {
            max-width: 100%;
        }

        .features-list {
            max-width: 100%;
        }
    }

    /* Spinner animation */
    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <!-- Left Section - Registration Form -->
            <div class="form-section">
                <div class="header-section">
                    <div class="logo-badge">
                        <i class="bi bi-building"></i>
                        <span>AYKON RECEPTION</span>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(session('status'))
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf

                    <div class="input-box">
                        <input 
                            type="text" 
                            name="name" 
                            id="name"
                            value="{{ old('name') }}" 
                            placeholder="Enter your full name" 
                            required
                            autocomplete="name"
                            autofocus
                        >
                        <i class="bi bi-person"></i>
                        @error('name')
                            <div class="error-message">
                                <i class="bi bi-exclamation-circle"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="input-box">
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            value="{{ old('email') }}" 
                            placeholder="Enter your email address" 
                            required
                            autocomplete="email"
                        >
                        <i class="bi bi-envelope"></i>
                        @error('email')
                            <div class="error-message">
                                <i class="bi bi-exclamation-circle"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="input-box">
                        <input 
                            type="tel" 
                            name="phone" 
                            id="phone"
                            value="{{ old('phone') }}" 
                            placeholder="Enter your phone number" 
                            required
                            autocomplete="tel"
                        >
                        <i class="bi bi-telephone"></i>
                        @error('phone')
                            <div class="error-message">
                                <i class="bi bi-exclamation-circle"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="input-box">
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            placeholder="Create a password" 
                            required
                            autocomplete="new-password"
                        >
                        <i class="bi bi-lock"></i>
                        @error('password')
                            <div class="error-message">
                                <i class="bi bi-exclamation-circle"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="input-box">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation"
                            placeholder="Confirm your password" 
                            required
                            autocomplete="new-password"
                        >
                        <i class="bi bi-lock-fill"></i>
                    </div>

                    <div class="input-box">
                        <input 
                            type="text" 
                            name="staff_code" 
                            id="staff_code"
                            value="{{ old('staff_code') }}" 
                            placeholder="Staff Code (Optional)"
                            autocomplete="off"
                        >
                        <i class="bi bi-id-card"></i>
                        @error('staff_code')
                            <div class="error-message">
                                <i class="bi bi-exclamation-circle"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-register">
                        <i class="bi bi-person-plus"></i>
                        <span>Create Account</span>
                    </button>
                </form>
            </div>

            <!-- Right Section - Branding -->
            <div class="branding-section">
                <div class="branding-content">
                    <div class="logo-container">
                        <img src="{{ asset('logo.png') }}" alt="Aykon Logo" onerror="this.style.display='none'">
                    </div>
                    <h2 class="branding-title">Welcome to Aykonsult Information Systems</h2>
                    
                    <ul class="features-list">
                        <li>
                            <i class="bi bi-shield-check"></i>
                            <span>Secure & Reliable</span>
                        </li>
                        <li>
                            <i class="bi bi-clock"></i>
                            <span>24/7 Access</span>
                        </li>
                        <li>
                            <i class="bi bi-people"></i>
                            <span>Easy Management</span>
                        </li>
                    </ul>
                    
                    <div class="action-buttons">
                        <a href="{{ route('login') }}" class="btn-secondary-action">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Already have an account?</span>
                        </a>
                        <a href="{{ route('check-in') }}" class="btn-secondary-action">
                            <i class="bi bi-person-check"></i>
                            <span>Visitor Check-In</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form validation enhancement
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            
            if (password !== passwordConfirmation) {
                e.preventDefault();
                alert('Passwords do not match. Please try again.');
                return false;
            }
        });

        // Add loading state to button
        document.getElementById('registerForm').addEventListener('submit', function() {
            const btn = document.querySelector('.btn-register');
            btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i><span>Creating account...</span>';
            btn.disabled = true;
        });

        // Real-time password match validation
        const passwordInput = document.getElementById('password');
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        
        passwordConfirmationInput.addEventListener('input', function() {
            if (this.value && passwordInput.value !== this.value) {
                this.style.borderColor = '#dc2626';
            } else {
                this.style.borderColor = '#e2e8f0';
            }
        });
    </script>
</body>
</html>
