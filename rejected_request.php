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
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('hidden');
    }

    function toggleDropdown(id) {
      const dropdown = document.getElementById(id);
      const icon = dropdown.previousElementSibling.querySelector('.fas.fa-chevron-right');
      dropdown.classList.toggle('hidden');
      icon.classList.toggle('rotate-90');
    }

    function openModal() {
      const modal = document.getElementById('addEmployeeModal');
      modal.classList.remove('hidden');
    }

    function closeModal() {
      const modal = document.getElementById('addEmployeeModal');
      modal.classList.add('hidden');
    }


    window.onclick = function(event) {
  const modal = document.getElementById('addEmployeeModal');
  if (event.target === modal) {
    closeModal();
  }
};

function showModal(reason) {
    // Set the rejection reason in the modal
    document.getElementById("reasonText").innerText = reason;

    // Display the modal
    document.getElementById("reasonModal").classList.remove("hidden");
}

function closeModal() {
    // Hide the modal
    document.getElementById("reasonModal").classList.add("hidden");
}

  </script>
  <style>
   .rotate-90 {
     transform: rotate(90deg);
     transition: transform 0.3s ease;
   }

   .z-50 {
  z-index: 50;
}


    .hidden { display: none; }
    .modal-overlay { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0, 0, 0, 0.5); }
    .modal-content { background: white; padding: 20px; border-radius: 5px; max-width: 500px; width: 90%; }
    .close-btn { background: #3490dc; color: white; padding: 8px 12px; border: none; border-radius: 3px; cursor: pointer; }

  </style>
 </head>  
 <body class="bg-gray-900">
  <div class="flex h-screen">
   <!-- Sidebar -->
   <div id="sidebar" class="w-64 bg-white p-4 shadow-lg z-10">
    <div class="flex items-center mb-6">
     <img alt="Movers logo" class="mr-2" height="200px" src="logo.png" width="250px"/>
    </div>
    <nav>
     <ul>
      <li class="mb-4">
       <a class="flex items-center text-blue-600 font-bold" href="TNVSFinance.php">
        <i class="fas fa-th-large mr-2"></i>
        Dashboard
       </a>
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer" onclick="toggleDropdown('employeeDropdown')">
         <i class="fas fa-calculator mr-2"></i>
         Budget
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300"></i>
        </a>
        <ul class="hidden pl-8 mt-2" id="employeeDropdown">
         <li class="mb-2">
          <a href="budget_request.php" class="text-gray-700 font-bold">Budget Request</a>
         </li>
         <li class="mb-2">
          <a href="view_employee.php" class="text-gray-700 font-bold">Rejected Request</a>
         </li>
         <li class="mb-2">
          <a href="view_employee.php" class="text-gray-700 font-bold">Budget Allocation</a>
         </li>
        </ul>
       </div>
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer" onclick="toggleDropdown('payrollDropdown')">
         <i class="fas fa-coins mr-2"></i>
         Disbursement
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300"></i>
        </a>
        <ul class="hidden pl-8 mt-2" id="payrollDropdown">
        <li class="mb-2">
          <a href="view_employee.php" class="text-gray-700 font-bold">Request Payout</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="approve_disbursement.php">Payout</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="reject_disbursement.php">Rejected Disbursement</a>
         </li>
        </ul>
       </div>
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer" onclick="toggleDropdown('compensationDropdown')">
         <i class="fas fa-gift mr-2"></i>
         Collection
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300"></i>
        </a>
        <ul class="hidden pl-8 mt-2" id="compensationDropdown">
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Compensation Plans</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Benefits Overview</a>
         </li>
        </ul>
       </div>
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer" onclick="toggleDropdown('recommendationDropdown')">
         <i class="fas fa-landmark mr-2"></i>
         Account Payables
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300"></i>
        </a>
        <ul class="hidden pl-8 mt-2" id="recommendationDropdown">
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="account_payable.php">Accounts Payable Invoice</a>
        </ul>
       </div>
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer" onclick="toggleDropdown('hatdogDropdown')">
         <i class="fas fa-file-invoice-dollar mr-2"></i>
         Account Receivables
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300"></i>
        </a>
        <ul class="hidden pl-8 mt-2" id="hatdogDropdown">
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Cheese Cake</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Palaman Hatdog</a>
         </li>
        </ul>
       </div>
       <a class="flex items-center text-gray-700 font-bold cursor-pointer" onclick="toggleDropdown('hatDropdown')">
         <i class="fas fa-file-invoice-dollar mr-2"></i>
         General Ledger
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300"></i>
         <ul class="hidden pl-8 mt-2" id="hatDropdown">
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Cheese Cake</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Palaman Hatdog</a>
         </li>
        </ul>
        </a>
      </li>
      <li>
       <a class="text-blue-600 font-bold" href="#">Report</a>
      </li>
     </ul>
    </nav>
   </div>
   <!-- Main content -->
   <div class="flex-1 flex flex-col">
    <!-- Header -->
    <header class="flex items-center justify-between bg-white p-4 shadow-lg">
     <div class="flex items-center">
      <button class="text-2xl mr-4" onclick="toggleSidebar()">
       <i class="fas fa-bars"></i>
      </button>
      <h1 class="text-xl font-bold text-blue-600">Finance</h1>
     </div>
     <div class="relative">
      <button class="flex items-center" onclick="toggleDropdown('userDropdown')">
       <img alt="User avatar" class="rounded-full" height="40" src="user.jpg" width="40"/>
      </button>
      <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 hidden">
       <a class="block px-4 py-2 text-gray-700 font-bold" href="#">Profile</a>
       <a class="block px-4 py-2 text-gray-700 font-bold" href="#">Settings</a>
       <a class="block px-4 py-2 text-gray-700 font-bold" href="logout.php">Logout</a>
      </div>
     </div>
    </header>
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
        <a class="text-gray-600 font-bold" href="#">Finance Operator</a>
       </li>
      </ol>
     </nav>
    </div>
    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6">
    <div class="flex-1 bg-blue-100 p-6 w-full">
     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
     <div class="w-full">
        <h1 class="font-bold text-xl">REJECTED REQUEST</h1>
       
        <div class="w-full px-4 pt-4">
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Account Name</th>
                    <th class="px-4 py-2">Requested Department</th>
                    <th class="px-4 py-2">Expense Categories</th>
                    <th class="px-4 py-2">Amount</th> 
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Document</th>
                    <th class="px-4 py-2">Payment Due</th>
                    <th>Reason Rejected</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">

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
                                          
                                          // Insert into the table
                                          $insert_sql = "INSERT INTO pa (id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number)
                                                         SELECT id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number FROM rr WHERE id = '$approveId'";
                                          
                                          if ($conn->query($insert_sql) === TRUE) {
                                            // After successful insertion, delete the row
                                            $delete_sql = "DELETE FROM rr WHERE id = '$approveId'";
                                            if ($conn->query($delete_sql) === TRUE) {
                                                echo "
                                                    <div id='success-message' class='bg-green-500 text-white p-4 rounded'>
                                                        Budget Approved!
                                                    </div>
                                                    <script>
                                                        setTimeout(function() {
                                                            document.getElementById('success-message').style.display = 'none';
                                                        }, 3000); // 3000 milliseconds = 3 seconds
                                                    </script>
                                                ";
                                            } else {
                                                echo "Error deleting record: " . $conn->error;
                                            }
                                        } else {
                                            echo "Error inserting record: " . $conn->error;
                                        }
                                      }
                                    }
// Handle rejection action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_id'])) {
  $rejectId = $_POST['reject_id'];
  $reason = $_POST['reason'];

  // Insert into the table
  $insert_sql = "INSERT INTO rr (id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number)
                 SELECT id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number FROM br WHERE id = '$rejectId'";

  if ($conn->query($insert_sql) === TRUE) {
      // After successful insertion, update rejection reason
      $update_sql = "UPDATE rr SET rejected_reason = ? WHERE id = ?";
      $stmt = $conn->prepare($update_sql);
      $stmt->bind_param("si", $reason, $rejectId);

      if ($stmt->execute()) {
          echo "<div class='bg-red-500 text-white p-4 rounded'>Budget Rejected!</div>";
      } else {
          echo "Error updating rejection reason: " . $conn->error;
      }
  } else {
      echo "Error inserting into rr: " . $conn->error;
  }
}


                                  

                                    // Fetch disbursement records
                                    $sql = "SELECT * FROM rr";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr class='border-b border-gray-300 hover:bg-gray-100'>";
                                            echo "<td class='py-3 px-6 text-left'>{$row['id']}</td>";
                                            echo "<td class='py-3 px-6 text-left'>{$row['account_name']}</td>";
                                            echo "<td class='py-3 px-6 text-left'>{$row['requested_department']}</td>";
                                            echo "<td class='py-3 px-6 text-left'>{$row['expense_categories']}</td>";
                                            echo "<td class='py-3 px-6 text-right'>" . number_format($row['amount'], 2) . "</td>";
                                            echo "<td class='py-3 px-6 text-left'>{$row['description']}</td>";
                                            echo "<td class='py-3 px-6 text-left'>{$row['document']}</td>";
                                            echo "<td class='py-3 px-6 text-left'>{$row['payment_due']}</td>";
                                            echo "<td>
                                            <button onclick=\"showModal('{$row['rejected_reason']}')\" class='text-blue-500 underline'>View Reason</button>
                                          </td>";
                                          
                                            
                                            echo "<td class='py-3 px-6 text-left'>
                                            <div class='flex justify-start items-center space-x-1'>  <!-- Reduced space between buttons -->
                                                <!-- Edit Button -->
                                                <a href='edit.php?id={$row['id']}' class='text-yellow-500 mb-3.5 w-8 h-8 flex justify-center items-center'>
                                                    <i class='fas fa-edit'></i>
                                                </a>

                                                  <!-- Approve Button -->
                                                <form method='POST' action=''>
                                                    <input type='hidden' name='approve_id' value='{$row['id']}'>
                                                    <button type='submit' class='text-blue-500 w-8 h-8 flex justify-center items-center'>  <!-- Smaller buttons -->
                                                        <i class='fas fa-paper-plane'></i>
                                                    </button>
                                                </form>
                                    
                                                <!-- Delete Button -->
                                                <form method='POST' action='del1.php' onsubmit='return confirm(\"Are you sure you want to delete this record?\");'>
                                                    <input type='hidden' name='id' value='{$row['id']}'>
                                                    <button type='submit' class='text-red-500 w-8 h-8 flex justify-center items-center'>
                                                        <i class='fas fa-trash-alt'></i>
                                                    </button>
                                                </form>
                                            </div>
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
        <div id="reasonModal" class="modal-overlay hidden">
    <div class="modal-content">
        <h2 class="text-lg font-bold mb-4">Reason for Rejection</h2>
        <p id="reasonText" class="mb-4 text-gray-700"></p>
        <button onclick="closeModal()" class="close-btn">Close</button>
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
