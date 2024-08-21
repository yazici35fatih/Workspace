<?php
session_start();
include 'connection.php';

$search_result = "";
$search_query = "";
$search_type = "";

// To reset the session when returning to the home page:
if (isset($_GET['reset'])) {
    unset($_SESSION['search_query']);
    unset($_SESSION['search_type']);
    header("Location: project_management.php");
    exit();
}

// Show all projects and contacts
if (isset($_GET['show_all'])) {
    $stmt = $conn->prepare("
        SELECT p.project_id AS project_id, p.project_name, p.start_date, p.end_date, u.user_id, u.name, u.surname, u.personnel_type, u.tcno 
        FROM projects p 
        LEFT JOIN project_user pu ON p.project_id = pu.project_id
        LEFT JOIN users u ON pu.user_id = u.user_id 
    ");
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // List project and person results
            $search_result = "<table><tr><th style='width: 200px;'>Proje Adı</th><th style='width: 120px;'>Başlangıç Tarihi</th><th style='width: 120px;'>Bitiş Tarihi</th><th>Adı ve Soyadı</th><th>Kullanıcı ID</th><th>TC No</th><th>Personel Tipi</th></tr>";
            while ($row = $result->fetch_assoc()) {
                $project_id = isset($row['project_id']) ? $row['project_id'] : '';
                $user_name = isset($row['name']) ? htmlspecialchars($row['name']) . " " . htmlspecialchars($row['surname']) : 'Atanmamış';
                $personnel_type = isset($row['personnel_type']) ? $row['personnel_type'] : 'N/A'; //Default value if not assigned
                $tcno = isset($row['tcno']) ? htmlspecialchars($row['tcno']) : 'N/A'; // Turkish Republic No

                $search_result .= "<tr>
                    <td><a href='project_details.php?id={$project_id}'>" . htmlspecialchars($row['project_name']) . "</a></td>
                    <td>{$row['start_date']}</td>
                    <td>{$row['end_date']}</td>
                    <td><a href='person_projects.php?id={$row['user_id']}'>" . $user_name . "</a></td>
                    <td>" . htmlspecialchars($row['user_id']) . "</td>
                    <td>{$tcno}</td>
                    <td>{$personnel_type}</td>
                </tr>";
            }
            $search_result .= "</table>";
        } else {
            $search_result = "Aramanıza uygun sonuç bulunamadı.";
        }
    } else {
        $search_result = "Sorguda bir hata oluştu: " . $stmt->error;
    }

    $stmt->close();
}

// When the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get search query and type
    $search_query = $_POST['search_query'];
    $search_type = isset($_POST['search_type']) ? $_POST['search_type'] : '';

    // Store search information in session
    $_SESSION['search_query'] = $search_query;
    $_SESSION['search_type'] = $search_type;

    if ($search_type == "project") {
        // Search by project name
        $stmt = $conn->prepare("
            SELECT p.project_id AS project_id, p.project_name, p.start_date, p.end_date, u.user_id, u.name, u.surname, u.personnel_type, u.tcno 
            FROM projects p 
            LEFT JOIN project_user pu ON p.project_id = pu.project_id
            LEFT JOIN users u ON pu.user_id = u.user_id 
            WHERE p.project_name LIKE ?
        ");
        
        $search_term = '%' . $search_query . '%';
        $stmt->bind_param("s", $search_term);
    } else if ($search_type == "person") {
        // Search by person name
        $stmt = $conn->prepare("
            SELECT p.project_id AS project_id, p.project_name, p.start_date, p.end_date, u.user_id, u.name, u.surname, u.personnel_type, u.tcno 
            FROM users u 
            LEFT JOIN project_user pu ON u.user_id = pu.user_id
            LEFT JOIN projects p ON pu.project_id = p.project_id 
            WHERE CONCAT(u.name, ' ', u.surname) LIKE ?
        ");
        $search_term = '%' . $search_query . '%';
        $stmt->bind_param("s", $search_term);
    }

    if (isset($stmt)) {
        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // List project and person results
                $search_result = "<table><tr><th style='width: 200px;'>Proje Adı</th><th style='width: 120px;'>Başlangıç Tarihi</th><th style='width: 120px;'>Bitiş Tarihi</th><th>Adı ve Soyadı</th><th>Kullanıcı ID</th><th>TC No</th><th>Personel Tipi</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    $project_id = isset($row['project_id']) ? $row['project_id'] : '';
                    $user_name = isset($row['name']) ? htmlspecialchars($row['name']) . " " . htmlspecialchars($row['surname']) : 'Atanmamış';
                    $personnel_type = isset($row['personnel_type']) ? $row['personnel_type'] : 'N/A'; // Default value if not assigned
                    $tcno = isset($row['tcno']) ? htmlspecialchars($row['tcno']) : 'N/A'; // Turkish Republic No

                    $search_result .= "<tr>
                        <td><a href='project_details.php?id={$project_id}'>" . htmlspecialchars($row['project_name']) . "</a></td>
                        <td>{$row['start_date']}</td>
                        <td>{$row['end_date']}</td>
                        <td><a href='person_projects.php?id={$row['user_id']}'>" . $user_name . "</a></td>
                        <td>" . htmlspecialchars($row['user_id']) . "</td>
                        <td>{$tcno}</td>
                        <td>{$personnel_type}</td>
                    </tr>";
                }
                $search_result .= "</table>";
            } else {
                $search_result = "Aramanıza uygun sonuç bulunamadı.";
            }
        } else {
            $search_result = "Sorguda bir hata oluştu: " . $stmt->error;
        }

        $stmt->close();
    } else if (!isset($stmt)) {
        $search_result = "Geçersiz arama türü seçildi.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proje Veya Kişi Ara</title>
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
            max-width: 800px; 
            margin: auto;
        }
        h1, h2 {
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
        input[type="text"], select {
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: #fff;
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
        <h1>Proje Veya Kişi Ara</h1>
        <form method="post" action="">
            <label for="search_type">Arama Türü:</label>
            <select id="search_type" name="search_type" required>
                <option value="project" <?= ($search_type == "project") ? "selected" : "" ?>>Proje Adı</option>
                <option value="person" <?= ($search_type == "person") ? "selected" : "" ?>>Kişi Adı</option>
            </select>
            <label for="search_query">Arama Sorgusu:</label>
            <input type="text" id="search_query" name="search_query" value="<?= htmlspecialchars($search_query) ?>" required>
            <button type="submit">Ara</button>
        </form>
        <button class="back-button" onclick="window.location.href='?show_all=true';">Tüm Kişi ve Projeleri Göster</button>
        <?php if (!empty($search_result)): ?>
            <div class="search-result">
                <?= $search_result; ?>
            </div>
        <?php endif; ?>
        <button class="back-button" onclick="window.location.href='project_management.php?reset=true';">Geri Dön</button>
    </div>
</body>
</html>
