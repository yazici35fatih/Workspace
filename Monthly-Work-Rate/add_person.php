<?php
session_start();
include 'connection.php';

$message = "";

// TR Identity Number verification function
function isValidTCNO($tcno) {
    if (strlen($tcno) != 11 || $tcno[0] == '0') {
        return false;
    }

    $odd_sum = $tcno[0] + $tcno[2] + $tcno[4] + $tcno[6] + $tcno[8];
    $even_sum = $tcno[1] + $tcno[3] + $tcno[5] + $tcno[7];
    
    $digit10 = (($odd_sum * 7) - $even_sum) % 10;
    $digit11 = array_sum(str_split(substr($tcno, 0, 10))) % 10;

    return $tcno[9] == $digit10 && $tcno[10] == $digit11;
}

// When the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $personnel_type = $_POST['personnel_type'];
    $tcno = $_POST['tcno'];

    // Turkish Identity Number verification
    if (!isValidTCNO($tcno)) {
        $message = "Geçersiz TC Kimlik Numarası.";
    } else {
        // Add to database
        $stmt = $conn->prepare("INSERT INTO users (name, surname, personnel_type, tcno) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $surname, $personnel_type, $tcno);

        if ($stmt->execute()) {
            $message = "Kişi başarıyla eklendi.";
        } else {
            $message = "Kişi eklenirken bir hata oluştu: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kişi Ekle</title>
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
        .success-message {
            color: #28a745; /* green color*/
            background-color: #d4edda; /* light green background */
            border: 1px solid #c3e6cb; 
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .error-message {
            color: #dc3545; /* red color */
            background-color: #f8d7da; /* light red background */
            border: 1px solid #f5c6cb; 
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .back-button {
            background-color: #6c757d; /* gray color */
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
        form {
            margin-bottom: 20px;
        }
        input[type="text"], select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
            width: calc(100% - 22px); /* 100% width - padding and border */
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Kişi Ekle</h1>
        <?php if (!empty($message)): ?>
            <div class="<?= strpos($message, 'hata') !== false ? 'error-message' : 'success-message' ?>">
                <?= $message; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <label for="name">Ad:</label>
            <input type="text" id="name" name="name" required>

            <label for="surname">Soyad:</label>
            <input type="text" id="surname" name="surname" required>

            <label for="personnel_type">Personel Tipi:</label>
            <select id="personnel_type" name="personnel_type" required>
                <option value="Akademik">Akademik</option>
                <option value="Sözleşmeli">Sözleşmeli</option>
            </select>

            <label for="tcno">TC Kimlik Numarası:</label>
            <input type="text" id="tcno" name="tcno" pattern="\d{11}" maxlength="11" required title="11 haneli sayı giriniz.">

            <button type="submit">Ekle</button>
        </form>

        <button class="back-button" onclick="window.location.href='project_management.php';">Geri Dön</button>
    </div>
</body>
</html>
