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
  <script>
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

    function openModal() {
      const modal = document.getElementById('addEmployeeModal');
      modal.classList.remove('hidden');
    }

    function closeModal() {
      const modal = document.getElementById('addEmployeeModal');
      modal.classList.add('hidden');
    }


    window.onclick = function(event) {
  const modal = document.getElementById('addEmployeeModal');
  if (event.target === modal) {
    closeModal();
  }
};

function showModal(reason) {
    // Set the rejection reason in the modal
    document.getElementById("reasonText").innerText = reason;

    // Display the modal
    document.getElementById("reasonModal").classList.remove("hidden");
}

function closeModal() {
    // Hide the modal
    document.getElementById("reasonModal").classList.add("hidden");
}

  </script>
  <style>
   .rotate-90 {
     transform: rotate(90deg);
     transition: transform 0.3s ease;
   }

   .z-50 {
  z-index: 50;
}


    .hidden { display: none; }
    .modal-overlay { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0, 0, 0, 0.5); }
    .modal-content { background: white; padding: 20px; border-radius: 5px; max-width: 500px; width: 90%; }
    .close-btn { background: #3490dc; color: white; padding: 8px 12px; border: none; border-radius: 3px; cursor: pointer; }

  </style>
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
        <a class="text-gray-600 font-bold" href="#">Rejected Request</a>
       </li>
      </ol>
     </nav>
    </div>
    <!-- Main content area -->
    <div class="flex-1 bg-blue-100 p-6">
     <div class="w-full">
        <h1 class="font-bold text-2xl text-blue-900 mb-8">REJECTED REQUEST</h1> 
        <table class="min-w-full bg-white border-8 border-blue-200 shadow-2xl">
            <thead>
                <tr class="bg-blue-200 text-blue-900 uppercase text-sm leading-normal">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Reference ID</th>
                    <th class="px-4 py-2">Account Name</th>
                    <th class="px-4 py-2">Department</th>
                    <th class="px-4 py-2">MOD</th>
                    <th class="px-4 py-2">Expense Category</th>
                    <th class="px-4 py-2">Amount</th> 
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Document</th>
                    <th class="px-4 py-2">Payment Due</th>
                    <th>Reject Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-900 text-sm font-light">

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
                                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                                      // Approve logic
                                      if (isset($_POST['approve_id'])) {
                                          $approveId = $_POST['approve_id'];
                                          
                                          // Insert into the table
                                          $insert_sql = "INSERT INTO br (id, reference_id, account_name, requested_department, mode_of_payment, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number)
                                                         SELECT id, reference_id, account_name, requested_department, mode_of_payment, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number FROM rr WHERE id = '$approveId'";
                                          
                                          if ($conn->query($insert_sql) === TRUE) {
                                            // After successful insertion, delete the row
                                            $delete_sql = "DELETE FROM rr WHERE id = '$approveId'";
                                            if ($conn->query($delete_sql) === TRUE) {
                                                echo "
                                                    <div id='success-message' class='bg-green-500 text-white p-4 rounded'>
                                                        Resent Success!
                                                    </div>
                                                    <script>
                                                        setTimeout(function() {
                                                            document.getElementById('success-message').style.display = 'none';
                                                        }, 3000); // 3000 milliseconds = 3 seconds
                                                    </script>
                                                ";
                                            } else {
                                                echo "Error deleting record: " . $conn->error;
                                            }
                                        } else {
                                            echo "Error inserting record: " . $conn->error;
                                        }
                                      }
                                    }
// Handle rejection action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_id'])) {
  $rejectId = $_POST['reject_id'];
  $reason = $_POST['reason'];

  // Insert into the table
  $insert_sql = "INSERT INTO rr (id, reference_id, account_name, requested_department, mode_of_payment, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number)
                 SELECT id, reference_id, account_name, requested_department, mode_of_payment, expense_categories, amount, description, document, payment_due, bank_name, bank_account_number FROM br WHERE id = '$rejectId'";

  if ($conn->query($insert_sql) === TRUE) {
      // After successful insertion, update rejection reason
      $update_sql = "UPDATE rr SET rejected_reason = ? WHERE id = ?";
      $stmt = $conn->prepare($update_sql);
      $stmt->bind_param("si", $reason, $rejectId);

      if ($stmt->execute()) {
          echo "<div class='bg-red-500 text-white p-4 rounded'>Budget Rejected!</div>";
      } else {
          echo "Error updating rejection reason: " . $conn->error;
      }
  } else {
      echo "Error inserting into rr: " . $conn->error;
  }
}


                                  

                                    // Fetch disbursement records
                                    $sql = "SELECT * FROM rr";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr class='border-b border-gray-300 hover:bg-gray-200'>";
                                            echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['id']}</td>";
                                            echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['reference_id']}</td>";
                                            echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['account_name']}</td>";
                                            echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['requested_department']}</td>";
                                            echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['mode_of_payment']}</td>";
                                            echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['expense_categories']}</td>";
                                            echo "<td class='py-3 px-6 text-left border-r border-gray-300'>" . number_format($row['amount'], 2) . "</td>";
                                            echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['description']}</td>";
                                            // Document download link
                              if (!empty($row['document']) && file_exists("files/" . $row['document'])) {
                                echo "<td class='py-3 px-6 text-left border-r border-gray-300'><a href='download.php?file=" . urlencode($row['document']) . "' style='color: blue; text-align: center; text-decoration: underline;'>Download</a></td>";
                              } else {
                            echo "<td>No document available</td>";
                                }
                                            echo "<td class='py-3 px-6 text-left border-r border-gray-300'>{$row['payment_due']}</td>";
                                            echo "<td class='border-r border-gray-300 text-center'>
                                            <button onclick=\"showModal('{$row['rejected_reason']}')\" class='text-blue-500 underline '>View Reason</button>
                                          </td>";
                                          
                                            
                                            echo "<td class='py-0 px-6 text-left border-r'>
                                            <div class='flex justify-start items-center space-x-1'>  <!-- Reduced space between buttons -->
                                                  <!-- Resent Button -->
                                                <form method='POST' action=''>
                                                    <input type='hidden' name='approve_id' value='{$row['id']}'>
                                                    <button type='submit' class='text-blue-500 w-8 h-8 flex justify-center items-center'>  <!-- Smaller buttons -->
                                                        <i class='fas fa-paper-plane'></i>
                                                    </button>
                                                </form>
                                    
                                                <!-- Delete Button -->
                                                <form method='POST' action='del1.php' onsubmit='return confirm(\"Are you sure you want to delete this record?\");'>
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
                                        echo "<tr><td colspan='5' class='text-center py-3'>No records found</td></tr>";
                                    }
                                    $conn->close();
                                    ?>
                


            </tbody>
        </table>
        <div id="reasonModal" class="modal-overlay hidden">
    <div class="modal-content">
        <h2 class="text-lg font-bold mb-4">Reason for Rejection</h2>
        <p id="reasonText" class="mb-4 text-gray-700"></p>
        <button onclick="closeModal()" class="close-btn">Close</button>
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
