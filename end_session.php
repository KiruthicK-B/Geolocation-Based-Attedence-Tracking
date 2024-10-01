<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['username']) && isset($_POST['duration']) && isset($_POST['sessionEndTime']) && isset($_POST['sessionStartTime'])) {
        $username = $_SESSION['username'];
        $sessionStartTime = date('Y-m-d H:i:s', strtotime($_POST['sessionStartTime']));  // Convert string to valid MySQL datetime format
        $sessionEndTime = date('Y-m-d H:i:s', strtotime($_POST['sessionEndTime']));  // Convert string to valid MySQL datetime format
        $sessionDuration = $_POST['duration'];

        $conn = new mysqli("localhost", "root", "", "dbgeo");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO user_sessions (username, session_start, session_end, session_duration, checkout_time) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $username, $sessionStartTime, $sessionEndTime, $sessionDuration);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    } else {
        echo "Session or POST data is missing.";
    }
}
?>
