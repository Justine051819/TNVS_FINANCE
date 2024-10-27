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

   th, td {
    padding: 8px 16px;
    text-align: left;
}

h1{
  
}

#sidebar{
  
}
  </style>
 </head>  
 <body class="bg-gray-100">
  <div class="flex h-screen">
   <!-- Sidebar -->
   <div id="sidebar" class="w-64 bg-white p-4 shadow-lg z-10 overflow-y-auto h-screen">
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
          <a class="text-gray-700 font-bold" href="javascript:void(0)" onclick="openModal()">Add Employee</a>
         </li>
         <li class="mb-2">
          <a href="view_employee.php" class="text-gray-700 font-bold">View Employees</a>
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
       <a class="block px-4 py-2 text-gray-700 font-bold" href="#">Logout</a>
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
     <div class="w-full">
        <h1 class="font-bold text-xl">View Employee</h1>
        <div class="mb-4"> <!-- Wrap the button in a div -->
                <a class="bg-green-500 text-white px-2 py-1 rounded text-lg cursor-pointer whitespace-nowrap mb-4" href="add_employee.php" role="button">New Account</a>
            </div>
        <br>
        <div class="w-full px-4 pt-4">
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Employee Name</th>
                    <th class="px-4 py-2">Position</th>
                    <th class="px-4 py-2">Department</th>
                    <th class="px-4 py-2">Created at</th> 
                    <th class="px-4 py-2">Updated at</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                 $servername = '127.0.0.1:3308';
                 $usernameDB = 'root';
                 $passwordDB = '';
                 $dbname = 'db';

                 $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

                 if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT * FROM ve";
                $result = $conn->query($sql);

                if (!$result){
                    die("invalid query: " . $connection->error);
                }

                while($row = $result->fetch_assoc()){
                    echo "
                    <tr class='align-middle'>
                    <td>{$row['id']}</td>
        <td class='py-3 px-6 text-left>{$row['employee_name']}</td>
        <td class='py-3 px-6 text-left>{$row['position']}</td>
        <td class='py-3 px-6 text-left>{$row['department']}</td>
        <td class='py-3 px-6 text-left>{$row['created_at']}</td>
        <td class='py-3 px-6 text-left>{$row['updated_at']}</td>
        <td class='px-4 py-2 flex space-x-2'>
            <a class='bg-green-500 text-white px-2 py-1 rounded text-lg' href='/TNVS_FINANCE/edit.php?id={$row['id']}'>Edit</a>
            <a class='bg-red-500 text-white px-2 py-1 rounded text-lg' href='/TNVS_FINANCE/del.php?id={$row['id']}'>Delete</a>
        </td>
                </tr>
                    ";
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

  <!-- Modal for Adding Employee -->
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="addEmployeeModal">
   <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
    <h2 class="text-xl font-bold mb-4">Add Employee</h2>
    <form>
     <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2" for="employeeName">Employee Name</label>
      <input class="w-full p-2 border border-gray-300 rounded-md" id="employeeName" placeholder="Enter employee name" type="text"/>
     </div>
     <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2" for="employeePosition">Position</label>
      <input class="w-full p-2 border border-gray-300 rounded-md" id="employeePosition" placeholder="Enter employee position" type="text"/>
     </div>
     <div class="flex justify-end">
      <button class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2" onclick="closeModal()" type="button">Cancel</button>
      <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">Add Employee</button>
     </div>
    </form>
   </div>
  </div>
 </body>
</html>
