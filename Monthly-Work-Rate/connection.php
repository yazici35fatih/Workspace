<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bilgiler";

// Link creation
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the link
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
