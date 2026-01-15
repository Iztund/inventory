<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>College of Medicine Inventory</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" type="image/png" href="{{ asset('build/assets/images/logo.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('build/assets/css/auth/login.css') }}" />
    
    <style>
        .error-alert {
            background-color: #fff1f2;
            border: 1px solid #ffe4e6;
            color: #e11d48;
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }
        .form-group.has-error {
            border-color: #e11d48 !important;
            box-shadow: 0 0 0 1px #e11d48;
        }
    </style>
</head>

<body>
    <div class="bg-mesh">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <div class="card-container">
        <header class="brand-header">
            <div class="logo-badge">
                <img src="{{ asset('build/assets/images/logo.png') }}" alt="CoM UI Logo">
            </div>
            <div class="brand-text">
                <h1>College of Medicine</h1>
                <span>University of Ibadan</span>
            </div>
        </header>

        <main class="login-card">
            <h2>Inventory System</h2>
            <p class="subtitle">Securely sign in to the administrative portal</p>

            {{-- Display Validation Errors --}}
            @if ($errors->any())
                <div class="error-alert">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form id="loginForm" action="{{ route('login.submit') }}" method="post" novalidate>
                @csrf
                <div class="form-group {{ $errors->any() ? 'has-error' : '' }}">
                    <i class="fa-solid fa-user-doctor main-icon"></i> 
                    <input id="identifier" name="identifier" type="text" placeholder="Username or Email" value="{{ old('identifier') }}" required>
                </div>

                <div class="form-group {{ $errors->any() ? 'has-error' : '' }}">
                    <i class="fa-solid fa-shield-halved main-icon"></i> 
                    <input id="password" name="password" type="password" placeholder="Password" required>
                    <i class="fa-solid fa-eye eye-toggle" id="eyeToggle" onclick="togglePass()"></i>
                </div>

                <button id="submitBtn" type="submit" class="btn-auth">
                    <span id="btnText">Sign In</span>
                </button>
            </form>
        </main>
    </div>

    <script>
        /**
         * Toggles the visibility of the password input field
         */
        function togglePass() {
            const passInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeToggle');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passInput.type = 'password';
                eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        /**
         * Handles form submission state (Loading UI)
         */
        document.getElementById('loginForm').onsubmit = function() {
            const btn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            
            // Disable button to prevent multiple clicks
            btn.disabled = true;
            btn.style.opacity = '0.7';
            btn.style.cursor = 'not-allowed';
            
            // Show medical-themed loading state
            btnText.innerHTML = '<i class="fas fa-dna fa-spin me-2"></i> Accessing...';
        };
    </script>
</body>
</html>