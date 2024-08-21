<?php
session_start();
include 'connection.php';

$user_id = isset($_GET['id']) ? $_GET['id'] : null; // Get user ID
$projects = [];

// Check if user ID is valid
if ($user_id) {
    // Get user's projects from database
    $stmt = $conn->prepare("
        SELECT p.project_id, p.project_name 
        FROM projects p
        JOIN project_user pu ON p.project_id = pu.project_id 
        WHERE pu.user_id = ?
    ");

    // If there is an error during the preparation phase
    if ($stmt === false) {
        die("Sorgu hatası: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id); // Connect by User ID
    
    // Run the query and check for errors
    if (!$stmt->execute()) {
        die("Sorgu hatası: " . $stmt->error);
    }
    
    $result = $stmt->get_result();

    // Get projects and add them to the array
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row; // Add projects to array
    }

    $stmt->close();
}

// Check the status of projects
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kişinin Projeleri</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background: #fff;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        li a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        li a:hover {
            text-decoration: underline;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <?php if (empty($projects)): ?>
        <p>Bu kişinin projeleri bulunamadı.</p>
    <?php else: ?>
        <h1>Kişinin Projeleri</h1>
        <ul>
            <?php foreach ($projects as $project): ?>
                <li>
                    <a href='project_details.php?id=<?php echo htmlspecialchars($project["project_id"]); ?>'><?php echo htmlspecialchars($project["project_name"]); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <button onclick="window.location.href='search_project.php';">Geri Dön</button>
</div>

</body>
</html>
