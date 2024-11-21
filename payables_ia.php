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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
</head>
<body class="bg-white">

    <?php include('navbar_sidebar.php'); ?>

    <!-- Breadcrumb -->
    <div class="bg-blue-200 p-4 shadow-lg">
        <nav class="text-gray-600 font-bold">
            <ol class="list-reset flex">
                <li><a class="text-gray-600 font-bold" href="TNVSFinance.php">Dashboard</a></li>
                <li><span class="mx-2">&gt;</span></li>
                <li><a class="text-gray-600 font-bold" href="#">Accounts Payables</a></li>
                <li><span class="mx-2">&gt;</span></li>
                <li><a class="text-gray-600 font-bold" href="#">Invoice Approval</a></li>
            </ol>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 bg-blue-100 p-6 h-full w-full">
        <h1 class="font-bold text-2xl text-blue-900 mb-6">INVOICE APPROVAL</h1>

        <!-- Filters -->
        <div class="flex justify-between items-center mb-4">
            <input 
                type="text" 
                id="searchInput" 
                class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm" 
                placeholder="Search Department or Account Name" 
                onkeyup="filterTable()"
            />
        </div>

        <!-- Table -->
        <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
            <thead>
                <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
                    <th class="px-4 py-2">Invoice ID</th>
                    <th class="px-4 py-2">Department</th>
                    <th class="px-4 py-2">Account Name</th>
                    <th class="px-4 py-2">Amount</th>
                    <th class="px-4 py-2">Payment Due</th>
                    <th class="px-4 py-2">Invoice</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody id="invoiceTable" class="text-gray-900 text-sm font-light">
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
        // Sample Invoice Data
        const invoices = [
    { id: "CORE1-2024-001", department: "Core-1", account: "Employee Benefits", amount: "₱100,000.00", due: "2024-11-01" },
    { id: "CORE2-2024-002", department: "Core-2", account: "Software Licensing", amount: "₱50,000.00", due: "2024-11-03" },
    { id: "HR1-2024-003", department: "Human Resource-1", account: "Recruitment Services", amount: "₱20,000.00", due: "2024-11-05" },
    { id: "HR2-2024-004", department: "Human Resource-2", account: "Training Materials", amount: "₱43,000.00", due: "2024-11-07" },
    { id: "HR3-2024-005", department: "Human Resource-3", account: "Payroll Services", amount: "₱45,000.00", due: "2024-11-10" },
    { id: "HR4-2024-006", department: "Human Resource-4", account: "Health Benefits", amount: "₱63,000.00", due: "2024-11-12" },
    { id: "LOG1-2024-007", department: "Logistics-1", account: "Transportation Costs", amount: "₱52,000.00", due: "2024-11-14" },
    { id: "LOG2-2024-008", department: "Logistics-2", account: "Storage Fees", amount: "₱36,000.00", due: "2024-11-16" },
    { id: "ADMIN-2024-009", department: "Admin", account: "Office Supplies", amount: "₱42,000.00", due: "2024-11-18" },
    { id: "FIN-2024-010", department: "Finance", account: "Audit Services", amount: "₱50,000.00", due: "2024-11-20" },
    { id: "CORE1-2024-011", department: "Core-1", account: "Project Management", amount: "₱75,000.00", due: "2024-11-21" },
    { id: "CORE2-2024-012", department: "Core-2", account: "Server Maintenance", amount: "₱55,000.00", due: "2024-11-22" },
    { id: "HR1-2024-013", department: "Human Resource-1", account: "Employee Training", amount: "₱35,000.00", due: "2024-11-23" },
    { id: "HR2-2024-014", department: "Human Resource-2", account: "Conference Sponsorships", amount: "₱22,000.00", due: "2024-11-24" },
    { id: "HR3-2024-015", department: "Human Resource-3", account: "HR Software", amount: "₱18,000.00", due: "2024-11-25" },
    { id: "HR4-2024-016", department: "Human Resource-4", account: "Event Planning", amount: "₱28,000.00", due: "2024-11-26" },
    { id: "LOG1-2024-017", department: "Logistics-1", account: "Fleet Maintenance", amount: "₱48,000.00", due: "2024-11-27" },
    { id: "LOG2-2024-018", department: "Logistics-2", account: "Warehouse Upgrades", amount: "₱40,000.00", due: "2024-11-28" },
    { id: "ADMIN-2024-019", department: "Admin", account: "Office Renovation", amount: "₱72,000.00", due: "2024-11-29" },
    { id: "FIN-2024-020", department: "Finance", account: "Tax Advisory Services", amount: "₱65,000.00", due: "2024-11-30" },
    { id: "CORE1-2024-021", department: "Core-1", account: "Research and Development", amount: "₱92,000.00", due: "2024-12-01" },
    { id: "CORE2-2024-022", department: "Core-2", account: "System Integration", amount: "₱67,000.00", due: "2024-12-02" },
    { id: "HR1-2024-023", department: "Human Resource-1", account: "Staff Recruitment", amount: "₱29,000.00", due: "2024-12-03" },
    { id: "HR2-2024-024", department: "Human Resource-2", account: "Employee Wellness Programs", amount: "₱31,000.00", due: "2024-12-04" },
    { id: "HR3-2024-025", department: "Human Resource-3", account: "Compensation Analysis", amount: "₱25,000.00", due: "2024-12-05" },
    { id: "HR4-2024-026", department: "Human Resource-4", account: "Annual Party", amount: "₱15,000.00", due: "2024-12-06" },
    { id: "LOG1-2024-027", department: "Logistics-1", account: "Packaging Supplies", amount: "₱33,000.00", due: "2024-12-07" },
    { id: "LOG2-2024-028", department: "Logistics-2", account: "Freight Charges", amount: "₱37,000.00", due: "2024-12-08" },
    { id: "ADMIN-2024-029", department: "Admin", account: "Utility Bills", amount: "₱50,000.00", due: "2024-12-09" },
    { id: "FIN-2024-030", department: "Finance", account: "Analysis Services", amount: "₱58,000.00", due: "2024-12-10" }
];

        let currentPage = 1;
        const rowsPerPage = 10;

        function renderTable() {
            const tableBody = document.getElementById("invoiceTable");
            tableBody.innerHTML = "";

            const filteredData = filterData();

            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = Math.min(startIndex + rowsPerPage, filteredData.length);

            for (let i = startIndex; i < endIndex; i++) {
                const row = document.createElement("tr");
                row.className = "border-b border-gray-300 hover:bg-gray-200";

                row.innerHTML = `
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].id}</td>
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].department}</td>
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].account}</td>
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].amount}</td>
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].due}</td>
                    <td class="py-3 px-6 text-center border-r border-gray-300"><button class="bg-blue-500 text-white px-3 py-1 rounded">view</button></td>
                    <td class="py-3 px-6 text-center border-r border-gray-300">
                        <button class="bg-green-500 text-white px-3 py-1 rounded">Approve</button>
                        <button class="bg-red-500 text-white px-3 py-1 rounded">Reject</button>
                    </td>
                `;
                tableBody.appendChild(row);
            }

            // Update page status
            const pageStatus = document.getElementById("pageStatus");
            pageStatus.classList.add('ml-4');
            pageStatus.innerText = `Showing ${currentPage} of ${Math.ceil(filteredData.length / rowsPerPage)}`;

            // Disable pagination buttons if necessary
            document.getElementById("prevPage").disabled = currentPage === 1;
            document.getElementById("nextPage").disabled = endIndex === filteredData.length;
        }

        function filterData() {
            const searchInput = document.getElementById("searchInput").value.toLowerCase();

            return invoices.filter((invoice) => {
                const matchesSearch =
                    invoice.department.toLowerCase().includes(searchInput) ||
                    invoice.account.toLowerCase().includes(searchInput);
                return matchesSearch;
            });
        }

        function filterTable() {
            currentPage = 1;
            renderTable();
        }

        function nextPage() {
            currentPage++;
            renderTable();
        }

        function prevPage() {
            currentPage--;
            renderTable();
        }

        // Initial Table Render
        renderTable();
    </script>
</body>
</html>
