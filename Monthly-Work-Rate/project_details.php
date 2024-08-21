<?php
session_start();
include 'connection.php';

$project_id = isset($_GET['id']) ? $_GET['id'] : null; // Get project ID
$project = [];
$employees = [];

// Check project ID
if ($project_id) {
    // Get project information
    $stmt = $conn->prepare("SELECT project_name, start_date, end_date FROM projects WHERE project_id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $project = $row; //Get project information
    } else {
        die("<p>Proje bulunamadı.</p>"); // Notify user when project not found
    }

    $stmt->close();

    // Get people working on the project
    $stmt = $conn->prepare("
        SELECT 
            u.user_id, CONCAT(u.name, ' ', u.surname) AS user_name, u.personnel_type
        FROM users u
        JOIN project_user pu ON u.user_id = pu.user_id
        WHERE pu.project_id = ?
    ");
    
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $employees[] = $row; // Add employees to array
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proje Detayları</title>
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
            max-width: 1000px;
            margin: auto;
        }
        h1, h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .back-button {
            background-color: #6c757d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Proje: <?= htmlspecialchars($project['project_name'] ?? 'N/A'); ?></h1>
        <p>Başlangıç Tarihi: <?= htmlspecialchars($project['start_date'] ?? 'N/A'); ?></p>
        <p>Bitiş Tarihi: <?= htmlspecialchars($project['end_date'] ?? 'N/A'); ?></p>

        <h2>Projede Çalışanlar</h2>
        <?php if (!empty($employees)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Kullanıcı</th>
                        <th>Personel Türü</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $user): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($user['user_name']) ?>
                            </td>
                            <td><?= htmlspecialchars($user['personnel_type']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Projede çalışan kişi bulunamadı.</p>
        <?php endif; ?>

        <button class="back-button" onclick="window.location.href='search_project.php';">Geri Dön</button>
    </div>
</body>
</html>
