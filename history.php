<?php
require_once 'config.php';
if (!isLoggedIn()) header('Location: login.php');
$user_id = getUserId();

$filter = $_GET['filter'] ?? 'all';

// Get ALL data first for counters, then filter
$all_expenses = $pdo->prepare("SELECT COUNT(*) as count FROM expenses WHERE user_id = ?");
$all_expenses->execute([$user_id]);
$expense_count = $all_expenses->fetch()['count'];

$all_incomes = $pdo->prepare("SELECT COUNT(*) as count FROM incomes WHERE user_id = ?");
$all_incomes->execute([$user_id]);
$income_count = $all_incomes->fetch()['count'];

// Build main query
if ($filter == 'income') {
    $query = "SELECT 'income' as type, amount, source as name, date, frequency as description
        FROM incomes 
        WHERE user_id = ?
        ORDER BY date DESC LIMIT 50";
    $params = [$user_id];
} elseif ($filter == 'expenses') {
    $query = "SELECT 'expense' as type, amount, category as name, date, description
        FROM expenses 
        WHERE user_id = ?
        ORDER BY date DESC LIMIT 50";
    $params = [$user_id];
} else {
    $query = "SELECT 'expense' as type, amount, category as name, date, description, 'expense' as table_type
        FROM expenses 
        WHERE user_id = ?
        UNION ALL
        SELECT 'income' as type, amount, source as name, date, frequency as description, 'income' as table_type
        FROM incomes 
        WHERE user_id = ?
        ORDER BY date DESC LIMIT 50";
    $params = [$user_id, $user_id];
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$history = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History • Financial Tracker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Perfect responsive background */
        .history-page {
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
        .page-wrapper {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            min-height: 100vh;
            max-width: 1400px;
            margin: 0 auto;
            box-shadow: 0 0 60px rgba(0,0,0,0.15);
            padding: 20px;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .history-page {
                background-attachment: scroll;
            }
            .page-wrapper {
                background: rgba(255, 255, 255, 0.98);
                margin: 0;
                padding: 15px;
                backdrop-filter: blur(8px);
            }
        }

        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            .page-wrapper {
                background: rgba(15, 23, 42, 0.98);
                color: #e2e8f0;
            }
        }

        /* Reset for full coverage */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        /* Filter tabs enhancement */
        .filter-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            overflow-x: auto;
            padding-bottom: 8px;
        }

        .tab {
            padding: 12px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.7);
            white-space: nowrap;
            backdrop-filter: blur(10px);
        }

        .tab.active {
            background: rgba(99, 102, 241, 0.2);
            color: #6366f1;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .tab:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        /* History list enhancements */
        .history-list {
            max-height: 70vh;
            overflow-y: auto;
        }

        .history-item {
            animation: slideInUp 0.6s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes slideInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="history-page">
    <div class="page-wrapper">
        <!-- Header -->
        <header class="page-header">
            <a href="dashboard.php" class="back-btn">
                <span class="icon">←</span> Dashboard
            </a>
            <div class="header-title">
                <span class="title-icon">⌛</span>
                <h1 style="color:purple"><i>Your Transaction History</i></h1>
            </div>
        </header>

        <!-- Filter Tabs -->
        <div class="filter-section">
            <div class="filter-tabs">
                <a href="?filter=all" class="tab <?= $filter=='all' ? 'active' : '' ?>">
                    All (<?= $expense_count + $income_count ?>)
                </a>
                <a href="?filter=expenses" class="tab <?= $filter=='expenses' ? 'active' : '' ?>">
                    Expenses (<?= $expense_count ?>)
                </a>
                <a href="?filter=income" class="tab <?= $filter=='income' ? 'active' : '' ?>">
                    Income (<?= $income_count ?>)
                </a>
            </div>
        </div>

        <!-- History List -->
        <div class="list-section">
            <div class="section-header">
                <h3>Recent Transactions (<?= count($history) ?>)</h3>
            </div>
            <div class="history-list">
                <?php if (empty($history)): ?>
                    <div class="empty-state">
                        <span class="empty-icon">📭</span>
                        <h3>No transactions yet</h3>
                        <p>Add expenses or income from dashboard to see history</p>
                        <div style="margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap; justify-content: center;">
                            <a href="expenses.php" class="btn btn-primary">+ Add Expense</a>
                            <a href="incomes.php" class="btn btn-success">+ Add Income</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($history as $index => $item): ?>
                        <div class="history-item <?= $item['type'] ?>" style="animation-delay: <?= $index * 0.05 ?>s">
                            <div class="history-icon <?= $item['type'] ?>">
                                <?php if ($item['type'] == 'income'): ?>
                                    💵
                                <?php else: ?>
                                    <?= getCategoryIcon($item['name']) ?>
                                <?php endif; ?>
                            </div>
                            <div class="history-details">
                                <div class="history-header">
                                    <div class="history-title"><?= htmlspecialchars($item['name']) ?></div>
                                    <div class="history-amount <?= $item['type'] ?>">
                                        <?= $item['type'] == 'income' ? '+' : '-' ?>₹<?= number_format($item['amount'], 2) ?>
                                    </div>
                                </div>
                                <div class="history-meta">
                                    <span class="history-date"><?= date('M j, Y', strtotime($item['date'])) ?></span>
                                    <?php if (!empty($item['description']) && $item['description'] != 'NULL'): ?>
                                        <span class="history-desc"><?= htmlspecialchars($item['description']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php 
    function getCategoryIcon($category) {
        $icons = [
            'groceries' => '🛒', 
            'utilities' => '⚡', 
            'entertainment' => '🎬',
            'transportation' => '🚗', 
            'dining' => '🍽️', 
            'others' => '📦',
            'Medical Expenses' => '🩺',
            'Sationary' => '🧾'
        ];
        $cat = strtolower($category);
        return $icons[$cat] ?? '💸';
    }
    ?>
</body>
</html>
