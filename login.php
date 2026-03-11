<?php
require_once 'config.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$show_success = isset($_GET['success']);

if ($_POST) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login • Financial Tracker</title>
    <link rel="shortcut icon" href="logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* LOGIN PAGE ENHANCEMENTS */
        :root {
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --primary-glow: 0 0 0 4px rgba(66, 153, 225, 0.3);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            overflow-x: hidden;
        }

        .login-wrapper {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 4rem;
            align-items: center;
            min-height: 100vh;
        }

        /* GLASS LOGIN CARD */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(30px);
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 35px 70px -20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
            animation: slideInRight 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4299e1, #667eea, #764ba2);
        }

        /* PREMIUM INPUTS WITH EYE */
        .input-group {
            position: relative;
            margin-bottom: 1.75rem;
        }

        .input-field {
            width: 100%;
            padding: 1.25rem 1rem 1.25rem 3.5rem;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 16px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: inherit;
        }

        .input-field:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: var(--primary-glow);
            background: white;
            transform: translateY(-2px);
        }

        .input-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.25rem;
            color: #64748b;
            z-index: 2;
            transition: color 0.3s ease;
        }

        .input-field:focus + .input-icon { color: #4299e1; }

        /* PASSWORD EYE TOGGLE */
        .password-toggle {
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #64748b;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .password-toggle:hover {
            color: #4299e1;
            background: rgba(66, 153, 225, 0.1);
            transform: translateY(-50%) scale(1.1);
        }

        /* FLOATING LABELS */
        .input-label {
            position: absolute;
            left: 3.5rem;
            top: 1.1rem;
            font-size: 0.95rem;
            color: #94a3b8;
            pointer-events: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .input-field:focus + .input-label,
        .input-field:not(:placeholder-shown) + .input-label {
            top: 0.5rem;
            font-size: 0.75rem;
            color: #4299e1;
        }

        /* CHECKBOX */
        .checkbox-custom {
            width: 22px;
            height: 22px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
        }

        .checkbox-custom::after {
            content: '✔';
            position: absolute;
            font-size: 12px;
            color: white;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            transition: transform 0.2s ease;
        }

        input:checked + .checkbox-custom {
            background: #4299e1;
            border-color: #4299e1;
        }

        input:checked + .checkbox-custom::after {
            transform: translate(-50%, -50%) scale(1);
        }

        /* BUTTON */
        .btn-premium {
            width: 100%;
            padding: 1.25rem;
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 30px rgba(66, 153, 225, 0.4);
        }

        .btn-premium:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(66, 153, 225, 0.5);
        }

        .btn-premium:active {
            transform: translateY(-1px);
        }

        /* MESSAGES */
        .message {
            padding: 1.25rem;
            border-radius: 16px;
            margin-bottom: 1.75rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideDown 0.4s ease-out;
        }

        .error { 
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #dc2626;
            border: 1px solid #fca5a5;
        }

        .success { 
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #166534;
            border: 1px solid #86efac;
        }

        /* ANIMATIONS */
        @keyframes slideInRight {
            from { 
                opacity: 0; 
                transform: translateX(50px); 
            }
            to { 
                opacity: 1; 
                transform: translateX(0); 
            }
        }

        @keyframes slideDown {
            from { 
                opacity: 0; 
                transform: translateY(-20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                gap: 2rem;
                padding: 1rem;
            }
            
            .login-card {
                margin: 0 auto;
                padding: 2rem;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 1.5rem;
                margin: 1rem;
            }
        }
    </style>
</head>
<body class="login-page">
    <div class="login-wrapper">
        <!-- Enhanced Branding -->
        <div class="login-left">
            <div class="login-branding" style="animation: slideInLeft 0.8s ease-out;">
                <div class="logo" style="margin-bottom: 2rem;">
                    <span class="logo-icon" style="
                        width: 64px; height: 64px; border-radius: 16px; 
                        background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);
                        display: flex; align-items: center; justify-content: center;
                    ">
                        <img src="logo.png" alt="Finance Tracker" style="width: 40px; height: 40px;">
                    </span>
                    
                </div>
                
                <div class="welcome-text">
                    <h2 style="font-size: 2.5rem; font-weight: 800; color: white; margin-bottom: 1rem;">
                        Welcome Back
                    </h2>
                    <p style="font-size: 1.25rem; color: rgba(255,255,255,0.9); line-height: 1.7; max-width: 400px;">
                        Access your capital management suite featuring live expense tracking, income forecasting, and dynamic budget controls.
                    </p>
                </div>
                
                <div class="features" style="margin-top: 3rem;">
                    <div class="feature" style="
                        display: flex; align-items: center; gap: 1rem; padding: 1.5rem; 
                        background: rgba(255,255,255,0.15); border-radius: 16px; 
                        backdrop-filter: blur(10px); margin-bottom: 1rem;
                    ">
                        <span class="feature-icon" style="font-size: 1.5rem;">📈</span>
                        <span style="color: white; font-weight: 500;">Real-time analytics</span>
                    </div>
                    <div class="feature" style="
                        display: flex; align-items: center; gap: 1rem; padding: 1.5rem; 
                        background: rgba(255,255,255,0.15); border-radius: 16px; 
                        backdrop-filter: blur(10px); margin-bottom: 1rem;
                    ">
                        <span class="feature-icon" style="font-size: 1.5rem;">📱</span>
                        <span style="color: white; font-weight: 500;">Mobile responsive</span>
                    </div>
                    <div class="feature" style="
                        display: flex; align-items: center; gap: 1rem; padding: 1.5rem; 
                        background: rgba(255,255,255,0.15); border-radius: 16px;
                    ">
                        <span class="feature-icon" style="font-size: 1.5rem;">🔑</span>
                        <span style="color: white; font-weight: 500;">Security</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- PREMIUM LOGIN FORM -->
        <div class="login-right">
            <div class="login-card">
                <div class="card-header" style="text-align: center; margin-bottom: 2rem;">
                    <h3 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem;">
                        <i class="fas fa-sign-in-alt" style="color: #4299e1; margin-right: 0.5rem;"></i>
                        Sign In
                    </h3>
                    <p style="color: #64748b; font-size: 1.1rem;">Continue to your dashboard</p>
                </div>

                <?php if ($show_success): ?>
                    <div class="message success">
                        <i class="fas fa-check-circle"></i>
                        Account created successfully! Please login.
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="message error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="login-form" novalidate>
                    <!-- EMAIL INPUT -->
                    <div class="input-group">
                        <input class="input-field" 
                               type="email" 
                               name="email" 
                               id="email"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               required 
                               autocomplete="email">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <label class="input-label" for="email">Email Address</label>
                    </div>

                    <!-- PASSWORD INPUT WITH EYE -->
                    <div class="input-group">
                        <input class="input-field" 
                               type="password" 
                               name="password" 
                               id="password"
                               required 
                               autocomplete="current-password">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <label class="input-label" for="password">Password</label>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye-slash" id="toggle-icon"></i>
                        </button>
                    </div>

                    <!-- REMEMBER ME -->
                    <div class="form-options" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem;">
                        <label class="checkbox-label" style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; font-size: 0.95rem; color: #475569;">
                            <input type="checkbox" name="remember" id="remember" style="display: none;" required >
                            <span class="checkbox-custom"></span>
                            Remember me
                        </label>
                        
                    </div>

                    <!-- SIGN IN BUTTON -->
                    <button type="submit" class="btn-premium">
                        <i class="fas fa-sign-in-alt" style="margin-right: 0.5rem;"></i>
                        Sign In Securely
                    </button>
                </form>

                <div class="divider" style="
                    text-align: center; margin: 2rem 0; position: relative; color: #94a3b8;
                ">
                    <span style="background: white; padding: 0 1.5rem;">or</span>
                </div>

                <p class="signup-prompt" style="
                    text-align: center; color: #64748b; font-size: 1rem; margin: 0;
                ">
                    Don't have an account? 
                    <a href="register.php" style="
                        color: #4299e1; font-weight: 700; text-decoration: none;
                    ">Create one now</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // PASSWORD VISIBILITY TOGGLE
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggle-icon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }

        // INPUT LABEL ANIMATIONS
        document.querySelectorAll('.input-field').forEach(field => {
            field.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            field.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // FORM SUBMISSION ANIMATION
        document.querySelector('.login-form').addEventListener('submit', function() {
            const btn = document.querySelector('.btn-premium');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
            btn.style.opacity = '0.8';
        });

        // ENTER KEY SUBMIT
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('.login-form')?.submit();
            }
        });
    </script>
</body>
</html>
