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
                <li><a class="text-gray-600 font-bold" href="#">Accounts Payable</a></li>
                <li><span class="mx-2">&gt;</span></li>
                <li><a class="text-gray-600 font-bold" href="#">Receipts</a></li>
            </ol>
        </nav>
    </div>

    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6 h-full w-full">

    <h1 class="font-bold text-2xl text-blue-900 mb-8">ACCOUNTS PAYABLE RECEIPTS</h1>

    <div class="mb-4 flex justify-between items-center">
        <!-- Search Bar -->
        <input
            type="text"
            id="searchInput"
            class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm"
            placeholder="Search Department or Description"
            onkeyup="filterTable()"
        />

        <!-- Filter by Payment Method -->
        <select
            id="paymentMethodFilter"
            class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm"
            onchange="filterTable()"
        >
            <option value="">All Payment Methods</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Cash">Cash</option>
            <option value="eCash">eCash</option>
            <option value="Cheque">Cheque</option>
        </select>
    </div>

    <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl overflow-y-auto">
        <thead>
            <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
                <th class="px-4 py-2">Receipt ID</th>
                <th class="px-4 py-2">Department</th>
                <th class="px-4 py-2">Description</th>
                <th class="px-4 py-2">Amount</th>
                <th class="px-4 py-2">Payment Method</th>
                <th class="px-4 py-2">Date Received</th>
                <th class="px-4 py-2">Receipts</th> <!-- View Button Column -->
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
    { id: "core1-001", department: "Core-1", description: "Employee Benefits", amount: "₱500.00", paymentMethod: "Bank Transfer", date: "2024-11-01" },
    { id: "core2-002", department: "Core-2", description: "Software Licensing", amount: "₱1,200.00", paymentMethod: "Credit Card", date: "2024-11-03" },
    { id: "hr1-003", department: "Human Resource-1", description: "Advertising", amount: "₱700.00", paymentMethod: "Cash", date: "2024-11-05" },
    { id: "hr2-004", department: "Human Resource-2", description: "Office Supplies", amount: "₱1,000.00", paymentMethod: "Cheque", date: "2024-11-07" },
    { id: "hr3-005", department: "Human Resource-3", description: "Audit Services", amount: "₱300.00", paymentMethod: "Bank Transfer", date: "2024-11-09" },
    { id: "hr4-006", department: "Human Resource-4", description: "Employee Training", amount: "₱1,500.00", paymentMethod: "eCash", date: "2024-11-10" },
    { id: "log1-007", department: "Logistics-1", description: "Hardware Purchase", amount: "₱2,500.00", paymentMethod: "Bank Transfer", date: "2024-11-11" },
    { id: "log2-008", department: "Logistics-2", description: "Event Sponsorship", amount: "₱3,000.00", paymentMethod: "Credit Card", date: "2024-11-12" },
    { id: "admin-009", department: "Admin", description: "Vehicle Maintenance", amount: "₱1,200.00", paymentMethod: "Cash", date: "2024-11-13" },
    { id: "fin-010", department: "Finance", description: "Consulting Fees", amount: "₱1,000.00", paymentMethod: "Cheque", date: "2024-11-14" },
    { id: "core1-011", department: "Core-1", description: "Recruitment Services", amount: "₱2,000.00", paymentMethod: "eCash", date: "2024-11-15" },
    { id: "core2-012", department: "Core-2", description: "Server Maintenance", amount: "₱3,500.00", paymentMethod: "Bank Transfer", date: "2024-11-16" },
    { id: "hr1-013", department: "Human Resource-1", description: "Digital Marketing Campaign", amount: "₱1,800.00", paymentMethod: "Credit Card", date: "2024-11-17" },
    { id: "hr2-014", department: "Human Resource-2", description: "Warehouse Rent", amount: "₱5,000.00", paymentMethod: "Cheque", date: "2024-11-18" },
    { id: "hr3-015", department: "Human Resource-3", description: "Audit Fees", amount: "₱2,500.00", paymentMethod: "Bank Transfer", date: "2024-11-19" },
    { id: "hr4-016", department: "Human Resource-4", description: "Employee Health Insurance", amount: "₱4,000.00", paymentMethod: "eCash", date: "2024-11-20" },
    { id: "log1-017", department: "Logistics-1", description: "Network Setup", amount: "₱6,000.00", paymentMethod: "Credit Card", date: "2024-11-21" },
    { id: "log2-018", department: "Logistics-2", description: "Promotional Materials", amount: "₱900.00", paymentMethod: "Cash", date: "2024-11-22" },
    { id: "admin-019", department: "Admin", description: "Office Furniture", amount: "₱7,000.00", paymentMethod: "Cheque", date: "2024-11-23" },
    { id: "fin-020", department: "Finance", description: "Accounting Software", amount: "₱3,200.00", paymentMethod: "eCash", date: "2024-11-24" },
    { id: "core1-021", department: "Core-1", description: "Employee Bonuses", amount: "₱10,000.00", paymentMethod: "Bank Transfer", date: "2024-11-25" },
    { id: "core2-022", department: "Core-2", description: "Cloud Storage Subscription", amount: "₱1,800.00", paymentMethod: "Credit Card", date: "2024-11-26" },
    { id: "hr1-023", department: "Human Resource-1", description: "Branding Services", amount: "₱2,300.00", paymentMethod: "Cash", date: "2024-11-27" },
    { id: "hr2-024", department: "Human Resource-2", description: "Maintenance Contracts", amount: "₱2,000.00", paymentMethod: "Cheque", date: "2024-11-28" },
    { id: "hr3-025", department: "Human Resource-3", description: "Tax Filing Services", amount: "₱1,500.00", paymentMethod: "Bank Transfer", date: "2024-11-29" },
    { id: "hr4-026", department: "Human Resource-4", description: "Employee Welfare", amount: "₱1,000.00", paymentMethod: "eCash", date: "2024-11-30" }
];


    let currentPage = 1;
    const rowsPerPage = 10;

    function displayData(page) {
        const table = document.getElementById('receiptsTable');
        table.innerHTML = '';

        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageData = data.slice(start, end);

        pageData.forEach((receipt) => {
            const row = document.createElement('tr');
            row.classList.add('border-b', 'border-gray-300', 'hover:bg-blue-50');
            row.innerHTML = `
                <td class="px-4 py-2 border-r border-gray-300">${receipt.id}</td>
                <td class="px-4 py-2 border-r border-gray-300">${receipt.department}</td>
                <td class="px-4 py-2 border-r border-gray-300">${receipt.description}</td>
                <td class="px-4 py-2 border-r border-gray-300">${receipt.amount}</td>
                <td class="px-4 py-2 border-r border-gray-300">${receipt.paymentMethod}</td>
                <td class="px-4 py-2 border-r border-gray-300">${receipt.date}</td>
                <td class="px-4 py-2 text-center">
                    <button id="viewBtn${receipt.id}" class="bg-blue-800 text-white px-4 py-2 rounded" onclick="viewReceipt('${receipt.id}')">
                        View
                    </button>
                </td>
            `;
            table.appendChild(row);
        });

        document.getElementById('prevPage').disabled = currentPage === 1;
        document.getElementById('nextPage').disabled = currentPage * rowsPerPage >= data.length;
    }

    function prevPage() {
        if (currentPage > 1) {
            currentPage--;
            displayData(currentPage);
        }
    }

    function nextPage() {
        if (currentPage * rowsPerPage < data.length) {
            currentPage++;
            displayData(currentPage);
        }
    }

    function filterTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const paymentMethodFilter = document.getElementById('paymentMethodFilter').value;

        const filteredData = data.filter(receipt => {
            const matchesSearch = receipt.department.toLowerCase().includes(searchInput) || receipt.description.toLowerCase().includes(searchInput);
            const matchesPaymentMethod = paymentMethodFilter === '' || receipt.paymentMethod.includes(paymentMethodFilter);
            return matchesSearch && matchesPaymentMethod;
        });

        const table = document.getElementById('receiptsTable');
        table.innerHTML = '';
        filteredData.forEach(receipt => {
            const row = document.createElement('tr');
            row.classList.add('border-b', 'border-blue-200', 'hover:bg-blue-50');
            row.innerHTML = `
                <td class="px-4 py-2 border-r border-gray-300">${receipt.id}</td>
                <td class="px-4 py-2 border-r border-gray-300">${receipt.department}</td>
                <td class="px-4 py-2 border-r border-gray-300">${receipt.description}</td>
                <td class="px-4 py-2 border-r border-gray-300">${receipt.amount}</td>
                <td class="px-4 py-2 border-r border-gray-300">${receipt.paymentMethod}</td>
                <td class="px-4 py-2">${receipt.date}</td>
                <td class="px-4 py-2 text center">
                    <button id="viewBtn${receipt.id}" class="bg-blue-800 text-white px-4 py-2 rounded" onclick="viewReceipt('${receipt.id}')">
                        View
                    </button>
                </td>
            `;
            table.appendChild(row);
        });
    }

    window.onload = () => {
        displayData(currentPage);
    };
</script>
</body>
</html>
