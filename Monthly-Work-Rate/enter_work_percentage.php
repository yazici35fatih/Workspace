<?php
session_start();
include 'connection.php';

// Get user and project information
if (!isset($_GET['user_id']) || !isset($_GET['project_id'])) {
    die('Kullanıcı veya proje bilgileri eksik.');
}

$user_id = $_GET['user_id'];
$project_id = $_GET['project_id'];

// Get project information
$project_query = "SELECT project_name, start_date, end_date FROM projects WHERE project_id = ?";
$project_stmt = $conn->prepare($project_query);
$project_stmt->bind_param("i", $project_id);
$project_stmt->execute();
$project_result = $project_stmt->get_result();
$project = $project_result->fetch_assoc();
$project_stmt->close();

if (!$project) {
    die('Proje bilgileri alınamadı.');
}

// Get user information
$user_query = "SELECT personnel_type, name, surname, tcno FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_stmt->close();

if (!$user) {
    die('Kullanıcı bilgileri alınamadı.');
}

// Calculate project duration
$start_date = new DateTime($project['start_date']);
$end_date = new DateTime($project['end_date']);
$interval = DateInterval::createFromDateString('1 month');
$period = new DatePeriod($start_date, $interval, $end_date->modify('+1 day'));

$message = ''; // Variable for success message

// Transactions after form submission
$submitted_work_percentages = []; // Will keep posted rates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get work rates
    $submitted_work_percentages = $_POST['work_percentages'];

    // Check user's current work rates
    $existing_work_query = "SELECT month_year, SUM(work_percentage) as total_percentage 
                             FROM monthly_work_rates 
                             WHERE user_id = ? AND month_year IN (" . implode(',', array_fill(0, count($submitted_work_percentages), '?')) . ") 
                             GROUP BY month_year";
    $existing_work_stmt = $conn->prepare($existing_work_query);
    $existing_work_stmt->bind_param("i" . str_repeat("s", count($submitted_work_percentages)), $user_id, ...array_keys($submitted_work_percentages));
    $existing_work_stmt->execute();
    $existing_work_result = $existing_work_stmt->get_result();
    
    $existing_totals = [];
    while ($row = $existing_work_result->fetch_assoc()) {
        $existing_totals[$row['month_year']] = $row['total_percentage'];
    }
    $existing_work_stmt->close();

    // Check operating rate limits
    $limit = $user['personnel_type'] === 'akademik' ? 80 : 100;
    $total_percentage = [];

    foreach ($submitted_work_percentages as $month_year => $percentage) {
        $percentage = (float)$percentage;
        $existing_percentage = $existing_totals[$month_year] ?? 0;

        // Compare the rate in the new project with the current rate
        if ($user['personnel_type'] === 'akademik') {
            $remaining_capacity = 80 - $existing_percentage;
            if ($percentage > $remaining_capacity) {
                $message = "Hata: $month_year için mevcut çalışma oranı $remaining_capacity'i geçemez.";
                break;
            }
        }

        // Check total in overlapping months
        if (($existing_percentage + $percentage) > $limit) {
            $message = "Hata: $month_year için toplam çalışma oranı $limit'i geçemez.";
            break;
        }
        
        $total_percentage[$month_year] = $percentage;
    }

    if (empty($message)) {
        // Save to database
        $stmt = $conn->prepare("INSERT INTO monthly_work_rates (user_id, project_id, month_year, work_percentage) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE work_percentage = VALUES(work_percentage)");
        
        foreach ($total_percentage as $month_year => $percentage) {
            // Use the correct types in the bind_param call
            $stmt->bind_param("iisd", $user_id, $project_id, $month_year, $percentage);
            if (!$stmt->execute()) {
                die("Veritabanına kaydedilirken hata oluştu: " . $stmt->error);
            }
        }
        $stmt->close();

        // success message
        $message = "Çalışma oranları başarıyla kaydedildi!";
    }
}

// Get old working rates
$stored_work_percentages = [];
$stored_work_query = "SELECT month_year, work_percentage FROM monthly_work_rates WHERE user_id = ? AND project_id = ?";
$stored_work_stmt = $conn->prepare($stored_work_query);
$stored_work_stmt->bind_param("ii", $user_id, $project_id);
$stored_work_stmt->execute();
$stored_work_result = $stored_work_stmt->get_result();

while ($row = $stored_work_result->fetch_assoc()) {
    $stored_work_percentages[$row['month_year']] = $row['work_percentage'];
}
$stored_work_stmt->close();

// Get work rates on all projects
$all_work_query = "SELECT month_year, SUM(work_percentage) as total_percentage 
                    FROM monthly_work_rates 
                    WHERE user_id = ? 
                    GROUP BY month_year";
$all_work_stmt = $conn->prepare($all_work_query);
$all_work_stmt->bind_param("i", $user_id);
$all_work_stmt->execute();
$all_work_result = $all_work_stmt->get_result();

$all_totals = [];
while ($row = $all_work_result->fetch_assoc()) {
    $all_totals[$row['month_year']] = $row['total_percentage'];
}
$all_work_stmt->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aylık Çalışma Oranı Girişi</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        h1 {
            color: #333;
            font-size: 28px; 
            text-align: center; 
            margin-bottom: 20px; 
        }

        h2 {
            color: #333;
            font-size: 20px;
            text-align: center;
            margin-bottom: 15px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px; 
            color: #555;
            font-weight: 500; 
        }

        input[type="number"] {
            padding: 12px; 
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        input[type="number"]:focus {
            border-color: #007bff; 
            outline: none;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3; 
        }

        .success {
            color: #28a745; 
            margin-bottom: 20px; 
            text-align: center;
            font-size: 16px;
        }

        .error {
            color: #dc3545;
            margin-bottom: 20px;
            text-align: center;
            font-size: 16px;
        }

        .back-button {
            background-color: #6c757d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s ease;
            margin-bottom: 15px;
        }

        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Display user information at the top -->
        <h2><?= htmlspecialchars($user['name']) ?> <?= htmlspecialchars($user['surname']) ?> - TC: <?= htmlspecialchars($user['tcno']) ?></h2>

        <h1><?= htmlspecialchars($project['project_name']) ?> - Aylık Çalışma Oranı Girişi</h1>
        
        <!-- Return Button -->
        <button class="back-button" onclick="window.location.href='add_monthly_work_rate.php?user_id=<?= $user_id ?>&project_id=<?= $project_id ?>';">Geri Dön</button>

        <?php if ($message): ?>
            <div class="<?= strpos($message, 'Hata') === 0 ? 'error' : 'success' ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <?php 
                $max_value = $user['personnel_type'] === 'akademik' ? 0.8 : 1; // Set maximum value
                foreach ($period as $dt): 
                    $month_year = $dt->format("Y-m");
                    $existing_percentage = $submitted_work_percentages[$month_year] ?? $stored_work_percentages[$month_year] ?? 0; // Girilen veya eski değerleri al
                    $total_percentage = $all_totals[$month_year] ?? 0; // Total rate across all projects
                    $remaining_capacity = $max_value - $total_percentage + $existing_percentage; // Calculate remaining capacity
            ?>
                <label for="work_percentage_<?= $month_year ?>"><?= $month_year ?> (Maks: <?= number_format($remaining_capacity, 2) ?>)</label>
                <input type="number" id="work_percentage_<?= $month_year ?>" name="work_percentages[<?= $month_year ?>]" step="0.01" min="0" max="<?= number_format(max($remaining_capacity, 0), 2) ?>" value="<?= htmlspecialchars($existing_percentage) ?>"> 
            <?php endforeach; ?>
            <button type="submit">Kaydet</button>
        </form>
    </div>
</body>
</html>
