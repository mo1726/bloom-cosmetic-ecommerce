<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://bloom-cosmet.com/");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Database connection
$host = "localhost";
$user = "u249983471_root";
$password = "Mouhcin2004.";
$database = "u249983471_bloom_cosmetic";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode([
        "success" => false,
        "message" => "Database connection failed: " . $conn->connect_error
    ]));
}

// Get the incoming data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['name']) || !isset($data['review']) || !isset($data['rating'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields"
    ]);
    exit;
}

// Sanitize inputs
$name = $conn->real_escape_string($data['name']);
$review = $conn->real_escape_string($data['review']);
$rating = intval($data['rating']);

// Prepare and execute the SQL statement
$sql = "INSERT INTO reviews (name, review, rating, created_at) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Statement preparation failed: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("ssi", $name, $review, $rating);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Review submitted successfully!"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>