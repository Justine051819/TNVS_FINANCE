<?php
// Start the session
session_start();

// Database connection
$servername = '127.0.0.1:3308';
$usernameDB = 'root';
$passwordDB = '';
$dbname = 'db';

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize messages
$error = '';
$success = '';

// Check if ID is provided
if (!isset($_GET['id'])) {
    die("Invalid request");
}

// Fetch existing data
$id = $_GET['id'];
$sql = "SELECT * FROM br WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get updated values from the form
    $account_name = $_POST['account_name'];
    $requested_department = $_POST['requested_department'];
    $expense_categories = $_POST['expense_categories'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $payment_due = $_POST['payment_due'];

    // Validate all fields
    if (empty($account_name) || empty($requested_department) || empty($expense_categories) || empty($amount) || empty($description) || empty($payment_due)) {
        $error = "All fields are required.";
    } else {
        // Update query
        $update_sql = "UPDATE br SET account_name = ?, requested_department = ?, expense_categories = ?, amount = ?, description = ?, payment_due = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssssi", $account_name, $requested_department, $expense_categories, $amount, $description, $payment_due, $id);

        if ($update_stmt->execute()) {
            // Display success message and redirect to main page
            echo "<div class='bg-green-500 text-white p-4 rounded'>Record updated successfully!</div>";
            header("Location: budget_request.php"); // Change "your_main_page.php" to the actual file
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
        
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Request</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<body>
    <div class="container mx-auto p-6 bg-white shadow-md rounded">
        <h1 class="text-xl font-bold mb-4">Edit Budget Request</h1>
        
        <!-- Display success or error message -->
        <?php if ($error): ?>
            <div class="bg-red-500 text-white p-4 mb-4 rounded"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="bg-green-500 text-white p-4 mb-4 rounded"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="account_name">Account Name:</label>
                <input type="text" id="account_name" name="account_name" value="<?= htmlspecialchars($row['account_name']) ?>" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="requested_department">Requested Department:</label>
                <input type="text" id="requested_department" name="requested_department" value="<?= htmlspecialchars($row['requested_department']) ?>" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="expense_categories">Expense Categories:</label>
                <input type="text" id="expense_categories" name="expense_categories" value="<?= htmlspecialchars($row['expense_categories']) ?>" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="amount">Amount:</label>
                <input type="text" id="amount" name="amount" value="<?= number_format($row['amount'], 2) ?>" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="description">Description:</label>
                <textarea id="description" name="description" class="w-full px-3 py-2 border rounded"><?= htmlspecialchars($row['description']) ?></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="payment_due">Payment Due:</label>
                <input type="text" id="payment_due" name="payment_due" value="<?= htmlspecialchars($row['payment_due']) ?>" class="w-full px-3 py-2 border rounded">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
            <a href="budget_request.php" class="bg-gray-300 text-black px-4 py-2 rounded">Cancel</a>
        </form>
    </div>
</body>
</html>
