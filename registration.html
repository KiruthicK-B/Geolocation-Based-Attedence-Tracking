<?php
session_start();

$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "dbgeo";

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        // Login logic
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['username'] = $username;
            header("Location: Admin.php");
            exit();
        }

        $query = "SELECT * FROM geoloc WHERE username='$username'";
        $result = $conn->query($query);

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            // Verify password hash
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $user['email'];
                $_SESSION['session_start_time'] = date("Y-m-d H:i:s");
                echo "<script>alert('Login Successful');
                window.location.href='index.php';
                </script>";
                exit();
            } else {
                echo "<script>alert('Incorrect Password');</script>";
            }
        } else {
            echo "<script>alert('Incorrect Username');</script>";
        }
    } elseif (isset($_POST['signup'])) {
        // Sign-up logic
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['crmpassword'];

        // Check if passwords match
        if ($password != $confirmPassword) {
            echo "<script>alert('Passwords do not match');</script>";
        } else {
            // Check if username already exists
            $stmt = $conn->prepare("SELECT * FROM geoloc WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<script>alert('Username already exists');</script>";
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO geoloc (username, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $email, $hashedPassword);
                if ($stmt->execute()) {
                    echo "<script>alert('Registration Successful');
                    window.location.href='index.php';
                    </script>";
                } else {
                    echo "<script>alert('Error during registration');</script>";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up / Login</title>
    <style>
        /* Styles similar to the ones you provided */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('bg.jpg'); 
            background-attachment: fixed; 
            background-size: cover;
            background-repeat: no-repeat; 
            font-family: 'Arial', sans-serif;
        }

        .container {
            width: 900px;
            height: 600px;
            background-color: white;
            display: flex;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        .left-section {
            width: 50%;
            background: #9b59b6;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            color: white;
        }

        .left-section img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background-color: #fff;
        }

        .left-section h1 {
            margin-top: 20px;
            font-size: 40px;
            font-weight: bold;
        }

        .left-section p {
            margin-top: 20px;
            font-size: 20px;
            text-align: center;
        }

        .right-section {
            width: 50%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: #f8f8f8;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
            display: inline-block;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-top: 5px;
        }

        .form-group input:focus {
            border-color: #8e44ad;
            outline: none;
        }

        .form-buttons {
            display: flex;
            justify-content: space-between;
        }

        .form-buttons button {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            background-color: #8e44ad;
            color: white;
            transition: 0.3s;
        }

        .form-buttons button:hover {
            background-color: #732d91;
        }

        .form-toggle {
            margin-top: 20px;
            text-align: center;
        }

        .form-toggle a {
            color: #8e44ad;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }

        .form-toggle a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        
        <!-- Left Section -->
        <div class="left-section">
            <img src="Group 24.png" alt="Illustration">
            <h1>GeoLoc</h1>
            <p>Elevating productivity and collaboration with innovative geolocation-based attendance solutions.</p>
        </div>

        <!-- Right Section -->
    <div class="right-section">
       

  <!-- Login Form -->
    <div id="loginForm">
                    <center>
                    <h2>Login</h2>
                </center>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <div class="form-buttons">
                        <button type="submit" name="login">Login</button>
                    </div>
                    <div class="form-toggle">
                        <p>Don't have an account? <a onclick="showSignUp()">Sign Up</a></p>
                    </div>
                </form>
            </div>

            <!-- Sign Up Form -->
            <div id="signUpForm" style="display: none;">
                <center>
                    <h2>Sign Up</h2>
                </center>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="signup-username" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="signup-email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="signup-password" name="password" placeholder="Create a password" required>
                    </div>
                    <div class="form-group">
                        <label for="crmpassword">Confirm Password</label>
                        <input type="password" id="signup-crmpassword" name="crmpassword" placeholder="Confirm your password" required>
                    </div>
                    <div class="form-buttons">
                        <button type="submit" name="signup">Sign Up</button>
                    </div>
                    <div class="form-toggle">
                        <p>Already have an account? <a onclick="showLogin()">Login</a></p>
                    </div>
                </form>
                
            </div>
        </div>
    </div>

    <script>
        function showSignUp() {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('signUpForm').style.display = 'block';
        }

        function showLogin() {
            document.getElementById('loginForm').style.display = 'block';
            document.getElementById('signUpForm').style.display = 'none';
        }
    </script>
</body>

</html>
