<?php
session_start();
include 'connection.php';

// debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get user and project lists
$stmt_users = $conn->prepare("SELECT user_id, name, surname, tcno FROM users");
$stmt_users->execute();
$result_users = $stmt_users->get_result();
$users = [];
while ($row = $result_users->fetch_assoc()) {
    $users[] = $row;
}
$stmt_users->close();

$stmt_projects = $conn->prepare("SELECT project_id, project_name FROM projects");
$stmt_projects->execute();
$result_projects = $stmt_projects->get_result();
$projects = [];
while ($row = $result_projects->fetch_assoc()) {
    $projects[] = $row;
}
$stmt_projects->close();

// When the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['project_id']) && isset($_POST['user_id'])) {
    $project_id = $_POST['project_id']; // Project ID
    $user_id = $_POST['user_id']; // Person ID

    // Debugging: Check project and user IDs
    if (empty($project_id) || empty($user_id)) {
        $_SESSION['message'] = "Proje veya kişi ID'si boş!";
        $_SESSION['message_type'] = 'error';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Check for the same user and project
    $stmt_check = $conn->prepare("SELECT * FROM project_user WHERE project_id = ? AND user_id = ?");
    $stmt_check->bind_param("ii", $project_id, $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // If the user and project relationship already exists
        $_SESSION['message'] = "Bu kişi zaten bu projeye eklenmiş.";
        $_SESSION['message_type'] = 'error';
        $stmt_check->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    $stmt_check->close();

    // Associate project and person
    $stmt = $conn->prepare("INSERT INTO project_user (project_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $project_id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Kişi projeye başarıyla eklendi.";
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = "Kişi projeye eklenirken bir hata oluştu: " . htmlspecialchars($stmt->error);
        $_SESSION['message_type'] = 'error';
    }

    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']); // Refresh page
    exit();
}

$message = '';
$message_type = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projeye Kişi Ekle</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            color: #555;
        }
        select, button {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            display: block;
        }
        .success-message {
            color: #28a745;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Projeye Kişi Ekle</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message <?= $message_type == 'error' ? 'error-message' : 'success-message' ?>">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <!-- Project selection -->
            <label for="project_id">Proje Seç:</label>
            <select id="project_id" name="project_id" required>
                <option value="">Proje seçin</option>
                <?php foreach ($projects as $project): ?>
                    <option value="<?= $project['project_id']; ?>"><?= htmlspecialchars($project['project_name']); ?></option>
                <?php endforeach; ?>
            </select>

            <!--Person selection -->
            <label for="user_id">Kişi Seç:</label>
            <select id="user_id" name="user_id" required>
                <option value="">Kişi seçin</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['user_id']; ?>">
                        <?= htmlspecialchars($user['name'] . ' ' . $user['surname'] . ' - TC: ' . $user['tcno']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Kaydet</button>
            <button type="button" onclick="window.location.href='project_management.php';">Geri Dön</button>
        </form>
    </div>
</body>
</html>
