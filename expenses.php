<?php
require_once 'config.php';
if (!isLoggedIn()) header('Location: login.php');
$user_id = getUserId();

if ($_POST) {
    $stmt = $pdo->prepare("INSERT INTO expenses (user_id, amount, category, description, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $_POST['amount'], $_POST['category'], $_POST['description'], $_POST['date']]);
    $success = "Expense added successfully!";
}

// Fetch recent expenses with category totals
$stmt = $pdo->prepare("
    SELECT e.*, 
           (SELECT SUM(amount) FROM expenses e2 WHERE e2.category = e.category AND e2.user_id = ? AND MONTH(e2.date) = MONTH(CURDATE())) as category_total
    FROM expenses e 
    WHERE e.user_id = ? 
    ORDER BY e.date DESC LIMIT 10
");
$stmt->execute([$user_id, $user_id]);
$expenses = $stmt->fetchAll();

// Category stats for this month
$category_stats = $pdo->prepare("
    SELECT category, SUM(amount) as total 
    FROM expenses 
    WHERE user_id = ? AND MONTH(date) = MONTH(CURDATE()) 
    GROUP BY category 
    ORDER BY total DESC LIMIT 5
");
$category_stats->execute([$user_id]);
$category_stats = $category_stats->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses • Financial Tracker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Perfect responsive background */
        .expenses-page {
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
            .expenses-page {
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
        }
    </style>
</head>
<body class="expenses-page">
    <div class="page-wrapper">
        <!-- Header -->
        <header class="page-header">
            <a href="dashboard.php" class="back-btn">
                <span class="icon">←</span>
                Dashboard
            </a>
            <div class="header-title">
                <span class="title-icon">
                    <h1 style="color:purple"><i>💸 Expenses</i></h1>
                </span>
            </div>
        </header>

        <!-- Success Message -->
        <?php if (isset($success)): ?>
            <div class="success-message">
                <span class="success-icon">✅</span>
                <span><?= $success ?></span>
            </div>
        <?php endif; ?>

        <!-- Stats Overview -->
        <?php if ($category_stats): ?>
        <div class="stats-overview">
            <h3>Category Wise Breakdown</h3>
            <div class="category-grid">
                <?php foreach ($category_stats as $stat): ?>
                <div class="category-card">
                    <div class="category-info">
                        <span class="category-icon category-<?= strtolower($stat['category']) ?>">
                            <?= getCategoryIcon($stat['category']) ?>
                        </span>
                        <div>
                            <div class="category-name"><?= ucfirst($stat['category']) ?></div>
                            <div class="category-amount">₹<?= number_format($stat['total'], 2) ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Add Expense Form -->
        <div class="form-section">
            <div class="section-header">
                <h3><span class="icon">➕</span> Add New Expense</h3>
            </div>
            <form method="POST" class="expense-form">
                <div class="form-row">
                    <div class="input-group icon-left">
                        <span class="input-icon">₹</span>
                        <input type="number" name="amount" placeholder=" " step="0.01" required>
                        <label>Amount</label>
                    </div>
                    <div class="input-group">
                        <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
                        <label>Date</label>
                    </div>
                </div>
                
                <div class="input-group icon-left">
                    <span class="input-icon">📂</span>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <option value="groceries">🛒 Groceries</option>
                        <option value="utilities">⚡ Utilities</option>
                        <option value="entertainment">🎬 Entertainment</option>
                        <option value="transportation">🚗 Transportation</option>
                        <option value="dining">🍽️ Dining Out</option>
                        <option value="Medical Expenses">🩺 Medical Expenses</option>
                        <option value="Sationary">🧾 Sationary</option>
                        <option value="others">📦 Others</option>
                    </select>
                    <label>Category</label>
                </div>

                <div class="input-group">
                    <textarea name="description" placeholder=" " rows="3"></textarea>
                    <label>Description (optional)</label>
                </div>

                <button type="submit" class="btn btn-primary full-width">
                    Add Expense
                </button>
            </form>
        </div>

        <!-- Recent Expenses -->
        <div class="list-section">
            <div class="section-header">
                <h3><span class="icon">📋</span> Recent Expenses (<?= count($expenses) ?>)</h3>
            </div>
            <div class="expenses-list">
                <?php foreach ($expenses as $expense): ?>
                    <div class="expense-item">
                        <div class="expense-icon category-<?= strtolower($expense['category']) ?>">
                            <?= getCategoryIcon($expense['category']) ?>
                        </div>
                        <div class="expense-details">
                            <div class="expense-header">
                                <div class="expense-category"><?= ucfirst($expense['category']) ?></div>
                                <div class="expense-amount negative">
                                    ₹<?= number_format($expense['amount'], 2) ?>
                                </div>
                            </div>
                            <div class="expense-meta">
                                <span class="expense-date"><?= date('M j, Y', strtotime($expense['date'])) ?></span>
                                <?php if ($expense['description']): ?>
                                    <div class="expense-description">
                                        <?= htmlspecialchars($expense['description']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($expenses)): ?>
                    <div class="empty-state">
                        <span class="empty-icon">📭</span>
                        <h3>No expenses yet</h3>
                        <p>Add your first expense using the form above</p>
                    </div>
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
        return $icons[$category] ?? '📦';
    }
    ?>

    <script>
        // Auto-hide success message
        setTimeout(() => {
            const success = document.querySelector('.success-message');
            if (success) success.style.opacity = '0';
        }, 4000);
    </script>
</body>
</html>
