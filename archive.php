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
    // ==================
    // Existing Scripts
    // ==================

    // For Reject Buttons
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

    // Example PDF Viewing (not critical, but left intact)
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
  <div class="bg-blue-200 p-4">
    <nav class="text-gray-600 font-bold">
      <ol class="list-reset flex">
        <li>
          <a class="text-gray-600 font-bold" href="TNVSFinance.php">Dashboard</a>
        </li>
        <li><span class="mx-2">&gt;</span></li>
        <li>
          <a class="text-gray-600 font-bold" href="#">Budget</a>
        </li>
        <li><span class="mx-2">&gt;</span></li>
        <li>
          <a class="text-gray-600 font-bold" href="budget_request.php">Budget Request</a>
        </li>
      </ol>
    </nav>
  </div>

  <!-- Main content area -->
  <div class="flex-1 bg-blue-100 p-6 w-full">

    <div class="w-full">


      <?php
      // =============================
      // Database and POST Handling
      // =============================
      $servername   = '127.0.0.1:3308';
      $usernameDB   = 'root';
      $passwordDB   = '';
      $dbname       = 'db';

      $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
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
          $reason   = $conn->real_escape_string($_POST['reason']);

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

      // ==============================
      // Re-establish DB (as in original code)
      // ==============================
      $servername = '127.0.0.1:3308';
      $usernameDB = 'root';
      $passwordDB = '';
      $dbname     = 'db';

      $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

      // =========================
      // Existing Time Period Filter
      // =========================
      $timePeriod = isset($_GET['time_period']) ? $_GET['time_period'] : 'all';

      // =========================
      // ADDED FOR REFERENCE ID SEARCH
      // =========================
      $searchID   = isset($_GET['search_id']) ? $_GET['search_id'] : '';

      // Build base query
      $sql = "SELECT * FROM archive WHERE 1=1";

      // Filter by time_period if not 'all'
      if ($timePeriod !== 'all') {
          $sql .= " AND time_period = '$timePeriod'";
      }

      // Filter by Reference ID if search box is not empty
      if (!empty($searchID)) {
          // Safe escape to prevent injection
          $safeSearch = $conn->real_escape_string($searchID);
          // Use LIKE for partial match
          $sql .= " AND reference_id LIKE '%$safeSearch%'";
      }

      // Finally run the query
      $result = $conn->query($sql);
      ?>

      <!-- Filter Form (Time Period + Search by Reference ID) -->
      <form method="GET" action="">
          <label for="time_period">Filter by Time Period:</label>
          <select name="time_period" id="time_period" onchange="this.form.submit()">
              <option value="all"      <?= ($timePeriod === 'all'     ? 'selected' : '') ?>>All</option>
              <option value="weekly"   <?= ($timePeriod === 'weekly'  ? 'selected' : '') ?>>Weekly</option>
              <option value="monthly"  <?= ($timePeriod === 'monthly' ? 'selected' : '') ?>>Monthly</option>
              <option value="annually" <?= ($timePeriod === 'annually'? 'selected' : '') ?>>Annually</option>
              <option value="yearly"   <?= ($timePeriod === 'yearly'  ? 'selected' : '') ?>>Yearly</option>
          </select>

          <!-- ADDED for Reference ID search -->
          <label for="search_id" class="ml-4">Search by Reference ID:</label>
          <input type="text" name="search_id" id="search_id"
                 value="<?= htmlspecialchars($searchID) ?>"
                 placeholder="e.g. BR-1234-2025"
                 class="border px-2 py-1 rounded" />

          <button type="submit" class="ml-2 px-4 py-1 bg-blue-600 text-white rounded">
              Search
          </button>
      </form>

      <!-- Table of Records -->
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
              <th class="sticky top-0 bg-blue-200 px-2 py-2">Time Period</th>
              <th class="sticky top-0 bg-blue-200">Payment Due</th>
            </tr>
          </thead>
          <tbody class="text-sm font-light bg-gray-100">

          <?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr class='border-b border-gray-300 hover:bg-gray-200'>";
        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['id']}</td>";
        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['reference_id']}</td>";
        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['account_name']}</td>";
        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['requested_department']}</td>";
        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['mode_of_payment']}</td>";
        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['expense_categories']}</td>";
        echo "<td class='py-3 px-6 text-left border-r border-gray-300 text-right'>" . number_format($row['amount'], 2) . "</td>";
        echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['description']}</td>";

        // Document download link
        if (!empty($row['document']) && file_exists("files/" . $row['document'])) {
            echo "<td class='border border-gray-300 text-center'>
                    <a href='download.php?file=" . urlencode($row['document']) . "' 
                       style='color: blue; text-decoration: underline;'>
                      Download
                    </a>
                  </td>";
        } else {
            echo "<td class='border border-gray-300 px-2 text-center'>No document available</td>";
        }

        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['time_period']}</td>";
        echo "<td class='py-2 px-6 text-left border border-gray-300'>{$row['payment_due']}</td>";
        echo "</tr>"; // optional: close the TR here
    } // <-- CLOSES THE while LOOP
} else {
    // Now we can safely do the else
    echo "<tr><td colspan='12' class='text-center'>No records found</td></tr>";
}
$conn->close();
?>

          </tbody>
        </table>
      </div>
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

  <!-- Custom Confirmation Modal -->
  <div id="confirmationModal" 
       class="modal fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50" 
       tabindex="-1" 
       role="dialog" 
       style="display: none;">
    <div class="bg-white p-6 rounded-lg shadow-lg w-80">
      <div class="text-center">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Confirm Rejection</h2>
        <p class="text-gray-600 mb-4">Are you sure you want to reject this request?</p>
        <div class="flex justify-center space-x-4">
          <button id="confirmRejectBtn" 
                  class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
            Yes, Reject
          </button>
          <button onclick="closeConfirmationModal()" 
                  class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript to manage modals and confirmation -->
  <script>
    let formToSubmit; // Global variable to store form reference for confirmation

    // Function to open the rejection modal
    function openModal(rejectId) {
        document.getElementById("reject_id").value = rejectId;
        document.getElementById("rejectModal").style.display = "flex";
    }

    // Function to close the rejection modal
    function closeModal() {
        document.getElementById("rejectModal").style.display = "none";
    }

    // Function to open the confirmation modal
    function openConfirmationModal() {
        document.getElementById("confirmationModal").style.display = "flex";
    }

    // Function to close the confirmation modal
    function closeConfirmationModal() {
        document.getElementById("confirmationModal").style.display = "none";
    }

    // Add confirmation step to the rejection form submission
    document.getElementById('rejectForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form from submitting immediately

        const reason = document.getElementById('reason').value.trim();
        if (reason === '') {
            alert('Please enter a reason for rejection.');
            return;
        }

        formToSubmit = this; // Store the form in a variable for later submission
        openConfirmationModal(); // Open the custom confirmation modal
    });

    // Confirm rejection when 'Yes, Reject' is clicked
    document.getElementById('confirmRejectBtn').addEventListener('click', function() {
        formToSubmit.submit(); // Submit the stored form
    });

    // Additional JS for Approvals: already included above in the echo block
  </script>

  <script>
    // ================
    // ADD REQUEST MODAL (Iframe)
    // ================
    function openAddRequestModal() {
      // Show the modal
      document.getElementById('addRequestModal').classList.remove('hidden');
      // Set the iframe source to your form page
      document.getElementById('addRequestIframe').src = 'add_ap.php';
    }

    // Close the Modal & Clear the iframe (optional)
    function closeAddRequestModal() {
      document.getElementById('addRequestModal').classList.add('hidden');
      document.getElementById('addRequestIframe').src = '';
    }
  </script>

  <!-- The Add Request Modal (Initially Hidden) -->
  <div id="addRequestModal"
       class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 hidden">
    <!-- Modal Content Wrapper -->
    <div class="bg-white w-11/12 md:w-3/4 lg:w-1/2 rounded-lg shadow-2xl relative overflow-hidden">
      <!-- Close Button (top-right) -->
      <button onclick="closeAddRequestModal()"
              class="absolute top-2 right-2 text-gray-700 text-2xl font-bold">&times;</button>
      <!-- Iframe Container -->
      <div class="p-4 h-[80vh]">
        <!-- Tailwind's h-[80vh] sets the height to 80% of the viewport -->
        <iframe id="addRequestIframe"
                src=""
                class="w-full h-full border-0"
                title="Add Request">
        </iframe>
      </div>
    </div>
  </div>

 </body>
</html>
