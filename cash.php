<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
?>

<html>
 <head>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
</head>
  <body class="bg-white">
    
    <?php include('navbar_sidebar.php'); ?>

    
   
    <!-- Breadcrumb -->
    <div class="bg-blue-200 p-4 shadow-lg">
     <nav class="text-gray-600 font-bold">
      <ol class="list-reset flex">
       <li>
        <a class="text-gray-600 font-bold" href="TNVSFinance.php">Dashboard</a>
       </li>
       <li>
        <span class="mx-2">&gt;</span>
       </li>
       <li>
        <a class="text-gray-600 font-bold" href="#">Disbursement</a>
       </li>
       <li>
        <span class="mx-2">&gt;</span>
       </li>
       <li>
        <a class="text-gray-600 font-bold" href="#">Cash Payout</a>
       </li>
      </ol>
     </nav>
    </div>

    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6 h-full w-full">

    <h1 class="font-bold text-2xl text-blue-900 mb-8">CASH PAYOUT</h1> 

    <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
    <thead>
        <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Reference ID</th>
                    <th class="px-4 py-2">Account Name</th>
                    <th class="px-4 py-2">Department</th>
                    <th class="px-4 py-2">Mode of Payment</th>
                    <th class="px-4 py-2">Expense Category</th>
                    <th class="px-4 py-2">Amount</th> 
                    <th>Payment Due</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-900 text-sm font-semilight">
            <?php
$servername = '127.0.0.1:3308';
$usernameDB = 'root';
$passwordDB = '';
$dbname = 'db';

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle approval action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Approve logic
    if (isset($_POST['approve_id'])) {
        $approveId = $_POST['approve_id'];

        // Insert into the approved disbursements table with 'DR-' prefix
        $insert_sql = "INSERT INTO dr (id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, mode_of_payment, reference_id)
                       SELECT id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, mode_of_payment, 
                              CONCAT('DR-', SUBSTRING(reference_id, 4)) AS reference_id
                       FROM cash WHERE id = ?";

        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("i", $approveId);

        if ($stmt_insert->execute()) {
            // After successful insertion into the dr table, update the status to 'disbursed' in cash table
            $update_status_sql = "UPDATE tr SET status = 'disbursed' WHERE id = ?";
            $stmt_update_status = $conn->prepare($update_status_sql);
            $stmt_update_status->bind_param("i", $approveId);

            if ($stmt_update_status->execute()) {
                // After updating the status, delete the row from cash table
                $delete_sql = "DELETE FROM cash WHERE id = ?";
                $stmt_delete = $conn->prepare($delete_sql);
                $stmt_delete->bind_param("i", $approveId);

                if ($stmt_delete->execute()) {
                    echo "<div class='bg-green-500 text-white p-4 rounded'>Disbursement Approved and Status Updated to Disbursed!</div>";
                } else {
                    echo "Error deleting record from cash table: " . $conn->error;
                }
            } else {
                echo "Error updating status to 'disbursed': " . $conn->error;
            }
        } else {
            echo "Error inserting into dr table: " . $conn->error;
        }
    }
}

// Fetch records from cash table
$sql = "SELECT * FROM cash";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr class='border-b border-gray-300 hover:bg-gray-200'>";
        echo "<td class='px-6 text-left border-r border-gray-300'>{$row['id']}</td>";
        echo "<td class='px-6 text-left border-r border-gray-300'>{$row['reference_id']}</td>";
        echo "<td class='px-6 text-left border-r border-gray-300'>{$row['account_name']}</td>";
        echo "<td class='px-6 text-left border-r border-gray-300'>{$row['requested_department']}</td>";
        echo "<td class='px-6 text-left border-r border-gray-300'>{$row['mode_of_payment']}</td>";
        echo "<td class='px-6 text-left border-r border-gray-300'>{$row['expense_categories']}</td>";
        echo "<td class='px-6 text-left border-r border-gray-300'>₱" . number_format($row['amount'], 2) . "</td>";
        echo "<td class='px-6 text-left border-r border-gray-300'>{$row['payment_due']}</td>";

        echo "<td class='pt-2 px-6 text-left border-r border-gray-300'>
                <form method='POST' action=''>
                    <input type='hidden' name='approve_id' value='{$row['id']}'>
                    <button type='submit' class='bg-green-500 text-white px-2 py-1'>Disburse</button>
                </form>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center py-3'>No records found</td></tr>";
}

$conn->close();
?>





                


            </tbody>
        </table>


</div>




 </body>
</html>