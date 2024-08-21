<?php
session_start();
include 'connection.php';

// get users
$users_query = "SELECT user_id, name, surname, tcno FROM users"; // Adding tc number
$users_result = $conn->query($users_query);

$message = ''; // Variable for success message

// Transactions after form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // User and project selection control
    if (!isset($_POST['user_id']) || !isset($_POST['project_id'])) {
        die('Kullanıcı veya proje seçilmedi.');
    }

    $user_id = $_POST['user_id'];
    $project_id = $_POST['project_id'];

    // Check if the user is registered to the project
    $check_query = "SELECT * FROM project_user WHERE user_id = ? AND project_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $user_id, $project_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows == 0) {
        $message = "Seçilen kullanıcı bu projeye kayıtlı değil!";
    } else {
        // If user is registered, redirect to enter work rate
        header("Location: enter_work_percentage.php?user_id=$user_id&project_id=$project_id");
        exit();
    }
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aylık Çalışma Oranı Ekle</title>
    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            background-color: #f4f4f4;
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
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            color: #333;
        }
        select, input[type="number"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
            width: 100%;
            font-size: 16px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .warning {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        .back-button {
            background-color: #6c757d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Aylık Çalışma Oranı Ekle</h1>
        <?php if ($message): ?>
            <div class="warning"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <label for="user_id">Kullanıcı Seç:</label>
            <select id="user_id" name="user_id" required>
                <option value="">Kullanıcı Seçin</option>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($user['user_id']) ?>">
                        <?= htmlspecialchars($user['name'] . ' ' . $user['surname'] . ' (TC No: ' . $user['tcno'] . ')') ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="project_id">Proje Seç:</label>
            <select id="project_id" name="project_id" required>
                <option value="">Proje Seçin</option>
                <?php 
                // Get projects (if project information is available)
                $projects_query = "SELECT project_id, project_name FROM projects";
                $projects_result = $conn->query($projects_query);
                while ($project = $projects_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($project['project_id']) ?>"><?= htmlspecialchars($project['project_name']) ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Devam Et</button>
        </form>

        <!-- Return Button -->
        <button class="back-button" onclick="window.location.href='project_management.php';">Geri Dön</button>
    </div>
</body>
</html>
