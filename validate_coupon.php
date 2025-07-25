<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

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

$couponCode = strtoupper($_GET['code']);

// Check if coupon code is provided
if (empty($couponCode)) {
    echo json_encode(["success" => false, "message" => "Coupon code is required."]);
    exit;
}

// Fetch coupon from database using prepared statement
$couponSql = "SELECT * FROM coupons WHERE code = ? AND quantity > 0 AND valid_from <= CURDATE() AND valid_to >= CURDATE()";
$couponStmt = $conn->prepare($couponSql);
$couponStmt->bind_param("s", $couponCode);
$couponStmt->execute();
$couponResult = $couponStmt->get_result();

if ($couponResult->num_rows > 0) {
    $coupon = $couponResult->fetch_assoc();
    
    // Return coupon details
    echo json_encode([
        "success" => true,
        "discount" => $coupon['discount'],
        "message" => "Coupon applied! You got a {$coupon['discount']}% discount."
    ]);
} else {
    // Coupon not found or invalid
    echo json_encode([
        "success" => false,
        "message" => "Invalid or expired coupon."
    ]);
}

$couponStmt->close();
$conn->close();
?>