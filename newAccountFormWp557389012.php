<?php
$registrationError = "";

$username = $email = $givenname = $initial = $surname = $address = $age = $contact = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $servername = "127.0.0.1:3308";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "db";

    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_POST["username"];
    $email = $_POST["email"];
    $givenname = $_POST["givenname"];
    $initial = $_POST["initial"];
    $surname = $_POST["surname"];
    $address = $_POST["address"];
    $age = $_POST["age"];
    $contact = $_POST["contact"];
    $password = $_POST["password"];
    $cpassword = isset($_POST["cpassword"]) ? $_POST["cpassword"] : "";

    if ($password !== $cpassword) {
        $registrationError = "Passwords don't match!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO userss (username, email, gname, minitial, surname, address, age, contact, password) 
                VALUES ('$username', '$email', '$givenname', '$initial', '$surname', '$address', '$age', '$contact', '$hashedPassword')";

        if ($conn->query($sql) === TRUE) {
            echo '<script>alert("Registration successful!"); window.location.href = "login.php";</script>';
            exit();
        } else {
            $registrationError = "Error: " . $conn->error;
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f0f6ff;
            font-family: Arial, sans-serif;
            color: #333;
            position: relative;
            padding: 20px;
            flex-direction: column;
            text-align: center;
        }

        .content-wrapper {
            display: flex;
            max-width: 1000px;
            width: 100%;
            gap: 50px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        header {
            flex: 1;
            text-align: right;
        }

        header img {
            height: 144px;
            transition: all 0.3s ease;
        }

        .register-container {
            background-color: #ffffff;
            padding: 0 30px 0px 30px;
            width: 400px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow-y: auto;
            height: 550px;
            position: relative;
        }

        .register-header {
            font-size: 24px;
            color: #333;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: left;
            position: sticky;
            top: 0;
            background-color: white;
            padding: 30px 30px 15px 0px;
            z-index: 10;
        }

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
        input[type="password"],
        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #dddfe2;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f9f9f9;
            margin-top: 5px;
        }
        button {
            padding: 12px 20px;
            font-size: 16px;
            background-color: #0056b3; /* Default button color */
            color: #fff;
            font-weight: bold;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
            
        }
        button:hover {
            background-color: #004494;
        }

        .button-container {
            display: flex;
            gap: 10px;
            position: sticky;
            bottom: 0;
            background-color: white;
            padding: 20px 0;
            margin-top: 20px;
        }

        .button-container button {
            flex: 1;
            padding: 12px;
            font-size: 16px;
            color: #fff;
            font-weight: bold;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }

        .register-btn {
            background-color: #0056b3 ;
        }

        .register-btn:hover {
            background-color:  #004494;
        }

        .cancel-btn {
            background-color: #dc3545;
        }

        .cancel-btn:hover {
            background-color: #c82333;
        }

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

        .bsit {
            position: absolute;
            bottom: 10px;
            font-size: 12px;
            color: #666;
            font-style: italic;
            width: 100%;
            text-align: center;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 20px;
            }

            header {
                order: -1;
            }

            header img {
                height: 120px;
                margin-bottom: 0;
            }

            .register-container {
                width: 90%;
                max-width: 400px;
                padding: 0 30px 0px 30px;
            }

            .register-header {
                padding: 30px 30px 15px 0px;
            }

            .button-container button {
                width: 100%;
            }
            .generatePassword{
                width: 100%;
            }

            .bsit {
                position: absolute;
                margin-top: 10px;
                bottom: 30px;
            }
        }

    </style>
</head>
<body>
    <div class="content-wrapper">
        <header>
            <img src="logo.png" alt="Finance System Logo">
        </header>
        <div class="register-container">
            <div class="register-header">Register</div>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" placeholder="Enter email address" required>
                </div>
                <div class="form-group">
                    <label for="givenname">Given Name</label>
                    <input type="text" id="givenname" name="givenname" placeholder="Enter given name" required>
                </div>
                <div class="form-group">
                    <label for="surname">Surname</label>
                    <input type="text" id="surname" name="surname" placeholder="Enter surname" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder="Enter address" required>
                </div>
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" placeholder="Enter your age" required>
                </div>
                <div class="form-group">
                    <label for="contact">Contact Number</label>
                    <input type="text" id="contact" name="contact" placeholder="Enter contact number" required>
                </div>
                <div class="form-group">
                    <button type="button" id="generatePassword">Generate Password</button>
                </div>
                <div class="password-container">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter Password" required>
                    <img src="close-eye2.jpg" id="eyeicon">
                </div>
                <div class="password-container">
                    <label for="cpassword">Confirm Password</label>
                    <input type="password" id="cpassword" name="cpassword" placeholder="Confirm Password" required>
                    <img src="close-eye2.jpg" id="cpass_eyeicon">
                </div>
                <!-- Register and Cancel buttons on the same line -->
                <div class="button-container">
                    <button type="submit" name="register" class="register-btn">Register</button>
                    <a href="login.php" style="flex: 1;">
                        <button type="button" class="cancel-btn">Cancel</button>
                    </a>
                </div>
            </form>
        </div>
    </div>
    <div class="bsit">&copy; BSIT</div>

    <script>
        let eyeicon = document.getElementById("eyeicon");
        let cpassEyeicon = document.getElementById("cpass_eyeicon");
        let password = document.getElementById("password");
        let cpassword = document.getElementById("cpassword");

        eyeicon.onclick = function () {
            if (password.type === "password") {
                password.type = "text";
                eyeicon.src = "open-eye2.jpg";
            } else {
                password.type = "password";
                eyeicon.src = "close-eye2.jpg";
            }
        };

        cpassEyeicon.onclick = function () {
            if (cpassword.type === "password") {
                cpassword.type = "text";
                cpassEyeicon.src = "open-eye2.jpg";
            } else {
                cpassword.type = "password";
                cpassEyeicon.src = "close-eye2.jpg";
            }
        };

        document.getElementById('generatePassword').addEventListener('click', function () {
            let generatedPassword = Math.random().toString(36).slice(-9);
            const randomCapitalLetter = String.fromCharCode(65 + Math.floor(Math.random() * 26));
            generatedPassword = randomCapitalLetter + generatedPassword;

            password.value = generatedPassword;
            cpassword.value = generatedPassword;

            password.setAttribute('type', 'text');
            cpassword.setAttribute('type', 'text');

            setTimeout(() => {
                password.setAttribute('type', 'password');
                cpassword.setAttribute('type', 'password');
            }, 1);
        });
    </script>
</body>
</html>

