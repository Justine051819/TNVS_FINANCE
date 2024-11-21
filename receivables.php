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
                <li><a class="text-gray-600 font-bold" href="#">Accounts Receivables</a></li>
                <li><span class="mx-2">&gt;</span></li>
                <li><a class="text-gray-600 font-bold" href="#">Receivables</a></li>
            </ol>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 bg-blue-100 p-6 h-full w-full">
        <h1 class="font-bold text-2xl text-blue-900 mb-6">RECEIVABLES</h1>

        <!-- Filters -->
        <div class="flex justify-between items-center mb-4">
            <input 
                type="text" 
                id="searchInput" 
                class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm w-80" 
                placeholder="Search Customer Name or Description" 
                onkeyup="filterTable()"
            />
        </div>

        <!-- Table -->
        <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
            <thead>
                <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
                    <th class="px-4 py-2">Invoice ID</th>
                    <th class="px-4 py-2">Driver Name</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Amount</th>
                    <th class="px-4 py-2">Amount Paid</th>
                    <th class="px-4 py-2">Remaining Balance</th>
                    <th class="px-4 py-2">Fully-Paid Due Date</th>
                </tr>
            </thead>
            <tbody id="invoiceTable" class="text-gray-900 text-sm font-light">
                <!-- Data will be dynamically inserted -->
            </tbody>
        </table>
        <!-- Modal Structure -->
<div id="paymentModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
        <h3 class="text-xl font-semibold text-blue-900 mb-4">Payment Details</h3>
        <form id="paymentForm">
            <div class="mb-4">
                <label for="paymentMethod" class="block text-gray-700">Payment Method</label>
                <select id="paymentMethod" class="border border-gray-300 rounded-lg w-full py-2 px-4 mt-2">
                    <option value="bank">Bank Transfer</option>
                    <option value="cash">Cash</option>
                    <option value="ecash">Ecash</option>
                    <option value="cheque">Cheque</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="amount" class="block text-gray-700">Amount Pay</label>
                <input type="number" id="amount" class="border border-gray-300 rounded-lg w-full py-2 px-4 mt-2" placeholder="Enter amount" required />
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Submit</button>
            </div>
        </form>
    </div>
</div>



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
          
          { id: "INV-001123", department: "Juan Dela Cruz", account: "Boundary", amount: "₱500.00", paid: "₱300.00", remaining: "₱200.00", due: "2024-11-18" },
        { id: "INV-001124", department: "Maria Santos", account: "Boundary", amount: "₱1,200.00", paid: "₱1,000.00", remaining: "₱200.00", due: "2024-11-19" },
        { id: "INV-001125", department: "Jose Garcia", account: "Boundary", amount: "₱750.00", paid: "₱500.00", remaining: "₱250.00", due: "2024-11-20" },
        { id: "INV-001126", department: "Ana Reyes", account: "Boundary", amount: "₱300.00", paid: "₱150.00", remaining: "₱150.00", due: "2024-11-21" },
        { id: "INV-001127", department: "Pedro Aquino", account: "Boundary", amount: "₱950.00", paid: "₱700.00", remaining: "₱250.00", due: "2024-11-22" },
        { id: "INV-001128", department: "John Doe", account: "Boundary", amount: "₱400.00", paid: "₱250.00", remaining: "₱150.00", due: "2024-11-23" },
        { id: "INV-001129", department: "Jane Smith", account: "Boundary", amount: "₱1,500.00", paid: "₱1,200.00", remaining: "₱300.00", due: "2024-11-24" },
        { id: "INV-001130", department: "Michael Brown", account: "Boundary", amount: "₱850.00", paid: "₱400.00", remaining: "₱450.00", due: "2024-11-25" },
        { id: "INV-001131", department: "Linda Wilson", account: "Boundary", amount: "₱1,100.00", paid: "₱500.00", remaining: "₱600.00", due: "2024-11-26" },
        { id: "INV-001132", department: "David Harris", account: "Boundary", amount: "₱620.00", paid: "₱300.00", remaining: "₱320.00", due: "2024-11-27" },
        { id: "INV-001133", department: "Susan Clark", account: "Boundary", amount: "₱430.00", paid: "₱200.00", remaining: "₱230.00", due: "2024-11-28" },
        { id: "INV-001134", department: "James Lewis", account: "Boundary", amount: "₱700.00", paid: "₱500.00", remaining: "₱200.00", due: "2024-11-29" },
        { id: "INV-001135", department: "Patricia Walker", account: "Boundary", amount: "₱950.00", paid: "₱700.00", remaining: "₱250.00", due: "2024-11-30" },
        { id: "INV-001136", department: "Mark Allen", account: "Boundary", amount: "₱1,200.00", paid: "₱1,000.00", remaining: "₱200.00", due: "2024-12-01" },
        { id: "INV-001137", department: "Nancy King", account: "Boundary", amount: "₱550.00", paid: "₱300.00", remaining: "₱250.00", due: "2024-12-02" },
        { id: "INV-001138", department: "Gary Young", account: "Boundary", amount: "₱800.00", paid: "₱500.00", remaining: "₱300.00", due: "2024-12-03" },
        { id: "INV-001139", department: "Deborah Scott", account: "Boundary", amount: "₱670.00", paid: "₱400.00", remaining: "₱270.00", due: "2024-12-04" },
        { id: "INV-001140", department: "Thomas Adams", account: "Boundary", amount: "₱950.00", paid: "₱700.00", remaining: "₱250.00", due: "2024-12-05" },
        { id: "INV-001141", department: "Betty Mitchell", account: "Boundary", amount: "₱400.00", paid: "₱200.00", remaining: "₱200.00", due: "2024-12-06" },
        { id: "INV-001142", department: "Christopher Perez", account: "Boundary", amount: "₱1,100.00", paid: "₱800.00", remaining: "₱300.00", due: "2024-12-07" },
        { id: "INV-001143", department: "Dorothy Roberts", account: "Boundary", amount: "₱650.00", paid: "₱450.00", remaining: "₱200.00", due: "2024-12-08" },
        { id: "INV-001144", department: "Kevin Turner", account: "Boundary", amount: "₱1,300.00", paid: "₱1,000.00", remaining: "₱300.00", due: "2024-12-09" }



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
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].paid}</td>
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].remaining}</td>
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].due}</td>
           

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
