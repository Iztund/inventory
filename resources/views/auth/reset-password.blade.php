<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Reset Password - CoMUI Inventory</title>
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

        /* Animation delay */
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }

        /* Password strength animations */
        @keyframes slideIn {
            from { width: 0%; opacity: 0; }
            to { opacity: 1; }
        }

        .strength-bar {
            animation: slideIn 0.5s ease-out;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-950 font-sans antialiased selection:bg-emerald-500 selection:text-white overflow-x-hidden">

    <!-- Animated Background Blobs -->
    <div class="fixed inset-0 z-0 overflow-hidden">
        <div class="absolute top-0 -left-4 w-72 h-72 bg-emerald-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute top-20 right-10 w-72 h-72 bg-teal-500 rounded-full mix-blend-multiply filter blur-3xl opacity-15 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-4000"></div>
    </div>

    <!-- Main Container -->
    <div class="relative z-10 min-h-screen d-flex align-items-center justify-content-center p-3 p-md-4">
        
        <div class="bg-white rounded-4 shadow-2xl overflow-hidden" style="max-width: 480px; width: 100%; box-shadow: 0 35px 120px -20px rgba(0,0,0,0.7);">
            
            <!-- Header Section -->
            <div class="text-center p-5 pb-4 bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 position-relative overflow-hidden">
                <!-- Grid Pattern -->
                <div class="position-absolute inset-0 opacity-10" style="background-image: radial-gradient(circle, rgba(255,255,255,0.3) 1px, transparent 1px); background-size: 30px 30px;"></div>
                
                <!-- Logo -->
                <div class="position-relative" style="z-index: 10;">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-15 backdrop-blur-lg rounded-4 p-3 mb-3 shadow-lg">
                        <img src="{{ asset('build/assets/images/logo.png') }}" alt="College of Medicine Logo" class="img-fluid" style="width: 56px; height: 56px;">
                    </div>
                    
                    <h1 class="fs-3 fw-black text-white mb-2" style="letter-spacing: -0.03em;">Set New Password</h1>
                    <p class="text-emerald-100 mb-0 fw-semibold" style="font-size: 0.9rem;">Create a secure password for your CoMUI Inventory account</p>
                </div>
            </div>

            <!-- Form Section -->
            <div class="p-5">
                
                <!-- Info Alert -->
                <div class="d-flex align-items-start gap-3 p-3 mb-4 bg-blue-50 border border-blue-200 rounded-3">
                    <div class="bg-blue-100 rounded-2 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                        <i class="fas fa-info-circle text-primary"></i>
                    </div>
                    <div>
                        <p class="mb-1 fw-bold text-primary" style="font-size: 0.75rem;">Password Requirements</p>
                        <ul class="mb-0 ps-3" style="font-size: 0.75rem; line-height: 1.6;">
                            <li>Minimum 8 characters</li>
                            <li>Start with uppercase letter</li>
                            <li>Include at least one number</li>
                        </ul>
                    </div>
                </div>

                <!-- Error Alert -->
                @if ($errors->any())
                <div class="d-flex align-items-center gap-3 p-3 mb-4 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3">
                    <div class="bg-danger bg-opacity-15 rounded-2 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                    </div>
                    <p class="mb-0 text-danger fw-semibold" style="font-size: 0.8rem;">{{ $errors->first() }}</p>
                </div>
                @endif

                <!-- Password Form -->
                <form action="{{ route('password.update') }}" method="POST" id="passwordForm" novalidate>
                    @csrf

                    <!-- New Password Input -->
                    <div class="mb-4">
                        <label for="password" class="d-block fw-black text-slate-700 text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 0.1em;">
                            <i class="fas fa-lock me-1 text-slate-400"></i> New Password
                        </label>
                        <div class="position-relative">
                            <i class="fas fa-key position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400" style="font-size: 1rem;"></i>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="form-control form-control-lg border-2 border-slate-200 rounded-3 fw-semibold text-slate-800"
                                style="padding: 0.9rem 3.5rem 0.9rem 3rem; font-size: 0.95rem;"
                                placeholder="Enter new password"
                                oninput="checkPasswordStrength()"
                                onfocus="this.classList.remove('border-slate-200'); this.classList.add('border-emerald-500', 'shadow-lg');"
                                onblur="if(!this.value) { this.classList.add('border-slate-200'); this.classList.remove('border-emerald-500', 'shadow-lg'); }">
                            <button 
                                type="button" 
                                onclick="togglePassword('password', 'toggleIcon1')" 
                                class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-slate-400 text-decoration-none p-0 me-3 border-0">
                                <i class="fas fa-eye" id="toggleIcon1" style="font-size: 1rem;"></i>
                            </button>
                        </div>

                        <!-- Password Strength Meter -->
                        <div class="mt-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-slate-600" style="font-size: 0.7rem;">Password Strength</span>
                                <span id="strengthText" class="fw-bold" style="font-size: 0.7rem;">Weak</span>
                            </div>
                            <div class="bg-slate-100 rounded-pill overflow-hidden" style="height: 8px;">
                                <div id="strengthBar" class="strength-bar rounded-pill" style="height: 100%; width: 0%; transition: width 0.3s ease, background-color 0.3s ease; background: #e5e7eb;"></div>
                            </div>
                            <div id="strengthHints" class="mt-2">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-circle text-slate-300" id="hint1Icon" style="font-size: 0.5rem;"></i>
                                    <span class="text-slate-500" id="hint1Text" style="font-size: 0.7rem;">At least 8 characters</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-circle text-slate-300" id="hint2Icon" style="font-size: 0.5rem;"></i>
                                    <span class="text-slate-500" id="hint2Text" style="font-size: 0.7rem;">Starts with uppercase letter</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-circle text-slate-300" id="hint3Icon" style="font-size: 0.5rem;"></i>
                                    <span class="text-slate-500" id="hint3Text" style="font-size: 0.7rem;">Contains at least one number</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password Input -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="d-block fw-black text-slate-700 text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 0.1em;">
                            <i class="fas fa-check-circle me-1 text-slate-400"></i> Confirm Password
                        </label>
                        <div class="position-relative">
                            <i class="fas fa-check-double position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400" style="font-size: 1rem;"></i>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                required
                                class="form-control form-control-lg border-2 border-slate-200 rounded-3 fw-semibold text-slate-800"
                                style="padding: 0.9rem 3.5rem 0.9rem 3rem; font-size: 0.95rem;"
                                placeholder="Re-enter password"
                                oninput="checkPasswordMatch()"
                                onfocus="this.classList.remove('border-slate-200'); this.classList.add('border-emerald-500', 'shadow-lg');"
                                onblur="if(!this.value) { this.classList.add('border-slate-200'); this.classList.remove('border-emerald-500', 'shadow-lg'); }">
                            <button 
                                type="button" 
                                onclick="togglePassword('password_confirmation', 'toggleIcon2')" 
                                class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-slate-400 text-decoration-none p-0 me-3 border-0">
                                <i class="fas fa-eye" id="toggleIcon2" style="font-size: 1rem;"></i>
                            </button>
                        </div>
                        <div id="matchMessage" class="mt-2" style="font-size: 0.75rem;"></div>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        id="submitBtn" 
                        class="btn btn-lg w-100 text-white fw-black text-uppercase rounded-3 shadow-lg border-0 mb-3"
                        style="background: linear-gradient(135deg, #059669 0%, #047857 100%); padding: 1rem; font-size: 0.9rem; letter-spacing: 0.15em; transition: all 0.3s;">
                        <i class="fas fa-lock me-2"></i>
                        <span id="btnText">Update Password</span>
                    </button>

                    <!-- Back to Login Link -->
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-emerald-600 text-decoration-none fw-bold d-inline-flex align-items-center gap-2" style="font-size: 0.85rem;">
                            <i class="fas fa-arrow-left"></i>
                            Back to Login
                        </a>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="p-4 bg-slate-50 border-top border-slate-200 text-center">
                <p class="mb-0 fw-bold text-slate-400 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.1em;">
                    <i class="fas fa-shield-halved me-1"></i> Secure Password Reset
                </p>
                <p class="mb-0 text-slate-400 mt-1" style="font-size: 0.65rem;">
                    College of Medicine Inventory System
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle Password Visibility
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Check Password Strength
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const bar = document.getElementById('strengthBar');
            const text = document.getElementById('strengthText');
            
            let strength = 0;
            let color = '#e5e7eb';
            let label = 'Weak';
            
            // Check length
            const hasLength = password.length >= 8;
            if (hasLength) strength += 33;
            updateHint(1, hasLength);
            
            // Check uppercase start
            const hasUpperStart = /^[A-Z]/.test(password);
            if (hasUpperStart) strength += 33;
            updateHint(2, hasUpperStart);
            
            // Check numbers
            const hasNumber = /[0-9]/.test(password);
            if (hasNumber) strength += 34;
            updateHint(3, hasNumber);
            
            // Set bar and text
            if (strength === 0) {
                color = '#e5e7eb';
                label = 'Weak';
                text.className = 'fw-bold text-slate-400';
            } else if (strength <= 33) {
                color = '#ef4444';
                label = 'Weak';
                text.className = 'fw-bold text-danger';
            } else if (strength <= 66) {
                color = '#f59e0b';
                label = 'Medium';
                text.className = 'fw-bold text-warning';
            } else {
                color = '#10b981';
                label = 'Strong';
                text.className = 'fw-bold text-success';
            }
            
            bar.style.width = strength + '%';
            bar.style.backgroundColor = color;
            text.textContent = label;
            
            // Also check password match
            checkPasswordMatch();
        }

        // Update individual hint
        function updateHint(hintNumber, passed) {
            const icon = document.getElementById('hint' + hintNumber + 'Icon');
            const text = document.getElementById('hint' + hintNumber + 'Text');
            
            if (passed) {
                icon.classList.remove('fa-circle', 'text-slate-300');
                icon.classList.add('fa-check-circle', 'text-success');
                text.classList.remove('text-slate-500');
                text.classList.add('text-success', 'fw-bold');
            } else {
                icon.classList.remove('fa-check-circle', 'text-success');
                icon.classList.add('fa-circle', 'text-slate-300');
                text.classList.remove('text-success', 'fw-bold');
                text.classList.add('text-slate-500');
            }
        }

        // Check Password Match
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            const message = document.getElementById('matchMessage');
            
            if (confirmation === '') {
                message.innerHTML = '';
                return;
            }
            
            if (password === confirmation) {
                message.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i><span class="text-success fw-bold">Passwords match</span>';
            } else {
                message.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i><span class="text-danger fw-bold">Passwords do not match</span>';
            }
        }

        // Form Submit Handler
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            
            // Validate password requirements
            if (password.length < 8 || !/^[A-Z]/.test(password) || !/[0-9]/.test(password)) {
                e.preventDefault();
                alert('Please meet all password requirements:\n• At least 8 characters\n• Start with uppercase letter\n• Include at least one number');
                return;
            }
            
            // Validate password match
            if (password !== confirmation) {
                e.preventDefault();
                alert('Passwords do not match. Please re-enter your password.');
                return;
            }
            
            // Show loading state
            const btn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            
            btn.disabled = true;
            btn.style.opacity = '0.7';
            btnText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        });
    </script>

</body>
</html>