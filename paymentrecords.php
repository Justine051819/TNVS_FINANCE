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
        <a class="text-gray-600 font-bold" href="#">Collection</a>
       </li>
       <li>
        <span class="mx-2">&gt;</span>
       </li>
       <li>
        <a class="text-gray-600 font-bold" href="#">Payment Records</a>
       </li>
      </ol>
     </nav>
    </div>

    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6 h-full w-full">



    <h1 class="font-bold text-2xl text-blue-900 mb-8">PAYMENT RECORDS</h1>

<div class="mb-4 flex justify-between items-center">
    <!-- Search Bar -->
    <input
        type="text"
        id="searchInputp"
        class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm"
        placeholder="Search Passenger Name or Ticket Number"
        onkeyup="filterTable()"
    />

    <!-- Filter by Payment Method -->
    <select
        id="paymentMethodFilter"
        class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm"
        onchange="filterTable()"
    >
        <option value="">All Payment Methods</option>
        <option value="Ecash">Ecash</option>
        <option value="Bank Transfer">Bank Transfer</option>
        <option value="Cash">Cash</option>
    </select>
</div>

<table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
    <thead>
        <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
            <th class="px-4 py-2">Payment ID</th>
            <th class="px-4 py-2">Passenger Name</th>
            <th class="px-4 py-2">Ticket Number</th>
            <th class="px-4 py-2">Payment Date</th>
            <th class="px-4 py-2">Amount Paid</th>
            <th class="px-4 py-2">Payment Method</th>
        </tr>
    </thead>
    <tbody id="paymentTable" class="text-gray-900 text-sm font-semilight">
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
</div> </div>

<script>
    const data = [
    { id: 2001, name: "Maria Santos", ticket: "TK-001", date: "2024-11-01", amount: "₱150.00", method: "Ecash" },
    { id: 2002, name: "Juan dela Cruz", ticket: "TK-002", date: "2024-11-03", amount: "₱120.00", method: "Bank Transfer" },
    { id: 2003, name: "Ana Reyes", ticket: "TK-003", date: "2024-11-05", amount: "₱80.00", method: "Cash" },
    { id: 2004, name: "Pedro Gonzales", ticket: "TK-004", date: "2024-11-07", amount: "₱180.00", method: "Ecash" },
    { id: 2005, name: "Liza Cruz", ticket: "TK-005", date: "2024-11-09", amount: "₱210.00", method: "Ecash" },
    { id: 2006, name: "Carlos Ramos", ticket: "TK-006", date: "2024-11-10", amount: "₱140.00", method: "Bank Transfer" },
    { id: 2007, name: "Sofia Bautista", ticket: "TK-007", date: "2024-11-11", amount: "₱175.00", method: "Cash" },
    { id: 2008, name: "David Santiago", ticket: "TK-008", date: "2024-11-12", amount: "₱200.00", method: "Ecash" },
    { id: 2009, name: "Olivia Cruz", ticket: "TK-009", date: "2024-11-13", amount: "₱130.00", method: "Bank Transfer" },
    { id: 2010, name: "Miguel Garcia", ticket: "TK-010", date: "2024-11-14", amount: "₱160.00", method: "Ecash" },
    { id: 2011, name: "Elena Mendoza", ticket: "TK-011", date: "2024-11-15", amount: "₱95.00", method: "Cash" },
    { id: 2012, name: "William Navarro", ticket: "TK-012", date: "2024-11-16", amount: "₱220.00", method: "Bank Transfer" },
    { id: 2013, name: "Amelia Villanueva", ticket: "TK-013", date: "2024-11-17", amount: "₱175.00", method: "Ecash" },
    { id: 2014, name: "Ethan Ramos", ticket: "TK-014", date: "2024-11-18", amount: "₱135.00", method: "Cash" },
    { id: 2015, name: "Catherine Lopez", ticket: "TK-015", date: "2024-11-19", amount: "₱185.00", method: "Bank Transfer" },
    { id: 2016, name: "Jackie Tan", ticket: "TK-016", date: "2024-11-20", amount: "₱155.00", method: "Ecash" },
    { id: 2017, name: "Ella Villamor", ticket: "TK-017", date: "2024-11-21", amount: "₱120.00", method: "Cash" },
    { id: 2018, name: "Henry Alcantara", ticket: "TK-018", date: "2024-11-22", amount: "₱250.00", method: "Bank Transfer" },
    { id: 2019, name: "Grace Manalo", ticket: "TK-019", date: "2024-11-23", amount: "₱180.00", method: "Ecash" },
    { id: 2020, name: "Oliver De Guzman", ticket: "TK-020", date: "2024-11-24", amount: "₱110.00", method: "Cash" },
    { id: 2021, name: "Sophia Pineda", ticket: "TK-021", date: "2024-11-25", amount: "₱145.00", method: "Bank Transfer" },
    { id: 2022, name: "James Mercado", ticket: "TK-022", date: "2024-11-26", amount: "₱210.00", method: "Ecash" },
    { id: 2023, name: "Isabella Salvador", ticket: "TK-023", date: "2024-11-27", amount: "₱125.00", method: "Cash" },
    { id: 2024, name: "Lucas Evangelista", ticket: "TK-024", date: "2024-11-28", amount: "₱195.00", method: "Bank Transfer" },
    { id: 2025, name: "Mia Robles", ticket: "TK-025", date: "2024-11-29", amount: "₱240.00", method: "Ecash" },
    { id: 2026, name: "Alexander Fernandez", ticket: "TK-026", date: "2024-11-30", amount: "₱130.00", method: "Cash" },
    { id: 2027, name: "Harper Mariano", ticket: "TK-027", date: "2024-12-01", amount: "₱160.00", method: "Bank Transfer" },
    { id: 2028, name: "Sebastian Rivera", ticket: "TK-028", date: "2024-12-02", amount: "₱180.00", method: "Ecash" },
    { id: 2029, name: "Emily Alvarado", ticket: "TK-029", date: "2024-12-03", amount: "₱145.00", method: "Cash" },
    { id: 2030, name: "Daniel Santiago", ticket: "TK-030", date: "2024-12-04", amount: "₱190.00", method: "Bank Transfer" }
];


let currentPage = 1;
    const rowsPerPage = 10;

    function renderTable() {
        const tableBody = document.getElementById("paymentTable");
        tableBody.innerHTML = "";

        const filteredData = filterData();

        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = Math.min(startIndex + rowsPerPage, filteredData.length);

        for (let i = startIndex; i < endIndex; i++) {
            const row = document.createElement("tr");
            row.className = "border-b border-gray-300 hover:bg-gray-200";

            row.innerHTML = `
                <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].id}</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].name}</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].ticket}</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].date}</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].amount}</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].method}</td>
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
        const searchInputp = document.getElementById("searchInputp").value.toLowerCase();
        const methodFilter = document.getElementById("paymentMethodFilter").value;

        return data.filter((item) => {
            const matchesSearch =
                item.name.toLowerCase().includes(searchInputp) ||
                item.ticket.toLowerCase().includes(searchInputp);
            const matchesMethod = methodFilter === "" || item.method === methodFilter;

            return matchesSearch && matchesMethod;
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
    document.getElementById("searchInputp").addEventListener("keyup", filterTable);
    document.getElementById("paymentMethodFilter").addEventListener("change", filterTable);
</script>







 </body>
</html>