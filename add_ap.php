<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database credentials
$servername   = "127.0.0.1:3308"; 
$usernameDB   = "root"; 
$passwordDB   = ""; 
$dbname       = "db"; 

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Auto-generate Reference ID
$currentYear  = date("Y");
// Generate a random number (e.g., between 1000 and 9999)
$randomNumber = rand(1000, 9999);
// Format: "BR-xxxx-current year"
$reference_id = "BR-" . $randomNumber . "-" . $currentYear;

// Initialize variables
$account_name         = "";
$requested_department = "";
$expense_categories   = "";
$amount               = "";
$description          = "";
$document             = "";  // We'll fill this after file upload
$payment_due          = "";
$bank_name            = "";
$bank_account_number  = "";
$mode_of_payment      = "";
$errorMessage         = "";
$successMessage       = "";
$time_period          = "";

// Handle POST submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Collect standard form fields
    $account_name         = $_POST["account_name"];
    $requested_department = $_POST["requested_department"];
    $expense_categories   = $_POST["expense_categories"];
    $amount               = str_replace(',', '', $_POST["amount"]);
    $description          = $_POST["description"];
    $time_period          = $_POST["time_period"];
    $payment_due          = $_POST["payment_due"];
    $bank_name            = $_POST["bank_name"];
    $bank_account_number  = $_POST["bank_account_number"];
    $mode_of_payment      = $_POST["mode_of_payment"];
    $reference_id         = $_POST["reference_id"];

    // =================================================================
    // 1) Handle File Upload FIRST, so we know $document for the INSERT.
    // =================================================================
    $document = "";  // default if no file is uploaded
    if (isset($_FILES['document']) && $_FILES['document']['error'] === 0) {
        // Basic info
        $fileName        = basename($_FILES['document']['name']); 
        $fileTmpName     = $_FILES['document']['tmp_name'];
        $fileDestination = "files/" . $fileName;
        $fileSize        = $_FILES['document']['size'];
        $fileError       = $_FILES['document']['error'];

        // Allowed file extensions
        $allowedFileTypes = ['pdf', 'doc', 'docx', 'jpg', 'png'];
        $fileExtension    = pathinfo($fileName, PATHINFO_EXTENSION);

        if ($fileSize > 0) {
            if (in_array($fileExtension, $allowedFileTypes)) {
                // Optional: check if the file already exists in the DB
                $checkQuery  = "SELECT * FROM filedownload WHERE filename = '$fileName'";
                $checkResult = $conn->query($checkQuery);

                if ($checkResult->num_rows === 0) {
                    // Insert filename into the filedownload table
                    $insertFile = "INSERT INTO filedownload(filename) VALUES ('$fileName')";
                    if ($conn->query($insertFile)) {
                        // Move uploaded file to destination folder
                        if (move_uploaded_file($fileTmpName, $fileDestination)) {
                            // Great—file is uploaded
                            // Set $document to the actual filename for br table
                            $document = $fileName;
                        } else {
                            $errorMessage = "Error moving uploaded file to /files folder.";
                        }
                    } else {
                        $errorMessage = "Error inserting filename into filedownload table: " . $conn->error;
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
        // If there's a file error or no file selected
        // $errorMessage = "File upload error or no file selected. Error code: " . $_FILES['document']['error'];
        // We won't forcibly stop the process if the user didn't upload a file
        // We'll just keep $document = ""
    }

    // =================================================================
    // 2) Validate the Required Fields (excluding the file, if not required).
    // =================================================================
    if (
        empty($account_name) ||
        empty($requested_department) ||
        empty($expense_categories) ||
        empty($amount) ||
        empty($time_period) ||
        empty($description) ||
        empty($payment_due) ||
        empty($mode_of_payment)
    ) {
        if (empty($errorMessage)) {
            // Only set if we don't already have a file-related error
            $errorMessage = "All fields (except 'Document') are required!";
        }
    }

    // =================================================================
    // 3) Insert into the `br` table if no error so far
    // =================================================================
    if (empty($errorMessage)) {
        // Build INSERT query
        $query = "INSERT INTO br (
                      account_name,
                      requested_department,
                      expense_categories,
                      amount,
                      description,
                      document,  -- store the actual filename
                      time_period,
                      payment_due,
                      bank_name,
                      bank_account_number,
                      mode_of_payment,
                      reference_id
                  ) VALUES (
                      '$account_name',
                      '$requested_department',
                      '$expense_categories',
                      '$amount',
                      '$description',
                      '$document',
                      '$time_period',
                      '$payment_due',
                      '$bank_name',
                      '$bank_account_number',
                      '$mode_of_payment',
                      '$reference_id'
                  )";

        if ($conn->query($query) === TRUE) {
            // On success, echo the snippet to close the parent modal
            echo "<script>
                  alert('Data inserted successfully!');
                  window.parent.closeAddRequestModal();
                  // If you want the parent page to refresh, uncomment:
                  // window.parent.location.reload();
                  </script>";
            exit; // Stop PHP execution here
        } else {
            $errorMessage = "Error inserting data into br: " . $conn->error;
        }
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
    <div class="bg-white p-10 pb-7 pt-7 rounded-lg shadow-2xl w-full max-w-3xl mt-8 mx-auto">
        <h1 class="text-left mb-6 font-bold text-2xl text-blue-900">BUDGET REQUEST</h1>

        <!-- Display Error or Success Message -->
        <?php if (!empty($errorMessage)): ?>
            <div class='alert bg-orange-500 text-white p-4 rounded mb-4'>
                <strong><?php echo $errorMessage; ?></strong>
                <button class='btn-close float-right'>&times;</button>
            </div>
        <?php elseif (!empty($successMessage)): ?>
            <div class='alert bg-green-500 text-white p-4 rounded mb-4'>
                <strong><?php echo $successMessage; ?></strong>
                <button class='btn-close float-right'>&times;</button>
            </div>
        <?php endif; ?>

        <!-- Form (Make sure to include enctype for file upload) -->
        <form method="post" class="bg-white rounded-lg w-full" enctype="multipart/form-data">
            <div class="grid grid-cols-2 gap-4">

                <!-- Reference ID -->
                <div class="mb-4">
                    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="reference_id">
                        Reference ID
                    </label>
                    <input type="text"
                           placeholder="Reference ID"
                           id="reference_id"
                           name="reference_id"
                           value="<?php echo isset($reference_id) ? $reference_id : ''; ?>"
                           class="w-full px-2 py-1 border border-gray-300 rounded-md bg-gray-100"
                           readonly>
                </div>

                <!-- Account Name -->
                <div class="mb-4">
                    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="account_name">
                        Account Name
                    </label>
                    <input type="text"
                           placeholder="Account Name"
                           id="account_name"
                           name="account_name"
                           value="<?php echo $account_name; ?>"
                           class="w-full px-2 py-1 border border-gray-300 rounded-md capitalize">
                </div>

                <!-- Requested Department -->
                <div class="mb-4">
                    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="requested_department">
                        Requested Department
                    </label>
                    <select id="requested_department"
                            name="requested_department"
                            class="w-full px-2 py-1 border border-gray-300 rounded-md">
                        <option value="None" <?php echo ($requested_department == 'None') ? 'selected' : ''; ?>>
                            Choose Option
                        </option>
                        <option value="Admin" <?php echo ($requested_department == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="Core-1" <?php echo ($requested_department == 'Core-1') ? 'selected' : ''; ?>>Core-1</option>
                        <option value="Core-2" <?php echo ($requested_department == 'Core-2') ? 'selected' : ''; ?>>Core-2</option>
                        <option value="Human Resource-1" <?php echo ($requested_department == 'Human Resource-1') ? 'selected' : ''; ?>>Human Resource-1</option>
                        <option value="Human Resource-2" <?php echo ($requested_department == 'Human Resource-2') ? 'selected' : ''; ?>>Human Resource-2</option>
                        <option value="Human Resource-3" <?php echo ($requested_department == 'Human Resource-3') ? 'selected' : ''; ?>>Human Resource-3</option>
                        <option value="Human Resource-4" <?php echo ($requested_department == 'Human Resource-4') ? 'selected' : ''; ?>>Human Resource-4</option>
                        <option value="Logistics-1" <?php echo ($requested_department == 'Logistics-1') ? 'selected' : ''; ?>>Logistic-1</option>
                        <option value="Logistics-2" <?php echo ($requested_department == 'Logistics-2') ? 'selected' : ''; ?>>Logistic-2</option>
                        <option value="Finance" <?php echo ($requested_department == 'Finance') ? 'selected' : ''; ?>>Finance</option>
                    </select>
                </div>

                <!-- Payment Due -->
                <div class="mb-4">
                    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="payment_due">
                        Payment Due
                    </label>
                    <input type="date"
                           id="payment_due"
                           name="payment_due"
                           value="<?php echo $payment_due; ?>"
                           class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>

                <!-- Expense Categories -->
                <div class="mb-4">
                    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="expense_categories">
                        Expense Categories
                    </label>
                    <select id="expense_categories"
                            name="expense_categories"
                            class="w-full px-2 py-1 border border-gray-300 rounded-md">
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
                </div>

                <!-- Amount -->
                <div class="mb-4">
                    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="amount">
                        Amount
                    </label>
                    <input type="text"
                           placeholder="Amount"
                           id="amount"
                           name="amount"
                           value="<?php echo $amount; ?>"
                           class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="description">
                        Description
                    </label>
                    <input type="text"
                           placeholder="Description"
                           id="description"
                           name="description"
                           value="<?php echo $description; ?>"
                           class="w-full px-2 py-1 border border-gray-300 rounded-md capitalize">
                </div>

                <!-- Document (file) -->
                <div class="mb-4">
                    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="document">
                        Document
                    </label>
                    <input type="file"
                           id="document"
                           name="document"
                           accept=".pdf, .doc, .docx, .jpg, .png"
                           class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>

                <!-- Mode of Payment -->
                <div class="mb-4">
                    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="mode_of_payment">
                        Mode of Payment
                    </label>
                    <select id="mode_of_payment"
                            name="mode_of_payment"
                            class="w-full px-2 py-1 border border-gray-300 rounded-md">
                        <option value="">Select Mode</option>
                        <option value="Bank Transfer" <?php echo ($mode_of_payment == 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                        <option value="Ecash" <?php echo ($mode_of_payment == 'Ecash') ? 'selected' : ''; ?>>Ecash</option>
                        <option value="Cash" <?php echo ($mode_of_payment == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                        <option value="Cheque" <?php echo ($mode_of_payment == 'Cheque') ? 'selected' : ''; ?>>Cheque</option>
                    </select>
                </div>

                <!-- Bank/Account Name -->
                <div class="mb-4">
                    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="bank_name">
                        Bank/Account Name
                    </label>
                    <input type="text"
                           placeholder="ex. BDO/Gcash (Optional)"
                           id="bank_name"
                           name="bank_name"
                           value="<?php echo $bank_name; ?>"
                           class="w-full px-2 py-1 border border-gray-300 rounded-md capitalize">
                </div>

                <!-- Bank Account Number -->
                <div class="col-span-2 mb-4">
                    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="bank_account_number">
                        Account Number
                    </label>
                    <input type="text"
                           placeholder="ex. 1234-5678-9101-2134 (Optional)"
                           id="bank_account_number"
                           name="bank_account_number"
                           value="<?php echo $bank_account_number; ?>"
                           class="w-full px-2 py-1 border border-gray-300 rounded-md">
                </div>
                <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.7/dist/inputmask.min.js"></script>
                <script>
                    // Apply inputmask to the bank account number field
                    var bankAccountInput = document.getElementById('bank_account_number');
                    var im = new Inputmask('99999999999999999', { placeholder: '' });
                    im.mask(bankAccountInput);
                </script>

                <!-- Time Period -->
                <div class="mb-4">
                    <label class="block text-white mb-1 bg-blue-800 p-1 rounded" for="time_period">
                        Time Period
                    </label>
                    <select id="time_period"
                            name="time_period"
                            class="w-full px-2 py-1 border border-gray-300 rounded-md">
                        <option value="">Select Mode</option>
                        <option value="Weekly" <?php echo ($time_period == 'Weekly') ? 'selected' : ''; ?>>Weekly</option>
                        <option value="Monthly" <?php echo ($time_period == 'Monthly') ? 'selected' : ''; ?>>Monthly</option>
                        <option value="Annually" <?php echo ($time_period == 'Annually') ? 'selected' : ''; ?>>Annually</option>
                        <option value="Yearly" <?php echo ($time_period == 'Yearly') ? 'selected' : ''; ?>>Yearly</option>
                    </select>
                </div>
            </div>

            <!-- Cancel + Submit Buttons -->
            <div class="flex justify-end mt-4">
                <!-- Cancel just closes the parent modal -->
                <button type="button"
                        onclick="window.parent.closeAddRequestModal()"
                        class="mr-2 px-4 py-2 bg-red-500 hover:bg-red-700 text-white rounded">
                    Cancel
                </button>

                <!-- Submit sends POST -->
                <button type="submit"
                        class="px-4 py-2 bg-green-500 hover:bg-green-700 text-white rounded">
                    Submit
                </button>
            </div>
        </form>
    </div>

    <!-- JS to handle alerts and format the Amount field -->
    <script>
        // Close alert banners
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

        // Format 'amount' as the user types (with commas)
        document.getElementById('amount').addEventListener('input', function (e) {
            let value = e.target.value;
            // Remove non-digits
            value = value.replace(/\D/g, "");
            // Convert to integer and format with commas
            if (value) {
                value = parseInt(value).toLocaleString();
            }
            e.target.value = value;
        });

        // If you had "Extra" categories that needed toggling, add that logic as needed
    </script>

    <!-- If user visits this page directly (outside iframe), 
         this function won't do anything unless you have an element 
         with id="addRequestModal" in the same page.
    -->
    <script>
    function closeAddRequestModal() {
      // Hides the modal in the parent page
      document.getElementById('addRequestModal').classList.add('hidden');
      // Clears the iframe source
      document.getElementById('addRequestIframe').src = '';
    }
    </script>
</body>
</html>
