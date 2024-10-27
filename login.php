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
                

                header("Location: TNVSFinance.php"); // Redirect to TNVSFinance.php
                exit();
            }
        } else {
            echo '<script>alert("Invalid username or password!"); window.history.back();</script>';
        }
    } else {
        echo '<script>alert("Invalid username or password!"); window.history.back();</script>';
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>FINANCE SYSTEM</title>
    <style>
        img{
            height: 150px;
            width: 420px;
        }

        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 75vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #C5D8FF, #6296FF);
            background-attachment: fixed;
            overflow: hidden;
        }
        header {
            text-align: center;
            color: black;
        }
        h1 {
            color: solid black;
            font-family: 'montserrat', sans-serif;
            position: relative;
            top: 120px;
            font-size: 12px;
            right: 28px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: center;
            position: relative; /* Added for positioning the icon */
        }
        label {
            display: block;
            margin-bottom: 5px; 
            font-weight: bold; 
        }
        input[type="text"], input[type="password"] {
            width: 100%; 
            padding: 12px 20px;
            border: 1px solid lightblue;
            box-sizing: border-box;
            border-radius: 20px;
            margin: 8px 0;
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #3498db;
            color: white;
            text-align: center;
            border-radius: 10px;
            text-decoration: none; /* Removes the underline */
            cursor: pointer;
            border: 1px solid black;
        }
        .button:hover {
            background-color: #2980b9;
        }
    
        .bsit {
            position: relative;
            top: 80px;
        }
        .show-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #black; /* Change this to your desired color */
        }

        .register .button{
            border-radius: 10px;
            position: relative;
            left: 70px;
            bottom: 40px;
            border-color: solid black;
            text-decoration: none;
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <header>
        <h1>FINANCE</h1>
        <img src="logo.png" alt="taxi">
    </header>
    <form action="login.php" method="post">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" placeholder="Enter Username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" placeholder="Enter Password" name="password" required>
            <span class="show-password" id="togglePassword"><i class="fas fa-eye"></i></span>
        </div>
        <button type="submit" class="button">Login</button>
    </form>
    <div class="register">
        <a href="register.php" class="button">Create Account</a>
    </div>
    <div class="bsit">
        <label>&copy; BSIT</label>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
