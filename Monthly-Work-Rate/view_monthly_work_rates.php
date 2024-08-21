<?php
session_start();
include 'connection.php';

// Creating a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Create an empty variable at startup for the search operation
$search_query = '';

// If search was made, get the search query
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

// Pulling data from the database
$sql = "SELECT mwr.user_id, mwr.project_id, CONCAT(u.name, ' ', u.surname) AS employee_name, 
               u.tcno, p.project_name, mwr.month_year, mwr.work_percentage, p.start_date, p.end_date
        FROM monthly_work_rates mwr
        JOIN users u ON mwr.user_id = u.user_id
        JOIN projects p ON mwr.project_id = p.project_id
        WHERE (p.project_name LIKE ? OR CONCAT(u.name, ' ', u.surname) LIKE ?) 
        ORDER BY p.project_name, mwr.user_id, mwr.month_year";

$stmt = $conn->prepare($sql);
$search_param = '%' . $search_query . '%';
$stmt->bind_param('ss', $search_param, $search_param); // Bind for two parameters
$stmt->execute();
$result = $stmt->get_result();

// Initialize work rates array
$work_rates = [];
$projects = []; // Array to store projects
$months = []; // Array to store unique months
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $month_year = new DateTime($row['month_year']);
        $month_year_formatted = $month_year->format('Y-m'); // Format date to Year-Month

        // Store unique months
        if (!in_array($month_year_formatted, $months)) {
            $months[] = $month_year_formatted;
        }

        // If project not yet set, initialize it
        if (!isset($work_rates[$row['project_name']])) {
            $work_rates[$row['project_name']] = [];
            $projects[$row['project_name']] = [
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date']
            ];
        }

        // Initialize employee array if not set
        if (!isset($work_rates[$row['project_name']][$row['employee_name']])) {
            $work_rates[$row['project_name']][$row['employee_name']] = [
                'tcno' => $row['tcno'],
                'total' => 0 // Initialize total work percentage
            ];
        }

        // Store monthly work percentage
        $work_rates[$row['project_name']][$row['employee_name']][$month_year_formatted] = $row['work_percentage'];

        // Update total work percentage
        $work_rates[$row['project_name']][$row['employee_name']]['total'] += $row['work_percentage'];
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aylık Çalışma Oranları</title>
    <style>
        /* CSS styles go here */
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #007bff;
            color: white;
        }

        h2 {
            text-align: center;
        }

        .search {
            text-align: center;
            margin-bottom: 20px;
        }

        .search input[type="text"] {
            width: 300px;
            padding: 10px;
            border: 2px solid #007bff;
            border-radius: 5px;
            font-size: 16px;
        }

        .search input[type="text"]:focus {
            outline: none;
            border-color: #0056b3;
            box-shadow: 0 0 5px rgba(0, 86, 179, 0.5);
        }

        .search button {
            padding: 10px 15px;
            margin-left: 5px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search button:hover {
            background-color: #0056b3;
        }

        .buton {
            display: block;
            text-align: center;
            width: 100px;
            padding: 10px;
            margin: 20px auto;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .buton:hover {
            background-color: #0056b3;
        }
        
    </style>
</head>
<body>

<h1 style="text-align: center;">Aylık Çalışma Oranları</h1>

<!-- Search Form -->
<form method="POST" action="" class="search">
    <input type="text" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="Proje veya çalışan adıyla ara...">
    <button type="submit">Ara</button>
</form>

<?php
// If search query, list projects and relevant months
if (!empty($search_query) && !empty($work_rates)) {
    // List projects and relevant months
    foreach ($projects as $project_name => $dates) {
        echo "<h2>Proje: " . htmlspecialchars($project_name) . " | Başlangıç: " . htmlspecialchars($dates['start_date']) . " | Bitiş: " . htmlspecialchars($dates['end_date']) . "</h2>";
        echo "<table>
                <tr>
                    <th>ÇALIŞAN ADI</th>
                    <th>TC KİMLİK NO</th>";

        // Display months as headers
        foreach ($months as $month) {
            echo "<th>" . htmlspecialchars($month) . "</th>";
        }

        echo "<th>TOPLAM</th>"; // Add a header for the yearly total
        echo "</tr>";

        // Show employees and their work rates across the months
        foreach ($work_rates[$project_name] as $employee_name => $months_data) {
            echo "<tr>
                    <td>" . htmlspecialchars($employee_name) . "</td>
                    <td>" . htmlspecialchars($months_data['tcno']) . "</td>";
            
            // Display work percentages for each month
            foreach ($months as $month) {
                if (isset($months_data[$month])) {
                    echo "<td>" . number_format($months_data[$month], 2) . "</td>";
                } else {
                    echo "<td>0.00</td>"; // Or leave empty if you prefer
                }
            }

            // Display the total work percentage for the year
            echo "<td>" . number_format($months_data['total'], 2) . "</td>";

            echo "</tr>";
        }

        echo "</table>";
    }
} else {
    // Show message if search query is empty or no matching results
    if (!empty($search_query)) {
        echo "<h2 style='text-align:center;'>Hiçbir sonuç bulunamadı.</h2>";
    }
}
?>

<!-- Return Button -->
<a href='project_management.php' class="buton">Geri Dön</a>

</body>
</html>
