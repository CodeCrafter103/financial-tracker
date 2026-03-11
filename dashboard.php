<?php
require_once 'config.php';
if (!isLoggedIn()) header('Location: login.php');
$user_id = getUserId();

$total_expenses = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM expenses WHERE user_id = ? AND MONTH(date) = MONTH(CURDATE())");
$total_expenses->execute([$user_id]);
$total_expenses = $total_expenses->fetch()['total'];

$total_income = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM incomes WHERE user_id = ? AND MONTH(date) = MONTH(CURDATE())");
$total_income->execute([$user_id]);
$total_income = $total_income->fetch()['total'];

$balance = $total_income - $total_expenses;

// Get recent activity (last 5 transactions)
$recent_activity = $pdo->prepare("
    SELECT 'expense' as type, amount, category as title, date 
    FROM expenses WHERE user_id = ? 
    UNION ALL
    SELECT 'income' as type, amount, source as title, date 
    FROM incomes WHERE user_id = ? 
    ORDER BY date DESC LIMIT 5
");
$recent_activity->execute([$user_id, $user_id]);
$recent_activity = $recent_activity->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard • Financial Tracker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Perfect responsive background */
        .dashboard-page {
            background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                              url('dashboard.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            position: relative;
            overflow-x: hidden;
        }

        /* Perfect content overlay */
        .dashboard-wrapper {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            min-height: 100vh;
            max-width: 1400px;
            margin: 0 auto;
            box-shadow: 0 0 60px rgba(0,0,0,0.15);
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .dashboard-page {
                background-attachment: scroll;
            }
            .dashboard-wrapper {
                background: rgba(255, 255, 255, 0.98);
                margin: 0;
                backdrop-filter: blur(8px);
            }
        }

        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            .dashboard-wrapper {
                background: rgba(15, 23, 42, 0.98);
                color: #e2e8f0;
            }
        }

        /* Reset for full coverage */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body class="dashboard-page">
    <div class="dashboard-wrapper">
        <!-- Main Content -->
        <div class="dashboard-main">
            <!-- Header -->
            <header class="dashboard-header">
                <div class="header-left">
                    <div class="logo">
                        <span class="logo-icon"><img src="logo.png" alt=""></span>
                    </div>
                    <div class="welcome-user">
                        <h2 style="color:purple"><i>Good <span id="greeting-time"></span> <?php echo 'Admin'." "."0".$user_id; ?></i></h2>
                        <p>Your financial snapshot this month</p>
                    </div>
                </div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </header>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card income">
                    <div class="stat-icon">💰</div>
                    <div class="stat-content">
                        <h3>₹<?= number_format($total_income, 2) ?></h3>
                        <p>Total Income</p>
                    </div>
                </div>
                <div class="stat-card expense">
                    <div class="stat-icon">💸</div>
                    <div class="stat-content">
                        <h3>₹<?= number_format($total_expenses, 2) ?></h3>
                        <p>Total Expenses</p>
                    </div>
                </div>
                <div class="stat-card balance <?= $balance >= 0 ? 'positive' : 'negative' ?>">
                    <div class="stat-icon <?= $balance >= 0 ? 'positive' : 'negative' ?>"><?= $balance >= 0 ? '⚖️📈' : '⚖️📉' ?></div>
                    <div class="stat-content">
                        <h3>₹<?= number_format($balance, 2) ?></h3>
                        <p>Current Balance</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="actions-section">
                <h3>Quick Actions</h3>
                <div class="quick-actions">
                    <a href="expenses.php" class="btn btn-primary">
                        ➕ Add Expense
                    </a>
                    <a href="incomes.php" class="btn btn-success">
                        ➕ Add Income
                    </a>
                    <a href="budgets.php" class="btn btn-warning">
                        💳 Manage Budgets
                    </a>
                    <a href="history.php" class="btn btn-info">
                        📜 View History
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="activity-section">
                <h3>Recent Activity</h3>
                <div class="activity-list">
                    <?php foreach ($recent_activity as $item): ?>
                        <div class="activity-item <?= $item['type'] ?>">
                            <div class="activity-icon <?= $item['type'] ?>">
                                <?= $item['type'] === 'income' ? '💵' : '💸' ?>
                            </div>
                            <div class="activity-details">
                                <div class="activity-title"><?= htmlspecialchars($item['title']) ?></div>
                                <div class="activity-date"><?= date('M j, Y', strtotime($item['date'])) ?></div>
                            </div>
                            <div class="activity-amount <?= $item['type'] ?>">
                                <?= $item['type'] === 'income' ? '+' : '-' ?>₹<?= number_format($item['amount'], 2) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dynamic greeting based on time
        const hour = new Date().getHours();
        const greeting = document.getElementById('greeting-time');
        if (hour < 12) greeting.textContent = 'Morning';
        else if (hour < 17) greeting.textContent = 'Afternoon';
        else greeting.textContent = 'Evening';
    </script>
</body>
</html>
