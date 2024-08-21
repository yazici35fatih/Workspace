<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user ID

    $user_id = $_POST['user_id'];

    // delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        echo "Kullanıcı başarıyla silindi.";
    } else {
        echo "Silme işlemi sırasında bir hata oluştu: " . $stmt->error;
    }
    
    $stmt->close();
    // Prompt user when deletion is complete
    header("Location: delete_person.php");
    exit();
} else {
    echo "Geçersiz istek.";
}
?>
