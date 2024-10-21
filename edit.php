<?php
$servername = "127.0.0.1:3308"; 
$usernameDB = "root"; 
$passwordDB = ""; 
$dbname = "db"; 

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

$id = "";
$employee_name = "";
$position = "";
$department = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["id"])) {
        header("location: /TNVS_FINANCE/view_employee.php");
        exit;
    }

    $id = $_GET["id"];

    $sql = "SELECT * FROM ve WHERE id=$id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: /TNVS_FINANCE/view_employee.php");
        exit;
    }

    $employee_name = $row["employee_name"];
    $position = $row["position"];
    $department = $row["department"];
} else {
    $id = $_POST["id"];
    $employee_name = $_POST["employee_name"];
    $position = $_POST["position"];
    $department = $_POST["department"];

    do {
        if (empty($id) || empty($employee_name) || empty($position) || empty($department)) {
            $errorMessage = "All the fields are required";
            break;
        }

        $sql = "UPDATE ve SET employee_name = '$employee_name', position = '$position', department = '$department' WHERE id = $id";

        $result = $conn->query($sql);

        if (!$result) {
            $errorMessage = "Invalid query: " . $conn->error;
            break;
        }

        $successMessage = "Account updated correctly";

        header("location: /TNVS_FINANCE/view_employee.php");
        exit;
    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account</title>
</head>
<body class="bg-blue-300 h-screen flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-center mb-6 font-bold text-lg">Edit Account</h2>

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
            <!-- Hidden input for ID -->
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <!-- Employee Name -->
            <div class="mb-3">
                <label class="block text-white font-bold mb-2 bg-blue-500 p-2 rounded" for="employee_name">Employee Name</label>
                <input type="text" id="employee_name" name="employee_name" value="<?php echo $employee_name ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>

            <!-- Position -->
            <div class="mb-3">
                <label class="block text-white font-bold mb-2 bg-blue-500 p-2 rounded" for="position">Position</label>
                <input type="text" id="position" name="position" value="<?php echo $position ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>

            <!-- Department -->
            <div class="mb-3">
                <label class="block text-white font-bold mb-2 bg-blue-500 p-2 rounded" for="department">Department</label>
                <input type="text" id="department" name="department" value="<?php echo $department ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="flex justify-end">
                <a href="/TNVS_FINANCE/view_employee.php" class="mr-2 px-4 py-2 bg-red-500 hover:bg-red-700 text-white rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-700 text-white rounded">Submit</button>
            </div>
        </form>
    </div>

    <!-- Close Alert Box -->
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
