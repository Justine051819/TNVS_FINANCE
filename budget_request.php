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

   
  </script>
 </head>  
 <body class="bg-blue-100 overflow-hidden">
    
 <?php include('navbar_sidebar.php'); ?>
  
    <!-- Breadcrumb -->
    <div class="bg-blue-200 p-4 ">
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
        <a class="text-gray-600 font-bold" href="#">Budget Request</a>
       </li>
      </ol>
     </nav>
    </div>


    <!-- Main content area -->

<div class="flex-1 bg-blue-100 p-6 w-full">

     <div class="w-full">
        <a class="bg-blue-700 text-white px-2 py-1 rounded text-lg cursor-pointer whitespace-nowrap mb-4 float-right shadow-lg" href="add_ap.php" role="button">ADD REQUEST</a>
        <h1 class="font-bold text-2xl text-blue-900">BUDGET REQUEST</h1> 
        <br> 
        <div class="overflow-y-scroll h-[530px] bg-white border-8 border-blue-200">
        <table class="min-w-full bg-white shadow-2xl">
            <thead>
                <tr class="bg-blue-200 text-blue-900 uppercase text-sm leading-normal">
                    <th class="sticky top-0 bg-blue-200 px-2 py-2">ID</th>
                    <th class="sticky top-0 bg-blue-200 px-2 py-2">Reference ID</th>
                    <th class="sticky top-0 bg-blue-200 px-2 py-2">Account Name</th>
                    <th class="sticky top-0 bg-blue-200 px-2 py-2">Department</th>
                    <th class="sticky top-0 bg-blue-200 px-2 py-2">Payment</th>
                    <th class="sticky top-0 bg-blue-200 px-2 py-2">Expense Category</th>
                    <th class="sticky top-0 bg-blue-200 px-2 py-2">Amount</th> 
                    <th class="sticky top-0 bg-blue-200 px-2 py-2">Description</th>
                    <th class="sticky top-0 bg-blue-200 px-2 py-2">Document</th>
                    <th class="sticky top-0 bg-blue-200 ">Payment Due</th>
                    <th class="sticky top-0 bg-blue-200 ">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm font-light bg-gray-100">

            

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
        $insert_sql = "INSERT INTO pa (account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number, reference_id, mode_of_payment)
        SELECT account_name, requested_department, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number,
               CONCAT('PA-', SUBSTRING(reference_id, 4)) AS reference_id, mode_of_payment
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

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert into rr table
        $insert_sql = "INSERT INTO rr (reference_id, account_name, requested_department, mode_of_payment, expense_categories, amount, description, document, payment_due, rejected_reason)
                       SELECT reference_id, account_name, requested_department, mode_of_payment, expense_categories, amount, description, document, payment_due, ?
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
                    <div id='success-message' class='bg-green-500 text-white p-4 rounded'>
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
        echo "<tr class='border-b border-gray-300  hover:bg-gray-200'>";
        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['id']}</td>";
        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['reference_id']}</td>";
        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['account_name']}</td>";
        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['requested_department']}</td>";
        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['mode_of_payment']}</td>";
        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['expense_categories']}</td>";
        echo "<td class='py-2 px-6 text-left border border-gray-300'>₱" . number_format($row['amount'], 2) . "</td>";
        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['description']}</td>";

        // Document download link
        if (!empty($row['document']) && file_exists("files/" . $row['document'])) {
            echo "<td class='border border-gray-300 text-center'><a href='download.php?file=" . urlencode($row['document']) . "' style='color: blue; text-decoration: underline;'>Download</a></td>";
        } else {
            echo "<td class='border border-gray-300 px-2 text-center'>No document available</td>";
        }

        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['payment_due']}</td>";

        // Action buttons
        echo "<td class=' pt-3 px-6 text-left border border-gray-300'>
            <div class='flex justify-start items-center space-x-1'>
                <form method='POST' action=''>
                <input type='hidden' name='approve_id' value='{$row['id']}'>
                <button type='submit' class='bg-blue-500 text-white w-20 h-8 text-sm rounded-lg items-center'>
                    Approve
                </button>
            </form>
            <form method='POST' action=''>
                <input type='hidden' name='reject_id' value='{$row['id']}'>
                <input type='hidden' name='reason' id='reason-{$row['id']}'>
                <button type='button' class='reject-btn bg-red-500 text-white w-20 h-8 text-sm flex justify-center rounded-lg items-center' data-id='{$row['id']}'>
                    Reject
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
        </div>
</div>
        <div class="mt-6">
        <canvas id="pdf-viewer" width="600" height="400"></canvas>
      </div>

<!-- Modal for Reject Reason -->
<div id="rejectModal" 
     class="modal fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50" 
     tabindex="-1" 
     role="dialog" 
     style="display: none;">
  <div class="bg-blue-900 text-white p-6 rounded-lg shadow-lg w-80">
    <div class="flex justify-between items-center">
      <h2 class="text-lg font-bold">Reason for Rejection</h2>
      <button type="button" aria-label="Close" onclick="closeModal()" class="text-white font-bold">&times;</button>
    </div>
    <form id="rejectForm" method="POST" action="budget_request.php" class="mt-4">
      <input type="hidden" name="reject_id" id="reject_id">
      <div>
        <label for="reason" class="text-sm">Reason:</label>
        <textarea name="reason" id="reason" rows="4" 
                  class="w-full p-2 mt-2 bg-white text-black rounded focus:outline-none focus:ring-2 focus:ring-blue-500" 
                  required></textarea>
      </div>
      <button type="submit" 
              class="bg-blue-600 hover:bg-blue-700 mt-4 w-full text-white font-bold py-2 px-4 rounded">
        Submit
      </button>
    </form>
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



        

  
 </body>
</html>