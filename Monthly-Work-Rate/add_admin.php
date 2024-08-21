<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // If not logged in, redirect to login page
    header('Location: index.php');
    exit();
}

// Database connection
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = isset($_POST['new_username']) ? $_POST['new_username'] : null;
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : null;

    if (!empty($new_username) && !empty($new_password)) {
        // Hash the password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Add new admin
        $sql = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        if ($sql === false) {
            $message = "SQL hazırlama hatası: " . $conn->error;
        } else {
            $sql->bind_param("ss", $new_username, $hashed_password);
            if ($sql->execute()) {
                $message = "Yeni admin başarıyla eklendi.";
            } else {
                $message = "Admin eklenirken bir hata oluştu: " . $sql->error;
            }
            $sql->close();
        }
    } else {
        $message = "Lütfen tüm alanları doldurun.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Admin Ekle</title>
    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        form {
            max-width: 300px;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        form label {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }
        form input[type="text"], form input[type="password"] {
            width: calc(100% - 20px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        form button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 3px;
        }
        form button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 10px;
            color: green;
        }
        .error {
            margin-top: 10px;
            color: red;
        }
        .back-button {
            background-color: #6c757d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 3px;
            margin-top: 10px;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <h2>Yeni Admin Ekle</h2>
    <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
    <form method="post" action="">
        <label for="new_username">Yeni Kullanıcı Adı:</label>
        <input type="text" id="new_username" name="new_username" required>
        <label for="new_password">Yeni Şifre:</label>
        <input type="password" id="new_password" name="new_password" required>
        <button type="submit">Ekle</button>
        <a href="project_management.php" class="back-button">Geri Dön</a>
    </form>
</body>
</html>
