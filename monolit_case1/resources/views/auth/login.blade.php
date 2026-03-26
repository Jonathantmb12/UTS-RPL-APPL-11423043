<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MediTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }

        .login-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 500px;
        }

        .login-left {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .login-left h1 {
            font-size: 40px;
            margin-bottom: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-left i {
            font-size: 48px;
        }

        .login-left p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .features {
            text-align: left;
            margin-top: 30px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .feature-item i {
            font-size: 20px;
            flex-shrink: 0;
        }

        .login-right {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-right h2 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
            font-weight: 700;
        }

        .login-right p {
            color: #888;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #c33;
        }

        .demo-credentials {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
            border-left: 4px solid #667eea;
        }

        .demo-credentials h4 {
            color: #333;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .credential-item {
            background: white;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 8px;
            font-size: 13px;
            color: #555;
        }

        .credential-item strong {
            color: #667eea;
        }

        @media (max-width: 768px) {
            .login-grid {
                grid-template-columns: 1fr;
            }

            .login-left {
                display: none;
            }

            .login-right {
                padding: 40px 30px;
            }

            .login-left h1 {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-grid">
            <!-- Left Side -->
            <div class="login-left">
                <h1>
                    <i class="bi bi-hospital"></i>
                    MediTrack
                </h1>
                <p>Healthcare Platform Management System</p>
                <div class="features">
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>Patient Management</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>Doctor Scheduling</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>Prescription Management</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>Electronic Health Records</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>Pharmacy Inventory</span>
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="login-right">
                <h2>Sign In</h2>
                <p>Welcome back to MediTrack</p>

                @if ($errors->any())
                    <div class="error-message">
                        <strong>Login Failed:</strong>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/login">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="Enter your email" 
                            value="{{ old('email') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Enter your password" 
                            required
                        >
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="bi bi-lock"></i> Sign In
                    </button>
                </form>

                <!-- Demo Credentials -->
                <div class="demo-credentials">
                    <h4><i class="bi bi-info-circle"></i> Demo Credentials</h4>
                    <div class="credential-item">
                        <strong>👤 Admin:</strong> admin@meditrack.local / password123
                    </div>
                    <div class="credential-item">
                        <strong>👨‍⚕️ Doctor:</strong> dr.john@meditrack.local / password123
                    </div>
                    <div class="credential-item">
                        <strong>🩺 Patient:</strong> patient@meditrack.local / password123
                    </div>
                    <div class="credential-item">
                        <strong>💊 Pharmacist:</strong> pharmacist@meditrack.local / password123
                    </div>
                    <div style="margin-top: 10px; font-size: 12px; color: #666; border-top: 1px solid #ddd; padding-top: 10px;">
                        Other patients: jane@meditrack.local, bob@meditrack.local, alice@meditrack.local, etc.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
