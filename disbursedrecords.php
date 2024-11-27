<?php
session_start();


// Handle the form submission for deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_confirmed'])) {
    // Check if the password is correct
    $password = $_POST['password']; // Assuming the password is stored securely somewhere
    $correct_password = 'hala'; // Example: hardcoded for demo, replace with real method

    if ($password === $correct_password) {
        // Process the deletion of selected records
        if (isset($_POST['selected_ids']) && !empty($_POST['selected_ids'])) {
            $selected_ids = $_POST['selected_ids'];
            // Create the SQL query to delete the selected records
            $servername = '127.0.0.1:3308';
            $usernameDB = 'root';
            $passwordDB = '';
            $dbname = 'db';

            $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $ids = implode(",", $selected_ids); // Prepare a comma-separated list of IDs
            $sql = "DELETE FROM dr WHERE id IN ($ids)"; // Delete records from the 'dr' table

            if ($conn->query($sql) === TRUE) {
                // Redirect to a different page or reload the current page after success
                header("Location: disbursedrecords.php"); // Redirect to the disbursed records page or wherever you'd like
                exit(); // Stop further execution
            } else {
                // Optionally, log the error without displaying it to the user
                error_log("Error deleting records: " . $conn->error);
                // Optionally redirect or show a generic error page
                header("Location: disbursedrecords.php"); // Redirect to an error page
                exit();
            }

            $conn->close();
        }
    } else {
        // Optionally log the failed password attempt without displaying the message
        error_log("Incorrect password attempt for deletion.");
        // Optionally redirect to an error or login page
        header("Location: disbursedrecords.php"); // Redirect to an error page
        exit();
    }
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
       <li><a class="text-gray-600 font-bold" href="#">Disbursement</a></li>
       <li><span class="mx-2">&gt;</span></li>
       <li><a class="text-gray-600 font-bold" href="#">Disbursed Records</a></li>
      </ol>
     </nav>
    </div>

    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6 h-full w-full">

    <h1 class="font-bold text-2xl text-blue-900 mb-8">DISBURSED RECORDS</h1> 

    <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
    <thead>
    </thead>
    <tbody class="text-gray-900 text-sm font-semilight">
    <?php
// Database connection
$servername = '127.0.0.1:3308';
$usernameDB = 'root';
$passwordDB = '';
$dbname = 'db';

// Establishing the connection to the database
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize search query for the independent search bar
$search = isset($_GET['dr_search']) ? $conn->real_escape_string($_GET['dr_search']) : '';

// Modify the SQL query to include the search condition for the `dr` table
$sql = "SELECT * FROM dr";
if ($search !== '') {
    $sql .= " WHERE reference_id LIKE '%$search%' 
              OR account_name LIKE '%$search%' 
              OR requested_department LIKE '%$search%' 
              OR mode_of_payment LIKE '%$search%'";
}

$result = $conn->query($sql);
$totalAmount = 0; // Initialize a variable for the total amount

// Display the independent search form
echo "<form method='GET' action='' class='mb-4'>";
echo "<div class='flex items-center'>";
echo "<input type='text' name='dr_search' value='$search' placeholder='Search by Reference ID, Account Name, Department, or Payment Mode' class='p-2 border border-gray-300 rounded mr-2'>";
echo "<button type='submit' class='bg-blue-500 text-white py-2 px-4 rounded :bg-blue-70hover0'>Search</button>";
echo "</div>";
echo "</form>";

// Fetch records from `dr` to display
if ($result->num_rows > 0) {
    // Optionally show a message about search results
    if (!empty($search)) {
        echo "<p class='hidden-text'>Showing results for: <strong>" . htmlspecialchars($search) . "</strong></p>";
    }

    // Render the table with results
    echo "<form method='POST' action=''>";
    echo "<table class='w-full border-collapse border border-gray-300'>";
    echo "<thead class='bg-gray-100'>";
    echo "<tr>";
    echo "<th class='py-3 px-6 text-left border border-gray-300'><input type='checkbox' id='select-all'></th>";
    echo "<th class='py-3 px-6 text-left border border-gray-300'>ID</th>";
    echo "<th class='py-3 px-6 text-left border border-gray-300'>Reference ID</th>";
    echo "<th class='py-3 px-6 text-left border border-gray-300'>Account Name</th>";
    echo "<th class='py-3 px-6 text-left border border-gray-300'>Requested Department</th>";
    echo "<th class='py-3 px-6 text-left border border-gray-300'>Mode of Payment</th>";
    echo "<th class='py-3 px-6 text-left border border-gray-300'>Expense Categories</th>";
    echo "<th class='py-3 px-6 text-left border border-gray-300'>Amount</th>";
    echo "<th class='py-3 px-6 text-left border border-gray-300'>Payment Due</th>";
    echo "<th class='py-3 px-6 text-left border border-gray-300'>Disbursed At</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    // Display table rows
    while ($row = $result->fetch_assoc()) {
        echo "<tr class='border-b border-gray-300  bg-white transition-colors duration-200'>";
        echo "<td class='py-3 px-6 text-left border border-gray-300'><input type='checkbox' name='selected_ids[]' value='{$row['id']}'></td>";
        echo "<td class='py-3 px-6 text-left border border-gray-300'>{$row['id']}</td>";
        echo "<td class='py-3 px-6 text-left border border-gray-300'>{$row['reference_id']}</td>";
        echo "<td class='py-3 px-6 text-left border border-gray-300'>{$row['account_name']}</td>";
        echo "<td class='py-3 px-6 text-left border border-gray-300'>{$row['requested_department']}</td>";
        echo "<td class='py-3 px-6 text-left border border-gray-300'>{$row['mode_of_payment']}</td>";
        echo "<td class='py-3 px-6 text-left border border-gray-300'>{$row['expense_categories']}</td>";
        echo "<td class='py-3 px-6 text-left border border-gray-300'>₱" . number_format($row['amount'], 2) . "</td>";
        echo "<td class='py-3 px-6 text-left border border-gray-300'>{$row['payment_due']}</td>";
        echo "<td class='py-3 px-6 text-left border border-gray-300'>{$row['disbursed_at']}</td>";
        echo "</tr>";

        // Add the amount to the total
        $totalAmount += $row['amount'];
    }

    // Add a row for the total
    echo "<tr class='font-bold bg-gray-100'>";
    echo "<td colspan='7' class='py-3 px-6 text-right border border-gray-300'>Total:</td>";
    echo "<td class='py-3 px-6 text-left border border-gray-300'>₱" . number_format($totalAmount, 2) . "</td>";
    echo "<td colspan='3'></td>"; // Empty cells for alignment
    echo "</tr>";

    echo "</tbody>";
    echo "</table>";
    
    // Password confirmation input
    echo "<div class='mt-4'>";
    echo "<label for='password' class='block text-gray-700'>Enter your password to confirm deletion:</label>";
    echo "<input type='password' id='password' name='password' class='mt-2 p-2 border border-gray-300 rounded' required>";
    echo "</div>";
    
    // Hidden field to confirm the deletion process
    echo "<input type='hidden' name='delete_confirmed' value='1'>";
    
    // Delete button
   
    echo '<div class="flex mt-4">
    <button type="submit" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-700 focus:ring focus:ring-red-300">
        Delete Selected
    </button>
</div>';

    echo "</form>"; // Form ends here
} else {
    echo "<p class='text-center py-3'>No records found for your search query.</p>";
}

$conn->close();
?>



    </tbody>
</table>
</div>
</body>
</html>

<script>
// JavaScript to select/deselect all checkboxes
document.getElementById('select-all').addEventListener('change', function() {
    var checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = document.getElementById('select-all').checked;
    });
});
</script>
