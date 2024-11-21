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
                <a class="text-gray-600 font-bold" href="#">Balance Sheet</a>
            </li>
        </ol>
    </nav>
</div>

<!-- Main content area -->
<div class="flex-1 bg-blue-100 p-6 h-full w-full">
<div class="float-right">
    <a class="bg-white border-2 border-blue-800 text-blue-800 font-bold px-2 py-1 mx-1 rounded text-lg cursor-pointer whitespace-nowrap mb-4 float-right shadow-lg" href="balance_sheet.php" role="button">BALANCE SHEET</a>
    <a class=" bg-blue-700 text-white   px-2 py-1 mx-1 rounded text-lg cursor-pointer whitespace-nowrap mb-4 float-right shadow-lg" href="financial_statement.php" role="button">INCOME STATEMENT</a>
</div>
    <h1 class="font-bold text-2xl text-blue-900 mb-10">BALANCE SHEET</h1>

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
        <!-- Assets Section -->
        <?php
        $assets = [
            ['Cash', '₱5,000.00'],
            ['eCash', '₱3,000.00'],
            ['Money in Bank', '₱8,000.00'],
            ['Cheques', '₱1,500.00'],
            ['Accounts Receivable', '₱4,000.00'],
            ['Equipment/Assets', '₱20,000.00']
        ];
        $total_assets = '₱41,500.00';
        
        // Assets Title Row with gray background
        echo '<tr class="bg-gray-200 text-blue-800 font-bold">
                <td class="py-2 px-4">Assets</td>
                <td class="py-2 px-4" colspan="2"> </td>
              </tr>';
        
        // Loop through assets and display them
        foreach ($assets as $asset) {
            echo "<tr class='border-b border-gray-200'>
                    <td></td>
                    <td class='py-2 px-4'>{$asset[0]}</td>
                    <td class='py-2 px-4'>{$asset[1]}</td>
                  </tr>";
        }
        echo "<tr class='font-bold text-blue-800 bg-blue-100'>
                <td class='py-2 px-4'></td>
                <td class='py-2 px-4'>Total Assets</td>
                <td class='py-2 px-4'>{$total_assets}</td>
              </tr>";
        ?>

        <!-- Liabilities Section -->
        <?php
        $liabilities = [
            ['Accounts Payable', '₱6,000.00'],
            ['Tax Payable', '₱2,000.00']
        ];
        $total_liabilities = '₱8,000.00';
        
        // Liabilities Title Row with gray background
        echo '<tr class="bg-gray-200 text-blue-800 font-bold">
                <td class="py-2 px-4">Liabilities</td>
                <td class="py-2 px-4" colspan="2"> </td>
              </tr>';
        
        // Loop through liabilities and display them
        foreach ($liabilities as $liability) {
            echo "<tr class='border-b'>
                    <td></td>
                    <td class='py-2 px-4'>{$liability[0]}</td>
                    <td class='py-2 px-4'>{$liability[1]}</td>
                  </tr>";
        }
        echo "<tr class='font-bold text-blue-800 bg-blue-100'>
                <td class='py-2 px-4'></td>
                <td class='py-2 px-4'>Total Liabilities</td>
                <td class='py-2 px-4'>{$total_liabilities}</td>
              </tr>";
        ?>
    </tbody>
</table>

</div>

</body>
</html>
