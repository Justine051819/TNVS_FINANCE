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
        <a class="text-gray-600 font-bold" href="#">Bank Transfer Payout</a>
       </li>
      </ol>
     </nav>
    </div>

    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6 h-full w-full">


<div class="w-full">
   
<h1 class="font-bold text-2xl text-blue-900 mb-8">BANK TRANSFER PAYOUT</h1> 
        <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
            <thead>
                <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Reference ID</th>
                    <th class="px-4 py-2">Account Name</th>
                    <th class="px-4 py-2">Department</th>
                    <th class="px-4 py-2">Mode of Payment</th>
                    <th class="px-4 py-2">Expense Categories</th>
                    <th class="px-4 py-2">Amount</th> 
                    <th class="px-4 py-2">Bank Name</th>
                    <th class="px-4 py-2">Bank Account Number</th>
                    <th>Payment Due</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-900 text-sm bg-gray-100 font-semilight">
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
                                          
                                          // Insert into the budget request table
                                          $insert_sql = "INSERT INTO dr (id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number)
                                                         SELECT id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number FROM payout WHERE id = '$approveId'";
                                          
                                          if ($conn->query($insert_sql) === TRUE) {
                                              // After successful insertion, delete the row
                                              $delete_sql = "DELETE FROM payout WHERE id = '$approveId'";
                                              if ($conn->query($delete_sql) === TRUE) {
                                                  echo "<div class='bg-green-500 text-white p-4 rounded'>Disbursement Approved!</div>";
                                              } else {
                                                  echo "Error deleting record: " . $conn->error;
                                              }
                                          } else {
                                              echo "Error inserting record: " . $conn->error;
                                          }
                                      }
                                  
                                      // Reject logic
                                      if (isset($_POST['reject_id'])) {
                                          $rejectId = $_POST['reject_id'];
                                          
                                          // Insert into the rejected disbursement table
                                          $insert_sql = "INSERT INTO dr (id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number)
                                                         SELECT id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number FROM payout WHERE id = '$rejectId'";
                                          
                                          if ($conn->query($insert_sql) === TRUE) {
                                              // After successful insertion, delete the row from Accounts Payable
                                              $delete_sql = "DELETE FROM payout WHERE id = '$rejectId'";
                                              if ($conn->query($delete_sql) === TRUE) {
                                                  echo "<div class='bg-red-500 text-white p-4 rounded'>Disbursement Rejected!</div>";
                                              } else {
                                                  echo "Error deleting record: " . $conn->error;
                                              }
                                          } else {
                                              echo "Error inserting record: " . $conn->error;
                                          }
                                      }
                                  }
                                  

                                    // Fetch records
                                    $sql = "SELECT * FROM payout";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr class='border-b border-gray-300 hover:bg-gray-200'>";
                                            echo "<td class=' px-6 text-left border-r border-gray-300'>{$row['id']}</td>";
                                            echo "<td class=' px-6 text-left border-r border-gray-300'>{$row['reference_id']}</td>";
                                            echo "<td class=' px-6 text-left border-r border-gray-300 '>{$row['account_name']}</td>";
                                            echo "<td class=' px-6 text-left border-r border-gray-300'>{$row['requested_department']}</td>";
                                            echo "<td class=' px-6 text-left border-r border-gray-300'>{$row['mode_of_payment']}</td>";
                                            echo "<td class=' px-6 text-left border-r border-gray-300'>{$row['expense_categories']}</td>";
                                            echo "<td class=' px-6 text-left border-r border-gray-300'>₱" . number_format($row['amount'], 2) . "</td>";
                                            echo "<td class=' px-6 text-left border-r border-gray-300'>{$row['bank_name']}</td>";
                                            echo "<td class=' px-6 text-left border-r border-gray-300'>{$row['bank_account_number']}</td>";
                                            echo "<td class=' px-6 text-left border-r border-gray-300'>{$row['payment_due']}</td>";
                                            
                                            echo "<td class=' pt-2 px-6 text-left border-r border-gray-300'>
                                                    <form method='POST' action=''>
                                                        <input type='hidden' name='approve_id' value='{$row['id']}'>
                                                        <button type='submit' class='bg-green-500 text-white py-1 px-2 rounded'>Disburse</button>
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




 </body>
</html>