<?php
$registrationError = "";

$username = $email = $givenname = $initial = $surname = $address = $age = $contact = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $servername = "127.0.0.1:3308";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "db";

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

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
<html>
<head>
    <title> Registration Form </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #C5D8FF, #6296FF);
            overflow-y: scroll;
            height: 100vh;
            overscroll-behavior: none;
        }

        .container {
            width: 300px;
            padding: 16px;
            background-color: white;
            margin: auto;
            margin-top: 50px;
            border: 1px solid black;
            border-radius: 8px;
        }

        input[type=text], input[type=password], input[type=number] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
            position: relative; /* Added for positioning the show/hide icon */
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            opacity: 0.8;
        }

        .error {
            color: red;
        }

        .show-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #3498db; /* Change this to your desired color */
        }

        .pass img{
            width: 30px;
            cursor: pointer;
        }

        .pass{
            align-items: center;
            display: flex;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- Add Font Awesome -->
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if (!empty($registrationError)): ?>
            <p class="error"><?php echo $registrationError; ?></p>
        <?php endif; ?>
        
        <form method="POST" action="register.php">
            <label for="username"><b>Username</b></label>
            <input type="text" placeholder="Enter username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>

            <label for="email"><b>Email</b></label>
            <input type="text" placeholder="Enter email address" name="email" value="<?php echo htmlspecialchars($email); ?>" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}">

            <label for="givenname"><b>Given name</b></label>
            <input type="text" placeholder="Enter Given name" name="givenname" value="<?php echo htmlspecialchars($givenname); ?>" required>

            <label for="initial"><b>Middle Initial</b></label>
            <input type="text" placeholder="Enter middle initial" name="initial" value="<?php echo htmlspecialchars($initial); ?>" required>

            <label for="surname"><b>Surname</b></label>
            <input type="text" placeholder="Enter surname" name="surname" value="<?php echo htmlspecialchars($surname); ?>" required>

            <label for="address"><b>Address</b></label>
            <input type="text" placeholder="Enter address" name="address" value="<?php echo htmlspecialchars($address); ?>" required>

            <label for="age"><b>Age</b></label>
            <input type="number" placeholder="Enter your age" name="age" value="<?php echo htmlspecialchars($age); ?>" required>

            <label for="contact"><b>Contact Number</b></label>
            <input type="text" placeholder="Enter contact number" name="contact" value="<?php echo htmlspecialchars($contact); ?>" required>

            <div class="pass">
            <label for="password"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="password" id="password" maxlength="10"required>
            <img src="close-eye2.jpg" id="eyeicon">
        </div>
        <span class="show-password" id="togglePassword1"><i class="fas fa-eye"></i></span>
        <button type="button" id="generatePassword" class="generate-btn">Generate Password</button>

        <div class="pass">
            <label for="cpassword"><b>Confirm Password</b></label>
            <input type="password" placeholder="Confirm Password" name="cpassword" id="cpassword" maxlength="10" required>
            <span class="show-password" id="togglePassword2"><i class="fas fa-eye"></i></span>
            <img src="close-eye2.jpg" id="cpass_eyeicon"> 
            </div>
            <button type="submit" name="register">Register</button>
            <a href="login.php">
                <button type="button" name="cancel">Cancel</button>
            </a>
        </form>
    </div>

    <script>

let eyeicon = document.getElementById("eyeicon");
let cpassEyeicon = document.getElementById("cpass_eyeicon");
let password = document.getElementById("password");
let cpassword = document.getElementById("cpassword");

// Toggle visibility for the password field
eyeicon.onclick = function () {
    if (password.type === "password") {
        password.type = "text";
        eyeicon.src = "open-eye2.jpg";
    } else {
        password.type = "password";
        eyeicon.src = "close-eye2.jpg";
    }
};

// Toggle visibility for the confirm password field
cpassEyeicon.onclick = function () {
    if (cpassword.type === "password") {
        cpassword.type = "text";
        cpassEyeicon.src = "open-eye2.jpg";
    } else {
        cpassword.type = "password";
        cpassEyeicon.src = "close-eye2.jpg";
    }
};


        const togglePassword1 = document.getElementById('togglePassword1');
        const passwordInput = document.getElementById('password');

        togglePassword1.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        const togglePassword2 = document.getElementById('togglePassword2');
        const confirmPasswordInput = document.getElementById('cpassword');

        togglePassword2.addEventListener('click', function () {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        document.getElementById('generatePassword').addEventListener('click', function () {
    // Generate a base random password
    let generatedPassword = Math.random().toString(36).slice(-9); // 9 characters
    // Add a random capital letter
    const randomCapitalLetter = String.fromCharCode(65 + Math.floor(Math.random() * 26)); // A-Z
    // Concatenate the capital letter and the generated password
    generatedPassword = randomCapitalLetter + generatedPassword;

    passwordInput.value = generatedPassword;
    confirmPasswordInput.value = generatedPassword;

    // Temporarily show the password
    passwordInput.setAttribute('type', 'text');
    confirmPasswordInput.setAttribute('type', 'text');

    // Hide it again after 3 seconds
    setTimeout(() => {
        passwordInput.setAttribute('type', 'password');
        confirmPasswordInput.setAttribute('type', 'password');
    }, 1); // Adjust the duration if needed
});

    </script>
</body>
</html>
