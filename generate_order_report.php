<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$servername = "localhost";
$username = "u249983471_root";
$password = "Mouhcin2004.";
$database = "u249983471_bloom_cosmetic";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Function to generate CSV file with all orders
function generateOrderReport($conn) {
    // Fetch all orders from the database
    $sql = "SELECT * FROM orders";
    $result = $conn->query($sql);

    $orders = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }

    // Create CSV file
    $tempFile = tempnam(sys_get_temp_dir(), 'order_report_') . '.csv';
    $handle = fopen($tempFile, 'w');

    // Add headers
    $headers = [
        'Order ID', 'Full Name', 'Number', 'Address', 
        'Coupon Code', 'Quantity', 'Total Price', 'Order Date'
    ];
    fputcsv($handle, $headers);
    
    // Add data
    foreach ($orders as $order) {
        $orderData = [
            $order['id'],
            $order['fullname'],
            $order['number'],
            $order['address'],
            $order['coupon_code'] ?? 'N/A',
            $order['quantity'] ?? 1,
            $order['total_price'],
            $order['created_at'] ?? 'N/A'
        ];
        fputcsv($handle, $orderData);
    }
    
    fclose($handle);
    
    return $tempFile;
}

// Function to send email with attachment
function sendEmailWithAttachment($to, $subject, $body, $attachmentPath) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Gmail SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mouhcinlahrech1@gmail.com'; // Your Gmail address
        $mail->Password   = 'tjjk rhec patd fgrx'; // Your Gmail password or app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        // Recipients
        $mail->setFrom('your_email@example.com', 'Bloom Cosmetics');
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        // Add attachment
        $mail->addAttachment($attachmentPath);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Generate the report
$csvFile = generateOrderReport($conn);

// Send email with the report
if (sendEmailWithAttachment('mouhcin.lahrech.dev@gmail.com', 'New Order Report', 'Please find the attached updated order report.', $csvFile)) {
    echo "Email sent successfully!";
} else {
    echo "Email could not be sent.";
}

// Clean up the temporary file
unlink($csvFile);

$conn->close();
?>