<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Inventory System - Change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="{{ asset('build/assets/css/style.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body class="login-page">
    <div class="bg-anim"></div>

    <div class="college">
        <img src="{{ asset('build/assets/images/logo.png') }}" alt="Logo" class="logo">
        <h1>College of Medicine University of Ibadan</h1>
    </div>

    <main class="login-card" role="main">
        <h1 class="logo1">New Password</h1>
        <p class="muted">Set a secure password to continue.</p>

        <form action="{{ route('password.update') }}" method="POST" id="changePasswordForm" novalidate>
            @csrf
            
            {{-- New Password Field --}}
            <div class="field" style="position: relative;">
                <i class="fa fa-lock icon"></i>
                <input name="password" id="password" type="password" placeholder="New Password" required 
                       style="padding-right: 45px;" onkeyup="checkPasswordStrength()" />
                <i class="fa fa-eye" id="toggle1" onclick="toggleVisibility('password', this)" 
                   style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #777;"></i>
            </div>

            {{-- Strength Meter --}}
            <div style="height: 4px; width: 100%; background: #eee; border-radius: 4px; margin: -5px 0 15px 0; overflow: hidden;">
                <div id="strength-bar" style="height: 100%; width: 0%; transition: 0.3s ease;"></div>
            </div>
            <p id="strength-text" style="font-size: 11px; margin-top: -12px; margin-bottom: 15px; color: #666;">Strength: 0%</p>

            {{-- Confirm Password Field --}}
            <div class="field" style="position: relative;">
                <i class="fa fa-check icon"></i>
                <input name="password_confirmation" id="password_confirmation" type="password" placeholder="Confirm New Password" required 
                       style="padding-right: 45px;" />
                <i class="fa fa-eye" id="toggle2" onclick="toggleVisibility('password_confirmation', this)" 
                   style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #777;"></i>
            </div>
            
            <div class="field actions">
                <button type="submit" class="btn" id="submitBtn">Update Password</button>
            </div>

            @if ($errors->any())
                <div style="color: #dc3545; background: #fff1f0; border: 1px solid #ffa39e; padding: 10px; border-radius: 4px; margin-top: 15px; font-size: 13px;">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif
        </form>
    </main>

    <script>
        function toggleVisibility(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const bar = document.getElementById('strength-bar');
            const text = document.getElementById('strength-text');
            let strength = 0;

            if (password.length >= 8) strength += 33;
            if (/^[A-Z]/.test(password)) strength += 33;
            if (/[0-9]/.test(password)) strength += 34;

            bar.style.width = strength + "%";
            
            if (strength <= 33) {
                bar.style.backgroundColor = "#ff4d4f";
                text.innerText = "Weak: Need 8+ chars and Capital start";
            } else if (strength <= 66) {
                bar.style.backgroundColor = "#ffc107";
                text.innerText = "Medium: Almost there, add numbers";
            } else {
                bar.style.backgroundColor = "#52c41a";
                text.innerText = "Strong: Perfect!";
            }
        }

        document.getElementById('changePasswordForm').onsubmit = function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.style.opacity = '0.7';
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        };
    </script>
</body>
</html>