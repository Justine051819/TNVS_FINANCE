<?php
$servername = "127.0.0.1:3308"; 
$usernameDB = "root"; 
$passwordDB = ""; 
$dbname = "db"; 

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

$employee_name = "";
$position = "";
$department = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_name = $_POST["employee_name"];
    $position = $_POST["position"];
    $department = $_POST["department"];
}

do {
    if (empty($employee_name) || empty($position) || empty($department)) {
        $errorMessage = "All the fields are required";
        break;
    }

    $sql = "INSERT INTO ve (employee_name, position, department) " . 
           "VALUES ('$employee_name', '$position', '$department')";
    $result = $conn->query($sql);

    if (!$result) {
        $errorMessage = "Invalid query: " . $conn->error;
        break;
    }

    $employee_name = "";
    $position = "";
    $department = "";

    $successMessage = "Account added correctly";

    header("Location: /TNVS_FINANCE/view_employee.php");
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
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-center mb-6 font-bold text-lg">New Account</h2>
        
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

        <form method="post" class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <div class="mb-3">
                <label class="block text-white font-bold mb-2 bg-blue-500 p-2 rounded" for="employee_name">Employee Name</label>
                <input type="text" id="employee_name" name="employee_name" value="<?php echo $employee_name ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div class="mb-3">
                <label class="block text-white font-bold mb-2 bg-blue-500 p-2 rounded" for="position">Position</label>
                <input type="text" id="position" name="position" value="<?php echo $position ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div class="mb-3">
                <label class="block text-white font-bold mb-2 bg-blue-500 p-2 rounded" for="department">Department</label>
                <input type="text" id="department" name="department" value="<?php echo $department ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>

            <div class="flex justify-end">
                <a href="/TNVS_FINANCE/view_employee.php" class="mr-2 px-4 py-2 bg-red-500 hover:bg-red-700 text-white rounded">Cancel</a>
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
    </script>
</body>
</html>
