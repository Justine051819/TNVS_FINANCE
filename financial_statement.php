<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Handling the form submission and filtering the data
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

// Convert dates to PHP DateTime objects for comparison (optional)
if ($start_date && $end_date) {
    $start_date_obj = new DateTime($start_date);
    $end_date_obj = new DateTime($end_date);
}

?>

<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
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
                <a class="text-gray-600 font-bold" href="#">Income Statement</a>
            </li>
        </ol>
    </nav>
</div>


<!-- Main content area -->
<div class="flex-1 bg-blue-100 p-6 h-full w-full">
<div class="float-right">
    <a class="bg-blue-700 text-white px-2 py-1 mx-1 rounded text-lg cursor-pointer whitespace-nowrap mb-4 float-right shadow-lg" href="balance_sheet.php" role="button">BALANCE SHEET</a>
    <a class=" bg-white border-2 border-blue-800 text-blue-800 font-bold  px-2 py-1 mx-1 rounded text-lg cursor-pointer whitespace-nowrap mb-4 float-right shadow-lg" href="financial_statement.php" role="button">INCOME STATEMENT</a>
</div>
    <h1 class="font-bold text-2xl text-blue-900 mb-6">INCOME STATEMENT</h1>
    
    
    <div>
    <form method="POST" class="flex items-center space-x-3">
    <div class="flex flex-col">
        <label for="start_date" class="text-xs font-medium text-gray-700">Start Date:</label>
        <input type="date" id="start_date" name="start_date" class="p-1.5 border border-gray-300 rounded-sm focus:ring-2 focus:ring-blue-500" value="<?php echo $start_date; ?>">
    </div>
    <div class="flex flex-col">
        <label for="end_date" class="text-xs font-medium text-gray-700">End Date:</label>
        <input type="date" id="end_date" name="end_date" class="p-1.5 border border-gray-300 rounded-sm focus:ring-2 focus:ring-blue-500" value="<?php echo $end_date; ?>">
    </div>
    <button type="submit" class="bg-blue-500 text-white text-xs py-2 px-4 mt-4 rounded-sm shadow-sm hover:bg-blue-600">Filter</button>
    <button type="submit" class="bg-blue-500 text-white text-xs py-2 px-4 mt-4 rounded-sm shadow-sm hover:bg-blue-600 flex items-center space-x-2">
    <!-- FontAwesome Icon on the left side -->
    <i class="fas fa-print"></i>
    <span>Print</span>
</button>

</form>
</div>


    <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
    <thead>
        <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
            <th class="px-4 py-2">Category</th>
            <th class="px-4 py-2">Account Name</th>
            <th class="px-4 py-2">Amount (₱)</th>
        </tr>
    </thead>
    <tbody class="text-gray-900 text-sm font-semilight">
        <!-- Revenue Section -->
        <?php
        // Sample revenue data (adjust based on your filtering logic)
        $revenue = [
            ['Platform Fee', '₱12,000.00'],
            ['Boundary', '₱3,000.00']
        ];
        $total_revenue = '₱15,000.00';
        
        // Revenue Title Row with gray background
        echo '<tr class="bg-gray-200 text-blue-800 font-bold">
                <td class="py-2 px-4">Revenue</td>
                <td class="py-2 px-4" colspan="2"> </td>
              </tr>';
        
        // Loop through revenue and display them
        foreach ($revenue as $rev) {
            echo "<tr class='border-b'>
                    <td></td>
                    <td class='py-2 px-4'>{$rev[0]}</td>
                    <td class='py-2 px-4'>{$rev[1]}</td>
                  </tr>";
        }
        echo "<tr class='bg-blue-100 font-bold text-blue-800'>
                <td class='py-2 px-4'></td>
                <td class='py-2 px-4'>Total Revenue</td>
                <td class='py-2 px-4'>{$total_revenue}</td>
              </tr>";
        ?>

        <!-- Expenses Section -->
        <?php
        $expenses = [
            ['Salaries Expense', '₱3,500.00'],
            ['Bonuses Expense', '₱1,000.00'],
            ['Facility Cost', '₱2,000.00'],
            ['Maintenance/Repair', '₱500.00'],
            ['Training Cost', '₱1,200.00'],
            ['Wellness Program Cost', '₱800.00'],
            ['Tax Payment', '₱1,000.00'],
            ['Miscellaneous Expense', '₱500.00']
        ];
        $total_expenses = '₱10,500.00';
        
        // Expenses Title Row with gray background
        echo '<tr class="bg-gray-200 text-blue-800 font-bold">
                <td class="py-2 px-4">Expenses</td>
                <td class="py-2 px-4" colspan="2"> </td>
              </tr>';
        
        // Loop through expenses and display them
        foreach ($expenses as $expense) {
            echo "<tr class='border-b'>
                    <td></td>
                    <td class='py-2 px-4'>{$expense[0]}</td>
                    <td class='py-2 px-4'>{$expense[1]}</td>
                  </tr>";
        }
        echo "<tr class='bg-blue-100 font-bold text-blue-800'>
                <td class='py-2 px-4'></td>
                <td class='py-2 px-4'>Total Expenses</td>
                <td class='py-2 px-4'>{$total_expenses}</td>
              </tr>";
        ?>

        <!-- Net Profit Section -->
        <?php
        $net_profit = '₱4,500.00';
        
        echo "<tr class='bg-blue-100 font-bold text-blue-800'>
                <td class='py-2 px-4'></td>
                <td class='py-2 px-4'>Net Profit (Revenue - Expenses)</td>
                <td class='py-2 px-4'>{$net_profit}</td>
              </tr>";
        ?>
    </tbody>
</table>

</div>

</body>
</html>
