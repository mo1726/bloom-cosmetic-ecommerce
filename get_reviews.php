<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://bloom-cosmet.com/"); // Allow requests from your frontend
header("Access-Control-Allow-Methods: GET"); // Allow GET requests

// Database connection
$host = "localhost";
$user = "u249983471_root"; // Replace with your MySQL username
$password = "Mouhcin2004."; // Replace with your MySQL password
$database = "u249983471_bloom_cosmetic"; // Replace with your database name

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        "success" => false,
        "message" => "Database connection failed: " . $conn->connect_error
    ]));
}

// Fetch reviews from the database
$sql = "SELECT * FROM reviews ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    echo json_encode([
        "success" => true,
        "data" => $reviews
    ]);
} else {
    echo json_encode([
        "success" => true,
        "data" => []
    ]);
}

$conn->close();
?>