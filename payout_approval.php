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
        <a class="text-gray-600 font-bold" href="#">Disbursement</a>
      </li>
      <li>
        <span class="mx-2">&gt;</span>
      </li>
      <li>
        <a class="text-gray-600 font-bold" href="#">Payout Approval</a>
      </li>
    </ol>
  </nav>
</div>

<!-- Main content area -->
<div class="flex-1 bg-blue-100 p-6 w-full">
  <h1 class="font-bold text-2xl text-blue-900">PAYOUT APPROVAL</h1>
  <br>

  <?php
  // Database connection
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
      $approveId = $_POST['approve_id'];

      // Fetch mode_of_payment to decide where to insert
      $mode_sql = "SELECT mode_of_payment FROM pa WHERE id = ?";
      $stmt_mode = $conn->prepare($mode_sql);
      $stmt_mode->bind_param("i", $approveId);
      $stmt_mode->execute();
      $stmt_mode->bind_result($mode_of_payment);
      $stmt_mode->fetch();
      $stmt_mode->close();

      // Check if mode_of_payment was fetched successfully
      if (empty($mode_of_payment)) {
          echo "<div class='bg-red-500 text-white p-4 rounded'>Error: Mode of payment not found or invalid for ID $approveId.</div>";
      } else {
          // Begin transaction
          $conn->begin_transaction();

          // Determine which table to insert into based on mode_of_payment
          if ($mode_of_payment == 'Bank Transfer') {
              $insert_sql = "
                INSERT INTO payout (
                  id, account_name, requested_department, expense_categories, amount, description, document, 
                  payment_due, bank_name, bank_account_number, reference_id, mode_of_payment
                )
                SELECT 
                  id, account_name, requested_department, expense_categories, amount, description, document, 
                  payment_due, bank_name, bank_account_number,
                  CONCAT('BT-', SUBSTRING(reference_id, 4)) AS reference_id, mode_of_payment
                FROM pa 
                WHERE id = ?
              ";
          } elseif ($mode_of_payment == 'Cash') {
              $insert_sql = "
                INSERT INTO cash (
                  id, account_name, requested_department, expense_categories, amount, description, document, 
                  payment_due, bank_name, bank_account_number, reference_id, mode_of_payment
                )
                SELECT 
                  id, account_name, requested_department, expense_categories, amount, description, document, 
                  payment_due, bank_name, bank_account_number,
                  CONCAT('CH-', SUBSTRING(reference_id, 4)) AS reference_id, mode_of_payment
                FROM pa 
                WHERE id = ?
              ";
          } elseif ($mode_of_payment == 'Ecash') {
              $insert_sql = "
                INSERT INTO ecash (
                  id, account_name, requested_department, expense_categories, amount, description, document, 
                  payment_due, bank_name, bank_account_number, reference_id, mode_of_payment
                )
                SELECT 
                  id, account_name, requested_department, expense_categories, amount, description, document, 
                  payment_due, bank_name, bank_account_number,
                  CONCAT('EC-', SUBSTRING(reference_id, 4)) AS reference_id, mode_of_payment
                FROM pa 
                WHERE id = ?
              ";
          } elseif ($mode_of_payment == 'Cheque') {
              $insert_sql = "
                INSERT INTO cheque (
                  id, account_name, requested_department, expense_categories, amount, description, document, 
                  payment_due, bank_name, bank_account_number, reference_id, mode_of_payment
                )
                SELECT 
                  id, account_name, requested_department, expense_categories, amount, description, document, 
                  payment_due, bank_name, bank_account_number,
                  CONCAT('CQ-', SUBSTRING(reference_id, 4)) AS reference_id, mode_of_payment
                FROM pa 
                WHERE id = ?
              ";
          } else {
              echo "<div class='bg-red-500 text-white p-4 rounded'>Invalid mode of payment: " . htmlspecialchars($mode_of_payment) . "</div>";
              exit; // Stop execution if mode_of_payment is invalid
          }

          // Prepare the insert query for payment method
          $stmt_insert = $conn->prepare($insert_sql);
          $stmt_insert->bind_param("i", $approveId);

          try {
              if ($stmt_insert->execute()) {
                  // After successful insertion, delete from 'pa' table
                  $delete_sql = "DELETE FROM pa WHERE id = ?";
                  $stmt_delete = $conn->prepare($delete_sql);
                  $stmt_delete->bind_param("i", $approveId);

                  // We need to fetch the reference_id first before we delete from pa,
                  // so we can update tr. Alternatively, we can do a subselect if we prefer:
                  $ref_query = "SELECT reference_id FROM pa WHERE id = ?";
                  $stmt_ref = $conn->prepare($ref_query);
                  $stmt_ref->bind_param("i", $approveId);
                  $stmt_ref->execute();
                  $stmt_ref->bind_result($ref_id_fetched);
                  $stmt_ref->fetch();
                  $stmt_ref->close();

                  if ($stmt_delete->execute()) {
                      // Update status in tr table
                      $update_tr_sql = "UPDATE tr SET status = 'disbursed' WHERE reference_id = ?";
                      $stmt_update_tr = $conn->prepare($update_tr_sql);
                      $stmt_update_tr->bind_param("s", $ref_id_fetched);
                      $stmt_update_tr->execute();

                      // Commit transaction if everything succeeds
                      $conn->commit();
                      echo "
                          <div id='success-message' class='bg-green-500 text-white p-4 rounded'>
                              Budget Approved and moved to the appropriate table!
                          </div>
                          <script>
                              setTimeout(function() {
                                  document.getElementById('success-message').style.display = 'none';
                              }, 2000);
                          </script>
                      ";
                  } else {
                      throw new Exception("Error deleting record from pa: " . $stmt_delete->error);
                  }
              } else {
                  throw new Exception("Error inserting record into appropriate table: " . $stmt_insert->error);
              }
          } catch (Exception $e) {
              $conn->rollback();
              echo "<div class='bg-red-500 text-white p-4 rounded'>Transaction failed: " . $e->getMessage() . "</div>";
          }
      }
  }

  /** 
   * ====================
   * FILTERS + PAGINATION
   * ====================
   */
  // Capture filter inputs (using GET so that pagination links can carry the filter parameters)
  $search_ref_id     = isset($_GET['search_ref_id'])     ? $_GET['search_ref_id']     : '';
  $search_name       = isset($_GET['search_name'])       ? $_GET['search_name']       : '';
  $date_from         = isset($_GET['date_from'])         ? $_GET['date_from']         : '';
  $date_to           = isset($_GET['date_to'])           ? $_GET['date_to']           : '';
  $department_filter = isset($_GET['department_filter']) ? $_GET['department_filter'] : '';

  // Pagination settings
  $records_per_page = 5; 
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  if ($page < 1) { 
      $page = 1;
  }
  $offset = ($page - 1) * $records_per_page;

  // Build the WHERE clause dynamically
  $sql_where = " WHERE 1=1";
  // Search by reference_id
  if (!empty($search_ref_id)) {
      $sql_where .= " AND reference_id LIKE '%" . $conn->real_escape_string($search_ref_id) . "%'";
  }
  // Search by account_name
  if (!empty($search_name)) {
      $sql_where .= " AND account_name LIKE '%" . $conn->real_escape_string($search_name) . "%'";
  }

  // Filter by payment_due date
  if (!empty($date_from) && !empty($date_to)) {
      $sql_where .= " AND payment_due BETWEEN '" . $conn->real_escape_string($date_from) . "' 
                                      AND '" . $conn->real_escape_string($date_to) . "'";
  } else if (!empty($date_from)) {
      $sql_where .= " AND payment_due >= '" . $conn->real_escape_string($date_from) . "'";
  } else if (!empty($date_to)) {
      $sql_where .= " AND payment_due <= '" . $conn->real_escape_string($date_to) . "'";
  }

  // Filter by department
  if (!empty($department_filter)) {
      $sql_where .= " AND requested_department = '" . $conn->real_escape_string($department_filter) . "'";
  }

  // 1) Count total records for pagination
  $count_sql = "SELECT COUNT(*) as total FROM pa $sql_where";
  $count_result = $conn->query($count_sql);
  $row_count = $count_result->fetch_assoc();
  $total_rows = $row_count['total'];
  $total_pages = ($total_rows > 0) ? ceil($total_rows / $records_per_page) : 1;

  // 2) Actual fetch with limit
  $sql = "SELECT * FROM pa $sql_where 
          ORDER BY id DESC
          LIMIT $offset, $records_per_page";
  $result = $conn->query($sql);
  ?>

  <!-- FILTER FORM -->
  <form method="GET" class="mb-4">
    <div class="flex flex-wrap gap-2">
      <!-- Search by Reference ID -->
      <input 
        type="text" 
        name="search_ref_id" 
        placeholder="Search by Reference ID" 
        class="border rounded px-2 py-1"
        value="<?php echo htmlspecialchars($search_ref_id); ?>"
      />

      <!-- Search by Account Name -->
      <input 
        type="text" 
        name="search_name" 
        placeholder="Search by Account Name" 
        class="border rounded px-2 py-1"
        value="<?php echo htmlspecialchars($search_name); ?>"
      />

      <!-- Date From -->
      <div class="flex items-center">
        <label class="mr-1 text-gray-700 font-semibold">From:</label>
        <input 
          type="date" 
          name="date_from" 
          class="border rounded px-2 py-1"
          value="<?php echo htmlspecialchars($date_from); ?>"
        />
      </div>

      <!-- Date To -->
      <div class="flex items-center">
        <label class="mr-1 text-gray-700 font-semibold">To:</label>
        <input 
          type="date" 
          name="date_to" 
          class="border rounded px-2 py-1"
          value="<?php echo htmlspecialchars($date_to); ?>"
        />
      </div>

      <!-- Department Filter -->
      <select name="department_filter" class="border rounded px-2 py-1">
        <option value="">All Departments</option>
        <!-- Your Department List -->
        <option value="Admin"             <?php if ($department_filter == 'Admin')             echo 'selected'; ?>>Admin</option>
        <option value="Core-1"           <?php if ($department_filter == 'Core-1')           echo 'selected'; ?>>Core-1</option>
        <option value="Core-2"           <?php if ($department_filter == 'Core-2')           echo 'selected'; ?>>Core-2</option>
        <option value="Human Resource-1" <?php if ($department_filter == 'Human Resource-1') echo 'selected'; ?>>Human Resource-1</option>
        <option value="Human Resource-2" <?php if ($department_filter == 'Human Resource-2') echo 'selected'; ?>>Human Resource-2</option>
        <option value="Human Resource-3" <?php if ($department_filter == 'Human Resource-3') echo 'selected'; ?>>Human Resource-3</option>
        <option value="Human Resource-4" <?php if ($department_filter == 'Human Resource-4') echo 'selected'; ?>>Human Resource-4</option>
        <option value="Logistic-1"       <?php if ($department_filter == 'Logistic-1')       echo 'selected'; ?>>Logistic-1</option>
        <option value="Logistic-2"       <?php if ($department_filter == 'Logistic-2')       echo 'selected'; ?>>Logistic-2</option>
        <option value="Finance"          <?php if ($department_filter == 'Finance')          echo 'selected'; ?>>Finance</option>
      </select>

      <!-- Submit Button -->
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
    </div>
  </form>

  <!-- TABLE -->
  <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
    <thead>
      <tr class="bg-blue-200 text-blue-800 uppercase text-sm leading-normal">
        <th class="px-4 py-2">ID</th>
        <th class="px-4 py-2">Reference ID</th>
        <th class="px-4 py-2">Account Name</th>
        <th class="px-4 py-2">Department</th>
        <th class="px-4 py-2">Mode of Payment</th>
        <th class="px-4 py-2">Category</th>
        <th class="px-4 py-2">Amount</th>
        <th class="px-4 py-2">Description</th>
        <th class="px-4 py-2">Document</th>
        <th class="px-4 py-2">Payment Due</th>
        <th class="px-4 py-2">Status</th>
      </tr>
    </thead>
    <tbody class="text-gray-900 text-sm">
      <?php
      if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<tr class='border-b border-gray-300 hover:bg-gray-200'>";
              echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>{$row['id']}</td>";
              echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>{$row['reference_id']}</td>";
              echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>{$row['account_name']}</td>";
              echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>{$row['requested_department']}</td>";
              echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>{$row['mode_of_payment']}</td>";
              echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>{$row['expense_categories']}</td>";
              echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>₱" . number_format($row['amount'], 2) . "</td>";
              echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>{$row['description']}</td>";

              // Document download link
              if (!empty($row['document']) && file_exists("files/" . $row['document'])) {
                  echo "<td class='border-r border-gray-300 text-center'><a href='download.php?file=" . urlencode($row['document']) . "' style='color: blue; text-align:center; text-decoration: underline;'>Download</a></td>";
              } else {
                  echo "<td class='border-r border-gray-300 text-center'>No document available</td>";
              }

              echo "<td class='py-3 px-6 border-r border-gray-300 text-left'>{$row['payment_due']}</td>";

              echo "<td class='text-left pl-3 border-r border-gray-300'>
                      <form method='POST' action=''>
                          <input type='hidden' name='approve_id' value='{$row['id']}' />
                          <button type='submit' class='bg-yellow-300 text-black p-1 mt-3 font-semibold shadow-lg'>Pending</button>
                      </form>
                    </td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='11' class='text-center py-3'>No records found</td></tr>";
      }
      ?>
    </tbody>
  </table>



<!-- PAGINATION LINKS -->
<?php
// Step 1: Calculate $start, $end, and build $baseQuery
$start = ($page - 1) * $records_per_page + 1;
$end   = min($page * $records_per_page, $total_rows);
if ($total_rows == 0) {
  $start = 0;
  $end   = 0;
}

$baseQuery = http_build_query([
  'search_ref_id'     => $search_ref_id,
  'search_name'       => $search_name,
  'date_from'         => $date_from,
  'date_to'           => $date_to,
  'department_filter' => $department_filter
]);

$prevPage = $page - 1;
$nextPage = $page + 1;
$prevLink = "?$baseQuery&page=$prevPage";
$nextLink = "?$baseQuery&page=$nextPage";
?>

<!-- PAGINATION UI (Previous/Next) -->
<div class="flex justify-between items-center  p-2 mt-4 rounded">
  <div class="text-gray-800 font-semibold">
    Showing <?php echo $start; ?> - <?php echo $end; ?> of <?php echo $total_rows; ?>
  </div>
  <div class="flex gap-2">
    <?php if ($page > 1): ?>
      <a href="<?php echo $prevLink; ?>" 
         class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
         Previous
      </a>
    <?php else: ?>
      <button class="bg-gray-300 text-white px-4 py-2 rounded cursor-not-allowed" disabled>
        Previous
      </button>
    <?php endif; ?>

    <?php if ($page < $total_pages): ?>
      <a href="<?php echo $nextLink; ?>"
         class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
         Next
      </a>
    <?php else: ?>
      <button class="bg-gray-300 text-white px-4 py-2 rounded cursor-not-allowed" disabled>
        Next
      </button>
    <?php endif; ?>
  </div>
</div>


</div>
</body>
</html>