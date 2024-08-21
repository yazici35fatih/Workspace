<?php
session_start();
include 'connection.php';

// message variable
$message = "";

// When the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from form
    $project_name = $_POST['project_name'];
    $start_month = $_POST['start_month']; // Start month
    $start_year = $_POST['start_year']; // Start year
    $end_month = $_POST['end_month']; // End month
    $end_year = $_POST['end_year']; // End year

    // Create start and end dates
    $start_date = $start_year . '-' . $start_month . '-01'; // Start date
    $end_date = $end_year . '-' . $end_month . '-01'; // End date

    //Check if end date is before start date
    if ($end_date < $start_date) {
        $_SESSION['message'] = "Bitiş tarihi, başlangıç tarihinden önce olamaz.";
        $_SESSION['message_type'] = 'error';
    } else {
        // Check if the same project name exists (case insensitive)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM projects WHERE LOWER(project_name) = LOWER(?)");
        $stmt->bind_param("s", $project_name);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $_SESSION['message'] = "Bu proje adı zaten mevcut. Lütfen farklı bir ad girin.";
            $_SESSION['message_type'] = 'error';
        } else {
            // Add the project to the database
            $stmt = $conn->prepare("INSERT INTO projects (project_name, start_date, end_date) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $project_name, $start_date, $end_date);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Proje başarıyla eklendi.";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Proje eklenirken bir hata oluştu: " . $stmt->error;
                $_SESSION['message_type'] = 'error';
            }

            $stmt->close();
        }
    }

    // Refresh the page with the redirect
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

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
    <title>Proje Ekle</title>
    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
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
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            color: #333;
        }
        input[type="text"],
        input[type="number"],
        select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .success-message {
            color: #28a745; 
            background-color: #d4edda; 
            border: 1px solid #c3e6cb; 
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .error-message {
            color: #dc3545; 
            background-color: #f8d7da; 
            border: 1px solid #f5c6cb; 
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .back-button {
            background-color: #6c757d; 
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 10px;
            text-align: center;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Proje Ekle</h1>
        <?php if (!empty($message)): ?>
            <div class="<?= $message_type == 'error' ? 'error-message' : 'success-message' ?>">
                <?= $message; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <label for="project_name">Proje Adı:</label>
            <input type="text" id="project_name" name="project_name" required>
            
            <label for="start_month">Başlangıç Ayı:</label>
            <select id="start_month" name="start_month" required>
                <option value="">Seçin</option>
                <option value="01">Ocak</option>
                <option value="02">Şubat</option>
                <option value="03">Mart</option>
                <option value="04">Nisan</option>
                <option value="05">Mayıs</option>
                <option value="06">Haziran</option>
                <option value="07">Temmuz</option>
                <option value="08">Ağustos</option>
                <option value="09">Eylül</option>
                <option value="10">Ekim</option>
                <option value="11">Kasım</option>
                <option value="12">Aralık</option>
            </select>

            <label for="start_year">Başlangıç Yılı:</label>
            <input type="number" id="start_year" name="start_year" required>
            
            <label for="end_month">Bitiş Ayı:</label>
            <select id="end_month" name="end_month" required>
                <option value="">Seçin</option>
                <option value="01">Ocak</option>
                <option value="02">Şubat</option>
                <option value="03">Mart</option>
                <option value="04">Nisan</option>
                <option value="05">Mayıs</option>
                <option value="06">Haziran</option>
                <option value="07">Temmuz</option>
                <option value="08">Ağustos</option>
                <option value="09">Eylül</option>
                <option value="10">Ekim</option>
                <option value="11">Kasım</option>
                <option value="12">Aralık</option>
            </select>

            <label for="end_year">Bitiş Yılı:</label>
            <input type="number" id="end_year" name="end_year" required>
            
            <button type="submit">Ekle</button>
        </form>
        <button class="back-button" onclick="window.location.href='project_management.php';">Geri Dön</button>
    </div>
</body>
</html>
