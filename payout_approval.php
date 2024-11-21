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
  <script>
   
   </script>
</head>
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
        <a class="text-gray-600 font-bold" href="#">Payout Approval</a>
       </li>
      </ol>
     </nav>
    </div>

    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6 w-full">
     <h1 class="font-bold text-2xl text-blue-900">PAYOUT APPROVAL</h1> 
        <br>

        <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
            <thead>
                <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Reference ID</th>
                    <th class="px-4 py-2">Account Name</th>
                    <th class="px-4 py-2">Department</th>
                    <th class="px-4 py-2">Mode of Payment</th>
                    <th class="px-4 py-2">Category</th>
                    <th class="px-4 py-2">Amount</th> 
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Document</th>
                    <th>Payment Due</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="text-gray-900 text-sm ">
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
    $approveId = $_POST['approve_id'];

    // Fetch mode_of_payment to decide where to insert
    $mode_sql = "SELECT mode_of_payment FROM pa WHERE id = ?";
    $stmt_mode = $conn->prepare($mode_sql);
    $stmt_mode->bind_param("i", $approveId);
    $stmt_mode->execute();
    $stmt_mode->bind_result($mode_of_payment);
    $stmt_mode->fetch();
    $stmt_mode->close();

    // Check if mode_of_payment was fetched successfully
    if (empty($mode_of_payment)) {
        echo "<div class='bg-red-500 text-white p-4 rounded'>Error: Mode of payment not found or invalid for ID $approveId.</div>";
    } else {
        // Begin transaction
        $conn->begin_transaction();

        // Determine which table to insert into based on mode_of_payment
        if ($mode_of_payment == 'Bank Transfer') {
            $insert_sql = "INSERT INTO payout (id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, reference_id, mode_of_payment)
                           SELECT id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, reference_id, mode_of_payment
                           FROM pa WHERE id = ?";
        } elseif ($mode_of_payment == 'Ecash') {
            $insert_sql = "INSERT INTO ecash (id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, reference_id, mode_of_payment)
                           SELECT id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, reference_id, mode_of_payment
                           FROM pa WHERE id = ?";
        } elseif ($mode_of_payment == 'Cheque') {
            $insert_sql = "INSERT INTO cheque (id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, reference_id, mode_of_payment)
                           SELECT id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, reference_id, mode_of_payment
                           FROM pa WHERE id = ?";
        }elseif ($mode_of_payment == 'Cash') {
            $insert_sql = "INSERT INTO cash (id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, reference_id, mode_of_payment)
                           SELECT id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, reference_id, mode_of_payment
                           FROM pa WHERE id = ?";
        }else {
            echo "<div class='bg-red-500 text-white p-4 rounded'>Invalid mode of payment: " . htmlspecialchars($mode_of_payment) . "</div>";
            exit; // Stop execution if mode_of_payment is invalid
        }

        // Prepare the insert query
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("i", $approveId);

        try {
            if ($stmt_insert->execute()) {
                // After successful insertion, delete from 'pa' table
                $delete_sql = "DELETE FROM pa WHERE id = ?";
                $stmt_delete = $conn->prepare($delete_sql);
                $stmt_delete->bind_param("i", $approveId);

                if ($stmt_delete->execute()) {
                    // Commit transaction if both queries succeed
                    $conn->commit();
                    echo "
                        <div id='success-message' class='bg-green-500 text-white p-4 rounded'>
                            Budget Approved and moved to the appropriate table!
                        </div>
                        <script>
                            setTimeout(function() {
                                document.getElementById('success-message').style.display = 'none';
                            }, 2000);
                        </script>
                    ";
                } else {
                    throw new Exception("Error deleting record from pa: " . $stmt_delete->error);
                }
            } else {
                throw new Exception("Error inserting record into appropriate table: " . $stmt_insert->error);
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo "<div class='bg-red-500 text-white p-4 rounded'>Transaction failed: " . $e->getMessage() . "</div>";
        }
    }
}

// Fetch records
$sql = "SELECT * FROM pa";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr class='border-b  border-gray-300 hover:bg-gray-200'>";
        echo "<td class='py-3 px-6 border-r border-gray-300  text-left'>{$row['id']}</td>";
        echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>{$row['reference_id']}</td>";
        echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>{$row['account_name']}</td>";
        echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>{$row['requested_department']}</td>";
        echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>{$row['mode_of_payment']}</td>";
        echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>{$row['expense_categories']}</td>";
        echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>₱" . number_format($row['amount'], 2) . "</td>";
        echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>{$row['description']}</td>";

        // Document download link
        if (!empty($row['document']) && file_exists("files/" . $row['document'])) {
            echo "<td class='border-r border-gray-300'><a href='download.php?file=" . urlencode($row['document']) . "' style='color: blue; text-align:center; text-decoration: underline;'>Download</a></td>";
        } else {
            echo "<td class='border-r border-gray-300 text-center'>No document available</td>";
        }

        echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>{$row['payment_due']}</td>";

        echo "<td class=' text-left pl-3 border-r border-gray-300'>
                <form method='POST' action=''>
                    <input type='hidden' name='approve_id' value='{$row['id']}'>
                    <button type='submit' class='bg-yellow-300 text-black p-1 mt-3  font-semibold shadow-lg'>Pending</button>
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
    </div>
     </div>
    </div>
   </div>
  </div>

   </div>
  </div>

   </div>
  </div>
 </body>
</html>
