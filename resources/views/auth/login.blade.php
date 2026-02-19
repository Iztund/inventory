<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>COMIS - College of Medicine Inventory System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" type="image/png" href="{{ asset('build/assets/images/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Inter', 'sans-serif'] 
                    },
                    colors: {
                        emerald: { 
                            DEFAULT: '#059669', 
                            50: '#f0fdf4', 
                            100: '#dcfce7',
                            500: '#10b981',
                            600: '#059669', 
                            700: '#047857', 
                            800: '#065f46' 
                        }
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #10b981; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #059669; }

        /* Animation delay utility */
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }

        /* Smooth focus transitions */
        .group:focus-within .group-focus-icon { color: #059669; }
        .group:focus-within .group-focus-label { color: #059669; }
    </style>
</head>

<body class="min-h-screen bg-slate-950 font-sans antialiased selection:bg-emerald-500 selection:text-white overflow-x-hidden">

    <!-- Animated Background Blobs -->
    <div class="fixed inset-0 z-0 overflow-hidden">
        <div class="absolute top-0 -left-4 w-72 h-72 bg-emerald-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute top-20 right-10 w-72 h-72 bg-teal-500 rounded-full mix-blend-multiply filter blur-3xl opacity-15 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-4000"></div>
        <div class="absolute bottom-10 left-20 w-72 h-72 bg-emerald-700 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
    </div>

    <!-- Main Container -->
    <div class="relative z-10 min-h-screen d-flex align-items-center justify-content-center p-3 p-md-4 p-lg-5">
        
        <div class="row g-0 w-100 shadow-[0_35px_120px_-20px_rgba(0,0,0,0.7)] rounded-[2.5rem] overflow-hidden bg-white" style="max-width: 1150px;">
            
            <!-- Left Column: Branding Panel -->
            <div class="col-lg-6 d-none d-lg-flex bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 p-12 position-relative overflow-hidden">
                
                <!-- Grid Pattern Overlay -->
                <div class="position-absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(circle, rgba(255,255,255,0.3) 1px, transparent 1px); background-size: 30px 30px;"></div>
                
                <!-- Floating Orbs -->
                <div class="position-absolute top-0 end-0 w-40 h-40 bg-white opacity-5 rounded-circle blur-3xl"></div>
                <div class="position-absolute bottom-0 start-0 w-32 h-32 bg-teal-300 opacity-10 rounded-circle blur-2xl"></div>
                
                <div class="d-flex flex-column justify-content-between w-100 text-white position-relative" style="z-index: 10;">
                    
                    <!-- Top Section -->
                    <div>
                        <!-- Logo & Brand Header -->
                        <div class="d-flex align-items-center gap-3 mb-5">
                            <div class="bg-white bg-opacity-10 backdrop-blur-xl border border-white border-opacity-20 rounded-4 p-3 shadow-2xl">
                                <img src="{{ asset('build/assets/images/logo.png') }}" alt="College of Medicine Logo" class="img-fluid" style="width: 86px; height: 72px;">
                            </div>
                            <div class="d-flex flex-column align-items-center text-center w-100">
                                <h1 class="fs-2 fw-black mb-1 lh-1 text-white" style="letter-spacing: -0.05em;">
                                    College of Medicine
                                </h1>
                                
                                <h2 class="text-emerald-200 fw-bold mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.15em;">
                                    University of Ibadan
                                </h2>
                                
                                <div class="px-3 py-1 bg-white/10 rounded-full border border-white/20 mt-2">
                                    <p class="text-emerald-50 fw-medium mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.2em;">
                                        Inventory System
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Main Message -->
                        <div class="mb-5">
                            <h2 class="display-6 fw-bold mb-4 lh-sm" style="letter-spacing: -0.02em;">
                                Comprehensive Inventory<br>Management Portal
                            </h2>
                            <p class="text-emerald-20 mb-0 lh-base" style="max-width: 420px; opacity: 0.85; font-size: 0.95rem;">
                                Official inventory tracking system for the University of Ibadan College of Medicine. Track assets, requests and supplies across faculties, departments, units, offices, and institutes.
                            </p>
                        </div>

                        <!-- Feature Cards -->
                        <div class="d-flex flex-column gap-3">
                            <!-- Feature 1 -->
                            <div class="d-flex align-items-center gap-3 bg-dark bg-opacity-5 border border-white border-opacity-10 backdrop-blur-md rounded-3 p-3 shadow-sm" style="transition: all 0.3s;">
                                <div class="bg-emerald-500 rounded-3 p-2 d-flex align-items-center justify-content-center shadow-lg flex-shrink-0" style="width: 44px; height: 44px;">
                                    <i class="fas fa-microscope"></i>
                                </div>
                                <div>
                                    <p class="mb-1 fw-bold text-uppercase text-emerald-500" style="font-size: 0.7rem; letter-spacing: 0.15em;">Manage Equipments and Supplies</p>
                                    <p class="mb-0 text-white text-slate-200" style="font-size: 0.75rem;">Track research instruments, devices, items across faculties, departments, units and offices</p>
                                </div>
                            </div>

                            <!-- Feature 2 -->
                            <div class="d-flex align-items-center gap-3 bg-dark bg-opacity-5 border border-white border-opacity-10 backdrop-blur-md rounded-3 p-3 shadow-sm" style="transition: all 0.3s;">
                                <div class="bg-emerald-500 rounded-3 p-2 d-flex align-items-center justify-content-center shadow-lg flex-shrink-0" style="width: 44px; height: 44px;">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <div>
                                    <p class="mb-1 fw-bold text-uppercase text-emerald-500" style="font-size: 0.7rem; letter-spacing: 0.15em;">Procurement & Requests</p>
                                    <p class="mb-0 text-white text-slate-200" style="font-size: 0.75rem;">Streamline purchase orders & approvals</p>
                                </div>
                            </div>

                            <!-- Feature 3 -->
                            <div class="d-flex align-items-center gap-3 bg-dark bg-opacity-5 border border-white border-opacity-10 backdrop-blur-md rounded-3 p-3 shadow-sm" style="transition: all 0.3s;">
                                <div class="bg-emerald-500 rounded-3 p-2 d-flex align-items-center justify-content-center shadow-lg flex-shrink-0" style="width: 44px; height: 44px;">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div>
                                    <p class="mb-1 fw-bold text-uppercase text-emerald-500" style="font-size: 0.7rem; letter-spacing: 0.15em;">Real-Time Analytics</p>
                                    <p class="mb-0 text-white text-slate-200" style="font-size: 0.75rem;">Live dashboards & detailed reports</p>
                                </div>
                            </div>

                            <!-- Feature 4 -->
                            <div class="d-flex align-items-center gap-3 bg-dark bg-opacity-5 border border-white border-opacity-10 backdrop-blur-md rounded-3 p-3 shadow-sm" style="transition: all 0.3s;">
                                <div class="bg-emerald-500 rounded-3 p-2 d-flex align-items-center justify-content-center shadow-lg flex-shrink-0" style="width: 44px; height: 44px;">
                                    <i class="fas fa-shield-halved"></i>
                                </div>
                                <div>
                                    <p class="mb-1 fw-bold text-uppercase text-emerald-500" style="font-size: 0.7rem; letter-spacing: 0.15em;">Secure & Auditable</p>
                                    <p class="mb-0 text-white text-slate-200" style="font-size: 0.75rem;">Role-based access & audit trails</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Login Form -->
            <div class="col-lg-6 bg-white">
                <div class="p-5 p-md-6" style="padding-top: 3rem !important; padding-bottom: 3rem !important;">
                    
                    <!-- Mobile Logo (shown only on mobile) -->
                   <div class="d-lg-none mb-5 flex flex-col items-center">
                        <div class="flex items-center justify-center gap-3 mb-2">
                            <img src="{{ asset('build/assets/images/logo.png') }}" 
                                alt="College of Medicine Logo" 
                                class="img-fluid" 
                                style="width: 42px; height: 42px; object-fit: contain;">
                            
                            <div class="flex items-center gap-3">
                                <h2 class="mb-0 flex items-center gap-2">
                                    <span class="fs-3 fw-black text-slate-900" style="letter-spacing: -0.05em;">CoMUI</span>
                                    <div class="vr opacity-25" style="height: 25px;"></div>
                                    <span class="text-slate-600 fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.1em; line-height: 1.2;">
                                        Inventory<br><span class="text-emerald-600">Portal</span>
                                    </span>
                                </h2>
                            </div>
                        </div>

                        <p class="text-slate-500 fw-medium mb-0" style="font-size: 0.85rem; letter-spacing: 0.05em;">
                            College of Medicine Inventory System
                        </p>
                    </div>

                    <!-- Desktop Header -->
                    <div class="mb-5 text-center text-lg-start">
                        <h2 class="fw-black text-slate-900 mb-2" style="font-size: 2rem; letter-spacing: -0.03em;">Welcome Back</h2>
                        <p class="text-slate-500 fw-semibold mb-0" style="font-size: 0.95rem;">Enter your credentials to access the system</p>
                    </div>

                    <!-- Session Timeout Alert -->
                    @if(request()->query('reason') === 'timeout')
                    <div class="d-flex align-items-center gap-3 p-4 mb-5 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 shadow-sm">
                        <div class="bg-warning bg-opacity-15 rounded-2 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                            <i class="fas fa-clock text-warning fs-5"></i>
                        </div>
                        <div>
                            <p class="mb-1 fw-bold text-warning text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Session Expired</p>
                            <p class="mb-0 text-slate-700 fw-medium" style="font-size: 0.8rem;">Your session timed out. Please log in again to continue.</p>
                        </div>
                    </div>
                    @endif

                    <!-- Error Alert -->
                    @if ($errors->any())
                    <div class="d-flex align-items-center gap-3 p-4 mb-5 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3 shadow-sm">
                        <div class="bg-danger bg-opacity-15 rounded-2 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                            <i class="fas fa-triangle-exclamation text-danger fs-5"></i>
                        </div>
                        <div>
                            <p class="mb-1 fw-bold text-danger text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Authentication Failed</p>
                            <p class="mb-0 text-slate-700 fw-medium" style="font-size: 0.8rem;">{{ $errors->first() }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Login Form -->
                    <form id="loginForm" action="{{ route('login.submit') }}" method="POST" novalidate>
                        @csrf

                        <!-- Username/Email Input -->
                        <div class="mb-5 group">
                            <label for="identifier" class="d-block fw-black text-slate-500 text-uppercase mb-3 ms-1 group-focus-label" style="font-size: 0.7rem; letter-spacing: 0.15em; transition: color 0.3s;">
                                Account Username
                            </label>
                            <div class="position-relative">
                                <i class="fas fa-user-doctor position-absolute top-50 start-0 translate-middle-y ms-4 text-slate-300 group-focus-icon" style="font-size: 1.1rem; transition: color 0.3s;"></i>
                                <input 
                                    type="text" 
                                    id="identifier" 
                                    name="identifier" 
                                    required 
                                    autofocus
                                    value="{{ old('identifier') }}"
                                    class="form-control form-control-lg border-2 border-slate-100 rounded-3 fw-bold text-slate-800 bg-slate-50"
                                    style="padding: 1rem 1.25rem 1rem 3.5rem; font-size: 0.95rem; transition: all 0.3s; outline: none;"
                                    placeholder="Username"
                                    onfocus="this.classList.remove('border-slate-100', 'bg-slate-50'); this.classList.add('border-emerald-500', 'bg-white', 'shadow-lg');"
                                    onblur="if(!this.value) { this.classList.add('border-slate-100', 'bg-slate-50'); this.classList.remove('border-emerald-500', 'bg-white', 'shadow-lg'); }">
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="mb-5 group">
                            <label for="password" class="d-block fw-black text-slate-500 text-uppercase mb-3 ms-1 group-focus-label" style="font-size: 0.7rem; letter-spacing: 0.15em; transition: color 0.3s;">
                                Security Password
                            </label>
                            <div class="position-relative">
                                <i class="fas fa-lock position-absolute top-50 start-0 translate-middle-y ms-4 text-slate-400 group-focus-icon" style="font-size: 1.1rem; transition: color 0.3s;"></i>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    required
                                    class="form-control form-control-lg border-2 border-slate-100 rounded-3 fw-bold text-slate-800 bg-slate-50"
                                    style="padding: 1rem 3.5rem 1rem 3.5rem; font-size: 0.95rem; transition: all 0.3s; outline: none;"
                                    placeholder="••••••••••">
                                
                                <button 
                                    type="button" 
                                    id="togglePasswordBtn"
                                    class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-slate-300 text-decoration-none p-0 me-4 border-0 shadow-none"
                                    style="z-index: 20;">
                                    <i class="fas fa-eye text-slate-400" id="toggleIcon" style="font-size: 1.1rem; pointer-events: none;"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            id="submitBtn" 
                            class="btn btn-lg w-100 text-white fw-black text-uppercase rounded-3 shadow-lg d-flex align-items-center justify-content-center gap-3 border-0"
                            style="background: linear-gradient(135deg, #059669 0%, #047857 100%); padding: 1rem; font-size: 0.9rem; letter-spacing: 0.2em; transition: all 0.3s;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 20px 40px -10px rgba(5, 150, 105, 0.4)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px -5px rgba(5, 150, 105, 0.3)';">
                            <span id="btnText">Login</span>
                            <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="mt-48 pt-8 border-top border-slate-100 flex flex-col items-center justify-center">
    
                        <div class="flex items-center gap-2 mb-3">
                            <img src="{{ asset('build/assets/images/logo.png') }}" 
                                alt="College of Medicine Logo" 
                                class="block object-contain opacity-80" 
                                style="width: 20px; height: 20px;"> 
                                
                            <p class="mb-0 fw-bold text-slate-500 text-uppercase tracking-widest" 
                            style="font-size: 0.65rem;">
                                2026 College of Medicine, UI
                            </p>
                        </div>

                        <a href="mailto:support.itu@com.ui.edu.ng" 
                        class="text-emerald-600 fw-black text-uppercase text-decoration-none transition-all hover:text-emerald-800" 
                        style="font-size: 0.7rem; letter-spacing: 0.1em;">
                            <i class="fas fa-headset me-1"></i> Contact IT Support
                        </a>

                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle Password Visibility
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('togglePasswordBtn');
            const toggleIcon = document.getElementById('toggleIcon');

            if (toggleButton) {
                toggleButton.addEventListener('click', function() {
                    // Toggle the type attribute
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    // Toggle the eye / eye-slash icon
                    if (type === 'password') {
                        toggleIcon.classList.remove('fa-eye-slash');
                        toggleIcon.classList.add('fa-eye');
                    } else {
                        toggleIcon.classList.remove('fa-eye');
                        toggleIcon.classList.add('fa-eye-slash');
                        toggleIcon.classList.add('text-emerald-600'); // Optional: highlight when visible
                    }
                });
            }
        });

        // Form Submit Handler
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            
            btn.disabled = true;
            btn.style.opacity = '0.7';
            btn.style.cursor = 'wait';
            btnText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';
        });

        // Auto-dismiss alerts after 8 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.bg-warning, .bg-danger');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s, transform 0.5s';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 500);
                }, 8000);
            });
        });

        // Add ripple effect to buttons
        document.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.6);
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    pointer-events: none;
                    transform: scale(0);
                    animation: ripple 0.6s ease-out;
                `;
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => ripple.remove(), 600);
            });
        });

        // Ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>

</body>
</html>