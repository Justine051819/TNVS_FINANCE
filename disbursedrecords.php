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
        <a class="text-gray-600 font-bold" href="#">Disbursed Records</a>
       </li>
      </ol>
     </nav>
    </div>

    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6 h-full w-full">

    <h1 class="font-bold text-2xl text-blue-900 mb-8">DISBURSED RECORDS</h1> 

    <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
    <thead>
        <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Reference ID</th> 
                    <th class="px-4 py-2">Account Name</th>
                    <th class="px-4 py-2">Department</th>
                    <th class="px-4 py-2">MOD</th> 
                    <th class="px-4 py-2">Expense Category</th>
                    <th class="px-4 py-2">Amount</th> 
                    <th>Payment Due</th>
                    <th>Disbursed At</th>
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

                                // Fetch records
                                $sql = "SELECT * FROM dr";
                                $result = $conn->query($sql);
                                $totalAmount = 0; // Initialize a variable for the total amount

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr class='border-b border-gray-300 hover:bg-gray-200'>";
                                        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['id']}</td>";
                                        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['reference_id']}</td>";
                                        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['account_name']}</td>";
                                        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['requested_department']}</td>";
                                        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['mode_of_payment']}</td>";
                                        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['expense_categories']}</td>";
                                        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>₱" . number_format($row['amount'], 2) . "</td>";
                                        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['payment_due']}</td>";
                                        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['disbursed_at']}</td>";
                                        echo "</tr>";
                                        
                                        // Add the amount to the total
                                        $totalAmount += $row['amount'];
                                    }

                                    // Add a row for the total
                                    echo "<tr class='font-bold bg-gray-100'>";
                                    echo "<td colspan='6' class='py-3 px-6 text-right border-r border-gray-300'>Total:</td>";
                                    echo "<td class='py-3 px-6 text-left border-r border-gray-300'>₱" . number_format($totalAmount, 2) . "</td>";
                                    echo "<td colspan='2'></td>"; // Empty cells for alignment
                                    echo "</tr>";
                                } else {
                                    echo "<tr><td colspan='9' class='text-center py-3'>No records found</td></tr>";
                                }

                                $conn->close();
                                ?>
                


            </tbody>
        </table>


</div>




 </body>
</html>