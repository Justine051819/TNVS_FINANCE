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
        <a class="text-gray-600 font-bold" href="#">Tax Management</a>
       </li>
       <li>
        <span class="mx-2">&gt;</span>
       </li>
       <li>
        <a class="text-gray-600 font-bold" href="#">Tax Records</a>
       </li>
      </ol>
     </nav>
    </div>

    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6 w-full">

<div class="w-full">
   <a class="bg-blue-700 text-white px-2 py-1 rounded text-lg cursor-pointer whitespace-nowrap mb-4 float-right shadow-lg" href="add_tr.php" role="button">ADD REQUEST</a>
   <h1 class="font-bold text-2xl text-blue-900">TAX RECORDS</h1> 
   <br> 

   <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl mt-6">
       <thead>
           <tr class="bg-blue-200 text-blue-900 uppercase text-sm leading-normal">
               <th class="sticky top-0 bg-blue-200 px-2 py-2">ID</th>
               <th class="sticky top-0 bg-blue-200 px-2 py-2">Reference ID</th>
               <th class="sticky top-0 bg-blue-200 px-2 py-2">Account Name</th>
               <th class="sticky top-0 bg-blue-200 px-2 py-2">Description</th>
               <th class="sticky top-0 bg-blue-200 px-2 py-2">Payment</th>
               <th class="sticky top-0 bg-blue-200 px-2 py-2">Amount</th> 
               <th class="sticky top-0 bg-blue-200 px-2 py-2">Document</th>
               <th class="sticky top-0 bg-blue-200 px-2 py-2">Paymemt Due</th>
               <th class="sticky top-0 bg-blue-200 ">Status</th>
           </tr>
       </thead>
       <tbody class="text-sm font-light bg-gray-100">
  </div>

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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_id'])) {
    $approveId = intval($_POST['approve_id']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into pa table
        $insert_sql = "INSERT INTO pa (id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, reference_id, mode_of_payment)
                       SELECT id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, reference_id, mode_of_payment
                       FROM br WHERE id = ?";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("i", $approveId);

        if ($stmt_insert->execute()) {
            // Now delete from br table
            $delete_sql = "DELETE FROM br WHERE id = ?";
            $stmt_delete = $conn->prepare($delete_sql);
            $stmt_delete->bind_param("i", $approveId);

            if ($stmt_delete->execute()) {
                // Commit transaction if both queries succeed
                $conn->commit();
                echo "
                    <div id='success-message' class='bg-green-500 text-white p-4 rounded'>
                        Budget Approved and moved to Payout!
                    </div>
                    <script>
                        setTimeout(function() {
                            document.getElementById('success-message').style.display = 'none';
                        }, 2000);
                    </script>
                ";
            } else {
                throw new Exception("Error deleting record from br: " . $stmt_delete->error);
            }
        } else {
            throw new Exception("Error inserting record into pa: " . $stmt_insert->error);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='bg-red-500 text-white p-4 rounded'>Transaction failed: " . $e->getMessage() . "</div>";
    }
}

// Handle rejection action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_id']) && isset($_POST['reason'])) {
    $rejectId = intval($_POST['reject_id']);
    $reason = $conn->real_escape_string($_POST['reason']);

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert into rr table
        $insert_sql = "INSERT INTO rr (id, reference_id, account_name, requested_department, mode_of_payment, expense_categories, amount, description, document, payment_due, rejected_reason)
                       SELECT id, reference_id, account_name, requested_department, mode_of_payment, expense_categories, amount, description, document, payment_due, ?
                       FROM br WHERE id = ?";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("si", $reason, $rejectId);

        if ($stmt_insert->execute()) {
            // Now delete from br table
            $delete_sql = "DELETE FROM br WHERE id = ?";
            $stmt_delete = $conn->prepare($delete_sql);
            $stmt_delete->bind_param("i", $rejectId);

            if ($stmt_delete->execute()) {
                // Commit transaction if both queries succeed
                $conn->commit();
                echo "
                    <div id='success-message' class='bg-green-500 text-white p-4 rounded'>
                        Budget Rejected and moved to Rejected Requests!
                    </div>
                    <script>
                        setTimeout(function() {
                            document.getElementById('success-message').style.display = 'none';
                        }, 2000);
                    </script>
                ";
            } else {
                throw new Exception("Error deleting record from br: " . $stmt_delete->error);
            }
        } else {
            throw new Exception("Error inserting record into rr: " . $stmt_insert->error);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='bg-red-500 text-white p-4 rounded'>Transaction failed: " . $e->getMessage() . "</div>";
    }
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);

    $delete_sql = "DELETE FROM br WHERE id = ?";
    $stmt_delete = $conn->prepare($delete_sql);
    $stmt_delete->bind_param("i", $deleteId);

    if ($stmt_delete->execute()) {
        echo "<div id='success-message' class='bg-green-500 text-white p-4 rounded'>
                Record deleted successfully!
              </div>
              <script>
                  setTimeout(function() {
                      document.getElementById('success-message').style.display = 'none';
                  }, 2000);
              </script>";
    } else {
        echo "<div class='bg-red-500 text-white p-4 rounded'>Error deleting record: " . $stmt_delete->error . "</div>";
    }
}

$sql = "SELECT id, reference_id, account_name, requested_department, mode_of_payment, 
        expense_categories, amount, description, document, payment_due, status, bank_name, bank_account_number
        FROM tr";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr class='border-b border-gray-300 hover:bg-gray-200'>";
        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['id']}</td>";
        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['reference_id']}</td>";
        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['account_name']}</td>";
        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['description']}</td>";
        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['mode_of_payment']}</td>";
        echo "<td class='py-2 px-6 text-left border border-gray-300'>₱" . number_format($row['amount'], 2) . "</td>";

        // Document download link
        if (!empty($row['document']) && file_exists("files/" . $row['document'])) {
            echo "<td class='border border-gray-300 text-center'><a href='download.php?file=" . urlencode($row['document']) . "' style='color: blue; text-decoration: underline;'>Download</a></td>";
        } else {
            echo "<td class='border border-gray-300 px-2 text-center'>No document available</td>";
        }

        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['payment_due']}</td>";

        // Status column
echo "<td class='pt-3 px-6 text-left border border-gray-300'>
<div class='flex justify-start items-center space-x-1'>";
if ($row['status'] === 'approved') {
echo "<span class='bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm'>Approved</span>";
} elseif ($row['status'] === 'rejected') {
echo "<span class='bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm'>Rejected</span>";
} elseif ($row['status'] === 'disbursed') {
echo "<span class='bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm'>Disbursed</span>";
} else {
echo "<span class='bg-yellow-100 text-yellow-600 px-3 py-1 rounded-full text-sm'>Pending</span>";
}
echo "</div></td>";


        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='11' class='text-center'>No records found</td></tr>";
}
$conn->close();
?>





 </body>
</html>