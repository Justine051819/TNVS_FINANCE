<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
// Database connection
$servername = '127.0.0.1:3308';
$usernameDB = 'root';
$passwordDB = '';
$dbname = 'db';

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add new budget allocation
if (isset($_POST['add'])) {
    $department = $_POST['department'];
    $category = $_POST['category'];
    $allocated_amount = $_POST['allocated_amount'];
    $spent = 0;  // Spent is always 0 when adding a new allocation
    $remaining_balance = $allocated_amount;  // Remaining Balance is equal to Allocated Amount

    $stmt = $conn->prepare("INSERT INTO budget_allocations (department, category, allocated_amount, remaining_balance, spent) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdds", $department, $category, $allocated_amount, $remaining_balance, $spent);
    $stmt->execute();
    $stmt->close();
}

// Update budget allocation
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $department = $_POST['department'];
    $category = $_POST['category'];
    $allocated_amount = $_POST['allocated_amount'];
    $remaining_balance = $_POST['remaining_balance'];
    $spent = $_POST['spent'];

    $stmt = $conn->prepare("UPDATE budget_allocations SET department=?, category=?, allocated_amount=?, remaining_balance=?, spent=? WHERE id=?");
    $stmt->bind_param("ssdddi", $department, $category, $allocated_amount, $remaining_balance, $spent, $id);
    $stmt->execute();
    $stmt->close();
}

// Delete budget allocation
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM budget_allocations WHERE id=$id");
}

// Fetch all budget allocations
$sql = "SELECT * FROM budget_allocations";
$result = $conn->query($sql);
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
        <a class="text-gray-600 font-bold" href="#">Budget</a>
       </li>
       <li>
        <span class="mx-2">&gt;</span>
       </li>
       <li>
        <a class="text-gray-600 font-bold" href="#">Budget Allocation</a>
       </li>
      </ol>
     </nav>
    </div>

    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6 w-full">
    <div class="w-full">
    <button class="bg-blue-700 text-white px-4 py-2 rounded-md mb-1 float-right" onclick="openModal('addModal')">ADD NEW ALLOCATION</button>
    <h1 class="font-bold text-2xl text-blue-900 mb-8">BUDGET ALLOCATION</h1> 
    
    <div class="bg-white">
    <table class="min-w-full bg-gray-100 border-8 border-blue-200 shadow-2xl">
    <thead class="bg-blue-200">
        <tr>
            <th class="py-2 px-4 text-center text-sm font-bold text-blue-900">EXPENSE CATEGORY</th>
            <th class="py-2 px-4 text-center text-sm font-bold text-blue-900">DEPARTMENT</th>
            <th class="py-2 px-4 text-center text-sm font-bold text-blue-900">ALLOCATED AMOUNT</th>
            <th class="py-2 px-4 text-center text-sm font-bold text-blue-900">SPENT</th>
            <th class="py-2 px-4 text-center text-sm font-bold text-blue-900">REMAINING BALANCE</th>
            <th class="py-2 px-4 text-center text-sm font-bold text-blue-900">ACTIONS</th>
        </tr>
    </thead>
    <tbody class="font-semilight">
        <?php
        $totalAllocated = 0;
        $totalSpent = 0;
        $totalRemaining = 0;

        while ($row = $result->fetch_assoc()) {
            // Add to the totals
            $totalAllocated += $row['allocated_amount'];
            $totalSpent += $row['spent'];
            $totalRemaining += $row['remaining_balance'];
        ?>
            <tr class="border-b hover:bg-gray-200">
                <td class="text-left text-sm py-2 px-4 border-r text-gray-800 border-gray-300"><?= $row['category'] ?></td>
                <td class="text-left text-sm py-2 px-4 border-r text-gray-800 border-gray-300"><?= $row['department'] ?></td>
                <td class="text-left text-sm py-2 px-4 border-r text-gray-800 border-gray-300">₱<?= number_format($row['allocated_amount'], 2) ?></td>
                <td class="text-left text-sm py-2 px-4 border-r text-gray-800 border-gray-300">₱<?= number_format($row['spent'], 2) ?></td>
                <td class="text-left text-sm py-2 px-4 border-r text-gray-800 border-gray-300">₱<?= number_format($row['remaining_balance'], 2) ?></td>
                <td class="text-center text-sm py-2 px-4 space-x-2">
                    <button class="bg-blue-700 text-white px-4 py-1 rounded-md" onclick="editBudget(<?= $row['id'] ?>, '<?= $row['department'] ?>', '<?= $row['category'] ?>', <?= $row['allocated_amount'] ?>, <?= $row['remaining_balance'] ?>, <?= $row['spent'] ?>)">Adjust</button>
                    <a href="?delete=<?= $row['id'] ?>" class="bg-red-500 text-white px-4 py-1 rounded-md" onclick="return confirmDelete()">Delete</a>
                </td>
            </tr>
        <?php } ?>
        <!-- Totals Row -->
        <tr class="font-semibold text-gray-700 ">
            <td colspan="2" class="text-center py-2 px-4 border-r text-gray-800 border-gray-300">Total</td>
            <td class="text-left text-sm py-2 px-4 border-r text-blue-700 border-gray-300">₱<?= number_format($totalAllocated, 2) ?></td>
            <td class="text-left text-sm py-2 px-4 border-r text-red-700 border-gray-300">₱<?= number_format($totalSpent, 2) ?></td>
            <td class="text-left text-sm py-2 px-4 border-r text-green-700 border-gray-300">₱<?= number_format($totalRemaining, 2) ?></td>
            <td class="text-center py-2 px-4"></td>
        </tr>
    </tbody>
</table>

    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 flex justify-center items-center bg-gray-500 bg-opacity-75 z-50 hidden">
    <div class="bg-white rounded-lg p-6 w-96 relative">
        <h3 class="text-xl font-bold mb-4">Add New Budget Allocation</h3>
        <form action="budget_allocation.php" method="POST">
            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700">Expense Category</label>
                <input type="text" name="category" id="category" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>
            <div class="mb-4">
                <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                <select name="department" id="department" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    <option value="" disabled selected>Select Department</option>
                    <option value="Admin">Admin</option>
                    <option value="Finance">Finance</option>
                    <option value="HR">HR</option>
                    <option value="Logistics">Logistics</option>
                    <option value="Core">Core</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="allocated_amount" class="block text-sm font-medium text-gray-700">Allocated Amount</label>
                <input type="number" name="allocated_amount" id="allocated_amount" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>

            <!-- Hidden fields for Spent and Remaining Balance (calculated values) -->
            <input type="hidden" name="spent" value="0"> <!-- Spent is always 0 -->
            <input type="hidden" name="remaining_balance" id="remaining_balance">

            <div class="flex space-x-4 mt-4">
                <!-- Save Allocation button -->
                <button type="submit" name="add" class="bg-blue-500 text-white px-4 py-2 rounded-md">Save Allocation</button>
                <!-- Close button -->
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-md" onclick="closeModal('addModal')">Close</button>
            </div>
        </form>
    </div>
</div>

<!-- Update Modal -->
<div id="updateModal" class="fixed inset-0 flex justify-center items-center bg-gray-500 bg-opacity-75 z-50 hidden">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-xl font-bold mb-4">Adjust Budget Allocation</h3>
        <form action="budget_allocation.php" method="POST">
            <input type="hidden" name="id" id="update_id">
            <div class="mb-4">
                <label for="update_category" class="block text-sm font-medium text-gray-700">Expense Category</label>
                <input type="text" name="category" id="update_category" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>
            <div class="mb-4">
                <label for="update_department" class="block text-sm font-medium text-gray-700">Department</label>
                <select name="department" id="update_department" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    <option value="" disabled selected>Select Department</option>
                    <option value="Admin">Admin</option>
                    <option value="Finance">Finance</option>
                    <option value="HR">HR</option>
                    <option value="Logistics">Logistics</option>
                    <option value="Core">Core</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="update_allocated_amount" class="block text-sm font-medium text-gray-700">Allocated Amount</label>
                <input type="number" name="allocated_amount" id="update_allocated_amount" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>

            <div class="mb-4">
                <label for="update_spent" class="block text-sm font-medium text-gray-700">Spent</label>
                <input type="number" name="spent" id="update_spent" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>

            <div class="mb-4">
                <label for="update_remaining_balance" class="block text-sm font-medium text-gray-700">Remaining Balance</label>
                <input type="number" name="remaining_balance" id="update_remaining_balance" class="w-full px-3 py-2 border border-gray-300 rounded-md" readonly>
            </div>

            <div class="flex space-x-4 mt-4">
                <button type="submit" name="update" class="bg-blue-500 text-white px-4 py-2 rounded-md">Update Allocation</button>
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-md" onclick="closeModal('updateModal')">Close</button>
            </div>
        </form>
    </div>
</div>

</div>

<script>
    // Function to open the modal
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    // Function to close the modal
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Automatically set the Remaining Balance when Allocated Amount changes in Add Modal
    document.getElementById('allocated_amount').addEventListener('input', function() {
        var allocatedAmount = parseFloat(this.value) || 0;
        document.getElementById('remaining_balance').value = allocatedAmount;
    });

    // Automatically set the Remaining Balance when Allocated Amount or Spent changes in Update Modal
    document.getElementById('update_allocated_amount').addEventListener('input', updateRemainingBalance);
    document.getElementById('update_spent').addEventListener('input', updateRemainingBalance);

    function updateRemainingBalance() {
        var allocatedAmount = parseFloat(document.getElementById('update_allocated_amount').value) || 0;
        var spent = parseFloat(document.getElementById('update_spent').value) || 0;
        document.getElementById('update_remaining_balance').value = allocatedAmount - spent;
    }

    // Function to pre-fill the Update Modal with the selected allocation details
    function editBudget(id, department, category, allocatedAmount, remainingBalance, spent) {
        document.getElementById('update_id').value = id;
        document.getElementById('update_department').value = department;
        document.getElementById('update_category').value = category;
        document.getElementById('update_allocated_amount').value = allocatedAmount;
        document.getElementById('update_spent').value = spent;
        document.getElementById('update_remaining_balance').value = remainingBalance;
        openModal('updateModal');
    }

    // Function to confirm deletion
    function confirmDelete() {
        return confirm('Are you sure you want to delete this allocation?');
    }
</script>  

<div class="w-full">
   

</div>




 </body>
</html>