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
</head>
<body class="bg-white">

<?php include('navbar_sidebar.php'); ?>

<!-- Breadcrumb -->
<div class="bg-blue-200 p-4 shadow-lg">
    <nav class="text-gray-600 font-bold">
        <ol class="list-reset flex">
            <li><a class="text-gray-600 font-bold" href="TNVSFinance.php">Dashboard</a></li>
            <li><span class="mx-2">&gt;</span></li>
            <li><a class="text-gray-600 font-bold" href="#">Audit Reports</a></li>
        </ol>
    </nav>
</div>

<!-- Main content -->
<div class="flex-1 bg-blue-100 p-6 h-full w-full">
<a class="bg-blue-700 text-white px-2 py-1 rounded text-lg cursor-pointer whitespace-nowrap mb-4 float-right shadow-lg" href="#" role="button">ADD AUDIT REPORT</a>
    <h1 class="font-bold text-2xl text-blue-900 mb-8">AUDIT REPORTS</h1>

    <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
        <thead>
            <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Date</th>
                <th class="px-4 py-2">Issue</th>
                <th class="px-4 py-2">Action</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Action</th>
            </tr>
        </thead>
        <tbody class="text-gray-900 text-sm font-light">
            <tr class="border-b border-gray-300 hover:bg-gray-200">
                <td class="py-3 px-6 text-left border-r border-gray-300">001</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">2024-11-01</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">Missing receipts</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">Collect and reconcile</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">
                    <span class="bg-red-200 text-red-800 py-1 px-3 rounded-full text-xs">Pending</span>
                </td>
                <td class="py-3 px-6 text-center">
                    <button class="bg-blue-500 text-white px-4 py-2 rounded shadow">Update</button>
                </td>
            </tr>
            <tr class="border-b border-gray-300 hover:bg-gray-200">
                <td class="py-3 px-6 text-left border-r border-gray-300">002</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">2024-11-05</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">Overdue receivables</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">Notify customers</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">
                    <span class="bg-yellow-200 text-yellow-800 py-1 px-3 rounded-full text-xs">In Progress</span>
                </td>
                <td class="py-3 px-6 text-center">
                    <button class="bg-blue-500 text-white px-4 py-2 rounded shadow">Update</button>
                </td>
            </tr>
            <tr class="border-b border-gray-300 hover:bg-gray-200">
                <td class="py-3 px-6 text-left border-r border-gray-300">003</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">2024-11-08</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">Incorrect tax calculation</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">Update settings</td>
                <td class="py-3 px-6 text-left border-r border-gray-300">
                    <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Completed</span>
                </td>
                <td class="py-3 px-6 text-center">
                    <button class="bg-blue-500 text-white px-4 py-2 rounded shadow">Update</button>
                </td>
            </tr>
            <!-- Add more rows as needed -->
        </tbody>
    </table>
</div>
</body>
</html>