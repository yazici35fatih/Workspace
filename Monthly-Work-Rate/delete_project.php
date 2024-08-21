<?php
session_start();
include 'connection.php';

$message = "";
$projects = [];

// Project deletion
if (isset($_GET['id'])) {
    $project_id = $_GET['id'];

    // Delete project from database
    $stmt = $conn->prepare("DELETE FROM projects WHERE project_id = ?");
    $stmt->bind_param("i", $project_id);

    if ($stmt->execute()) {
        $message = "Proje başarıyla silindi.";
    } else {
        $message = "Proje silinirken bir hata oluştu: " . $stmt->error;
    }

    $stmt->close();
}

// List projects
$search_query = "";
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

$stmt = $conn->prepare("SELECT project_id, project_name FROM projects WHERE project_name LIKE ?");
$search_term = "%" . $search_query . "%";
$stmt->bind_param("s", $search_term);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proje Sil</title>
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
        form {
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
            width: calc(100% - 22px); 
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
            background-color: #f2f2f2;
        }
        .delete-button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Proje Sil</h1>
        <?php if (!empty($message)): ?>
            <div class="<?= strpos($message, 'hata') !== false ? 'error-message' : 'success-message' ?>">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="text" name="search" placeholder="Proje adını ara..." value="<?= htmlspecialchars($search_query) ?>">
            <button type="submit">Ara</button>
        </form>

        <?php if (!empty($projects)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Proje Adı</th>
                        <th>Sil</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?= htmlspecialchars($project['project_name']); ?></td>
                            <td>
                                <a href="?id=<?= $project['project_id']; ?>" class="delete-button" onclick="return confirm('Bu projeyi silmek istediğinize emin misiniz?');">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Arama sonuçlarına göre proje bulunamadı.</p>
        <?php endif; ?>
        
        <button class="back-button" onclick="window.location.href='project_management.php';">Geri Dön</button>
    </div>
</body>
</html>
