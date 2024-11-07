<?php
$servername = "127.0.0.1:3308"; 
$usernameDB = "root"; 
$passwordDB = ""; 
$dbname = "db"; 

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

$account_name = "";
$requested_department = "";
$expense_categories = "";
$amount = "";
$description = "";
$document = "";
$payment_due = "";
$status = "";
$bank_name = "";
$bank_account_number = "";


$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $account_name = $_POST["account_name"];
    $requested_department = $_POST["requested_department"];
    $expense_categories = $_POST["expense_categories"];
    $amount = $_POST["amount"];
    $description = $_POST["description"];
    $document = $_POST["document"];
    $payment_due = $_POST["payment_due"];
    $bank_name = $_POST["bank_name"];
    $bank_account_number = $_POST["bank_account_number"];

    $amount = str_replace(',', '', $amount);

}

do {
    if (empty($account_name) || empty($requested_department) || empty($expense_categories) || empty($amount) || empty($description) || empty($document) || empty($bank_name) || empty($bank_account_number)) {
        $errorMessage = "All the fields are required";
        break;
    }

    $sql = "INSERT INTO br (account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number) " . 
           "VALUES ('$account_name', '$requested_department', '$expense_categories', '$amount', '$description', '$document', '$payment_due', '$bank_name', '$bank_account_number')";
    $result = $conn->query($sql);

    if (!$result) {
        $errorMessage = "Invalid query: " . $conn->error;
        break;
    }

    $account_name = "";
$requested_department = "";
$expense_categories = "";
$amount = "";
$description = "";
$document = "";
$payment_due = "";
$bank_account_number = "";
$bank_name = "";

    $successMessage = "Account added correctly";

    header("Location: /TNVS_FINANCE/budget_request.php");
    exit;

} while (false);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
</head>
<body class="bg-blue-300 h-screen flex items-center justify-center">
    <div class="bg-white p-10 rounded-lg shadow-lg w-full max-w-3xl">
        <h2 class="text-center mb-4 font-bold text-lg">New Account</h2>
        
        <!-- Display Error or Success Message -->
        <?php if (!empty($errorMessage)) : ?>
            <div class='alert bg-orange-500 text-white p-4 rounded mb-4'>
                <strong><?php echo $errorMessage; ?></strong>
                <button class='btn-close float-right'>&times;</button>
            </div>
        <?php elseif (!empty($successMessage)) : ?>
            <div class='alert bg-green-500 text-white p-4 rounded mb-4'>
                <strong><?php echo $successMessage; ?></strong>
                <button class='btn-close float-right'>&times;</button>
            </div>
        <?php endif; ?>

        <form method="post" class="bg-white rounded-lg shadow-lg w-full">
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="account_name">Account Name</label>
                    <input type="text" id="account_name" name="account_name" value="<?php echo $account_name ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="requested_department">Requested Department</label>
                    <input type="text" id="requested_department" name="requested_department" value="<?php echo $requested_department ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="expense_categories">Expense Categories</label>
                    <input type="text" id="expense_categories" name="expense_categories" value="<?php echo $expense_categories ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="amount">Amount</label>
                    <input type="text" id="amount" name="amount" value="<?php echo $amount ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="description">Description</label>
                    <input type="text" id="description" name="description" value="<?php echo $description ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="document">Document</label>
                    <input type="file" id="document" name="document" accept=".pdf, .doc, .docx, .jpg, .png" value="<?php echo $document ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="payment_due">Payment Due</label>
                    <input type="date" id="payment_due" name="payment_due" value="<?php echo $payment_due ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="bank_name">Bank Name</label>
                    <input type="text" id="bank_name" name="bank_name" value="<?php echo $bank_name ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>
                <div class="col-span-2 mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="bank_account_number">Bank Account Number</label>
                    <input type="text" id="bank_account_number" name="bank_account_number" value="<?php echo $bank_account_number ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>
            </div>

            <div class="flex justify-end mt-4">
                <a href="/TNVS_FINANCE/budget_request.php" class="mr-2 px-4 py-2 bg-red-500 hover:bg-red-700 text-white rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-700 text-white rounded">Submit</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-close').forEach(button => {
                button.addEventListener('click', function() {
                    const alertBox = button.closest('.alert');
                    if (alertBox) {
                        alertBox.style.display = 'none';
                    }
                });
            });
        });

        document.getElementById('amount').addEventListener('input', function (e) {
            let value = e.target.value;
            value = value.replace(/\D/g, "");  // Remove all non-digit characters
            value = parseInt(value).toLocaleString();  // Convert to number with commas
            e.target.value = value;
        });
    </script>
</body>
</html>
