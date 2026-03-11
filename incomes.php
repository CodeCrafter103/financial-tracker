<?php
require_once 'config.php';
if (!isLoggedIn()) header('Location: login.php');
$user_id = getUserId();

if ($_POST) {
    $stmt = $pdo->prepare("INSERT INTO incomes (user_id, amount, source, frequency, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $_POST['amount'], $_POST['source'], $_POST['frequency'], $_POST['date']]);
    $success = "Income added successfully!";
}

// Fetch recent incomes
$stmt = $pdo->prepare("SELECT * FROM incomes WHERE user_id = ? ORDER BY date DESC LIMIT 10");
$stmt->execute([$user_id]);
$incomes = $stmt->fetchAll();

// Income stats by frequency for this month
$frequency_stats = $pdo->prepare("
    SELECT frequency, SUM(amount) as total, COUNT(*) as count
    FROM incomes 
    WHERE user_id = ? AND MONTH(date) = MONTH(CURDATE())
    GROUP BY frequency
    ORDER BY total DESC
");
$frequency_stats->execute([$user_id]);
$frequency_stats = $frequency_stats->fetchAll();

// Top sources
$top_sources = $pdo->prepare("
    SELECT source, SUM(amount) as total
    FROM incomes 
    WHERE user_id = ? AND MONTH(date) = MONTH(CURDATE())
    GROUP BY source
    ORDER BY total DESC 
    LIMIT 5
");
$top_sources->execute([$user_id]);
$top_sources = $top_sources->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incomes • Financial Tracker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Perfect responsive background */
        .incomes-page {
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
            .incomes-page {
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
<body class="incomes-page">
    <div class="page-wrapper">
        <!-- Header -->
        <header class="page-header">
            <a href="dashboard.php" class="back-btn">
                <span class="icon">←</span>
                Dashboard
            </a>
            <div class="header-title">
                <span class="title-icon">
                    <h1 style="color:purple"><i>💵 Incomes</i></h1>
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

        <!-- Income Stats -->
        <?php if (!empty($frequency_stats) || !empty($top_sources)): ?>
        <div class="stats-overview">
            <h3>Income Breakdown Overview</h3>
            <div class="stats-grid">
                <?php foreach ($frequency_stats as $stat): ?>
                <div class="stat-card income">
                    <div class="stat-icon"><?= getFrequencyIcon($stat['frequency']) ?></div>
                    <div class="stat-content">
                        <h4><?= ucfirst($stat['frequency']) ?></h4>
                        <div class="stat-amount">₹<?= number_format($stat['total'], 2) ?></div>
                        <div class="stat-label"><?= $stat['count'] ?> occurrence<?= $stat['count'] > 1 ? 's' : '' ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Add Income Form -->
        <div class="form-section">
            <div class="section-header">
                <h3><span class="icon">➕</span> Add New Income</h3>
            </div>
            <form method="POST" class="income-form">
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
                    <span class="input-icon">🏢</span>
                    <input type="text" name="source" placeholder=" " required>
                    <label>Source (Salary, Freelance, etc.)</label>
                </div>

                <div class="input-group icon-left">
                    <span class="input-icon">🔄</span>
                    <select name="frequency" required>
                        <option value="one-time">One-time</option>
                        <option value="monthly">Monthly</option>
                        <option value="weekly">Weekly</option>
                    </select>
                    <label>Frequency</label>
                </div>

                <button type="submit" class="btn btn-success full-width">
                    Add Income
                </button>
            </form>
        </div>

        <!-- Recent Incomes -->
        <div class="list-section">
            <div class="section-header">
                <h3><span class="icon">📋</span> Recent Incomes (<?= count($incomes) ?>)</h3>
            </div>
            <div class="incomes-list">
                <?php if (empty($incomes)): ?>
                    <div class="empty-state">
                        <span class="empty-icon">📭</span>
                        <h3>No income entries yet</h3>
                        <p>Track your first income source using the form above</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($incomes as $income): ?>
                        <div class="income-item">
                            <div class="income-icon">
                                <?= getFrequencyIcon($income['frequency']) ?>
                            </div>
                            <div class="income-details">
                                <div class="income-header">
                                    <div class="income-source"><?= htmlspecialchars($income['source']) ?></div>
                                    <div class="income-amount positive">
                                        +₹<?= number_format($income['amount'], 2) ?>
                                    </div>
                                </div>
                                <div class="income-meta">
                                    <span class="income-frequency"><?= ucfirst($income['frequency']) ?></span>
                                    <span class="income-date"><?= date('M j, Y', strtotime($income['date'])) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php 
    function getFrequencyIcon($frequency) {
        $icons = [
            'one-time' => '💵',
            'monthly' => '💳',
            'weekly' => '📅'
        ];
        return $icons[$frequency] ?? '💰';
    }
    ?>

    <script>
        // Auto-hide success message
        setTimeout(() => {
            const success = document.querySelector('.success-message');
            if (success) {
                success.style.opacity = '0';
                setTimeout(() => success.remove(), 500);
            }
        }, 4000);
    </script>
</body>
</html>
