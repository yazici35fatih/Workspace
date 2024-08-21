<?php
session_start();
include 'connection.php';

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null; // Get user ID
$user = [];

// Check User ID
if ($user_id) {
    // Get user information
    $stmt = $conn->prepare("SELECT user_id, name, surname, personnel_type FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $user = $row; // Get user information
    } else {
        echo "<p>Kullanıcı bulunamadı.</p>"; // Notify user when user not found
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Detayları</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        h1 {
            color: #333;
        }
        p {
            color: #555;
        }
        .back-button {
            background-color: #6c757d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 20px;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Kullanıcı Detayları</h1>
        <?php if (!empty($user)): ?>
            <p><strong>Adı:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Soyadı:</strong> <?= htmlspecialchars($user['surname']) ?></p>
            <p><strong>Personel Türü:</strong> <?= htmlspecialchars($user['personnel_type']) ?></p>
        <?php else: ?>
            <p>Kullanıcı bilgileri mevcut değil.</p>
        <?php endif; ?>

        <button class="back-button" onclick="window.location.href='annual_work_rates.php';">Geri Dön</button>
    </div>
</body>
</html>
