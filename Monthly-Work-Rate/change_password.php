<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    // If not logged in, redirect to login page
    header('Location: index.php');
    exit();
}

// Database connection
include 'connection.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : null;
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : null;
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : null;

    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password !== $confirm_password) {
            $message = "Yeni şifreler uyuşmuyor.";
        } else {
            $admin_id = $_SESSION['admin_id'];

            // Check current password
            $sql = $conn->prepare("SELECT password FROM admins WHERE admin_id = ?");
            $sql->bind_param("i", $admin_id);
            $sql->execute();
            $result = $sql->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($current_password, $row['password'])) {
                    // Hash the new password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update password
                    $sql_update = $conn->prepare("UPDATE admins SET password = ? WHERE admin_id = ?");
                    $sql_update->bind_param("si", $hashed_password, $admin_id);

                    if ($sql_update->execute()) {
                        $message = "Şifre başarıyla güncellendi.";
                    } else {
                        $message = "Şifre güncellenirken bir hata oluştu: " . $sql_update->error;
                    }
                } else {
                    $message = "Mevcut şifre yanlış.";
                }
            } else {
                $message = "Kullanıcı bulunamadı.";
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
    <title>Şifre Değiştir</title>
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
            margin-bottom: 20px;
        }
        form label {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }
        form input[type="password"] {
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
            padding: 10px;
            border-radius: 3px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .back-button {
            background-color: #6c757d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 3px;
            margin-top: 10px;
            display: inline-block;
            text-decoration: none; /* remove line */
        }
        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <h2>Şifre Değiştir</h2>
    <?php if (!empty($message)) { 
        $message_class = strpos($message, 'başarıyla') !== false ? 'success' : 'error';
        echo "<p class='message $message_class'>$message</p>"; 
    } ?>
    <form method="post" action="">
        <label for="current_password">Mevcut Şifre:</label>
        <input type="password" id="current_password" name="current_password" required>
        <label for="new_password">Yeni Şifre:</label>
        <input type="password" id="new_password" name="new_password" required>
        <label for="confirm_password">Yeni Şifre (Tekrar):</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <button type="submit">Değiştir</button>
    </form>
    <a href="project_management.php" class="back-button">Geri Dön</a>
</body>
</html>
