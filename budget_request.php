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
  <script>

  var url = 'uploads/your-file.pdf';
  var pdfjsLib = window['pdfjs-dist'];

  pdfjsLib.getDocument(url).promise.then(function(pdf) {
    pdf.getPage(1).then(function(page) {
      var scale = 1.5;
      var viewport = page.getViewport({ scale: scale });

      var canvas = document.getElementById('pdf-viewer');
      var context = canvas.getContext('2d');
      canvas.height = viewport.height;
      canvas.width = viewport.width;

      page.render({
        canvasContext: context,
        viewport: viewport
      });
    });
  });

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

  

  </script>
  <style>
   .rotate-90 {
     transform: rotate(90deg);
     transition: transform 0.3s ease;
   }


  </style>
 </head>  
 <body class="bg-white-900">
  <div class="flex h-screen">
   <!-- Sidebar -->
   <div id="sidebar" class="w-64 bg-white p-4  z-10 transition-all duration-300">
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
          <a href="budget_request.php" class="text-gray-700 font-bold">Rejected Request</a>
         </li>
         <li class="mb-2">
          <a href="budget_request.php" class="text-gray-700 font-bold">Budget Allocation</a>
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
          <a class="text-gray-700 font-bold" href="payout_approval.php">Payout Approval</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="payout.php">Payout</a>
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
          <a class="text-gray-700 font-bold" href="account_payable.php">Account Payable Invoice</a>
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
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer" onclick="toggleDropdown('hatDropdown')">
         <i class="fas fa-file-invoice-dollar mr-2"></i>
         General Ledger
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300"></i>
        </a>
        <ul class="hidden pl-8 mt-2" id="hatDropdown">
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Charts of Accounts</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="disburse_records.php">Disburse Records</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Collected Records</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Asset Records</a>
         </li>
        </ul>
       </div>
      </li>
      <li>
       <a class="text-blue-600 font-bold" href="#">Report</a>
      </li>
     </ul>
    </nav>
   </div>
   <!-- Main content -->
   <div id="mainContent" class="flex-1 flex flex-col bg-blue-100 transition-all duration-300">
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
       <a class="block px-4 py-2 text-gray-700 font-bold" href="login.php">Logout</a>
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
    <div class="flex-1 bg-blue-100 p-6 w-full">

<div class="flex-1 bg-blue-100 p-6 w-full">
     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
     <div class="w-full">
        <h1 class="font-bold text-xl">Budget Request</h1>
        <a class="bg-green-500 text-white px-2 py-1 rounded text-lg cursor-pointer whitespace-nowrap mb-4" href="add_ap.php" role="button">Add Request</a>
        <br>
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
                    <th>Payment Due</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">

            <!-- Modal Structure -->
<div id="verificationModal" style="display: none;">
    <div>
        <h2>Verification Required</h2>
        <form id="verificationForm">
            <label for="actionPassword">Enter Action Password:</label>
            <input type="password" id="actionPassword" required>
            <input type="hidden" id="actionType" name="actionType">
            <button type="submit">Submit</button>
            <button type="button" id="cancelButton">Cancel</button>
        </form>
    </div>
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
                                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                                      // Approve logic
                                      if (isset($_POST['approve_id'])) {
                                          $approveId = $_POST['approve_id'];
                                          
                                          // Insert into the table
                                          $insert_sql = "INSERT INTO pa (id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number)
                                                         SELECT id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number FROM br WHERE id = '$approveId'";
                                          
                                          if ($conn->query($insert_sql) === TRUE) {
                                            // After successful insertion, delete the row
                                            $delete_sql = "DELETE FROM br WHERE id = '$approveId'";
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
                                  
                                      // Reject logic
                                      if (isset($_POST['reject_id'])) {
                                          $rejectId = $_POST['reject_id'];
                                          
                                          // Insert into the table
                                          $insert_sql = "INSERT INTO pa (id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number)
                                                         SELECT id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number FROM br WHERE id = '$rejectId'";
                                          
                                          if ($conn->query($insert_sql) === TRUE) {
                                              // After successful insertion, delete the row from Accounts Payable
                                              $delete_sql = "DELETE FROM br WHERE id = '$rejectId'";
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
                                  

                                    // Fetch disbursement records
                                    $sql = "SELECT * FROM br";
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
                                            
                                            echo "<td class='py-3 px-6 text-left'>
                                            <div class='flex justify-start items-center space-x-1'>  <!-- Reduced space between buttons -->
                                                <!-- Approve Button -->
                                                <form method='POST' action=''>
                                                    <input type='hidden' name='approve_id' value='{$row['id']}'>
                                                    <button type='submit' class='text-blue-500 w-8 h-8 flex justify-center items-center'>  <!-- Smaller buttons -->
                                                        <i class='fas fa-check'></i>
                                                    </button>
                                                </form>
                                    
                                                <!-- Reject Button -->
                                                <form method='POST' action=''>
                                                    <input type='hidden' name='reject_id' value='{$row['id']}'>
                                                    <button type='submit' class='text-red-500 w-8 h-8 flex justify-center items-center'>  <!-- Smaller buttons -->
                                                        <i class='fas fa-times'></i>
                                                    </button>
                                                </form>
                                    
                                                <!-- Edit Button -->
                                                <a href='edit.php?id={$row['id']}' class='text-yellow-500 mb-3.5 w-8 h-8 flex justify-center items-center'>
                                                    <i class='fas fa-edit'></i>
                                                </a>
                                    
                                                <!-- Delete Button -->
                                                <form method='POST' action='del.php' onsubmit='return confirm(\"Are you sure you want to delete this record?\");'>
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

        <div class="mt-6">
        <canvas id="pdf-viewer" width="600" height="400"></canvas>
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
  </div>
 </body>
</html>
