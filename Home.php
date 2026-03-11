<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Tracker • Personal Finance Management Solution</title>
    <link rel="shortcut icon" href="logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        body.light {
            background-color: #f8fafc;
            color: #1f2937;
        }

        body.dark {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Header */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            color: #1f2937;
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        body.dark header {
            background: rgba(15, 23, 42, 0.95);
            border-bottom-color: #334155;
            color: #e2e8f0;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e40af;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        body.dark .logo {
            color: #60a5fa;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: #374151;
            font-weight: 500;
            transition: color 0.2s;
        }

        body.dark .nav-links a {
            color: #cbd5e1;
        }

        .nav-links a:hover {
            color: #1e40af;
        }

        body.dark .nav-links a:hover {
            color: #60a5fa;
        }

        .theme-toggle {
            background: none;
            border: 1px solid #d1d5db;
            color: #374151;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        body.dark .theme-toggle {
            background: #1e293b;
            border-color: #475569;
            color: #e2e8f0;
        }

        .theme-toggle:hover {
            background: #f3f4f6;
        }

        body.dark .theme-toggle:hover {
            background: #334155;
        }

        .cta-button {
            background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
            color: white;
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .cta-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(30, 64, 175, 0.3);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 140px 0 100px;
            text-align: center;
            transition: background 0.3s ease;
        }

        body.dark .hero {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        }

        .hero-content h1 {
            font-size: 3.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #111827;
            line-height: 1.1;
        }

        body.dark .hero-content h1 {
            color: #f8fafc;
        }

        .hero-content .subtitle {
            font-size: 1.25rem;
            color: #6b7280;
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        body.dark .hero-content .subtitle {
            color: #94a3b8;
        }

        /* Features */
        .features {
            padding: 100px 0;
            background: white;
            transition: background 0.3s ease;
        }

        body.dark .features {
            background: #0f172a;
        }

        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 4rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1rem;
        }

        body.dark .section-header h2 {
            color: #f8fafc;
        }

        .section-header p {
            font-size: 1.125rem;
            color: #6b7280;
        }

        body.dark .section-header p {
            color: #94a3b8;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2.5rem;
        }

        .feature-card {
            padding: 2.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: white;
            transition: all 0.2s ease;
        }

        body.dark .feature-card {
            background: #1e293b;
            border-color: #334155;
        }

        .feature-card:hover {
            border-color: #1e40af;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }

        body.dark .feature-card:hover {
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            border-color: #60a5fa;
        }

        .feature-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        body.dark .feature-card h3 {
            color: #f8fafc;
        }

        .feature-card p {
            color: #6b7280;
            font-size: 0.95rem;
        }

        body.dark .feature-card p {
            color: #cbd5e1;
        }

        /* Stats */
        .stats {
            padding: 100px 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            transition: background 0.3s ease;
        }

        body.dark .stats {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 3rem;
            margin-top: 3rem;
        }

        .stat-item h3 {
            font-size: 2.75rem;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 0.25rem;
        }

        body.dark .stat-item h3 {
            color: #60a5fa;
        }

        .stat-item p {
            font-size: 0.95rem;
            color: #6b7280;
            font-weight: 500;
        }

        body.dark .stat-item p {
            color: #94a3b8;
        }

        /* Footer */
        footer {
            background: #111827;
            color: #9ca3af;
            padding: 3rem 0 1.5rem;
            text-align: center;
            transition: background 0.3s ease;
        }

        body.light footer {
            background: #f8fafc;
            color: #6b7280;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: #d1d5db;
            text-decoration: none;
            font-weight: 500;
        }

        body.light .footer-links a {
            color: #6b7280;
        }

        .footer-links a:hover {
            color: white;
        }

        body.light .footer-links a:hover {
            color: #1f2937;
        }

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .nav-links {
                display: none;
            }
            
            .features-grid,
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }
    </style>
</head>
<body class="light">
    <!-- Header -->
    <header>
        <nav class="container">
            <a href="#" class="logo">
                <img src="logo.png" alt="Finance Tracker" style="width: 40px; height: 40px;"> Financial Tracker
            </a>
            <ul class="nav-links"></ul>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <button class="theme-toggle" id="themeToggle">
                    <span id="themeIcon">🌙</span>
                    <span id="themeText">Dark Mode</span>
                </button>
                <a href="login.php" class="cta-button">Login</a>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Personal Finance Management Solution</h1>
                <p class="subtitle">Track spending, set budgets, view insights securely</p>
                <a href="register.php" class="cta-button">Register</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-header">
                <h2>Your Financial Control</h2>
                <p>Intuitive tools for managing finances.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-header">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #1e40af, #3b82f6); color: white;">📊</div>
                        <div>
                            <h3>Expense Tracking</h3>
                        </div>
                    </div>
                    <p>Transaction logger: categories, tags, date filters.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-header">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #059669, #10b981); color: white;">💰</div>
                        <div>
                            <h3>Budget Management</h3>
                        </div>
                    </div>
                    <p>Category budgets with real-time tracking, overspend alerts.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-header">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #dc2626, #ef4444); color: white;">🔒</div>
                        <div>
                            <h3>Security</h3>
                        </div>
                    </div>
                    <p>Password hashing and encrypted data storage features.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-header">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #7c3aed, #a855f7); color: white;">📈</div>
                        <div>
                            <h3>Income Tracking</h3>
                        </div>
                    </div>
                    <p>Track multiple incomes with recurring projections.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-header">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #ea580c, #f97316); color: white;">🔍</div>
                        <div>
                            <h3>Transaction History</h3>
                        </div>
                    </div>
                    <p>Advanced search and export for transactions.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-header">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #16a34a, #22c55e); color: white;">🎯</div>
                        <div>
                            <h3>Goal Setting</h3>
                        </div>
                    </div>
                    <p>Automated milestone progress and notifications</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="section-header">
                <h2>Trusted by Finance Partners</h2>
            </div>
            <div class="stats-grid">
                <div class="stat-item">
                    <h3>1K+</h3>
                    <p>Active Users</p>
                </div>
                <div class="stat-item">
                    <h3>100%</h3>
                    <p>Budget Analysis</p>
                </div>
                <div class="stat-item">
                    <h3>10K+</h3>
                    <p>Transactions Processed</p>
                </div>
                <div class="stat-item">
                    <h3>100%</h3>
                    <p>Performance</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-links">
                    
                </div>
                <p>© 2026 Financial Tracker • All rights reserved • Personal Finance Management Solution.</p>
            </div>
        </div>
    </footer>

    <script>
        // Dark/Light Mode Toggle
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const themeText = document.getElementById('themeText');
        const body = document.body;

        // Check for saved theme preference or detect system preference
        const savedTheme = localStorage.getItem('theme') || 
                          (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        
        body.className = savedTheme;

        function initTheme() {
            if (savedTheme === 'dark') {
                themeIcon.textContent = '☀️';
                themeText.textContent = 'Light Mode';
            } else {
                themeIcon.textContent = '🌙';
                themeText.textContent = 'Dark Mode';
            }
        }

        function toggleTheme() {
            if (body.classList.contains('light')) {
                body.classList.remove('light');
                body.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                themeIcon.textContent = '☀️';
                themeText.textContent = 'Light Mode';
            } else {
                body.classList.remove('dark');
                body.classList.add('light');
                localStorage.setItem('theme', 'light');
                themeIcon.textContent = '🌙';
                themeText.textContent = 'Dark Mode';
            }
        }

        // Initialize theme on load
        initTheme();
        themeToggle.addEventListener('click', toggleTheme);

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                body.className = e.matches ? 'dark' : 'light';
                initTheme();
            }
        });
    </script>
</body>
</html>
