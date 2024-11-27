<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

$servername = "127.0.0.1:3308"; 
$usernameDB = "root"; 
$passwordDB = ""; 
$dbname = "db"; 

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Auto-generate Reference ID
$currentYear = date("Y");
$randomNumber = rand(1000, 9999);
$reference_id = "TR-" . $randomNumber . "-" . $currentYear;

$account_name = "";
$requested_department = "";
$expense_categories = "";
$amount = "";
$description = "";
$document = "";
$payment_due = "";
$bank_name = "";
$bank_account_number = "";
$mode_of_payment = "";
$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $account_name = $_POST["account_name"];
    $requested_department = $_POST["requested_department"];
    $expense_categories = $_POST["expense_categories"];
    $amount = str_replace(',', '', $_POST["amount"]);
    $description = $_POST["description"];
    $document = $_POST["document"];
    $payment_due = $_POST["payment_due"];
    $bank_name = $_POST["bank_name"];
    $bank_account_number = $_POST["bank_account_number"];
    $mode_of_payment = $_POST["mode_of_payment"];  

    if (empty($account_name) || empty($requested_department) || empty($expense_categories) || empty($amount) || 
        empty($description) || empty($payment_due) || empty($mode_of_payment)) {
        $errorMessage = "All fields are required!";
    } else {
        // Begin transaction
        $conn->begin_transaction();
        try {
            // Insert data into `tr` table
            $query_tr = $conn->prepare("INSERT INTO tr (account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, mode_of_payment, reference_id, status) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $status = 'pending'; // Default status
            $query_tr->bind_param("ssssssssssss", $account_name, $requested_department, $expense_categories, $amount, $description, $document, $payment_due, $bank_name, $bank_account_number, $mode_of_payment, $reference_id, $status);
            $query_tr->execute();

            // Get the ID of the inserted row from the `tr` table
            $tr_id = $conn->insert_id;

            // Insert into `br` table using data from `tr`
            $insert_br_sql = "INSERT INTO br (reference_id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, mode_of_payment) 
                              SELECT reference_id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, mode_of_payment 
                              FROM tr WHERE id = ?";
            $stmt = $conn->prepare($insert_br_sql);
            $stmt->bind_param("i", $tr_id);
            $stmt->execute();

            // Update the status in the `tr` table after inserting into `br`
            $update_tr_sql = "UPDATE tr SET status = 'in review' WHERE id = ?";
            $stmt = $conn->prepare($update_tr_sql);
            $stmt->bind_param("i", $tr_id);
            $stmt->execute();

            // Commit transaction
            $conn->commit();

            $successMessage = "Data inserted successfully into both tables!";
            // Redirect to a different page after successful insertion
            header("Location: paid_tax.php");
            exit(); // Ensure the script stops executing after redirection
        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            $conn->rollback();
            $errorMessage = "Transaction failed: " . $e->getMessage();
        }
    }




    

    // Check if a file was uploaded
    if (isset($_FILES['document']) && $_FILES['document']['error'] === 0) {
        // File upload handling code here
        $fileName = basename($_FILES['document']['name']); // Prevent directory traversal
        $fileTmpName = $_FILES['document']['tmp_name'];
        $fileDestination = "files/" . $fileName;

        // Check if file size is greater than 0
        if ($_FILES['document']['size'] > 0) {
            $allowedFileTypes = ['pdf', 'doc', 'docx', 'jpg', 'png'];
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

            // Check if file extension is allowed
            if (in_array($fileExtension, $allowedFileTypes)) {
                // Check if file already exists in the database
                $checkQuery = "SELECT * FROM filedownload WHERE filename = '$fileName'";
                $checkResult = $conn->query($checkQuery);

                if ($checkResult->num_rows === 0) {
                    // Insert filename into the database
                    $query = "INSERT INTO filedownload(filename) VALUES ('$fileName')";
                    if ($conn->query($query)) {
                        // Move uploaded file to destination folder
                        if (move_uploaded_file($fileTmpName, $fileDestination)) {
                            // File uploaded successfully
                        } else {
                            $errorMessage = "Error moving uploaded file.";
                        }
                    } else {
                        $errorMessage = "Error inserting filename into database: " . $conn->error;
                    }
                } else {
                    $errorMessage = "File already exists in the database.";
                }
            } else {
                $errorMessage = "Invalid file type. Allowed types: " . implode(', ', $allowedFileTypes);
            }
        } else {
            $errorMessage = "Uploaded file is empty.";
        }
    } elseif (isset($_FILES['document']) && $_FILES['document']['error'] !== 0) {
        // Handle case when file upload fails or no file was selected
        $errorMessage = "File upload error or no file selected. Error code: " . $_FILES['document']['error'];
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Budget</title>
</head>
<body class="bg-blue-200">
    
    <?php include('navbar_sidebar.php'); ?>


    <div class="bg-white p-10 pb-7 pt-7 rounded-lg shadow-2xl w-full max-w-3xl mt-8 mx-auto">
        <h1 class="text-left mb-6 font-bold text-2xl text-blue-900">TAX RECORDS</h1>
        
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

        <form method="post" class="bg-white rounded-lg w-full">
            <div class="grid grid-cols-2 gap-4">

            <div class="mb-4">
    <label class="block text-white  mb-1 bg-blue-800 p-1 rounded" for="reference_id">Reference ID</label>
    <input type="text" placeholder="Reference ID" id="reference_id" name="reference_id" value="<?php echo isset($reference_id) ? $reference_id : ''; ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md bg-gray-100" readonly>
</div>


                <div class="mb-4">
                    <label class="block text-white  mb-1 bg-blue-800 p-1 rounded" for="account_name">Account Name</label>
                    <input type="text" placeholder="Account Name" id="account_name" name="account_name" value="<?php echo $account_name ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md  capitalize">
                </div>
                <div class="mb-4">
    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="requested_department">Requested Department</label>
    <input type="text" id="requested_department" name="requested_department" value="Finance" class="w-full px-2 py-1 border border-gray-300 rounded-md" readonly>
</div>



<div class="mb-4">
                    <label class="block text-white pl-2  mb-1 bg-blue-800 p-1 rounded" for="payment_due">Payment Due</label>
                    <input type="date" id="payment_due" name="payment_due" value="<?php echo $payment_due ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>


                <div class="mb-4">
    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="expense_categories">Expense Categories</label>
    <input type="text" id="expense_categories" name="expense_categories" value="Tax Payment" class="w-full px-2 py-1 border border-gray-300 rounded-md" readonly>
</div>

                <div class="mb-4">
                    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="amount">Amount</label>
                    <input type="text" placeholder="Amount" id="amount" name="amount" value="<?php echo $amount ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="description">Description</label>
                    <input type="text" placeholder="Description" id="description" name="description" value="<?php echo $description ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md capitalize">
                </div>
                <div class="mb-4">
                    <label class="block text-white  mb-1 bg-blue-800 p-1 rounded" for="document">Document</label>
                    <input type="file" id="document" name="document" accept=".pdf, .doc, .docx, .jpg, .png" action="add_ap.php" method="POST" enctype="multipart/form-data" value="<?php echo $document ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>

                <div class="mb-4">
                    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="mode_of_payment">Mode of Payment</label>
                    <select id="mode_of_payment" name="mode_of_payment" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                        <option value="">Select Mode</option>
                        <option value="Bank Transfer" <?php echo ($mode_of_payment == 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                        <option value="Ecash" <?php echo ($mode_of_payment == 'Ecash') ? 'selected' : ''; ?>>Ecash</option>
                        <option value="Cash" <?php echo ($mode_of_payment == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                        <option value="Cheque" <?php echo ($mode_of_payment == 'Cheque') ? 'selected' : ''; ?>>Cheque</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-white  mb-1 bg-blue-800 p-1 rounded text-transform:capitalize" for="bank_name">Bank/Account Name</label>
                    <input type="text" placeholder="ex. BDO/Gcash (Optional)" id="bank_name" name="bank_name" value="<?php echo $bank_name ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md capitalize">
                </div>
                <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.7/dist/inputmask.min.js"></script>

<div class="col-span-2 mb-4">
    <label class="block text-white  mb-1 bg-blue-800 p-1 rounded" for="bank_account_number">Account Number</label>
    <input type="text" placeholder="ex. 1234-5678-9101-2134 (Optional)" id="bank_account_number" name="bank_account_number" value="<?php echo $bank_account_number ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
</div>

<script>
    var bankAccountInput = document.getElementById('bank_account_number');
    var im = new Inputmask('99999999999999999', {
        placeholder: ''  // Removes the underscore placeholders
    });
    im.mask(bankAccountInput);
</script>




            </div>

            <div class="flex justify-end mt-4">
                <a href="/TNVS_FINANCE/paid_tax.php" class="mr-2 px-4 py-2 bg-red-500 hover:bg-red-700 text-white rounded">Cancel</a>
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