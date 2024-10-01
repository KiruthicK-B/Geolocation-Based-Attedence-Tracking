<?php
if(isset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['crmpassword'])) {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $des      = $_POST['password'];
    $password = $_POST['crmpassword'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'dbgeo');
    if ($conn->connect_error) {
        die("Connection Failed: " . $conn->connect_error);
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT * FROM geoloc WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $username_exists = $result->num_rows > 0;
        $stmt->close();

        // Check if email exists
        $stmt = $conn->prepare("SELECT * FROM geoloc WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $email_exists = $result->num_rows > 0;
        $stmt->close();

        if ($username_exists) {
            echo "<script>alert('Username already exists!');</script>";
        } elseif ($email_exists) {
            echo "<script>alert('Email already registered!');</script>";
        } else {
            // Insert new record
            $stmt = $conn->prepare("INSERT INTO geoloc (username, email, password, crmpassword) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $des, $password);

            $execval = $stmt->execute();
            if ($execval === TRUE) {
                echo "<script>alert('Registration successful!');</script>";
            } else {
                echo "<script>alert('Error: " . $conn->error . "');</script>";
            }

            $stmt->close();
        }
    }
} else {
   
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
    <title>GeoLoc Attendance - Sign up</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            background: linear-gradient(135deg, #5BBA6F, #A3D3A9);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .signup-form {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .signup-form h2 {
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
            color: #4CAF50;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border-radius: 6px;
            border: 2px solid #ddd;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            border-color: #4CAF50;
            outline: none;
        }

        .form-group .bi {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            color: #aaa;
        }

        .btn-custom-lg {
            width: 100%;
            padding: 0.75rem;
            background-color: #4CAF50;
            color: #fff;
            font-size: 1.125rem;
            border: none;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .btn-custom-lg:hover {
            background-color: #45a049;
        }

        .login-link {
            display: inline-block;
            margin-top: 10px;
            color: #4CAF50;
            text-align: center;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        p a {
            text-decoration: none;
            color: #4CAF50;
        }

        .container {
            width: 100%;
            max-width: 960px;
            padding: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="signup-form">
            <h2>Sign Up</h2>
            <form id="signupForm" onsubmit="return validate()" method="post" action="signupgeo.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-group">
                        <input type="text" id="username" name="username" class="form-control"
                            placeholder="Enter your username">
                        <span class="bi bi-person"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <div class="input-group">
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email">
                        <span class="bi bi-envelope"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Enter your password">
                        <span class="bi bi-lock"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" id="confirmPassword" name="crmpassword" class="form-control"
                            placeholder="Confirm your password">
                        <span class="bi bi-lock"></span>
                    </div>
                </div>
                <p>If any discrepancies <a href="contact.html">contact us</a></p>
                <button type="submit" class="btn btn-custom-lg">Sign Up</button>
                <p class="login-link">Already registered? <a href="logingeo.php">Log in</a></p>
            </form>
        </div>
    </div>
    <script>
        function validate() {
            username = document.getElementById("username").value;
            email = document.getElementById("email").value;
            password = document.getElementById("password").value;
            confirmPassword = document.getElementById("confirmPassword").value;

            if (username == "" || username == " ") {
                alert("Username must not be empty");
                return false;
            }
            else if (email == "" || email == " ") {
                alert("E-mail must not be empty");
                return false;
            }
            else if (password == "") {
                alert("Password must not be empty");
                return false;
            }
            else if (password.length < 8) {
                alert("Password must contain at least 8 characters");
                return false;
            }
            else if (confirmPassword == "") {
                alert("Confirm Password is empty");
                return false;
            }
            else if (password != confirmPassword) {
                alert("Password Mismatch");
                return false;
            }
            else {
                return true;
            }
        }
    </script>
</body>

</html>
