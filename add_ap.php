<?php
$servername = "127.0.0.1:3308"; 
$usernameDB = "root"; 
$passwordDB = ""; 
$dbname = "db"; 

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Auto-generate Reference ID
$currentYear = date("Y");

// Generate a random number (e.g., between 1000 and 9999)
$randomNumber = rand(1000, 9999);

// Generate the reference ID with the format "BR-xxxx-current year"
$reference_id = "BR-" . $randomNumber . "-" . $currentYear;


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
    $reference_id = $_POST["reference_id"];  

    if (empty($account_name) || empty($requested_department) || empty($expense_categories) || empty($amount) || 
    empty($description) || empty($document) || empty($payment_due) || empty($mode_of_payment)) {
    $errorMessage = "All fields are required!";
} else {

    // File upload logic...

    // Insert other data into database
    $query = "INSERT INTO br (account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number,  mode_of_payment, reference_id)
              VALUES ('$account_name', '$requested_department', '$expense_categories', '$amount', '$description', '$document', '$payment_due', '$bank_name', '$bank_account_number', '$mode_of_payment', '$reference_id')";

if ($conn->query($query) === TRUE) {
    $successMessage = "Data inserted successfully!";
    // Redirect to the budget_request page after successful insertion
    header("Location: budget_request.php");
    exit(); // Ensure the script stops executing after redirection
} else {
    $errorMessage = "Error inserting data: " . $conn->error;
}
}

    if (isset($_FILES['document'])) {
        if ($_FILES['document']['error'] === 0) {
            echo "File uploaded successfully.";
        } else {
            echo "Error uploading file: " . $_FILES['document']['error'];
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
    <title>Add Employee</title>
</head>
<body class="bg-blue-300 h-screen flex items-center justify-center">
    <div class="bg-white p-10 rounded-lg shadow-lg w-full max-w-3xl">
        <h2 class="text-center mb-4 font-bold text-lg">Add Request</h2>
        
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
    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="reference_id">Reference ID</label>
    <input type="text" placeholder="Reference ID" id="reference_id" name="reference_id" value="<?php echo isset($reference_id) ? $reference_id : ''; ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md bg-gray-100" readonly>
</div>


                <div class="mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="account_name">Account Name</label>
                    <input type="text" placeholder="Acccount Name" id="account_name" name="account_name" value="<?php echo $account_name ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md capitalize">
                </div>
                <div class="mb-4">
                <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="requested_department">Requested Department</label>
    <select id="requested_department" name="requested_department" class="w-full px-2 py-1 border border-gray-300 rounded-md">
        <option value="None" <?php echo ($requested_department == 'department1') ? 'selected' : ''; ?>>Choose Option</option>
        <option value="Admin" <?php echo ($requested_department == 'department2') ? 'selected' : ''; ?>>Admin</option>
        <option value="Core" <?php echo ($requested_department == 'department3') ? 'selected' : ''; ?>>Core</option>
        <option value="Finance" <?php echo ($requested_department == 'department4') ? 'selected' : ''; ?>>Finance</option>
        <option value="Human Resource" <?php echo ($requested_department == 'department5') ? 'selected' : ''; ?>>Human Resource</option>
        <option value="Logistic" <?php echo ($requested_department == 'department6') ? 'selected' : ''; ?>>Logistic</option>
    </select>
</div>

<div class="mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="payment_due">Payment Due</label>
                    <input type="date" id="payment_due" name="payment_due" value="<?php echo $payment_due ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>


                <div class="mb-4">
    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="expense_categories">Expense Categories</label>
    <select id="expense_categories" name="expense_categories" class="w-full px-2 py-1 border border-gray-300 rounded-md" onchange="toggleExtraInput()">
        <option value="None" <?php echo ($expense_categories == 'None') ? 'selected' : ''; ?>>Choose Option</option>
        <option value="Equipment/Assets" <?php echo ($expense_categories == 'Equipment/Assets') ? 'selected' : ''; ?>>Equipments/Assets</option>
        <option value="Maintenance/Repair" <?php echo ($expense_categories == 'Maintenance/Repair') ? 'selected' : ''; ?>>Maintenance/Repair</option>
        <option value="Salaries" <?php echo ($expense_categories == 'Salaries') ? 'selected' : ''; ?>>Salaries</option>
        <option value="Bonuses" <?php echo ($expense_categories == 'Bonuses') ? 'selected' : ''; ?>>Bonuses</option>
        <option value="Facility Cost" <?php echo ($expense_categories == 'Facility Cost') ? 'selected' : ''; ?>>Facility Cost</option>
        <option value="Training Cost" <?php echo ($expense_categories == 'Training Cost') ? 'selected' : ''; ?>>Training Cost</option>
        <option value="Wellness Program Cost" <?php echo ($expense_categories == 'Wellness Program Cost') ? 'selected' : ''; ?>>Wellness Program Cost</option>
        <option value="Tax Payment" <?php echo ($expense_categories == 'Tax Payment') ? 'selected' : ''; ?>>Tax Payment</option>
        <option value="Extra" <?php echo ($expense_categories == 'Extra') ? 'selected' : ''; ?>>Extra</option>
    </select>
    
    <!-- Extra Input field -->
    <div id="extra_input_container" class="mt-2" style="display: none;">
        <label class="block text-white font-bold mb-1" for="extra_category">Please specify:</label>
        <input type="text" id="extra_category" name="extra_category" class="w-full px-2 py-1 border border-gray-300 rounded-md">
    </div>
</div>
                <div class="mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="amount">Amount</label>
                    <input type="text" placeholder="Amount" id="amount" name="amount" value="<?php echo $amount ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="description">Description</label>
                    <input type="text" placeholder="Description" id="description" name="description" value="<?php echo $description ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md capitalize">
                </div>
                <div class="mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="document">Document</label>
                    <input type="file" id="document" name="document" accept=".pdf, .doc, .docx, .jpg, .png" action="add_ap.php" method="POST" enctype="multipart/form-data" value="<?php echo $document ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>

                <div class="mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="mode_of_payment">Mode of Payment</label>
                    <select id="mode_of_payment" name="mode_of_payment" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                        <option value="">Select Mode</option>
                        <option value="Bank Transfer" <?php echo ($mode_of_payment == 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                        <option value="Ecash" <?php echo ($mode_of_payment == 'Ecash') ? 'selected' : ''; ?>>Ecash</option>
                        <option value="Cash" <?php echo ($mode_of_payment == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                        <option value="Cheque" <?php echo ($mode_of_payment == 'Cheque') ? 'selected' : ''; ?>>Cheque</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded text-transform:capitalize" for="bank_name">Bank/Account Name</label>
                    <input type="text" placeholder="ex. BDO/Gcash (Optional)" id="bank_name" name="bank_name" value="<?php echo $bank_name ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md capitalize">
                </div>
                <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.7/dist/inputmask.min.js"></script>

<div class="col-span-2 mb-4">
    <label class="block text-white font-bold mb-1 bg-blue-500 p-1 rounded" for="bank_account_number">Account Number</label>
    <input type="text" placeholder="ex. 1234-5678-9101-2134 (Optional)" id="bank_account_number" name="bank_account_number" value="<?php echo $bank_account_number ?>" class="w-full px-2 py-1 border border-gray-300 rounded-md">
</div>

<script>
    var bankAccountInput = document.getElementById('bank_account_number');
    var im = new Inputmask('9999-9999-9999-9999-9', {
        placeholder: ''  // Removes the underscore placeholders
    });
    im.mask(bankAccountInput);
</script>




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