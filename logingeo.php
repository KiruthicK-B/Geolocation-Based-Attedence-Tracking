<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['user'];
    $password = $_POST['pass'];

    $host = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "dbgeo";

    $conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['username'] = $username;
        header("Location: Admin.php");
        exit();
    }

    $query = "SELECT * FROM geoloc WHERE username='$username' AND password='$password'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $user['email'];
        $_SESSION['session_start_time'] = date("Y-m-d H:i:s");
        echo "<script>alert('Login Successfull');
        window.location.href='index.php';
        </script>";
        exit();
    } else {
        // Check if username exists
        $query = "SELECT * FROM geoloc WHERE username='$username'";
        $result = $conn->query($query);
        if ($result->num_rows == 0) {
            echo "<script>alert('Login Failed! Incorrect Username');</script>";
        } else {
            echo "<script>alert('Login Failed! Incorrect Password');</script>";
        }
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Login</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            background-image: linear-gradient(340deg, rgba(76, 76, 76,0.02) 0%, rgba(76, 76, 76,0.02) 34%,transparent 34%, transparent 67%,rgba(142, 142, 142,0.02) 67%, rgba(142, 142, 142,0.02) 73%,rgba(151, 151, 151,0.02) 73%, rgba(151, 151, 151,0.02) 100%),linear-gradient(320deg, rgba(145, 145, 145,0.02) 0%, rgba(145, 145, 145,0.02) 10%,transparent 10%, transparent 72%,rgba(35, 35, 35,0.02) 72%, rgba(35, 35, 35,0.02) 76%,rgba(69, 69, 69,0.02) 76%, rgba(69, 69, 69,0.02) 100%),linear-gradient(268deg, rgba(128, 128, 128,0.02) 0%, rgba(128, 128, 128,0.02) 5%,transparent 5%, transparent 76%,rgba(78, 78, 78,0.02) 76%, rgba(78, 78, 78,0.02) 83%,rgba(224, 224, 224,0.02) 83%, rgba(224, 224, 224,0.02) 100%),linear-gradient(198deg, rgba(25, 25, 25,0.02) 0%, rgba(25, 25, 25,0.02) 36%,transparent 36%, transparent 85%,rgba(180, 180, 180,0.02) 85%, rgba(180, 180, 180,0.02) 99%,rgba(123, 123, 123,0.02) 99%, rgba(123, 123, 123,0.02) 100%),linear-gradient(90deg, rgb(255,255,255),rgb(255,255,255));
            background-repeat: no-repeat;
            background-size: cover;
        }
        .login-form {
            max-width: 450px;
            margin: 50px auto;
            padding: 40px;
            background-color: #f5f5f5;
            color: #010101;
            font-family: Helvetica;
            border-radius: 8px;
            box-shadow: inset 0 0 15px rgba(0, 0, 0, 0.267);
            margin-top: 170px;
            background: transparent;
            backdrop-filter: blur(10px);
        }

        .form-group {
            position: relative;
        }

        .form-group .form-control {
            padding-right: 2.5rem;
            /* Space for the icon */
        }

        .form-group .bi {
            position: absolute;
            top: 50%;
            right: 10px;
            /* Adjust this based on the desired space between the icon and the input edge */
            transform: translateY(-50%);
            color: #010101;
            margin-left: 20px;
        }

        .form-group input{
            background: transparent;
            backdrop-filter: blur(10px);
            border-radius: 9px;
            border-width: 2px;
            border-color:   rgba(0, 0, 0, 0.086);
        }

        .login-link {
            margin-left: 10px;
            /* Space between text and button */
        }

        .btn-custom-lg {
            padding: 0.75rem 1.5rem;
            /* Increase padding for a larger button */
            font-size: 1.125rem;
            /* Increase font size */
            border-radius: 0.375rem;
            /* Optional: adjust border radius if needed */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-form">
            <h2 class="text-center mb-4">User Login</h2>
            <form id="loginForm" onsubmit="return validate()" method="post" action="logingeo.php">
                <div class="mb-3 form-group">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <input type="text" id="user" name="user" class="form-control"
                            placeholder="Enter your username">
                        <span class="bi bi-person"></span>
                    </div>
                </div>
                <div class="mb-3 form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" id="pass" name="pass" class="form-control"
                            placeholder="Enter your password">
                        <span class="bi bi-lock"></span>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-primary btn-custom-lg">Login</button>
                    <p>Don't have an account?<a href="signupgeo.php" class="login-link">Sign Up</a></p>
                </div>
            </form>
        </div>
    </div>
    <script>
        function validate() {
            user = document.getElementById("user").value;
            pass = document.getElementById("pass").value;

            if (user == "") {
                alert("Username must not be empty")
                return false
            }
            else if (pass == "") {
                alert("Password is empty")
                return false
            }
            else {
                // alert("Login Successful")
                return true
            }
        }
    </script>
</body>

</html>