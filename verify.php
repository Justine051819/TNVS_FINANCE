<?php
session_start();

// Hardcoded preset OTP
$set_verification_code = "123456"; // Replace with dynamic generation in the future

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Combine all OTP input boxes into a single code
    $entered_code = $_POST['otp1'] . $_POST['otp2'] . $_POST['otp3'] . $_POST['otp4'] . $_POST['otp5'] . $_POST['otp6'];

    // Verify the code
    if ($entered_code === $set_verification_code) {
        // Redirect to the dashboard if the code matches
        header("Location: TNVSFinance.php");
        exit();
    } else {
        // Redirect back to the verification page with an error
        echo "<script>
                alert('Incorrect verification code. Please try again.');
                window.location.href = 'verification.php';
              </script>";
        exit();
    }
}
?>
