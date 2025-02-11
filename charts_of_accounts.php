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
        <a class="text-gray-600 font-bold" href="charts_of_accounts.php">Chart of Accounts</a>
       </li>
      </ol>
     </nav>
    </div>

    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6 h-full w-full">

    <h1 class="font-bold text-2xl text-blue-900 mb-8">CHART OF ACCOUNTS</h1>

    <div class="mb-4 flex justify-between items-center">
        <!-- Search Bar -->
        <input
            type="text"
            id="searchInput"
            class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm w-80"
            placeholder="Search Account Name or Code"
            onkeyup="filterTable()"
        />

        <!-- Filter by Account Type -->
        <select
            id="accountTypeFilter"
            class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm"
            onchange="filterTable()"
        >
            <option value="">All Account Types</option>
            <option value="Asset">Asset</option>
            <option value="Liability">Liability</option>
            <option value="Revenue">Revenue</option>
            <option value="Expense">Expense</option>
        </select>
    </div>

    <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
        <thead>
            <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
                <th class="px-4 py-2">Account Code</th>
                <th class="px-4 py-2">Account Name</th>
                <th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Description</th>
            </tr>
        </thead>
        <tbody id="accountTable" class="text-gray-900 text-sm font-semilight">
            <!-- Data will be dynamically inserted -->
        </tbody>
    </table>

    <div class="mt-4 flex justify-between items-center">
        <!-- Page Status (Bottom-Left) -->
        <div id="pageStatus" class="text-gray-700 font-bold float-left"></div>
    </div>

    <!-- Pagination Controls -->
    <div class="mt-4 flex justify-end">
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

<script>
    // Full dataset for the Chart of Accounts
    const data = [
        { code: 100, name: "Cash", type: "Asset", description: "Money in hand." },
        { code: 101, name: "eCash", type: "Asset", description: "Balances in digital wallets or apps." },
        { code: 102, name: "Money in Bank", type: "Asset", description: "Business bank account balances." },
        { code: 103, name: "Cheques", type: "Asset", description: "Cheques held as assets for potential large payments." },
        { code: 110, name: "Accounts Receivable", type: "Asset", description: "Customer payments owed." },
        { code: 120, name: "Equipment/Assets", type: "Asset", description: "Physical items like vehicles, machinery." },
        { code: 200, name: "Accounts Payable", type: "Liability", description: "Money owed to suppliers." },
        { code: 210, name: "Tax Payable", type: "Liability", description: "Taxes owed but not yet paid." },
        { code: 300, name: "Boundary", type: "Revenue", description: "Revenue from company vehicle services." },
        { code: 310, name: "Platform fee", type: "Revenue", description: "Shared revenue from drivers for using the platform" },
        { code: 400, name: "Salaries Expense", type: "Expense", description: "Employee wages and benefits." },
        { code: 410, name: "Bonuses Expense", type: "Expense", description: "Additional payments to employees." },
        { code: 420, name: "Facility Cost", type: "Expense", description: "Rent, utilities, or facility-related costs." },
        { code: 430, name: "Maintenance/Repair", type: "Expense", description: "Costs for maintaining or repairing assets." },
        { code: 440, name: "Training Cost", type: "Expense", description: "Expenses for employee training." },
        { code: 450, name: "Wellness Program Cost", type: "Expense", description: "Costs for employee wellness initiatives." },
        { code: 460, name: "Tax Payment", type: "Expense", description: "Paid taxes (income, property, etc.)." },
        { code: 470, name: "Miscellaneous Expense", type: "Expense", description: "General expenses not categorized above." },
    ];
    

    let currentPage = 1;
    const rowsPerPage = 10;

    function renderTable() {
        const tableBody = document.getElementById("accountTable");
        tableBody.innerHTML = "";

        const filteredData = filterData();

        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = Math.min(startIndex + rowsPerPage, filteredData.length);

        for (let i = startIndex; i < endIndex; i++) {
            const row = document.createElement("tr");
            row.className = "border-b border-gray-300 hover:bg-gray-200";

            row.innerHTML = `
                <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].code}</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].name}</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].type}</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].description}</td>
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
        const accountTypeFilter = document.getElementById("accountTypeFilter").value;

        return data.filter((item) => {
            const matchesSearch =
                item.name.toLowerCase().includes(searchInput) ||
                item.code.toString().includes(searchInput);
            const matchesType = accountTypeFilter === "" || item.type === accountTypeFilter;

            return matchesSearch && matchesType;
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
    document.getElementById("accountTypeFilter").addEventListener("change", filterTable);





 
    function renderTable() {
        const tableBody = document.getElementById("accountTable");
        tableBody.innerHTML = "";

        const filteredData = filterData();

        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = Math.min(startIndex + rowsPerPage, filteredData.length);

        for (let i = startIndex; i < endIndex; i++) {
            const row = document.createElement("tr");
            
            // Condition to apply different color for "Asset" type
            if (filteredData[i].type === "Asset") {
                row.className = "border-b border-gray-300 hover:bg-green-100";  // Greenish background for Asset
            } else if (filteredData[i].type === "Liability") {
                row.className = "border-b border-gray-300 hover:bg-yellow-100";  // Yellowish background for Liability
            } else if (filteredData[i].type === "Equity") {
                row.className = "border-b border-gray-300 hover:bg-blue-100";  // Blueish background for Equity
            } else if (filteredData[i].type === "Revenue") {
                row.className = "border-b border-gray-300 hover:bg-orange-100";  // Orangish background for Revenue
            } else if (filteredData[i].type === "Expense") {
                row.className = "border-b border-gray-300 hover:bg-red-100";  // Reddish background for Expense
            }

            row.innerHTML = `
                <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].code}</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].name}</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].type}</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].description}</td>
            `;
            tableBody.appendChild(row);
        }

        // Update page status
        const pageStatus = document.getElementById("pageStatus");
        pageStatus.innerText = `Showing ${startIndex + 1} - ${Math.min(endIndex, filteredData.length)} of ${filteredData.length}`;

        document.getElementById("prevPage").disabled = currentPage === 1;
        document.getElementById("nextPage").disabled = endIndex === filteredData.length;
    }

</script>

</body>
</html>
