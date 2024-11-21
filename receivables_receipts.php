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
                <li><a class="text-gray-600 font-bold" href="TNVSFinance.php">Dashboard</a></li>
                <li><span class="mx-2">&gt;</span></li>
                <li><a class="text-gray-600 font-bold" href="#">Collections</a></li>
                <li><span class="mx-2">&gt;</span></li>
                <li><a class="text-gray-600 font-bold" href="#">Receivables Receipts</a></li>
            </ol>
        </nav>
    </div>

    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6 h-full w-full">

    <h1 class="font-bold text-2xl text-blue-900 mb-8">ACCOUNTS RECEIVABLE RECEIPTS</h1>

    <div class="mb-4 flex justify-between items-center">
        <!-- Search Bar -->
        <input
            type="text"
            id="searchInput"
            class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm"
            placeholder="Search Customer Name or Invoice Reference"
            onkeyup="filterTable()"
        />

        <!-- Filter by Payment Method -->
        <select
            id="paymentMethodFilter"
            class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm"
            onchange="filterTable()"
        >
            <option value="">All Payment Methods</option>
            <option value="Cash">Cash</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Debit Card">Debit Card</option>
            <option value="Bank Transfer">Bank Transfer</option>
        </select>
    </div>

    <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
        <thead>
            <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
                <th class="px-4 py-2">Receipt ID</th>
                <th class="px-4 py-2">Driver Name</th>
                <th class="px-4 py-2">Amount Received</th>
                <th class="px-4 py-2">Payment Date</th>
                <th class="px-4 py-2">Invoice Reference</th>
                <th class="px-4 py-2">Payment Method</th> <!-- New column -->
                <th class="px-4 py-2">Action</th>
            </tr>
        </thead>
        <tbody id="receiptsTable" class="text-gray-900 text-sm font-semilight">
            <!-- Data will be dynamically inserted -->
        </tbody>
    </table>

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

<script>
    const data = [
        { id: "R20241118001", name: "Juan Dela Cruz", amount: "₱500.00", date: "2024-11-18", invoice: "INV-001123", paymentMethod: "Cash" },
        { id: "R20241118002", name: "Maria Santos", amount: "₱1,200.00", date: "2024-11-19", invoice: "INV-001124", paymentMethod: "Credit Card" },
        { id: "R20241118003", name: "Jose Garcia", amount: "₱750.00", date: "2024-11-20", invoice: "INV-001125", paymentMethod: "Bank Transfer" },
        { id: "R20241118004", name: "Ana Reyes", amount: "₱300.00", date: "2024-11-21", invoice: "INV-001126", paymentMethod: "Cash" },
        { id: "R20241118005", name: "Pedro Aquino", amount: "₱950.00", date: "2024-11-22", invoice: "INV-001127", paymentMethod: "Credit Card" },
        { id: "R20241118006", name: "John Doe", amount: "₱400.00", date: "2024-11-23", invoice: "INV-001128", paymentMethod: "Cash" },
        { id: "R20241118007", name: "Jane Smith", amount: "₱1,500.00", date: "2024-11-24", invoice: "INV-001129", paymentMethod: "Bank Transfer" },
        { id: "R20241118008", name: "Michael Brown", amount: "₱850.00", date: "2024-11-25", invoice: "INV-001130", paymentMethod: "Debit Card" },
        { id: "R20241118009", name: "Linda Wilson", amount: "₱1,100.00", date: "2024-11-26", invoice: "INV-001131", paymentMethod: "Cash" },
        { id: "R20241118010", name: "David Harris", amount: "₱620.00", date: "2024-11-27", invoice: "INV-001132", paymentMethod: "Bank Transfer" },
        { id: "R20241118011", name: "Susan Clark", amount: "₱430.00", date: "2024-11-28", invoice: "INV-001133", paymentMethod: "Credit Card" },
        { id: "R20241118012", name: "James Lewis", amount: "₱700.00", date: "2024-11-29", invoice: "INV-001134", paymentMethod: "Cash" },
        { id: "R20241118013", name: "Patricia Walker", amount: "₱950.00", date: "2024-11-30", invoice: "INV-001135", paymentMethod: "Debit Card" },
        { id: "R20241118014", name: "Mark Allen", amount: "₱1,200.00", date: "2024-12-01", invoice: "INV-001136", paymentMethod: "Bank Transfer" },
        { id: "R20241118015", name: "Nancy King", amount: "₱550.00", date: "2024-12-02", invoice: "INV-001137", paymentMethod: "Credit Card" },
        { id: "R20241118016", name: "Gary Young", amount: "₱800.00", date: "2024-12-03", invoice: "INV-001138", paymentMethod: "Cash" },
        { id: "R20241118017", name: "Deborah Scott", amount: "₱670.00", date: "2024-12-04", invoice: "INV-001139", paymentMethod: "Debit Card" },
        { id: "R20241118018", name: "Thomas Adams", amount: "₱950.00", date: "2024-12-05", invoice: "INV-001140", paymentMethod: "Bank Transfer" },
        { id: "R20241118019", name: "Betty Mitchell", amount: "₱400.00", date: "2024-12-06", invoice: "INV-001141", paymentMethod: "Credit Card" },
        { id: "R20241118020", name: "Christopher Perez", amount: "₱1,100.00", date: "2024-12-07", invoice: "INV-001142", paymentMethod: "Debit Card" },
        { id: "R20241118021", name: "Dorothy Roberts", amount: "₱650.00", date: "2024-12-08", invoice: "INV-001143", paymentMethod: "Cash" },
        { id: "R20241118022", name: "Kevin Turner", amount: "₱1,300.00", date: "2024-12-09", invoice: "INV-001144", paymentMethod: "Credit Card" }
    ];

    let currentPage = 1;
    const rowsPerPage = 10;

    // Function to display table data
    function displayTable() {
        const tableBody = document.getElementById('receiptsTable');
        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;

        tableBody.innerHTML = "";

        const filteredData = filterData();

        for (let i = startIndex; i < endIndex && i < filteredData.length; i++) {
            const row = document.createElement('tr');
            row.className = "border-b border-gray-300 hover:bg-gray-200";

            row.innerHTML = `
                <td class="border px-6 py-3 border-r border-gray-300">${filteredData[i].id}</td>
                <td class="border px-6 py-3 border-r border-gray-300">${filteredData[i].name}</td>
                <td class="border px-6 py-3 border-r border-gray-300">${filteredData[i].amount}</td>
                <td class="border px-6 py-3 border-r border-gray-300">${filteredData[i].date}</td>
                <td class="border px-6 py-3 border-r border-gray-300">${filteredData[i].invoice}</td>
                <td class="border px-6 py-3 border-r border-gray-300">${filteredData[i].paymentMethod}</td>
                <td class="py-3 px-6 text-center border-r border-gray-300">
    <button class="bg-green-600 text-white px-3 py-1 rounded" onclick="#">Collect</button>
</td>
            `;
            tableBody.appendChild(row);
        }

        // Update pagination buttons
        document.getElementById('prevPage').disabled = currentPage === 1;
        document.getElementById('nextPage').disabled = currentPage === Math.ceil(filteredData.length / rowsPerPage);
    }

    // Function to filter data based on search input and payment method filter
    function filterData() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const paymentMethodFilter = document.getElementById('paymentMethodFilter').value;

        return data.filter(item => {
            const matchesSearch = item.name.toLowerCase().includes(searchInput) || item.invoice.toLowerCase().includes(searchInput);
            const matchesPaymentMethod = paymentMethodFilter ? item.paymentMethod === paymentMethodFilter : true;
            return matchesSearch && matchesPaymentMethod;
        });
    }

    // Function to filter the table based on input changes
    function filterTable() {
        currentPage = 1; // Reset to first page when filtering
        displayTable();
    }

    // Pagination functions
    function prevPage() {
        if (currentPage > 1) {
            currentPage--;
            displayTable();
        }
    }

    function nextPage() {
        const filteredData = filterData();
        if (currentPage < Math.ceil(filteredData.length / rowsPerPage)) {
            currentPage++;
            displayTable();
        }
    }

    // Initial display of the table
    displayTable();
</script>

</body>
</html>
