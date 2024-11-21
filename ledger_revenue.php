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
        <a class="text-gray-600 font-bold" href="#">Ledger</a>
       </li>
      </ol>
     </nav>
    </div>

    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6 h-full w-full">
      <div class="float-right">
    <a class="bg-blue-700 text-white px-2 py-1 mx-1 rounded text-lg cursor-pointer whitespace-nowrap mb-4 float-right shadow-lg" href="ledger_liabilities.php" role="button">Liabilities</a>
    <a class="bg-white border-2 border-blue-800 text-blue-800 font-bold px-2 py-1 mx-1 rounded text-lg cursor-pointer whitespace-nowrap mb-4 float-right shadow-lg" href="ledger_revenue.php" role="button">Revenue</a>
    <a class="bg-blue-700 text-white px-2 py-1 mx-1 rounded text-lg cursor-pointer whitespace-nowrap mb-4 float-right shadow-lg" href="ledger_expense.php" role="button">Expense</a>
    <a class="bg-blue-700 text-white px-2 py-1 mx-1 rounded text-lg cursor-pointer whitespace-nowrap mb-4 float-right shadow-lg" href="ledger.php" role="button">Asset</a>
</div>
    <h1 class="font-bold text-2xl text-blue-900 mb-8">LEDGER (REVENUE)</h1>

        <div class="mb-4 flex justify-between items-center">
            <!-- Search Bar -->
            <input
                type="text"
                id="searchInput"
                class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm"
                placeholder="Search Account Name"
                onkeyup="filterTable()"
            />

            <!-- Filter by Date -->
            <input
                type="date"
                id="dateFilter"
                class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm"
                onchange="filterTable()"
            />
        </div>

        <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
            <thead>
                <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Account Name</th>
                    <th class="px-4 py-2">Debit Amount</th>
                    <th class="px-4 py-2">Credit Amount</th>
                    <th class="px-4 py-2">Balance</th>
                </tr>
            </thead>
            <tbody id="trialBalanceTable" class="text-gray-900 text-sm font-semilight">
                <!-- Data will be dynamically inserted -->
            </tbody>
        </table>

        <div class="mt-4 flex justify-between items-center">
            <!-- Page Status (Bottom-Left) -->
            <div id="pageStatus" class="text-gray-700 font-bold float-left"></div>
            <div class="mt-4 flex justify-end">
                <!-- Pagination Controls -->
                <button
                    id="prevPage"
                    class="bg-blue-500 text-white px-4 py-2 rounded mr-2"
                    onclick="prevPage()"
                >
                    Previous
                </button>
                <button
                    id="nextPage"
                    class="bg-blue-500 text-white px-4 py-2 rounded"
                    onclick="nextPage()"
                >
                    Next
                </button>
            </div>
        </div>

    </div>

    <script>
        const trialBalanceData = [
            
                    { "date": "2024-11-19", "account": "Boundary", "debit": "", "credit": "₱3,000.00", "balance": "₱3,000.00" },
                    { "date": "2024-11-19", "account": "Platform Fee", "debit": "", "credit": "₱500.00", "balance": "₱500.00" },
                    { "date": "2024-11-19", "account": "Boundary", "debit": "", "credit": "₱1,000.00", "balance": "₱4,000.00" },
                    { "date": "2024-11-20", "account": "Platform Fee", "debit": "", "credit": "₱300.00", "balance": "₱800.00" },
                    { "date": "2024-11-20", "account": "Boundary", "debit": "", "credit": "₱1,500.00", "balance": "₱5,500.00" },
                    { "date": "2024-11-20", "account": "Platform Fee", "debit": "", "credit": "₱200.00", "balance": "₱1,000.00" },
                    { "date": "2024-11-21", "account": "Boundary", "debit": "", "credit": "₱2,000.00", "balance": "₱7,500.00" },
                    { "date": "2024-11-21", "account": "Platform Fee", "debit": "", "credit": "₱700.00", "balance": "₱1,700.00" },
                    { "date": "2024-11-22", "account": "Boundary", "debit": "", "credit": "₱500.00", "balance": "₱8,000.00" },
                    { "date": "2024-11-22", "account": "Platform Fee", "debit": "", "credit": "₱600.00", "balance": "₱2,300.00" }


        ];

        let currentPage = 1;
        const rowsPerPage = 10;

        function renderTable() {
            const tableBody = document.getElementById("trialBalanceTable");
            tableBody.innerHTML = "";

            const filteredData = filterData();

            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = Math.min(startIndex + rowsPerPage, filteredData.length);

            for (let i = startIndex; i < endIndex; i++) {
                const row = document.createElement("tr");
                row.className = "border-b border-gray-300 hover:bg-gray-200";

                row.innerHTML = `
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].date}</td>
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].account}</td>
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].debit}</td>
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].credit}</td>
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].balance}</td>
                `;
                tableBody.appendChild(row);
            }

            // Update page status
            const pageStatus = document.getElementById("pageStatus");
            pageStatus.innerText = `Showing ${startIndex + 1} - ${Math.min(endIndex, filteredData.length)} of ${filteredData.length}`;

            document.getElementById("prevPage").disabled = currentPage === 1;
            document.getElementById("nextPage").disabled = endIndex === filteredData.length;
        }

        function filterData() {
            const searchInput = document.getElementById("searchInput").value.toLowerCase();
            const dateFilter = document.getElementById("dateFilter").value;

            return trialBalanceData.filter((item) => {
                const matchesSearch = item.account.toLowerCase().includes(searchInput);
                const matchesDate = !dateFilter || item.date === dateFilter;

                return matchesSearch && matchesDate;
            });
        }

        function filterTable() {
            currentPage = 1;  // Reset to first page on search/filter change
            renderTable();    // Re-render the table with filtered data
        }

        function nextPage() {
            currentPage++;
            renderTable();
        }

        function prevPage() {
            currentPage--;
            renderTable();
        }

        // Initially render the table
        renderTable();

        // Attach event listeners
        document.getElementById("searchInput").addEventListener("keyup", filterTable);
        document.getElementById("dateFilter").addEventListener("change", filterTable);
    </script>

</body>
</html>
