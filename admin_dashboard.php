<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbgeo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for storing query results
$singleUserResult = null;
$totalDurationResult = null;

if (isset($_GET['username']) && !empty($_GET['username'])) {
    $inputUsername = $conn->real_escape_string($_GET['username']);
    
    // Query to get total session duration for the entered username
    $totalDurationQuery = "
        SELECT 
            username, 
            SEC_TO_TIME(SUM(session_duration)) AS total_session_duration 
        FROM 
            user_sessions 
        WHERE username = '$inputUsername'
        GROUP BY username
    ";
    
    $totalDurationResult = $conn->query($totalDurationQuery);

    // Query to get all day-wise check-in data for the entered username
    $userQuery = "
        SELECT 
            username, 
            session_start, 
            session_end, 
            session_duration,
            checkout_time
        FROM 
            user_sessions 
        WHERE username = '$inputUsername'
        ORDER BY session_start
    ";
    
    $singleUserResult = $conn->query($userQuery);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css"> <!-- Link to your CSS file -->
</head>
<body>
    <nav>
        <h1>Admin Dashboard</h1>
    </nav>

    <main>
        <!-- Form to fetch user details -->
        <form method="GET" action="">
            <label>Enter Username to fetch Detail:</label>
            <input type="text" name="username" required>
            <input type="submit" value="Fetch">
        </form>

        <!-- Display total session duration if form is submitted -->
        <?php if ($totalDurationResult && $totalDurationResult->num_rows > 0): ?>
            <h3>Total Session Duration for User: <?php echo htmlspecialchars($inputUsername); ?></h3>
            <table border="1">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Total Weekly Working hour Duration(Hrs:Min:Sec)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $totalDurationResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_session_duration']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Display day-wise check-in data if form is submitted -->
        <?php if ($singleUserResult && $singleUserResult->num_rows > 0): ?>
            <h3>Day-wise Check-in Data for User: <?php echo htmlspecialchars($inputUsername); ?></h3>
            <table border="1">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>LogIn DETAILS</th>
                        <th>LogOut DETAILS</th>
                        <th>WORKING TIME(secs)</th>
                        <th>checkOutTime</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $singleUserResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['session_start']); ?></td>
                            <td><?php echo htmlspecialchars($row['session_end']); ?></td>
                            <td><?php echo htmlspecialchars($row['session_duration']); ?></td>
                            <td><?php echo htmlspecialchars($row['checkout_time']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php elseif (isset($inputUsername)): ?>
            <p>No data found for user: <?php echo htmlspecialchars($inputUsername); ?></p>
        <?php endif; ?>
    </main>

    <?php $conn->close(); ?>
</body>
</html>
