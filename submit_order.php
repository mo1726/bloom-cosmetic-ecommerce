<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "u249983471_root";
$password = "Mouhcin2004.";
$database = "u249983471_bloom_cosmetic";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

// Read input data
$data = json_decode(file_get_contents("php://input"), true);

// Validate input fields
if (!isset($data['fullname']) || !isset($data['number']) || !isset($data['address']) || !isset($data['quantity']) || !isset($data['totalPrice'])) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

// Sanitize and prepare data
$fullname = $conn->real_escape_string($data['fullname']);
$number = $conn->real_escape_string($data['number']);
$address = $conn->real_escape_string($data['address']);
$quantity = intval($data['quantity']);
$couponCode = isset($data['couponCode']) ? $conn->real_escape_string($data['couponCode']) : NULL;
$totalPrice = floatval($data['totalPrice']);

// If coupon code is provided, validate and update quantity
if (!empty($couponCode)) {
    // Validate coupon code
    $couponSql = "SELECT * FROM coupons WHERE code = ? AND quantity >= ? AND valid_from <= CURDATE() AND valid_to >= CURDATE()";
    $couponStmt = $conn->prepare($couponSql);
    $couponStmt->bind_param("si", $couponCode, $quantity);
    $couponStmt->execute();
    $couponResult = $couponStmt->get_result();
    
    if ($couponResult->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Invalid or expired coupon code or insufficient quantity."]);
        $couponStmt->close();
        $conn->close();
        exit;
    }
    
    // Decrease coupon quantity by the order quantity
    $updateSql = "UPDATE coupons SET quantity = quantity - ? WHERE code = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("is", $quantity, $couponCode);
    
    if (!$updateStmt->execute()) {
        echo json_encode(["success" => false, "message" => "Failed to update coupon quantity."]);
        $updateStmt->close();
        $couponStmt->close();
        $conn->close();
        exit;
    }
    
    $updateStmt->close();
    $couponStmt->close();
}

// Insert data into database with current timestamp
$sql = "INSERT INTO orders (fullname, number, address, quantity, coupon_code, total_price, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Statement preparation failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("sssdsd", $fullname, $number, $address, $quantity, $couponCode, $totalPrice);

if ($stmt->execute()) {
    // Order successfully added
    
    // Check if generate_order_report.php exists
    if (!file_exists('generate_order_report.php')) {
        echo json_encode(["success" => false, "message" => "Report generation file missing"]);
        exit;
    }
    
    // Include the report generation script
    require 'generate_order_report.php';
    
    // Ensure only JSON is output
    ob_clean(); // Clean output buffer
    echo json_encode(["success" => true, "message" => "Order placed successfully!"]);
    exit;
} else {
    echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
    exit;
}

// Close connections
$stmt->close();
$conn->close();
?>