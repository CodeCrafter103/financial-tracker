<?php
require_once 'config.php';
if (!isLoggedIn()) header('Location: login.php');
$user_id = getUserId();

if ($_POST) {
    $stmt = $pdo->prepare("REPLACE INTO budgets (user_id, category, amount, month) VALUES (?, ?, ?, YEAR(CURDATE()))");
    $stmt->execute([$user_id, $_POST['category'], $_POST['amount']]);
    $success = "Budget updated!";
}

$stmt = $pdo->prepare("
    SELECT b.category, b.amount as budget, COALESCE(SUM(e.amount), 0) as spent
    FROM budgets b 
    LEFT JOIN expenses e ON b.category = e.category AND e.user_id = ? AND YEAR(e.date) = YEAR(CURDATE()) AND MONTH(e.date) = MONTH(CURDATE())
    WHERE b.user_id = ? AND b.month = YEAR(CURDATE())
    GROUP BY b.category, b.amount
");
$stmt->execute([$user_id, $user_id]);
$budgets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budgets • Financial Tracker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Perfect responsive background */
        body.budgets-page {
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
            font-family: 'Inter', sans-serif;
        }

        /* Perfect content overlay */
        .container {
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
            body.budgets-page {
                background-attachment: scroll;
            }
            .container {
                background: rgba(255, 255, 255, 0.98);
                margin: 0;
                padding: 15px;
                backdrop-filter: blur(8px);
            }
        }

        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            .container {
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

        /* Enhanced budget styling */
        header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .back-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6366f1;
            text-decoration: none;
            font-weight: 500;
            padding: 12px 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: rgba(99, 102, 241, 0.1);
            backdrop-filter: blur(10px);
        }

        .back-btn:hover {
            background: rgba(99, 102, 241, 0.2);
            transform: translateY(-2px);
        }

        h1 {
            margin: 0;
            flex: 1;
        }

        .success {
            background: rgba(34, 197, 94, 0.2);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            color: #16a34a;
            backdrop-filter: blur(10px);
        }

        .form-group {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid rgba(0,0,0,0.1);
            border-radius: 12px;
            font-size: 16px;
            margin-bottom: 16px;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.9);
        }

        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.4);
        }

        .list-group {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .list-item.budget {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            padding: 20px;
            border-left: 4px solid #10b981;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .list-item.budget:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }

        .budget-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .progress-bar {
            width: 100%;
            height: 10px;
            background: rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #34d399);
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .progress-text {
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
        }
    </style>
</head>
<body class="budgets-page">
    <div class="container">
        <header>
            <a href="dashboard.php" class="back-btn">← Dashboard</a>
            <h1 style="color:purple"><i>💰 Budgets</i></h1>
        </header>

        <?php if (isset($success)): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" class="form-group">
            <h3 style="margin-top: 0;">Set Monthly Budget</h3>
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
            <input type="number" name="amount" placeholder="Budget Amount (₹)" step="0.01" required>
            <button type="submit" class="btn btn-warning">Set Budget</button>
        </form>

        <h3 style="margin-bottom: 20px;">Budget Progress</h3>
        <div class="list-group">
            <?php foreach ($budgets as $budget): 
                $percentage = $budget['budget'] > 0 ? min(100, ($budget['spent'] / $budget['budget']) * 100) : 0;
            ?>
                <div class="list-item budget">
                    <div class="budget-header">
                        <span><?= ucfirst($budget['category']) ?></span>
                        <span>₹<?= number_format($budget['spent'], 2) ?> / ₹<?= number_format($budget['budget'], 2) ?></span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
                    </div>
                    <div class="progress-text"><?= round($percentage) ?>% used</div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($budgets)): ?>
                <div class="list-item budget" style="text-align: center; opacity: 0.7;">
                    <p>No budgets set yet. Create your first budget above!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto-hide success message
        setTimeout(() => {
            const success = document.querySelector('.success');
            if (success) {
                success.style.opacity = '0';
                setTimeout(() => success.remove(), 500);
            }
        }, 4000);
    </script>
</body>
</html>
