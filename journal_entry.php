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
          <a class="text-gray-600 font-bold" href="#">Journal Entry</a>
        </li>
      </ol>
    </nav>
  </div>

  <!-- Main content area -->
  <div class="flex-1 bg-blue-100 p-6 h-full w-full">

    <h1 class="font-bold text-2xl text-blue-900 mb-8">JOURNAL ENTRY</h1>

    <!-- Search and Filter -->
    <div class="mb-4 flex justify-between items-center">
      <div class="flex space-x-4">
        <input
          type="text"
          id="searchInput"
          class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm w-80"
          placeholder="Search by Account Name"
          onkeyup="filterTable()"
        />
        <input
          type="date"
          id="dateInput"
          class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm"
          onchange="filterTable()"
        />
      </div>
    </div>

    <!-- Journal Entry Table -->
    <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
      <thead>
        <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
          <th class="px-4 py-2">Date</th>
          <th class="px-4 py-2">Account Name</th>
          <th class="px-4 py-2">Debit Amount</th>
          <th class="px-4 py-2">Credit Amount</th>
        </tr>
      </thead>
      <tbody id="journalTable" class="text-gray-900 text-sm font-semilight">
        <!-- Dynamic rows will be inserted here -->
      </tbody>
    </table>

    <!-- Pagination and Showing Info -->
    <div class="mt-4 flex justify-between items-center">
      <div>
        <span id="showingInfo" class="text-gray-600"></span>
      </div>
      <div class="flex justify-end">
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
    // Journal Entry Data
    const journalData = [
      { date: '2024-11-19', account: 'Cash', debit: 1000, credit: 0 },
      { date: '2024-11-19', account: 'Accounts Receivable', debit: 0, credit: 1000 },
      { date: '2024-11-19', account: 'Salaries Expense', debit: 2000, credit: 0 },
      { date: '2024-11-19', account: 'Cash', debit: 0, credit: 2000 },
      { date: '2024-11-19', account: 'Equipment/Assets', debit: 5000, credit: 0 },
      { date: '2024-11-19', account: 'Cash', debit: 0, credit: 5000 },
      { date: '2024-11-19', account: 'Maintenance/Repair', debit: 500, credit: 0 },
      { date: '2024-11-19', account: 'Cash', debit: 0, credit: 500 },
      { date: '2024-11-19', account: 'Platform Fee', debit: 0, credit: 200 },
      { date: '2024-11-19', account: 'Accounts Receivable', debit: 200, credit: 0 }
    ];

    let currentPage = 1;
    const rowsPerPage = 10;

    function renderTable() {
      const tableBody = document.getElementById("journalTable");
      tableBody.innerHTML = "";

      const filteredData = filterData();

      const startIndex = (currentPage - 1) * rowsPerPage;
      const endIndex = Math.min(startIndex + rowsPerPage, filteredData.length);

      for (let i = startIndex; i < endIndex; i++) {
        const row = document.createElement("tr");

        // Alternating row colors logic
        if (Math.floor(i / 2) % 2 === 0) {
          row.className = "bg-gray-200";
        } else {
          row.className = "bg-white";
        }

        row.innerHTML = `
          <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].date}</td>
          <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].account}</td>
          <td class="py-3 px-6 text-left border-r border-gray-300">${formatCurrency(filteredData[i].debit)}</td>
          <td class="py-3 px-6 text-left border-r border-gray-300">${formatCurrency(filteredData[i].credit)}</td>
        `;
        tableBody.appendChild(row);
      }

      document.getElementById("prevPage").disabled = currentPage === 1;
      document.getElementById("nextPage").disabled = endIndex === filteredData.length;

      // Update the "Showing X - Y of Z" info
      const showingInfo = document.getElementById("showingInfo");
      showingInfo.textContent = Showing ${startIndex + 1} - ${endIndex} of ${filteredData.length};
      showingInfo.style.fontWeight = 'bold';
    }

    // Use Peso sign instead of Dollar
    function formatCurrency(amount) {
      return ₱${amount.toFixed(2)};
    }

    function filterData() {
      const searchInput = document.getElementById("searchInput").value.toLowerCase();
      const dateInput = document.getElementById("dateInput").value;
      return journalData.filter(item => {
        const matchesSearch = item.account.toLowerCase().includes(searchInput) || item.date.includes(searchInput);
        const matchesDate = dateInput ? item.date === dateInput : true;
        return matchesSearch && matchesDate;
      });
    }

    function filterTable() {
      currentPage = 1;
      renderTable();
    }

    function prevPage() {
      if (currentPage > 1) {
        currentPage--;
        renderTable();
      }
    }

    function nextPage() {
      if ((currentPage - 1) * rowsPerPage < filterData().length) {
        currentPage++;
        renderTable();
      }
    }

    // Initial rendering of the table
    renderTable();
  </script>
</body>
</html>