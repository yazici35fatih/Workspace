<?php
session_start();
include 'connection.php';

// Get search query
$search_query = isset($_POST['search_query']) ? $_POST['search_query'] : '';

// get users
$query = "SELECT user_id, name, surname FROM users WHERE CONCAT(name, ' ', surname) LIKE ?";
$search_term = '%' . $search_query . '%';
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $search_term);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kişi Sil</title>
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
        input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
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
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .delete-button {
            background-color: #dc3545;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
        .back-button {
            background-color: #6c757d;
            margin-top: 20px;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Kişi Sil</h1>
        <form method="post" action="">
            <label for="search_query">Kişi Ara:</label>
            <input type="text" id="search_query" name="search_query" value="<?= htmlspecialchars($search_query) ?>" required>
            <button type="submit">Ara</button>
        </form>

        <?php if (!empty($users)): ?>
            <table>
                <tr>
                    <th>Adı</th>
                    <th>Soyadı</th>
                    <th>İşlem</th>
                </tr>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['surname']) ?></td>
                        <td>
                            <form method="post" action="delete_action.php" onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');">
                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                <button type="submit" class="delete-button">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Aramanıza uygun kullanıcı bulunamadı.</p>
        <?php endif; ?>
        <a href="project_management.php"><button class="back-button">Geri Dön</button></a>
    </div>
</body>
</html>
