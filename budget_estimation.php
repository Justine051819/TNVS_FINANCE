<?php
session_start(); // Start the session

include 'session_manager.php'; // Include the session manager

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Optionally, check for double login and alert if necessary
if (is_user_logged_in($_SESSION['users_username'])) {
    // Optionally, log them out or handle the session as needed
}

// Your page content here...

?>

<html>
 <head>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/> 
</head>
  <body>
  
    
    <?php include('navbar_sidebar.php'); ?>


   <!-- Breadcrumb -->
   <div class="bg-blue-200 p-4 ">
     <nav class="text-gray-600 font-bold">
      <ol class="list-reset flex">
       <li>
        <a class="text-gray-600 font-bold" href="TNVSFinance.php">Dashboard</a>
       </li>
       <li>
        <span class="mx-2">&gt;</span>
       </li>
       <li>
        <a class="text-gray-600 font-bold" href="#">Budget</a>
       </li>
       <li>
        <span class="mx-2">&gt;</span>
       </li>
       <li>
        <a class="text-gray-600 font-bold" href="#">Budget Estimation</a>
       </li>
      </ol>
     </nav>
    </div>
    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6">
    <h1 class="font-bold text-2xl text-blue-900 mb-8">BUDGET ESTIMATION</h1> 
        
     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 
         <!-- Card 4 -->
         <div class="bg-white p-4 rounded-lg shadow-lg">
    <h2 class="text-xl font-bold text-gray-800 mb-4">BUDGET VS ACTUAL</h2>

    <!-- Budget vs Actual Table -->
<table class=" min-w-full text-sm text-left float-right">
    <thead>
        <tr class="border-b">
            <th class="py-2 px-4 text-gray-600">Category</th>
            <th class="py-2 px-4 text-gray-600">Budgeted</th>
            <th class="py-2 px-4 text-gray-600">Actual</th>
            <th class="py-2 px-4 text-gray-600">Variance</th>
        </tr>
    </thead>
    <tbody>
        <tr class="border-b">
            <td class="py-2 px-4 text-gray-700">Equipments</td>
            <td class="py-2 px-4 text-gray-700">₱30,000</td>
            <td class="py-2 px-4 text-gray-700">₱28,000</td>
            <td class="py-2 px-4 text-green-500">₱2,000</td>
        </tr>
        <tr class="border-b">
            <td class="py-2 px-4 text-gray-700">Maintenance</td>
            <td class="py-2 px-4 text-gray-700">₱10,000</td>
            <td class="py-2 px-4 text-gray-700">₱12,500</td>
            <td class="py-2 px-4 text-red-500">-₱2,500</td>
        </tr>
        <tr class="border-b">
            <td class="py-2 px-4 text-gray-700">Salaries</td>
            <td class="py-2 px-4 text-gray-700">₱100,000</td>
            <td class="py-2 px-4 text-gray-700">₱98,000</td>
            <td class="py-2 px-4 text-green-500">₱2,000</td>
        </tr>
        <tr class="border-b">
            <td class="py-2 px-4 text-gray-700">Bonuses</td>
            <td class="py-2 px-4 text-gray-700">₱20,000</td>
            <td class="py-2 px-4 text-gray-700">₱18,000</td>
            <td class="py-2 px-4 text-green-500">₱2,000</td>
        </tr>
        <tr class="border-b">
            <td class="py-2 px-4 text-gray-700">Facility Cost</td>
            <td class="py-2 px-4 text-gray-700">₱15,000</td>
            <td class="py-2 px-4 text-gray-700">₱15,000</td>
            <td class="py-2 px-4 text-gray-500">₱0</td>
        </tr>
        <tr class="border-b">
            <td class="py-2 px-4 text-gray-700">Training</td>
            <td class="py-2 px-4 text-gray-700">₱8,000</td>
            <td class="py-2 px-4 text-gray-700">₱7,500</td>
            <td class="py-2 px-4 text-green-500">₱500</td>
        </tr>
        <tr class="border-b">
            <td class="py-2 px-4 text-gray-700">Wellness</td>
            <td class="py-2 px-4 text-gray-700">₱3,000</td>
            <td class="py-2 px-4 text-gray-700">₱3,500</td>
            <td class="py-2 px-4 text-red-500">-₱500</td>
        </tr>
        <tr class="border-b">
            <td class="py-2 px-4 text-gray-700">Tax</td>
            <td class="py-2 px-4 text-gray-700">₱12,000</td>
            <td class="py-2 px-4 text-gray-700">₱12,000</td>
            <td class="py-2 px-4 text-gray-500">₱0</td>
        </tr>
        
    </tbody>
</table>
</div>
         <!-- Card 5 -->
         <div class="bg-white p-4 rounded-lg shadow-lg">
    <h2 class="text-xl font-bold mb-4 text-gray-800">REMAINING BUDGET</h2>
    
    <!-- Remaining Budget for Equipments/Assets -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Equipments/Assets</span>
            <span class="text-gray-700">₱2,000</span> <!-- Remaining Budget -->
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
            <div class="bg-green-500 h-2 rounded-full" style="width: 93%;"></div> <!-- Used 93% -->
        </div>
    </div>
    
    <!-- Remaining Budget for Maintenance/Repair -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Maintenance/Repair</span>
            <span class="text-gray-700">₱2,500</span> <!-- Remaining Budget -->
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
            <div class="bg-red-500 h-2 rounded-full" style="width: 100%;"></div> <!-- Maximum width of 100% -->
        </div>
    </div>
    
    <!-- Remaining Budget for Salaries -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Salaries</span>
            <span class="text-gray-700">₱2,000</span> <!-- Remaining Budget -->
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
            <div class="bg-green-500 h-2 rounded-full" style="width: 98%;"></div> <!-- Used 98% -->
        </div>
    </div>

    <!-- Remaining Budget for Bonuses -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Bonuses</span>
            <span class="text-gray-700">₱2,000</span> <!-- Remaining Budget -->
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
            <div class="bg-green-500 h-2 rounded-full" style="width: 90%;"></div> <!-- Used 90% -->
        </div>
    </div>

    <!-- Remaining Budget for Facility Cost -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Facility Cost</span>
            <span class="text-gray-700">₱0</span> <!-- Remaining Budget -->
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
            <div class="bg-gray-300 h-2 rounded-full" style="width: 100%;"></div> <!-- No remaining budget -->
        </div>
    </div>

    <!-- Remaining Budget for Training Cost -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Training Cost</span>
            <span class="text-gray-700">₱500</span> <!-- Remaining Budget -->
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
            <div class="bg-green-500 h-2 rounded-full" style="width: 94%"></div> <!-- Used 94% -->
        </div>
    </div>

    <!-- Remaining Budget for Wellness Program Cost -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Wellness Program Cost</span>
            <span class="text-gray-700">₱500</span> <!-- Remaining Budget -->
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
            <div class="bg-red-500 h-2 rounded-full" style="width: 100%;"></div> <!-- Maximum width of 100% -->
        </div>
    </div>

    <!-- Remaining Budget for Tax Payment -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Tax Payment</span>
            <span class="text-gray-700">₱0</span> <!-- Remaining Budget -->
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
            <div class="bg-gray-300 h-2 rounded-full" style="width: 100%;"></div> <!-- No remaining budget -->
        </div>
    </div>
</div>


         <!-- Card 6 -->
         <div class="bg-white p-4 rounded-lg shadow-lg">
    <h2 class="text-xl font-bold mb-4 text-gray-800">ESTIMATED BUDGET</h2>
    
    <!-- Forecast for Equipments -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Equipments</span>
            <span class="text-gray-700">₱30,000</span> <!-- Budgeted amount -->
        </div>
        <div class="text-sm text-gray-600">
            <span>Variance: </span>
            <span class="text-green-500">+₱2,000</span> <!-- Predicted increase based on variance -->
        </div>
    </div>
    
    <!-- Forecast for Maintenance -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Maintenance</span>
            <span class="text-red-600">₱12,500</span> <!-- Budgeted amount -->
        </div>
        <div class="text-sm text-gray-600">
            <span>Variance: </span>
            <span class="text-red-500">-₱2,500</span> <!-- Predicted increase due to variance -->
        </div>
    </div>
    
    <!-- Forecast for Salaries -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Salaries</span>
            <span class="text-gray-700">₱100,000</span> <!-- Budgeted amount -->
        </div>
        <div class="text-sm text-gray-600">
            <span>Variance: </span>
            <span class="text-green-500">+₱2,000</span> <!-- Predicted savings based on variance -->
        </div>
    </div>

    <!-- Forecast for Bonuses -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Bonuses</span>
            <span class="text-gray-700">₱20,000</span> <!-- Budgeted amount -->
        </div>
        <div class="text-sm text-gray-600">
            <span>Variance: </span>
            <span class="text-green-500">+₱2,000</span> <!-- Predicted savings based on variance -->
        </div>
    </div>

    <!-- Forecast for Facility Cost -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Facility Cost</span>
            <span class="text-gray-700">₱15,000</span> <!-- Budgeted amount -->
        </div>
        <div class="text-sm text-gray-600">
            <span>Variance: </span>
            <span class="text-gray-600">₱0</span> <!-- No change forecasted -->
        </div>
    </div>

    <!-- Forecast for Training -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Training</span>
            <span class="text-gray-700">₱8,000</span> <!-- Budgeted amount -->
        </div>
        <div class="text-sm text-gray-600">
            <span>Variance: </span>
            <span class="text-green-500">+₱500</span> <!-- Slight increase forecasted -->
        </div>
    </div>

    <!-- Forecast for Wellness Program -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Wellness Program</span>
            <span class="text-red-600">₱3,500</span> <!-- Budgeted amount -->
        </div>
        <div class="text-sm text-gray-600">
            <span>Variance: </span>
            <span class="text-red-500">-₱500</span> <!-- Forecasted increase based on variance -->
        </div>
    </div>

    <!-- Forecast for Tax -->
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-gray-700">Tax Payment</span>
            <span class="text-gray-700">₱12,000</span> <!-- Budgeted amount -->
        </div>
        <div class="text-sm text-gray-600">
            <span>Variance: </span>
            <span class="text-gray-600">₱0</span> <!-- No change forecasted -->
        </div>
    </div>
    <a class="bg-blue-700 text-white px-2 py-1 rounded text-lg cursor-pointer whitespace-nowrap mb-4 float-right shadow-lg mt-4" href="budget_allocation.php" role="button">Allocate</a>
</div>


     </div>
     
    </div>
   
  <!-- Modal for Adding Employee -->
  </div>
 </body>
</html>
