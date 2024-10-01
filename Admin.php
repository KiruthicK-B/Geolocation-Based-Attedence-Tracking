<?php
session_start();
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
$userCountQuery = "SELECT COUNT(DISTINCT username) AS user_count FROM user_sessions";
$userCountResult = $conn->query($userCountQuery);
$userCount = 0;

if ($userCountResult->num_rows > 0) {
    $row = $userCountResult->fetch_assoc();
    $userCount = $row['user_count'];
}
if (empty($inputUsername)) {
  echo "<span style='color:#ffc107;display:none;'>______________________________________Welcome! ADMIN</span>";
} else {
$dayWiseDataQuery = "
    SELECT 
        DATE(session_start) as date, 
        COUNT(session_start) as checkins, 
        COUNT(session_end) as checkouts, 
        SEC_TO_TIME(SUM(session_duration)) as total_duration 
    FROM user_sessions 
    WHERE username = '$inputUsername'
    GROUP BY DATE(session_start)
    ORDER BY date
";

$dayWiseResult = $conn->query($dayWiseDataQuery);

$days = [];
$checkins = [];
$checkouts = [];
$totalDurations = [];

while ($row = $dayWiseResult->fetch_assoc()) {
    $days[] = $row['date'];
    $checkins[] = $row['checkins'];
    $checkouts[] = $row['checkouts'];
    $totalDurations[] = $row['total_duration'];
}
}
$resultData = null;

if (isset($_GET['input_date']) && isset($_GET['start_time']) && isset($_GET['end_time'])) {
    $inputDate = $conn->real_escape_string($_GET['input_date']);
    $inputStartTime = $_GET['start_time'];
    $inputEndTime = $_GET['end_time'];

    // Convert the input date (in mm-dd-yyyy) to yyyy-mm-dd format
    $dateObject = DateTime::createFromFormat('d-m-Y', $inputDate);

    if ($dateObject) {
        // If the date conversion was successful, format it
        $formattedDate = $dateObject->format('Y-d-m');
    } else {
        // Handle the error (invalid date format or conversion failure)
        die("Invalid date format. Please enter a date in mm-dd-yyyy format.");
    }

    // Convert 12-hour format to 24-hour format using PHP's DateTime object
    $startTime24 = date("H:i:s", strtotime($inputStartTime));
    $endTime24 = date("H:i:s", strtotime($inputEndTime));

    // Query to fetch usernames and checkout_time within the specified date and time range
    $query = "
        SELECT 
            username, 
            session_start, 
            checkout_time 
        FROM 
            user_sessions 
        WHERE 
            DATE(session_start) = '$formattedDate'
            AND TIME(session_start) >= '$startTime24'
            AND TIME(session_start) <= '$endTime24'
        ORDER BY session_start
    ";
    
    $resultData = $conn->query($query);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin</title>
  <link rel="stylesheet" href="Admin.css">
  <link rel="stylesheet" href="admintable.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <aside class="sidebar position-fixed top-0 left-0 overflow-auto h-100 float-left" id="show-side-navigation1">
    <i class="uil-bars close-aside d-md-none d-lg-none" data-close="show-side-navigation1"></i>
    <div class="sidebar-header d-flex justify-content-center align-items-center px-3 py-4">
      <img
           class="rounded-pill img-fluid"
           width="65"
           src="Group 34056.png"
           alt="">
      <div class="ms-2">
        <h5 class="fs-6 mb-0">
          <a class="text-decoration-none" href="#">
            <?php if (isset($_SESSION['username'])): ?>
            <p><?php echo $_SESSION['username']; ?>!</p>
             <?php else: ?>
               <p>ADMIN</p>
             <?php endif; ?>
            </a>
        </h5>
        <p class="mt-1 mb-0">Geolocation Based Attendence Tracking</p>
      </div>
    </div>
  
    <div class="search position-relative text-center px-4 py-3 mt-2">
      <input type="text" class="form-control w-100 border-0 bg-transparent" placeholder="Search">
      <i class="fa fa-search position-absolute d-block fs-6"></i>
    </div>
  
    <ul class="categories list-unstyled">
      <li class="has-dropdown">
        <i class="uil-estate fa-fw"></i><a href="#"> Dashboard</a>
      </li>
      <li class="">
        <i class="uil-folder"></i><a href="#">Coordinates Locker</a>
      </li>
    </ul>
  </aside>
  
  <section id="wrapper">
    <nav class="navbar navbar-expand-md">
      <div class="container-fluid mx-2">
        <div class="navbar-header">
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#toggle-navbar" aria-controls="toggle-navbar" aria-expanded="false" aria-label="Toggle navigation">
            <i class="uil-bars text-white"></i>
          </button>
          <a class="navbar-brand" href="#">admin<span class="main-color">kit</span></a>
        </div>
        <div class="collapse navbar-collapse" id="toggle-navbar">
          <ul class="navbar-nav ms-auto">
              <a style="text-decoration:none;color:white;" href="registration.php" id="navbarDropdown" role="button" aria-expanded="false">
                LOGOUT
              </a>
          </ul>
        </div>
      </div>
    </nav>
  
    <div class="p-4">
      <div class="welcome">
        <div class="content rounded-3 p-3">
          <h1 class="fs-3">Welcome to Dashboard</h1>
        </div>
      </div>
  
      <section class="statistics mt-4">
        <div class="row">
          <div class="col-lg-4">
            <div class="box d-flex rounded-2 align-items-center mb-4 mb-lg-0 p-3">
              <i class="uil-envelope-shield fs-2 text-center bg-primary rounded-circle"></i>
              <div class="ms-3">
                <div class="d-flex align-items-center">
                  <h3 class="mb-0">0</h3> <span class="d-block ms-2">Emails</span>
                </div>
                <p class="fs-normal mb-0"></p>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="box d-flex rounded-2 align-items-center mb-4 mb-lg-0 p-3">
              <i class="uil-file fs-2 text-center bg-danger rounded-circle"></i>
              <div class="ms-3">
                <div class="d-flex align-items-center">
                  <h3 class="mb-0">0</h3> <span class="d-block ms-2">Active users</span>
                </div>
                <p class="fs-normal mb-0"></p>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="box d-flex rounded-2 align-items-center p-3">
              <i class="uil-users-alt fs-2 text-center bg-success rounded-circle"></i>
              <div class="ms-3">
                <div class="d-flex align-items-center">
                    <h3 class="mb-0"><?php echo $userCount; ?></h3> <span class="d-block ms-2">Users</span>
                </div>
                <p class="fs-normal mb-0"></p>
            </div>
            </div>
          </div>
        </div>
      </section>
  
      <section class="charts mt-4">
        <div class="row">
          <div class="col-lg-6">
            <div class="chart-container rounded-2 p-3">
            <form method="GET" action="">
              <h3><label>Enter Username to fetch Detail:</label></h3>
              <input type="text" name="username" required>
              <button  style="margin-left:30px;">
              <input type="submit" style="background:transparent;border:none;" value="FETCH">
                <div id="clip">
                    <div id="leftTop" class="corner"></div>
                    <div id="rightBottom" class="corner"></div>
                    <div id="rightTop" class="corner"></div>
                    <div id="leftBottom" class="corner"></div>
                </div>
                <span id="rightArrow" class="arrow"></span>
                <span id="leftArrow" class="arrow"></span>
            </button>
            </form><br>
            <h3 class="fs-6 mb-3">Total Session Duration for User: 
            <?php 
            if (isset($inputUsername) && !empty($inputUsername)) {
                echo htmlspecialchars($inputUsername);
            } else {
                echo "<span style='color: red;'>Please enter an Employee ID or username</span>";
            }
            ?></h3>
              <table border="1">
                  <thead>
                      <tr>
                          <th>Username</th>
                          <th>Total Weekly Working hour Duration (Hrs:Min:Sec)</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php if ($totalDurationResult && $totalDurationResult->num_rows > 0): ?>
                          <?php while($row = $totalDurationResult->fetch_assoc()): ?>
                              <tr>
                                  <td><?php echo htmlspecialchars($row['username']); ?></td>
                                  <td><?php echo htmlspecialchars($row['total_session_duration']); ?></td>
                              </tr>
                          <?php endwhile; ?>
                      <?php else: ?>
                          <tr>
                              <td colspan="2">No data available for this user.</td>
                          </tr>
                      <?php endif; ?>
                  </tbody>
              </table>
              <canvas id="myChart"></canvas>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="chart-container rounded-2 p-3">
              <h3 class="fs-6 mb-3">Analytics</h3>
              <canvas id="myChart2"></canvas>
            </div>
          </div>
        </div>
      </section>
  
      <section class="admins mt-4">
        <div class="row">
          <div class="col-md-6">
            <div class="box">
              <!-- <h4>Admins:</h4> -->
              <!-- <div class="admin d-flex align-items-center rounded-2 p-3 mb-4">
                <div class="img">
                  <img class="img-fluid rounded-pill"
                       width="75" height="75"
                       src="https://uniim1.shutterfly.com/ng/services/mediarender/THISLIFE/021036514417/media/23148906966/small/1501685402/enhance"
                       alt="admin">
                </div>
                <div class="ms-3">
                  <h3 class="fs-5 mb-1">Joge Lucky</h3>
                  <p class="mb-0">Lorem ipsum dolor sit amet consectetur elit.</p>
                </div>
              </div>
              <div class="admin d-flex align-items-center rounded-2 p-3 mb-4">
                <div class="img">
                  <img class="img-fluid rounded-pill"
                       width="75" height="75"
                       src="https://uniim1.shutterfly.com/ng/services/mediarender/THISLIFE/021036514417/media/23148907137/small/1501685404/enhance"
                       alt="admin">
                </div>
                <div class="ms-3">
                  <h3 class="fs-5 mb-1">Joge Lucky</h3>
                  <p class="mb-0">Lorem ipsum dolor sit amet consectetur elit.</p>
                </div>
              </div>
              <div class="admin d-flex align-items-center rounded-2 p-3">
                <div class="img">
                  <img class="img-fluid rounded-pill"
                       width="75" height="75"
                       src="https://uniim1.shutterfly.com/ng/services/mediarender/THISLIFE/021036514417/media/23148907019/small/1501685403/enhance"
                       alt="admin">
                </div>
                <div class="ms-3">
                  <h3 class="fs-5 mb-1">Joge Lucky</h3>
                  <p class="mb-0">Lorem ipsum dolor sit amet consectetur elit.</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="box">
              <h4>Moderators:</h4> -->
              <!--<div class="admin d-flex align-items-center rounded-2 p-3 mb-4">
                <div class="img">
                  <img class="img-fluid rounded-pill"
                       width="75" height="75"
                       src="https://uniim1.shutterfly.com/ng/services/mediarender/THISLIFE/021036514417/media/23148907114/small/1501685404/enhance"
                       alt="admin">
                </div>
                <div class="ms-3">
                  <h3 class="fs-5 mb-1">Joge Lucky</h3>
                  <p class="mb-0">Lorem ipsum dolor sit amet consectetur elit.</p>
                </div>
              </div>
              <div class="admin d-flex align-items-center rounded-2 p-3 mb-4">
                <div class="img">
                  <img class="img-fluid rounded-pill"
                       width="75" height="75"
                       src="https://uniim1.shutterfly.com/ng/services/mediarender/THISLIFE/021036514417/media/23148907086/small/1501685404/enhance"
                       alt="admin">
                </div>
                <div class="ms-3">
                  <h3 class="fs-5 mb-1">Joge Lucky</h3>
                  <p class="mb-0">Lorem ipsum dolor sit amet consectetur elit.</p>
                </div>
              </div>
              <div class="admin d-flex align-items-center rounded-2 p-3">
                <div class="img">
                  <img class="img-fluid rounded-pill"
                       width="75" height="75"
                       src="https://uniim1.shutterfly.com/ng/services/mediarender/THISLIFE/021036514417/media/23148907008/medium/1501685726/enhance"
                       alt="admin">
                </div>
                <div class="ms-3">
                  <h3 class="fs-5 mb-1">Joge Lucky</h3>
                  <p class="mb-0">Lorem ipsum dolor sit amet consectetur elit.</p>
                </div>
              </div>
            </div> -->
          </div>
        </div>
      </section>
  
      <section class="statis mt-4 text-center">
        <div class="row">
          <div class="col-md-6 col-lg-3 mb-4 mb-lg-0">
            <div class="box bg-primary p-3">
              <i class="uil-eye"></i>
              <h3>4</h3>
              <p class="lead">Admins</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-3 mb-4 mb-lg-0">
            <div class="box bg-danger p-3">
              <i class="uil-user"></i>
              <h3>245</h3>
              <p class="lead">User registered</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-3 mb-4 mb-md-0">
            <div class="box bg-warning p-3">
              <i class="uil-shopping-cart"></i>
              <h3>5,154</h3>
              <p class="lead">NO of CheckIns</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="box bg-success p-3">
              <i class="uil-feedback"></i>
              <h3>5,154</h3>
              <p class="lead">NO of CheckOuts</p>
            </div>
          </div>
        </div>
      </section>
      <section class="charts mt-4" id="tab">
        <div class="chart-container p-3" id="tab2">
          <h3 class="fs-6 mb-3">Time wise Monitoring</h3>
            <form method="GET" action="">
                <h3 class="fs-6 mb-3"><label>Enter Date (DD-MM-YYYY):</label></h3>
                <input type="text" name="input_date" required placeholder="eg: 01-05-2024">
                
                <h3 class="fs-6 mb-3"><label>Enter Start Time (hh:mm am/pm):</label></h3>
                <input type="text" name="start_time" required placeholder="eg: 03:00 am">
                
                <h3 class="fs-6 mb-3"><label>Enter End Time (hh:mm am/pm):</label></h3>
                <input type="text" name="end_time" required placeholder="eg: 04:00 pm">
                <button style="margin-left:30px;">
                  <input type="submit" style="background:transparent;border:none;" value="FETCH">
                    <div id="clip">
                        <div id="leftTop" class="corner"></div>
                        <div id="rightBottom" class="corner"></div>
                        <div id="rightTop" class="corner"></div>
                        <div id="leftBottom" class="corner"></div>
                    </div>
                    <span id="rightArrow" class="arrow"></span>
                    <span id="leftArrow" class="arrow"></span>
                </button>
            </form><br>
          <?php if ($resultData && $resultData->num_rows > 0): ?>
            <h3>Users who checked in on <?php echo htmlspecialchars($inputDate); ?> between <?php echo htmlspecialchars($inputStartTime); ?> and <?php echo htmlspecialchars($inputEndTime); ?>:</h3>
            <table border="1">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Checkin Time</th>
                        <th>Checkout Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $resultData->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['session_start']); ?></td>
                            <td><?php echo htmlspecialchars($row['checkout_time']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
          <?php elseif (isset($inputDate)): ?>
              <p style=color:red;>No data found for the specified date and time range.</p>
          <?php endif; ?>
          <div style="height: 300px">
            <canvas id="chart3" width="100%">
            </canvas>
          </div>
        </div>
      </section>
      <section class="charts mt-4" id="tab">
        <div class="chart-container p-3" id="tab2">
          <h3 class="fs-6 mb-3">User History</h3>
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
                <p style=color:red;>No data found for user: <?php echo htmlspecialchars($inputUsername); ?></p>
            <?php endif; ?>
          <div style="height: 300px">
            <canvas id="chart3" width="100%">
            </canvas>
          </div>
        </div>
      </section>
    </div>
  </section>
</body>
<script src="Admin.js"></script>
<script>
    // PHP arrays to JavaScript arrays
    const days = <?php echo json_encode($days); ?>;
    const checkins = <?php echo json_encode($checkins); ?>;
    const checkouts = <?php echo json_encode($checkouts); ?>;
    const totalDurations = <?php echo json_encode($totalDurations); ?>;

    const ctx = document.getElementById('myChart2').getContext('2d');
    const attendanceChart = new Chart(ctx, {
        type: 'bar', // You can change to 'line' or other chart types
        data: {
            labels: days,
            datasets: [
                {
                    label: 'Check-ins',
                    data: checkins,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)', // Blue color
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Checkouts',
                    data: checkouts,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)', // Red color
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Attendance Count (Hrs)'
                    },
                    beginAtZero: true
                }
            }
        }
    });
</script>
</html>