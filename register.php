<?php
require_once 'config.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($email)) $errors[] = "Email is required";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    
    if (empty($password)) $errors[] = "Password is required";
    elseif (strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
    elseif ($password !== $confirm_password) $errors[] = "Passwords don't match";
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email already registered";
        }
    }
    
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->execute([$email, $hashed_password]);
        $success = "Account created successfully! <a href='login.php?success=1'>Login now →</a>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register • Financial Tracker</title>
    <link rel="shortcut icon" href="logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* REGISTER PAGE - MATCHES LOGIN PERFECTLY */
        :root {
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --primary-glow: 0 0 0 4px rgba(66, 153, 225, 0.3);
            --success-glow: 0 0 0 4px rgba(72, 187, 120, 0.3);
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

        .register-wrapper {
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

        /* GLASS REGISTER CARD */
        .register-card {
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

        .register-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4299e1, #667eea, #764ba2);
        }

        /* PREMIUM INPUTS */
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

        .input-field.success { border-color: #10b981; box-shadow: var(--success-glow); }
        .input-field.error { border-color: #ef4444; box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.2); }

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

        /* PASSWORD MATCH INDICATOR */
        .password-match {
            position: absolute;
            right: 3.5rem;
            top: 1.1rem;
            font-size: 1.25rem;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .match-valid { color: #10b981; }
        .match-invalid { color: #ef4444; }

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

        .btn-premium:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(66, 153, 225, 0.5);
        }

        .btn-premium:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
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
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .register-wrapper {
                grid-template-columns: 1fr;
                gap: 2rem;
                padding: 1rem;
            }
            
            .register-card {
                margin: 0 auto;
                padding: 2rem;
            }
        }

        @media (max-width: 480px) {
            .register-card {
                padding: 1.5rem;
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-wrapper">
        <!-- Enhanced Branding (Same as Login) -->
        <div class="register-left" style="animation: slideInLeft 0.8s ease-out;">
            <div style="margin-bottom: 2rem;">
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
                    Create Your Account
                </h2>
                <p style="font-size: 1.25rem; color: rgba(255,255,255,0.9); line-height: 1.7; max-width: 400px;">
                    Join a professional community tracking finances with enterprise-grade analytics and budgeting
                </p>
            </div>
            
            <div class="features" style="margin-top: 3rem;">
                <div class="feature" style="
                    display: flex; align-items: center; gap: 1rem; padding: 1.5rem; 
                    background: rgba(255,255,255,0.15); border-radius: 16px; 
                    backdrop-filter: blur(10px); margin-bottom: 1rem;
                ">
                    <span style="font-size: 1.5rem;">📈</span>
                    <span style="color: white; font-weight: 500;">Smart analytics</span>
                </div>
                <div class="feature" style="
                    display: flex; align-items: center; gap: 1rem; padding: 1.5rem; 
                    background: rgba(255,255,255,0.15); border-radius: 16px; 
                    backdrop-filter: blur(10px); margin-bottom: 1rem;
                ">
                    <span style="font-size: 1.5rem;">⚡</span>
                    <span style="color: white; font-weight: 500;">Lightning fast</span>
                </div>
                <div class="feature" style="
                    display: flex; align-items: center; gap: 1rem; padding: 1.5rem; 
                    background: rgba(255,255,255,0.15); border-radius: 16px;
                ">
                    <span style="font-size: 1.5rem;">🔑</span>
                    <span style="color: white; font-weight: 500;">Secure</span>
                </div>
            </div>
        </div>

        <!-- PREMIUM REGISTER FORM -->
        <div class="register-right">
            <div class="register-card">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <h3 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem;">
                        <i class="fas fa-user-plus" style="color: #4299e1; margin-right: 0.5rem;"></i>
                        Create Account
                    </h3>
                    <p style="color: #64748b; font-size: 1.1rem;">Start tracking your finances today</p>
                </div>

                <?php if ($success): ?>
                    <div class="message success">
                        <i class="fas fa-check-circle"></i>
                        <?= $success ?>
                    </div>
                <?php else: ?>
                    <?php if ($errors): ?>
                        <div class="message error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php foreach ($errors as $error): ?>
                                <div><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="register-form" novalidate>
                        <!-- EMAIL -->
                        <div class="input-group">
                            <input class="input-field" type="email" name="email" id="email" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autocomplete="email">
                            <span class="input-icon"><i class="fas fa-envelope"></i></span>
                            <label class="input-label" for="email">Email Address</label>
                        </div>

                        <!-- PASSWORD -->
                        <div class="input-group">
                            <input class="input-field" type="password" name="password" id="password" 
                                   minlength="6" required autocomplete="new-password">
                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                            <label class="input-label" for="password">Password (min 6 chars)</label>
                            <button type="button" class="password-toggle" onclick="togglePassword('password', 'toggle1')">
                                <i class="fas fa-eye-slash" id="toggle1"></i>
                            </button>
                        </div>

                        <!-- CONFIRM PASSWORD -->
                        <div class="input-group">
                            <input class="input-field" type="password" name="confirm_password" id="confirm_password" 
                                   minlength="6" required autocomplete="new-password">
                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                            <label class="input-label" for="confirm_password">Confirm Password</label>
                            <span class="password-match" id="match-indicator"></span>
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', 'toggle2')">
                                <i class="fas fa-eye-slash" id="toggle2"></i>
                            </button>
                        </div>

                        <button type="submit" class="btn-premium" id="register-btn">
                            <i class="fas fa-user-plus"></i>
                            Create Account
                        </button>
                    </form>

                    <div style="text-align: center; margin: 2rem 0; position: relative; color: #94a3b8;">
                        <span style="background: white; padding: 0 1.5rem;">or</span>
                    </div>

                    <p style="text-align: center; color: #64748b; font-size: 1rem; margin: 0;">
                        Already have an account? 
                        <a href="login.php" style="color: #4299e1; font-weight: 700; text-decoration: none;">
                            Sign in here
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // PASSWORD VISIBILITY TOGGLES
        function togglePassword(fieldId, toggleId) {
            const field = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(toggleId);
            
            if (field.type === 'password') {
                field.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                field.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }

        // REAL-TIME PASSWORD MATCH CHECK
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            const indicator = document.getElementById('match-indicator');
            const confirmField = document.getElementById('confirm_password');
            
            if (confirm.length === 0) {
                indicator.textContent = '';
                confirmField.classList.remove('success', 'error');
                return;
            }
            
            if (password === confirm && password.length >= 6) {
                indicator.innerHTML = '<i class="fas fa-check-circle match-valid"></i>';
                confirmField.classList.add('success');
                confirmField.classList.remove('error');
            } else {
                indicator.innerHTML = '<i class="fas fa-times-circle match-invalid"></i>';
                confirmField.classList.add('error');
                confirmField.classList.remove('success');
            }
        });

        // FORM VALIDATION & BUTTON STATE
        document.querySelector('.register-form').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            const email = document.getElementById('email').value;
            const btn = document.getElementById('register-btn');
            
            if (email && password.length >= 6 && password === confirm) {
                btn.disabled = false;
            } else {
                btn.disabled = true;
            }
        });

        // FORM SUBMISSION
        document.querySelector('.register-form').addEventListener('submit', function() {
            const btn = document.getElementById('register-btn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
            btn.disabled = true;
        });

        // ENTER KEY SUBMIT
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('.register-form')?.submit();
            }
        });
    </script>
</body>
</html>
