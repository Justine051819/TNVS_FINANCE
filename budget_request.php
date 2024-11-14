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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>

$(document).on('click', '.reject-btn', function() {
    var rejectId = $(this).data('id');  // Get the ID from the button's data-id attribute
    $('#reject_id').val(rejectId);      // Set the reject_id input field with the ID
    $('#rejectModal').show();           // Show the modal
});

// Close the modal when the close button is clicked
$('.close').on('click', function() {
    $('#rejectModal').hide();           // Hide the modal
});

// Optionally close the modal when clicked outside of the modal content
$(window).on('click', function(event) {
    if ($(event.target).is('#rejectModal')) {
        $('#rejectModal').hide();
    }
});


  var url = 'uploads/your-file.pdf';
  var pdfjsLib = window['pdfjs-dist'];

  pdfjsLib.getDocument(url).promise.then(function(pdf) {
    pdf.getPage(1).then(function(page) {
      var scale = 1.5;
      var viewport = page.getViewport({ scale: scale });

      var canvas = document.getElementById('pdf-viewer');
      var context = canvas.getContext('2d');
      canvas.height = viewport.height;
      canvas.width = viewport.width;

      page.render({
        canvasContext: context,
        viewport: viewport
      });
    });
  });

    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('hidden');
    }

    function toggleDropdown(id) {
      const dropdown = document.getElementById(id);
      const icon = dropdown.previousElementSibling.querySelector('.fas.fa-chevron-right');
      dropdown.classList.toggle('hidden');
      icon.classList.toggle('rotate-90');
    }
  

  </script>
  <style>
   .rotate-90 {
     transform: rotate(90deg);
     transition: transform 0.3s ease;
   }

   .modal {
    display: none; /* Hidden by default */
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: rgba(0, 0, 0, 0.5); /* Overlay effect */
    z-index: 1050;
    width: 100%;
    max-width: 500px;
}



.modal-header {
    font-size: 18px;
}

.close {

    cursor: pointer;
    font-size: 20px;
    float:right;
    margin-top:-12px;
}


  </style>
 </head>  
 <body class="bg-white-900">
  <div class="flex h-screen">
   <!-- Sidebar -->
   <div id="sidebar" class="w-64 bg-white p-4  z-10 transition-all duration-300">
    <div class="flex items-center mb-6">
     <img alt="Movers logo" class="mr-2" height="200px" src="logo.png" width="250px"/>
    </div>
    <nav>
     <ul>
      <li class="mb-4">
       <a class="flex items-center text-blue-600 font-bold" href="TNVSFinance.php">
        <i class="fas fa-th-large mr-2"></i>
        Dashboard
       </a>
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer" onclick="toggleDropdown('employeeDropdown')">
         <i class="fas fa-calculator mr-2"></i>
         Budget
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300"></i>
        </a>
        <ul class="hidden pl-8 mt-2" id="employeeDropdown">
         <li class="mb-2">
          <a href="budget_request.php" class="text-gray-700 font-bold">Budget Request</a>
         </li>
         <li class="mb-2">
          <a href="rejected_request.php" class="text-gray-700 font-bold">Rejected Request</a>
         </li>
         <li class="mb-2">
          <a href="budget_request.php" class="text-gray-700 font-bold">Budget Allocation</a>
         </li>
        </ul>
       </div>
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer" onclick="toggleDropdown('payrollDropdown')">
         <i class="fas fa-coins mr-2"></i>
         Disbursement
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300"></i>
        </a>
        <ul class="hidden pl-8 mt-2" id="payrollDropdown">
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="payout_approval.php">Payout Approval</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="payout.php">Bank Transfer Payout</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="ecash.php">Ecash Payout</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="cheque.php">Cheque Payout</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="cash.php">Cash Payout</a>
         </li>
        </ul>
       </div>
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer" onclick="toggleDropdown('compensationDropdown')">
         <i class="fas fa-gift mr-2"></i>
         Collection
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300"></i>
        </a>
        <ul class="hidden pl-8 mt-2" id="compensationDropdown">
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Compensation Plans</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Benefits Overview</a>
         </li>
        </ul>
       </div>
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer" onclick="toggleDropdown('recommendationDropdown')">
         <i class="fas fa-landmark mr-2"></i>
         Account Payables
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300"></i>
        </a>
        <ul class="hidden pl-8 mt-2" id="recommendationDropdown">
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="payables.php">Payables</a>
        </ul>
       </div>
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer" onclick="toggleDropdown('hatdogDropdown')">
         <i class="fas fa-file-invoice-dollar mr-2"></i>
         Account Receivables
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300"></i>
        </a>
        <ul class="hidden pl-8 mt-2" id="hatdogDropdown">
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Cheese Cake</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Palaman Hatdog</a>
         </li>
        </ul>
       </div>
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer" onclick="toggleDropdown('hatDropdown')">
         <i class="fas fa-file-invoice-dollar mr-2"></i>
         General Ledger
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300"></i>
        </a>
        <ul class="hidden pl-8 mt-2" id="hatDropdown">
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Charts of Accounts</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="disburse_records.php">Disburse Records</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Collected Records</a>
         </li>
         <li class="mb-2">
          <a class="text-gray-700 font-bold" href="#">Asset Records</a>
         </li>
        </ul>
       </div>
      </li>
      <li>
       <a class="text-blue-600 font-bold" href="#">Report</a>
      </li>
     </ul>
    </nav>
   </div>
   <!-- Main content -->
   <div id="mainContent" class="flex-1 flex flex-col bg-blue-100 transition-all duration-300">
    <!-- Header -->
    <header class="flex items-center justify-between bg-white p-4 shadow-lg">
     <div class="flex items-center">
      <button class="text-2xl mr-4" onclick="toggleSidebar()">
       <i class="fas fa-bars"></i>
      </button>
      <h1 class="text-xl font-bold text-blue-600">Finance</h1>
     </div>
     <div class="relative">
      <button class="flex items-center" onclick="toggleDropdown('userDropdown')">
       <img alt="User avatar" class="rounded-full" height="40" src="user.jpg" width="40"/>
      </button>
      <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 hidden">
       <a class="block px-4 py-2 text-gray-700 font-bold" href="#">Profile</a>
       <a class="block px-4 py-2 text-gray-700 font-bold" href="#">Settings</a>
       <a class="block px-4 py-2 text-gray-700 font-bold" href="login.php">Logout</a>
      </div>
     </div>
    </header>
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
        <a class="text-gray-600 font-bold" href="#">Finance Operator</a>
       </li>
      </ol>
     </nav>
    </div>
    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6 w-full">

<div class="flex-1 bg-blue-100 p-6 w-full">
     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
     <div class="w-full">
        <h1 class="font-bold text-xl">BUDGET REQUEST</h1>
        <a class="bg-green-500 text-white px-2 py-1 rounded text-lg cursor-pointer whitespace-nowrap mb-4" href="add_ap.php" role="button">Add Request</a>
        <br>
        <div class="w-full px-4 pt-4">
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Reference ID</th>
                    <th class="px-4 py-2">Account Name</th>
                    <th class="px-4 py-2">Requested Department</th>
                    <th class="px-4 py-2">Mode of Payment</th>
                    <th class="px-4 py-2">Expense Categories</th>
                    <th class="px-4 py-2">Amount</th> 
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Document</th>
                    <th>Payment Due</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">

            <!-- Modal Structure -->
<div id="verificationModal" style="display: none;">
    <div>
        <h2>Verification Required</h2>
        <form id="verificationForm">
            <label for="actionPassword">Enter Action Password:</label>
            <input type="password" id="actionPassword" required>
            <input type="hidden" id="actionType" name="actionType">
            <button type="submit">Submit</button>
            <button type="button" id="cancelButton">Cancel</button>
        </form>
    </div>
</div>

<?php
$servername = '127.0.0.1:3308';
$usernameDB = 'root';
$passwordDB = '';
$dbname = 'db';

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle approval action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_id'])) {
    $approveId = intval($_POST['approve_id']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into pa table
        $insert_sql = "INSERT INTO pa (id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, reference_id, mode_of_payment)
                       SELECT id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, reference_id, mode_of_payment
                       FROM br WHERE id = ?";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("i", $approveId);

        if ($stmt_insert->execute()) {
            // Now delete from br table
            $delete_sql = "DELETE FROM br WHERE id = ?";
            $stmt_delete = $conn->prepare($delete_sql);
            $stmt_delete->bind_param("i", $approveId);

            if ($stmt_delete->execute()) {
                // Commit transaction if both queries succeed
                $conn->commit();
                echo "
                    <div id='success-message' class='bg-green-500 text-white p-4 rounded'>
                        Budget Approved and moved to Payout!
                    </div>
                    <script>
                        setTimeout(function() {
                            document.getElementById('success-message').style.display = 'none';
                        }, 2000);
                    </script>
                ";
            } else {
                throw new Exception("Error deleting record from br: " . $stmt_delete->error);
            }
        } else {
            throw new Exception("Error inserting record into pa: " . $stmt_insert->error);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='bg-red-500 text-white p-4 rounded'>Transaction failed: " . $e->getMessage() . "</div>";
    }
}

// Handle rejection action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_id']) && isset($_POST['reason'])) {
    $rejectId = intval($_POST['reject_id']);
    $reason = $conn->real_escape_string($_POST['reason']);

    try {
        // Insert into rr table
        $insert_sql = "INSERT INTO rr (id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, rejected_reason)
                       SELECT id, account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, ?
                       FROM br WHERE id = ?";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("si", $reason, $rejectId);

        if ($stmt_insert->execute()) {
            // Now delete from br table
            $delete_sql = "DELETE FROM br WHERE id = ?";
            $stmt_delete = $conn->prepare($delete_sql);
            $stmt_delete->bind_param("i", $rejectId);

            if ($stmt_delete->execute()) {
                // Commit transaction if both queries succeed
                $conn->commit();
                echo "
                    <div id='success-message' class='bg-red-500 text-white p-4 rounded'>
                        Budget Rejected and moved to Rejected Requests!
                    </div>
                    <script>
                        setTimeout(function() {
                            document.getElementById('success-message').style.display = 'none';
                        }, 2000);
                    </script>
                ";
            } else {
                throw new Exception("Error deleting record from br: " . $stmt_delete->error);
            }
        } else {
            throw new Exception("Error inserting record into rr: " . $stmt_insert->error);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='bg-red-500 text-white p-4 rounded'>Transaction failed: " . $e->getMessage() . "</div>";
    }
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);

    $delete_sql = "DELETE FROM br WHERE id = ?";
    $stmt_delete = $conn->prepare($delete_sql);
    $stmt_delete->bind_param("i", $deleteId);

    if ($stmt_delete->execute()) {
        echo "<div id='success-message' class='bg-green-500 text-white p-4 rounded'>
                Record deleted successfully!
              </div>
              <script>
                  setTimeout(function() {
                      document.getElementById('success-message').style.display = 'none';
                  }, 2000);
              </script>";
    } else {
        echo "<div class='bg-red-500 text-white p-4 rounded'>Error deleting record: " . $stmt_delete->error . "</div>";
    }
}

// Fetch disbursement records
$sql = "SELECT * FROM br";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr class='border-b border-gray-300 hover:bg-gray-100'>";
        echo "<td class='py-3 px-6 text-left'>{$row['id']}</td>";
        echo "<td class='py-3 px-6 text-left'>{$row['reference_id']}</td>";
        echo "<td class='py-3 px-6 text-left'>{$row['account_name']}</td>";
        echo "<td class='py-3 px-6 text-left'>{$row['requested_department']}</td>";
        echo "<td class='py-3 px-6 text-left'>{$row['mode_of_payment']}</td>";
        echo "<td class='py-3 px-6 text-left'>{$row['expense_categories']}</td>";
        echo "<td class='py-3 px-6 text-right'>" . number_format($row['amount'], 2) . "</td>";
        echo "<td class='py-3 px-6 text-left'>{$row['description']}</td>";

        // Document download link
        if (!empty($row['document']) && file_exists("files/" . $row['document'])) {
            echo "<td><a href='download.php?file=" . urlencode($row['document']) . "' style='color: blue; text-decoration: underline;'>Download</a></td>";
        } else {
            echo "<td>No document available</td>";
        }

        echo "<td class='py-3 px-6 text-left'>{$row['payment_due']}</td>";

        // Action buttons
        echo "<td class='py-3 px-6 text-left'>
            <div class='flex justify-start items-center space-x-1'>
                <form method='POST' action=''>
                    <input type='hidden' name='approve_id' value='{$row['id']}'>
                    <button type='submit' class='text-blue-500 w-8 h-8 flex justify-center items-center'>
                        <i class='fas fa-check'></i>
                    </button>
                </form>
                <form method='POST' action=''>
                    <input type='hidden' name='reject_id' value='{$row['id']}'>
                    <input type='hidden' name='reason' id='reason-{$row['id']}'>
                    <button type='button' class='reject-btn text-red-500 w-8 h-8 flex justify-center items-center' data-id='{$row['id']}'>
                        <i class='fas fa-times'></i>
                    </button>
                </form>

                <a href='edit.php?id={$row['id']}' class='text-yellow-500 w-8 h-8 flex justify-center items-center mb-3'>
                    <i class='fas fa-edit'></i>
                </a>
                <form method='POST' action='del.php' onsubmit='return confirm(\"Are you sure you want to delete this record?\");'>
                    <input type='hidden' name='id' value='{$row['id']}'>
                    <button type='submit' class='text-red-500 w-8 h-8 flex justify-center items-center'>
                        <i class='fas fa-trash-alt'></i>
                    </button>
                </form>
            </div>
        </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='9' class='text-center'>No records found</td></tr>";
}
$conn->close();
?>


</script>




                                    
                                  

                                    
                


            </tbody>
        </table>

        <div class="mt-6">
        <canvas id="pdf-viewer" width="600" height="400"></canvas>
      </div>

<!-- Modal for Reject Reason -->
<div id="rejectModal" class="modal" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-gray-800 text-white p-4"> <!-- Added padding for better spacing -->
            <div class="modal-header">
                
                <button type="button" class="close text-white" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="rejectForm" method="POST" action="budget_request.php">
                    <!-- Hidden input to pass the reject_id -->-
                    <input type="hidden" name="reject_id" id="reject_id">
                    
                    <div class="form-group">
                        <!-- Textarea for entering reason for rejection -->
                        <label for="reason" class="text-xs mt-1">REASON FOR REJECTION:</label>
                        <textarea class="form-control bg-gray-700 text-white p-2 w-full mt-2" name="reason" id="reason" rows="4" required style="resize: none;"></textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary bg-blue-600 hover:bg-blue-700 mt-3 w-full">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to toggle the modal -->
<script>
    // Function to open the modal and set the reject_id (you can trigger this with your Reject button)
    function openModal(rejectId) {
        document.getElementById("reject_id").value = rejectId; // Set the hidden reject_id value
        document.getElementById("rejectModal").style.display = "block"; // Show the modal
    }

    // Function to close the modal
    function closeModal() {
        document.getElementById("rejectModal").style.display = "none"; // Hide the modal
    }
</script>



              </div>
    </div>
     </div>
    </div>
   </div>
  </div>

   </div>
  </div>

   </div>
  </div>
 </body>
</html>