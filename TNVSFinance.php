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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
  <body>
  
    
    <?php include('navbar_sidebar.php'); ?>


    <!-- Breadcrumb -->
    <div class="bg-blue-200 p-4 shadow-lg">
     <nav class="text-gray-600 font-bold">
      <ol class="list-reset flex">
       <li>
        <a class="text-gray-600 font-bold" href="TNVSFinance.php">Dashboard</a>
       </li>
      </ol>
     </nav>
    </div>
    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6">
        
     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

      <!-- Card 1 -->
<!-- Card 1 -->
<div class="bg-white p-6 rounded-lg shadow-lg w-full">
    <h2 class="text-xl font-bold mb-4 text-gray-800">TOTAL REVENUE</h2>
    
    <!-- Revenue Amount -->
    <p class="text-3xl font-bold text-blue-600 mb-2">₱600,000</p>

    <!-- Revenue Trend -->
    <div class="flex items-center mb-4">
        <span class="text-lg text-gray-600">Change: </span>
        <span class="ml-2 text-green-500 font-semibold">+12.5%</span>
    </div>

    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
        <div class="bg-blue-600 h-2 rounded-full" style="width: 80%;"></div>
    </div>
    
    <!-- Comparison to Last Month -->
    <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
        <span>Last Month</span>
        <span>₱675,000</span>
    </div>

    <!-- Revenue Breakdown -->
    <div class="mt-4">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Revenue Breakdown</h3>
        <ul class="list-none text-sm text-gray-600">
    <li>Ride Earnings: <span class="text-blue-600">₱60,000</span></li>
    <li>Boundary Payments: <span class="text-blue-600">₱25,000</span></li>
    <li>Services: <span class="text-blue-600">₱10,000</span></li>
    <li>Other Revenue: <span class="text-blue-600">₱5,000</span></li>
</ul>

    </div>

    <!-- Button for More Details -->
    <div class="mt-4">
        <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold">View Details</a>
    </div>
</div>


      <!-- Card 2 -->
      <div class="bg-white p-6 rounded-lg shadow-lg w-100">
    <h2 class="text-xl font-bold mb-4 text-gray-800">TOTAL EXPENSES</h2>
    
    <!-- Total Expenses Amount -->
    <p class="text-3xl font-bold text-red-600 mb-2">₱194,000</p>

    <!-- Expense Trend -->
    <div class="flex items-center mb-4">
        <span class="text-lg text-gray-600">Change: </span>
        <span class="ml-2 text-red-500 font-semibold">+8.7%</span>
    </div>

    <!-- Progress Bar (Expense Efficiency) -->
    <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
        <div class="bg-red-600 h-2 rounded-full" style="width: 60%;"></div>
    </div>
    
    <!-- Comparison to Last Month -->
    <div class="flex justify-between items-center text-sm text-gray-500">
        <span>Last Month</span>
        <span>₱177,122</span>
    </div>

    <!-- Expense Breakdown -->
    <div class="mt-4">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Expense Breakdown</h3>
        <ul class="list-none text-sm text-gray-600">
            <li>Salary: <span class="text-red-600">₱25,000</li>
            <li>Utilities:<span class="text-red-600"> ₱12,000</li>
            <li>Repair/Maintenance:<span class="text-red-600"> ₱5,000</li>
            <li>Extras: <span class="text-red-600">₱8,000</li>
        </ul>
    </div>

    <!-- Button for More Details -->
    <div class="mt-4">
        <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold">View Details</a>
    </div>
</div>

       <!-- Card 3 -->
       <div class="bg-white p-6 rounded-lg shadow-lg w-100">
    <h2 class="text-xl font-bold mb-4 text-gray-800">NET INCOME</h2>
    
    <!-- Net Income Amount -->
    <p class="text-3xl font-bold text-green-600 mb-2">₱406,000</p>

    <!-- Net Income Trend -->
    <div class="flex items-center mb-4">
        <span class="text-lg text-gray-600">Change: </span>
        <span class="ml-2 text-green-500 font-semibold">+15.3%</span>
    </div>

    <!-- Progress Bar (Income Growth) -->
    <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
        <div class="bg-green-600 h-2 rounded-full" style="width: 75%;"></div>
    </div>
    
    <!-- Comparison to Last Month -->
    <div class="flex justify-between items-center text-sm text-gray-500">
        <span>Last Month</span>
        <span>₱497,878</span>
    </div>

    <!-- Net Income Breakdown -->
    <div class="mt-4">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Income Breakdown</h3>
        <ul class="list-none text-sm text-gray-600">
            <li>Revenue:<span class="text-green-800"> ₱100,000</li>
            <li>Rides Revenue: <span class="text-green-800">₱70,000</li>
            <li>Boundary Revenue: <span class="text-green-800">₱70,000</li>
            <li>Operating Expenses: <span class="text-green-800">₱15,000</li>
        </ul>
    </div>

    <!-- Button for More Details -->
    <div class="mt-4">
        <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold">View Details</a>
    </div>
</div>

  </div>
<br>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 ">
    <!-- Container for 2/3 of the width -->
    <div class="col-span-1 md:col-span-2 bg-white rounded-lg shadow-lg">
    <?php include('monthly_sales.php'); ?> 
    </div>
    
    <!-- Container for 1/3 of the width -->
    <div class="col-span-1 bg-white rounded-lg shadow-lg">
    <?php include('growth.php'); ?> 
    </div>
</div>
 </body>
</html>