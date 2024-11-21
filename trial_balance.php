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
                <a class="text-gray-600 font-bold" href="#">Trial Balance</a>
            </li>
        </ol>
    </nav>
</div>

<!-- Main content area -->
<div class="flex-1 bg-blue-100 p-6 h-full w-full">
   

    <!-- Date Filter -->
    <div class="mb-6 float-right">
        <label for="startDate" class="font-bold text-gray-700">Start Date:</label>
        <input type="date" id="startDate" class="border p-2 rounded-md mx-4" />

        <label for="endDate" class="font-bold text-gray-700">End Date:</label>
        <input type="date" id="endDate" class="border p-2 rounded-md" />

        <button onclick="filterByDate()" class="ml-4 bg-blue-500 text-white p-2 rounded-md">Apply Filter</button>
    </div>
    <h1 class="font-bold text-2xl text-blue-900 mb-8">TRIAL BALANCE</h1>
    <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
        <thead>
            <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
                <th class="px-4 py-2">Account Name</th>
                <th class="px-4 py-2">Debit Amount</th>
                <th class="px-4 py-2">Credit Amount</th>
            </tr>
        </thead>
        <tbody id="ledgerTable" class="text-gray-900 text-sm font-semilight">
            <!-- Data will be dynamically inserted -->
        </tbody>
    </table>

</div>

<script>
    // Full dataset for the Trial Balance with associated dates
    const data = [
        { account: "Cash", debit: 1000, credit: 2000, date: "2024-11-19" },
        { account: "eCash", debit: 0, credit: 2300, date: "2024-11-19" },
        { account: "Money in Bank", debit: 0, credit: 0, date: "2024-11-19" },
        { account: "Cheques", debit: 0, credit: 0, date: "2024-11-19" },
        { account: "Accounts Receivable", debit: 1000, credit: 2000, date: "2024-11-19" },
        { account: "Equipment/Assets", debit: 5000, credit: 1000, date: "2024-11-19" },
        { account: "Accounts Payable", debit: 0, credit: 0, date: "2024-11-19" },
        { account: "Tax Payable", debit: 0, credit: 0, date: "2024-11-19" },
        { account: "Owner's Equity", debit: 0, credit: 0, date: "2024-11-19" },
        { account: "Boundary", debit: 0, credit: 0, date: "2024-11-19" },
        { account: "Platform Fee", debit: 0, credit: 200, date: "2024-11-19" },
        { account: "Salaries Expense", debit: 2000, credit: 0, date: "2024-11-19" },
        { account: "Bonuses Expense", debit: 0, credit: 0, date: "2024-11-19" },
        { account: "Facility Cost", debit: 0, credit: 0, date: "2024-11-19" },
        { account: "Maintenance/Repair", debit: 500, credit: 0, date: "2024-11-19" },
        { account: "Training Cost", debit: 0, credit: 0, date: "2024-11-19" },
        { account: "Wellness Program Cost", debit: 0, credit: 2000, date: "2024-11-19" },
        { account: "Tax Payment", debit: 0, credit: 0, date: "2024-11-19" },
        { account: "Miscellaneous Expense", debit: 0, credit: 0, date: "2024-11-19" }
    ];

    function formatPeso(amount) {
        return `₱${amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`; // Format as Peso
    }

    function renderTable(filteredData) {
        const tableBody = document.getElementById("ledgerTable");
        tableBody.innerHTML = "";
        let totalDebit = 0;
        let totalCredit = 0;

        filteredData.forEach((item, i) => {
            const row = document.createElement("tr");
            row.className = i % 2 === 0 ? "border-b border-gray-200 hover:bg-blue-100" : "bg-gray-200 border-b border-gray-200 hover:bg-blue-100";

            totalDebit += item.debit;
            totalCredit += item.credit;

            row.innerHTML = `
                <td class="py-1 px-6 text-left border-r border-gray-300">${item.account}</td>
                <td class="py-1 px-6 text-left border-r border-gray-300">${formatPeso(item.debit)}</td>
                <td class="py-1 px-6 text-left border-r border-gray-300">${formatPeso(item.credit)}</td>
            `;
            tableBody.appendChild(row);
        });

        // Add a row for the totals
        const totalRow = document.createElement("tr");
        totalRow.className = "font-bold border-t border-gray-300 bg-blue-200 text-blue-800";
        totalRow.innerHTML = `
            <td class="py-2 px-6 text-left border-r border-gray-300">Total</td>
            <td class="py-2 px-6 text-left border-r border-gray-300">${formatPeso(totalDebit)}</td>
            <td class="py-2 px-6 text-left border-r border-gray-300">${formatPeso(totalCredit)}</td>
        `;
        tableBody.appendChild(totalRow);
    }

    // Function to filter the data by the selected date range
    function filterByDate() {
        const startDate = document.getElementById("startDate").value;
        const endDate = document.getElementById("endDate").value;

        if (startDate && endDate) {
            const filteredData = data.filter(item => {
                return item.date >= startDate && item.date <= endDate;
            });
            renderTable(filteredData);
        } else {
            renderTable(data);  // Render full data if no filter is applied
        }
    }

    // Initially render the table with all data
    renderTable(data);
</script>

</body>
</html>
