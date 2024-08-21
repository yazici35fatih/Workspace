<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php'; // Add database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_name = isset($_POST['project_name']) ? $_POST['project_name'] : '';
    $project_type = isset($_POST['project_type']) ? $_POST['project_type'] : '';
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';

    if (!empty($project_name) && !empty($project_type) && !empty($start_date)) {
        // Adding a project to the database
        $sql = $conn->prepare("INSERT INTO projects (name, type, start_date) VALUES (?, ?, ?)");
        $sql->bind_param("sss", $project_name, $project_type, $start_date);

        if ($sql->execute()) {
            echo "Proje başarıyla eklendi.";
        } else {
            echo "Proje eklenirken bir hata oluştu: " . $sql->error;
        }
        $sql->close();
    } else {
        echo "Lütfen tüm alanları doldurun.";
    }
}

$conn->close();
?>
