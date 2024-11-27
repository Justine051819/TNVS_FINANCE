<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
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
                <li><a class="text-gray-600 font-bold" href="TNVSFinance.php">Dashboard</a></li>
                <li><span class="mx-2">&gt;</span></li>
                <li><a class="text-gray-600 font-bold" href="#">Expense Reports</a></li>
            </ol>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 bg-blue-100 p-6 h-full w-full">
        <h1 class="font-bold text-2xl text-blue-900 mb-6">EXPENSE REPORTS</h1>

        <!-- Filters -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex space-x-4">
                <input 
                    type="text" 
                    id="searchInput" 
                    class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm w-80" 
                    placeholder="Search Department or Expense Category" 
                    onkeyup="filterTable()"
                />
                <input 
                    type="date" 
                    id="startDate" 
                    class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm" 
                    onchange="filterTable()"
                />
               <h5 class="mt-3">-</h5>
                <input 
                    type="date" 
                    id="endDate" 
                    class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm" 
                    onchange="filterTable()"
                />
            </div>
        </div>

        <!-- Table -->
        <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
            <thead>
                <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Department</th>
                    <th class="px-4 py-2">Expense Category</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Amount Spent</th>
                    <th class="px-4 py-2">Date</th>
                </tr>
            </thead>
            <tbody id="expenseReportTable" class="text-gray-900 text-sm font-light">
                <!-- Data will be dynamically inserted -->
            </tbody>
        </table>

        <!-- Pagination Controls -->
        <div class="mt-4 flex justify-between items-center">
            <!-- Page Status (Bottom-Left) -->
            <div id="pageStatus" class="text-gray-700 font-bold"></div>

            <!-- Navigation Buttons (Bottom-Right) -->
            <div>
                <button 
                    id="prevPage" 
                    class="bg-blue-500 text-white px-4 py-2 rounded mr-2 disabled:opacity-50" 
                    onclick="prevPage()"
                >
                    Previous
                </button>
                <button 
                    id="nextPage" 
                    class="bg-blue-500 text-white px-4 py-2 rounded disabled:opacity-50" 
                    onclick="nextPage()"
                >
                    Next
                </button>
            </div>
        </div>
    </div>

    <script>
        // Sample Expense Report Data
        const expenseReports = [
            { id: "EXP-2024-001", department: "Human Resource-1", category: "Salaries", description: "Monthly salary payment", amount: "₱100,000.00", date: "2024-11-01" },
            { id: "EXP-2024-002", department: "Core-1", category: "Equipments/Assets", description: "New server purchase", amount: "₱150,000.00", date: "2024-11-03" },
            { id: "EXP-2024-003", department: "Admin", category: "Facility Cost", description: "Office rent payment", amount: "₱200,000.00", date: "2024-11-05" },
            { id: "EXP-2024-004", department: "Logistics-1", category: "Maintenance/Repair", description: "Vehicle maintenance", amount: "₱30,000.00", date: "2024-11-07" },
            { id: "EXP-2024-005", department: "Human Resource-2", category: "Training Cost", description: "Employee skills development", amount: "₱25,000.00", date: "2024-11-10" },
            { id: "EXP-2024-006", department: "Core-2", category: "Wellness Program Cost", description: "Employee wellness program", amount: "₱15,000.00", date: "2024-11-12" },
            { id: "EXP-2024-007", department: "Human Resource-3", category: "Bonuses", description: "Annual performance bonuses", amount: "₱50,000.00", date: "2024-11-14" },
            { id: "EXP-2024-008", department: "Logistics-2", category: "Salaries", description: "Salary payment for logistics team", amount: "₱40,000.00", date: "2024-11-16" },
            { id: "EXP-2024-009", department: "Finance", category: "Tax Payment", description: "Corporate tax payment", amount: "₱200,000.00", date: "2024-11-18" },
            { id: "EXP-2024-010", department: "Human Resource-4", category: "Salaries", description: "Employee salary payment", amount: "₱75,000.00", date: "2024-11-20" },
            { id: "EXP-2024-011", department: "Core-1", category: "Equipments/Assets", description: "New computers purchase", amount: "₱100,000.00", date: "2024-11-22" },
            { id: "EXP-2024-012", department: "Admin", category: "Facility Cost", description: "Cleaning services for office", amount: "₱10,000.00", date: "2024-11-23" },
            { id: "EXP-2024-013", department: "Finance", category: "Tax Payment", description: "VAT payment for the quarter", amount: "₱80,000.00", date: "2024-11-24" },
            { id: "EXP-2024-014", department: "Logistics-1", category: "Maintenance/Repair", description: "Repair of delivery trucks", amount: "₱60,000.00", date: "2024-11-25" },
            { id: "EXP-2024-015", department: "Core-2", category: "Salaries", description: "Salary payment for core team", amount: "₱120,000.00", date: "2024-11-26" },
            { id: "EXP-2024-016", department: "Human Resource-2", category: "Training Cost", description: "Leadership training for employees", amount: "₱20,000.00", date: "2024-11-27" },
            { id: "EXP-2024-017", department: "Logistics-2", category: "Transportation", description: "Fuel for company vehicles", amount: "₱35,000.00", date: "2024-11-28" },
            { id: "EXP-2024-018", department: "Finance", category: "Bonuses", description: "Holiday bonuses for finance team", amount: "₱40,000.00", date: "2024-11-29" },
            { id: "EXP-2024-019", department: "Admin", category: "Wellness Program Cost", description: "Employee fitness programs", amount: "₱12,000.00", date: "2024-11-30" },
            { id: "EXP-2024-020", department: "Logistics-1", category: "Extra", description: "Miscellaneous maintenance", amount: "₱10,000.00", date: "2024-12-01" }
        ];

        // Pagination variables
        let currentPage = 1;
        const rowsPerPage = 10;

        // Function to filter and display the data
        function filterTable() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            const filteredReports = expenseReports.filter(report => {
                const reportDate = report.date;

                // Search filter
                const matchesSearch = 
                    report.department.toLowerCase().includes(searchInput) || 
                    report.category.toLowerCase().includes(searchInput);

                // Date filter
                const matchesDate =
                    (startDate === "" || reportDate >= startDate) &&
                    (endDate === "" || reportDate <= endDate);

                return matchesSearch && matchesDate;
            });

            displayTable(filteredReports);
        }

        // Function to display the table
        function displayTable(data) {
            const tableBody = document.getElementById('expenseReportTable');
            tableBody.innerHTML = "";

            // Paginate the data
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const paginatedData = data.slice(start, end);

            paginatedData.forEach(report => {
                const row = `
                    <tr class="border-b border-blue-100">
                        <td class="px-4 py-2">${report.id}</td>
                        <td class="px-4 py-2">${report.department}</td>
                        <td class="px-4 py-2">${report.category}</td>
                        <td class="px-4 py-2">${report.description}</td>
                        <td class="px-4 py-2">${report.amount}</td>
                        <td class="px-4 py-2">${report.date}</td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });

            // Update page status
            const pageStatus = document.getElementById('pageStatus');
            pageStatus.innerHTML = `Showing ${start + 1} to ${Math.min(end, data.length)} of ${data.length} entries`;
        }

        // Pagination functions
        function prevPage() {
            if (currentPage > 1) {
                currentPage--;
                filterTable();
            }
        }

        function nextPage() {
            const filteredData = expenseReports.filter(report => {
                const searchInput = document.getElementById('searchInput').value.toLowerCase();
                const matchesSearch = 
                    report.department.toLowerCase().includes(searchInput) || 
                    report.category.toLowerCase().includes(searchInput);
                return matchesSearch;
            });

            if (currentPage * rowsPerPage < filteredData.length) {
                currentPage++;
                filterTable();
            }
        }

        // Initial display
        filterTable();
    </script>
</body>
</html>