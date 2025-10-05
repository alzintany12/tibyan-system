<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>تسجيل الدخول - منظومة التبيان</title>
    
    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: 20px;
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
        
        .login-right {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .logo {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .system-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .system-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .login-form h2 {
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 15px;
            font-size: 1rem;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .form-check {
            margin: 20px 0;
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .alert {
            border: none;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .features {
            margin-top: 30px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 1rem;
        }
        
        .feature-item i {
            margin-left: 15px;
            font-size: 1.2rem;
        }
        
        @media (max-width: 768px) {
            .login-left {
                padding: 40px 30px;
            }
            
            .login-right {
                padding: 40px 30px;
            }
            
            .system-title {
                font-size: 1.5rem;
            }
            
            .logo {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="row g-0 h-100">
            <!-- Left Side - Branding -->
            <div class="col-lg-6 d-none d-lg-block">
                <div class="login-left h-100">
                    <div>
                        <div class="logo">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <h1 class="system-title">منظومة التبيان</h1>
                        <p class="system-subtitle">نظام إدارة شامل للشركات القانونية</p>
                        
                        <div class="features">
                            <div class="feature-item">
                                <i class="fas fa-gavel"></i>
                                <span>إدارة القضايا والجلسات</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-users"></i>
                                <span>إدارة العملاء والمستندات</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>الفواتير والمصروفات</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-chart-bar"></i>
                                <span>التقارير والإحصائيات</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>التقويم والتنبيهات</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Login Form -->
            <div class="col-lg-6">
                <div class="login-right h-100">
                    <div class="login-form">
                        <!-- Mobile Logo -->
                        <div class="text-center d-lg-none mb-4">
                            <i class="fas fa-balance-scale text-primary" style="font-size: 3rem;"></i>
                            <h2 class="text-primary mt-2">منظومة التبيان</h2>
                        </div>
                        
                        <h2 class="d-none d-lg-block">تسجيل الدخول</h2>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                @foreach ($errors->all() as $error)
                                    {{ $error }}
                                @endforeach
                            </div>
                        @endif
                        
                        @if (session('error'))
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="البريد الإلكتروني" value="{{ old('email') }}" required autofocus>
                                <label for="email">
                                    <i class="fas fa-envelope me-2"></i>البريد الإلكتروني
                                </label>
                            </div>
                            
                            <div class="form-floating">
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="كلمة المرور" required>
                                <label for="password">
                                    <i class="fas fa-lock me-2"></i>كلمة المرور
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    تذكرني
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                تسجيل الدخول
                            </button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                © 2024 منظومة التبيان - جميع الحقوق محفوظة
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // تأثير على الحقول عند التركيز
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
        
        // إخفاء التنبيهات تلقائياً
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>