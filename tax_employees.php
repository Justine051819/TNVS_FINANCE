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
                <li><a class="text-gray-600 font-bold" href="#">Tax Management</a></li>
                <li><span class="mx-2">&gt;</span></li>
                <li><a class="text-gray-600 font-bold" href="#">Employees Tax Records</a></li>
            </ol>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 bg-blue-100 p-6 h-full w-full">
        <h1 class="font-bold text-2xl text-blue-900 mb-6">EMPLOYEES TAX RECORDS</h1>

        <!-- Filters -->
        <div class="flex justify-between items-center mb-4">
            <input 
                type="text" 
                id="searchInput" 
                class="border border-gray-300 rounded-lg px-4 py-2 shadow-sm w-80" 
                placeholder="Search Employee Name or Department" 
                onkeyup="filterTable()"
            />
        </div>

        <!-- Table -->
        <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
            <thead>
                <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
                    <th class="px-4 py-2">Employee ID</th>
                    <th class="px-4 py-2">Employee Name</th>
                    <th class="px-4 py-2">Gross Salary (₱)</th>
                    <th class="px-4 py-2">Taxable Income (₱)</th>
                    <th class="px-4 py-2">Tax Rate (%)</th>
                    <th class="px-4 py-2">Tax Paid (₱)</th>
                    <th class="px-4 py-2">Paid Date</th>
                </tr>
            </thead>
            <tbody id="salaryTable" class="text-gray-900 text-sm ">
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
        // Sample Payroll Data
        const payrollData = [
    { "id": "001", "name": "Juan Dela Cruz", "gross_salary": "25,000", "taxable_income": "20,000", "tax_rate": "20%", "tax_due": "4,000", "payment_date": "2024-11-05" },
    { "id": "002", "name": "Maria Reyes", "gross_salary": "30,000", "taxable_income": "25,000", "tax_rate": "20%", "tax_due": "5,000", "payment_date": "2024-11-10" },
    { "id": "003", "name": "Carlos Garcia", "gross_salary": "40,000", "taxable_income": "35,000", "tax_rate": "25%", "tax_due": "8,750", "payment_date": "2024-11-12" },
    { "id": "004", "name": "Ana Santos", "gross_salary": "45,000", "taxable_income": "40,000", "tax_rate": "25%", "tax_due": "10,000", "payment_date": "2024-11-15" },
    { "id": "005", "name": "Pedro Cruz", "gross_salary": "50,000", "taxable_income": "45,000", "tax_rate": "30%", "tax_due": "13,500", "payment_date": "2024-11-20" },
    { "id": "006", "name": "Jessica Lim", "gross_salary": "38,000", "taxable_income": "33,000", "tax_rate": "22%", "tax_due": "7,260", "payment_date": "2024-11-22" },
    { "id": "007", "name": "Robert Tan", "gross_salary": "28,500", "taxable_income": "23,500", "tax_rate": "18%", "tax_due": "4,230", "payment_date": "2024-11-25" },
    { "id": "008", "name": "Linda Wu", "gross_salary": "35,000", "taxable_income": "30,000", "tax_rate": "20%", "tax_due": "6,000", "payment_date": "2024-11-28" },
    { "id": "009", "name": "Mark Salazar", "gross_salary": "48,000", "taxable_income": "43,000", "tax_rate": "28%", "tax_due": "12,040", "payment_date": "2024-11-30" },
    { "id": "010", "name": "Clara Mendoza", "gross_salary": "32,000", "taxable_income": "27,000", "tax_rate": "22%", "tax_due": "5,940", "payment_date": "2024-12-02" },
    { "id": "011", "name": "Isabel Bautista", "gross_salary": "55,000", "taxable_income": "50,000", "tax_rate": "30%", "tax_due": "15,000", "payment_date": "2024-12-05" },
    { "id": "012", "name": "Nathaniel Cruz", "gross_salary": "41,000", "taxable_income": "36,000", "tax_rate": "25%", "tax_due": "9,000", "payment_date": "2024-12-08" },
    { "id": "013", "name": "Samantha Flores", "gross_salary": "39,000", "taxable_income": "34,000", "tax_rate": "22%", "tax_due": "7,480", "payment_date": "2024-12-10" },
    { "id": "014", "name": "Victor Torres", "gross_salary": "43,500", "taxable_income": "38,500", "tax_rate": "25%", "tax_due": "9,625", "payment_date": "2024-12-12" },
    { "id": "015", "name": "Gloria Rojas", "gross_salary": "29,500", "taxable_income": "24,500", "tax_rate": "18%", "tax_due": "4,410", "payment_date": "2024-12-15" }
        ];

        let currentPage = 1;
        const rowsPerPage = 10;

        function renderTable() {
            const tableBody = document.getElementById("salaryTable");
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
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].gross_salary}</td>
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].taxable_income}</td>
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].tax_rate}</td>
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].tax_due}</td>
                    <td class="py-3 px-6 text-left border-r border-gray-300">${filteredData[i].payment_date}</td>
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

            return payrollData.filter((employee) => {
                const matchesSearch =
                    employee.name.toLowerCase().includes(searchInput) ||
                    employee.id.includes(searchInput);
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