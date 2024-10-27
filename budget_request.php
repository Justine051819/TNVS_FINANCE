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
          <a class="text-gray-700 font-bold" href="#">Process Payroll</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Payroll Reports</a>
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
                <?php
                 $servername = '127.0.0.1:3308';
                 $usernameDB = 'root';
                 $passwordDB = '';
                 $dbname = 'db';

                 $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

                 if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT * FROM br";
                $result = $conn->query($sql);

                if (!$result) {
                    die("Invalid query: " . $connection->error);
                }
                
                while($row = $result->fetch_assoc()){
                    echo "
                    <tr class='border-b border-gray-300 hover:bg-gray-100'>
                        <td class='py-3 px-6 text-left'>{$row['id']}</td>
                        <td class='py-3 px-6 text-left'>{$row['account_name']}</td>
                        <td class='py-3 px-6 text-left'>{$row['requested_department']}</td>
                        <td class='py-3 px-6 text-left'>{$row['expense_categories']}</td>
                        <td class='py-3 px-6 text-left'>" . number_format($row['amount'], 2) . "</td>
                        <td class='py-3 px-6 text-left'>{$row['description']}</td>
                        <td class='py-3 px-6 text-left'>{$row['document']}</td>
                        <td class='py-3 px-6 text-left'>{$row['payment_due']}</td>

                        <td class='py-3 px-6 text-left'>
                                                    <form method='POST' action=''>
                                                        <input type='hidden' name='approve_id' value='{$row['id']}'>
                                                        <button type='submit' class='bg-blue-500 text-white px-2 py-1 mb-2 rounded'>Approve</button>
                                                    </form>
                                                     <form method='POST' action=''>
                                                        <input type='hidden' name='reject_id' value='{$row['id']}'>
                                                        <button type='submit' class='bg-red-500 text-white px-2 py-1 rounded'>Reject</button>
                                                    </form>
                                                  </td>";
                }
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
