<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
if (!isset($_SESSION['username'])) {
    die("User not logged in.");
}

$username = $_SESSION['username'];

$conn = new mysqli("localhost", "root", "", "dbgeo");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Calculate number of checkouts in the last week
$query = "SELECT COUNT(*) as checkout_count FROM user_sessions WHERE username = ? AND checkout_time >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$checkoutCount = $row['checkout_count'];

// Calculate total session duration
$query = "SELECT SUM(session_duration) as total_duration FROM user_sessions WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalDuration = $row['total_duration'];

// Format total duration
$totalMinutes = floor($totalDuration / 60);
$totalSeconds = $totalDuration % 60;

echo "Number of checkouts in the last week: $checkoutCount<br>";
echo "Total session time: {$totalMinutes}m {$totalSeconds}s";

$stmt->close();
$conn->close();
?>
