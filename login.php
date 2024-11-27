<?php
session_start(); // Start session at the top
include 'session_manager.php'; // Include session manager

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "127.0.0.1:3308"; 
    $usernameDB = "root"; 
    $passwordDB = ""; 
    $dbname = "db"; 

    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM userss WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); 
        
        if (password_verify($password, $user['password'])) {
            // Check if the user is already logged in
            if (is_user_logged_in($username)) {
                echo '<script>alert("User is already logged in from another session!"); window.history.back();</script>';
            } else {
                // Set session variables upon successful login
                $_SESSION['users_username'] = $username; 
                $_SESSION['logged_in'] = true;
                
                // Mark this user as logged in
                log_user_in($username, $conn);
                

                header("Location: verification.php"); // Redirect to TNVSFinance.php
                exit();
            }
        } else {
            echo '<script>alert("Invalid username or password!"); window.history.back();</script>';
        }
    } else {
        echo '<script>alert("Invalid username or password!"); window.history.back();</script>';
    }

    echo "Username: $username<br>";
    echo "Password: $password<br>";

    if ($result->num_rows > 0) {
    echo "User found.<br>";
    } else {
    echo "No user found.<br>";
    }


    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);


    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FINANCE SYSTEM</title>
    <style>
        /* General reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body styling */
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f0f6ff; /* Lightened blue background */
            font-family: Arial, sans-serif;
            color: #333;
            position: relative;
            padding: 20px;
            flex-direction: column;
            text-align: center;
        }

        /* Container for logo and login form */
        .content-wrapper {
            display: flex;
            max-width: 1000px;
            width: 100%;
            gap: 50px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Header/logo styling */
        header {
            flex: 1;
            text-align: right;
        }

        header img {
            height: 144px; /* 80% of original size */
            transition: all 0.3s ease; /* Smooth resizing */
        }

        /* Login container styling */
        .login-container {
            background-color: #ffffff;
            padding: 40px;
            width: 400px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        /* Main heading */
        .login-header {
            font-size: 24px;
            color: #333;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: left; /* Left-aligned header */
        }

        /* Sign-up link */
        .signup-link {
            margin-bottom: 30px;
            font-size: 14px;
            color: #0056b3;
            text-align: left; /* Left-aligned link */
        }

        .signup-link a {
            color: #0056b3;
            text-decoration: none;
            font-weight: bold;
        }

        /* Form styling */
        .form-group {
            margin-bottom: 20px;
            position: relative;
            text-align: left;
        }

        label {
            font-size: 15px;
            color: #333;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #dddfe2;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f9f9f9;
            margin-top: 5px;
        }

        /* Sign-in button styling */
        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .button {
            padding: 12px 20px;
            font-size: 16px;
            background-color: #0056b3;
            color: #fff;
            font-weight: bold;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #004494;
        }

        /* Password visibility toggle icon styling */
        .password-container {
            position: relative;
            width: 100%;
        }

        .password-container img {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            cursor: pointer;
            opacity: 0.7;
        }

        /* Footer / Copyright styling */
        .bsit {
            position: absolute;
            bottom: 10px;
            font-size: 12px;
            color: #666;
            font-style: italic;
            width: 100%;
            text-align: center;
        }

        /* Media query for smaller screens (mobile view) */
        @media (max-width: 768px) {
            .content-wrapper {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 20px; /* Reduced gap between logo and form */
            }

            header {
                order: -1; /* Move logo above the form */
            }

            header img {
                height: 120px; /* Adjusted logo size for mobile */
                margin-bottom: 0; /* No margin between logo and form */
            }

            .login-container {
                width: 90%;
                max-width: 400px;
                padding: 30px;
            }

            .signup-link {
                text-align: center;
                margin-top: 15px;
            }

            .button {
                width: 100%;
            }

            /* Move CBSIT text further down and allow scrolling */
            .bsit {
                position: absolute;
                bottom: 30px; /* Increased space below the form */
                width: 100%;
                text-align: center;
            }
        }

    </style>
</head>
<body>
    <div class="content-wrapper">
        <header>
            <img src="logo.png" alt="Finance System Logo">
        </header>
        <div class="login-container">
            <div class="login-header">Login</div>
            <div class="signup-link">
                Don’t have an account? <a href="register.php">Sign up</a>
            </div>
            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter Username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="Enter Password" required>
                        <img src="close-eye2.jpg" id="eyeicon" alt="Toggle Password Visibility">
                    </div>
                </div>
                <div class="button-container">
                    <button type="submit" class="button">Sign In</button>
                </div>
            </form>
        </div>
    </div>
    <div class="bsit">&copy; BSIT</div>

    <script>
        const eyeicon = document.getElementById("eyeicon");
        const password = document.getElementById("password");

        eyeicon.onclick = function () {
            if (password.type === "password") {
                password.type = "text";
                eyeicon.src = "open-eye2.jpg";
            } else {
                password.type = "password";
                eyeicon.src = "close-eye2.jpg";
            }
        };
    </script>
</body>
</html>

