<?php
  // Set the current page from the URL parameter
  $page = $_GET['page'] ?? 'dashboard'; 

  $dropdowns = [
    'budget' => ['budgetrequest', 'rejectrequest', 'budgetallocation', 'budgetestimation'],
    'disburse' => ['payoutapproval', 'banktransfer', 'ecash' , 'cheque' , 'cash', 'disbursedrecords'],
    'collect' => ['paymentrecords', 'apreceipts', 'arreceipts'],
    'ap' => ['iapayables', 'payables', 'payablesrecords'],
    'ar' => ['iareceivables', 'receivables','receivablesrecords'],
    'gl' => ['chartsofaccounts', 'journalentry', 'ledger', 'trialbalance', 'financialstatement', 'assetrecords', 'payrollrecords', 'auditreports'],
    'tax' => ['taxemployees', 'taxpaidrecords']
];

$activeDropdown = null;

// Check kung aling dropdown ang dapat bukas
foreach ($dropdowns as $dropdown => $pages) {
    if (in_array($page, $pages)) {
        $activeDropdown = $dropdown;
        break;
    }
}

?>
<?php
// Set the current page from the URL parameter
$page = $_GET['page'] ?? 'dashboard'; 

// Modules data
$modules = [
    'dashboard' => 'Dashboard',
    'budgetrequest' => 'Budget Requests',
    'rejectrequest' => 'Rejected Requests',
    'budgetallocation' => 'Budget Allocation',
    'budgetestimation' => 'Budget Estimation',
    'payoutapproval' => 'Payout Approval',
    'banktransfer' => 'Bank Transfer Payout',
    'ecash' => 'Ecash Payout',
    'cheque' => 'Cheque Payout',
    'cash' => 'Cash Payout',
    'disbursedrecords' => 'Disbursed Records',
    'paymentrecords' => 'Payment Records',
    'apreceipts' => 'Payables Receipts',
    'arreceipts' => 'Receivables Receipts',
    'iapayables' => 'Account Payables',
    'payables' => 'Payables',
    'payablesrecords' => 'Payables Records',
    'iareceivables' => 'Account Receivables',
    'receivables' => 'Receivables',
    'receivablesrecords' => 'Receivables Records',
    'chartsofaccounts' => 'Charts of Accounts',
    'journalentry' => 'Journal Entry',
    'ledger' => 'Ledger',
    'trialbalance' => 'Trial Balance',
    'financialstatement' => 'Financial Statement',
    'assetrecords' => 'Asset Records',
    'payrollrecords' => 'Payroll Records',
    'auditreports' => 'Audit Reports',
    'taxemployees' => 'Employees Tax Records',
    'taxpaidrecords' => 'Tax Records',
];

// Handle search query
$searchQuery = $_GET['search'] ?? '';
$searchResults = [];

if (!empty($searchQuery)) {
    // Search through modules for matching terms
    foreach ($modules as $key => $value) {
        if (stripos($value, $searchQuery) !== false) {
            $searchResults[] = ['key' => $key, 'name' => $value];
        }
    }
}
?>

<!-- Display search results -->
<?php if (!empty($searchQuery)): ?>
    <div>
        <h3>Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h3>
        <ul>
            <?php if (empty($searchResults)): ?>
                <li>No results found.</li>
            <?php else: ?>
                <?php foreach ($searchResults as $result): ?>
                    <li>
                        <a href="?page=<?php echo $result['key']; ?>">
                            <?php echo $result['name']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>
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
</script>

<div class="flex  h-screen">
    <!-- Sidebar -->
   <div id="sidebar" class="w-64  bg-white p-4 z-10 overflow-y-auto">
    <div class="flex items-center mb-6 ">
     <img alt="Movers logo" class="mr-2" height="200px" src="logo.png" width="250px"/>
     <span class="text-2xl font-bold text-blue-600">
      
     </span>
    </div>
    <nav>
     <ul>
      <li class="mb-4">
      <a href="TNVSFinance.php?page=dashboard" class="flex items-center font-bold shadow-lg pb-2 <?php echo ($page == 'dashboard' ? 'text-blue-600' : 'text-gray-600'); ?>">
        <i class="fas fa-th-large mr-2">
        </i>
        Dashboard
       </a>
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer shadow-lg pb-2" onclick="toggleDropdown('budgetDropdown')">
       <i class="fas fa-calculator mr-2"></i>
         </i>
         Budget
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300 <?php echo ($activeDropdown == 'budget' ? 'rotate-90' : ''); ?>"></i>
        </a>
        <ul class="<?php echo ($activeDropdown == 'budget' ? '' : 'hidden'); ?> pl-8 mt-2" id="budgetDropdown">
         <li class="mb-2">
         <a href="budget_request.php?page=budgetrequest" class="flex items-center font-bold <?php echo ($page == 'budgetrequest' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Budget Requests
          </a>
         </li>
         <li class="mb-2">
         <a href="rejected_request.php?page=rejectrequest" class="flex items-center font-bold <?php echo ($page == 'rejectrequest' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Rejected Requests
          </a>
         </li>
         <li class="mb-2">
         <a href="budget_allocation.php?page=budgetallocation" class="flex items-center font-bold <?php echo ($page == 'budgetallocation' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Budget Allocation
          </a>
         </li>
         <li class="mb-2">
         <a href="budget_estimation.php?page=budgetestimation" class="flex items-center font-bold <?php echo ($page == 'budgetestimation' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Budget Estimation
          </a>
         </li>
        </ul>
        
       </div>
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer shadow-lg pb-2" onclick="toggleDropdown('disburseDropdown')">
         <i class="fas fa-coins mr-2">
         </i>
         Disbursement
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300 <?php echo ($activeDropdown == 'disburse' ? 'rotate-90' : ''); ?>"></i>
         </i>
        </a>
        <ul class="<?php echo ($activeDropdown == 'disburse' ? '' : 'hidden'); ?> pl-8 mt-2" id="disburseDropdown">
         <li class="mb-2">
         <a href="payout_approval.php?page=payoutapproval" class="flex items-center font-bold <?php echo ($page == 'payoutapproval' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Payout Approval
          </a>
         </li>
         <li class="mb-2">
         <a href="banktransfer.php?page=banktransfer" class="flex items-center font-bold <?php echo ($page == 'banktransfer' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Bank Transfer Payout
          </a>
         </li>
         <li class="mb-2">
         <a href="ecash.php?page=ecash" class="flex items-center font-bold <?php echo ($page == 'ecash' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Ecash Payout
          </a>
         </li>
         <li class="mb-2">
         <a href="cheque.php?page=cheque" class="flex items-center font-bold <?php echo ($page == 'cheque' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Cheque Payout
          </a>
         </li>
         <li class="mb-2">
         <a href="cash.php?page=cash" class="flex items-center font-bold <?php echo ($page == 'cash' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Cash Payout
          </a>
         </li>
         <li class="mb-2">
         <a href="disbursedrecords.php?page=disbursedrecords" class="flex items-center font-bold <?php echo ($page == 'disbursedrecords' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Disbursed Records
          </a>
         </li>
        </ul>
       </div>
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer shadow-lg pb-2" onclick="toggleDropdown('collectDropdown')">
         <i class="fas fa-Gift mr-2">
         </i>
         Collection
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300 <?php echo ($activeDropdown == 'collect' ? 'rotate-90' : ''); ?>"></i>
         </i>
        </a>
        <ul class="<?php echo ($activeDropdown == 'collect' ? '' : 'hidden'); ?> pl-8 mt-2" id="collectDropdown">
         <li class="mb-2">
         <a href="paymentrecords.php?page=paymentrecords" class="flex items-center font-bold <?php echo ($page == 'paymentrecords' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Payment Records
          </a>
         </li>
         <li class="mb-2">
         <a href="payables_receipts.php?page=apreceipts" class="flex items-center font-bold <?php echo ($page == 'apreceipts' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Payables Receipts
          </a>
         </li>
         <li class="mb-2">
         <a href="receivables_receipts.php?page=arreceipts" class="flex items-center font-bold <?php echo ($page == 'arreceipts' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Receivables Receipts
          </a>
         </li>
        </ul>
       </div>
      </li>
      <li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer shadow-lg pb-2" onclick="toggleDropdown('apDropdown')">
         <i class="fas fa-landmark mr-2">
         </i>
         Account Payables
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300 <?php echo ($activeDropdown == 'ap' ? 'rotate-90' : ''); ?>"></i>
         </i>
        </a>
        <ul class="<?php echo ($activeDropdown == 'ap' ? '' : 'hidden'); ?> pl-8 mt-2" id="apDropdown">
         <li class="mb-2">
         <a href="payables_ia.php?page=iapayables" class="flex items-center font-bold <?php echo ($page == 'iapayables' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Invoice Approval
          </a>
         </li>
         <li class="mb-2">
         <a href="payables.php?page=payables" class="flex items-center font-bold <?php echo ($page == 'payables' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Payables
          </a>
         </li>
         <li class="mb-2">
         <a href="payables_records.php?page=payablesrecords" class="flex items-center font-bold <?php echo ($page == 'payablesrecords' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Payables Records
          </a>
         </li>
        </ul>
       </div>
      </li>
      <li>
<li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer shadow-lg pb-2" onclick="toggleDropdown('arDropdown')">
         <i class="fas fa-file-invoice-dollar mr-2">
         </i>
         Account Receivables
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300  <?php echo ($activeDropdown == 'ar' ? 'rotate-90' : ''); ?>"></i>
         </i>
        </a>
        <ul class="<?php echo ($activeDropdown == 'ar' ? '' : 'hidden'); ?> pl-8 mt-2" id="arDropdown">
        <li class="mb-2">
        <a href="receivables_ia.php?page=iareceivables" class="flex items-center font-bold <?php echo ($page == 'iareceivables' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Invoice Approval
          </a>
         </li>
         <li class="mb-2">
         <a href="receivables.php?page=receivables" class="flex items-center font-bold <?php echo ($page == 'receivables' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Receivables
          </a>
         </li>
         <li class="mb-2">
         <a href="receivables_records.php?page=receivablesrecords" class="flex items-center font-bold <?php echo ($page == 'receivablesrecords' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Receivables Records
          </a>
         </li>
        </ul>
       </div>
      </li>
      <li>
	<li>
<li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer shadow-lg pb-2" onclick="toggleDropdown('glDropdown')">
         <i class="fas fa-book mr-2">
         </i>
         General Ledger
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300 <?php echo ($activeDropdown == 'gl' ? 'rotate-90' : ''); ?>"></i>
        </a>
        <ul class="<?php echo ($activeDropdown == 'gl' ? '' : 'hidden'); ?> pl-8 mt-2" id="glDropdown">
         <li class="mb-2">
         <a href="charts_of_accounts.php?page=chartsofaccounts" class="flex items-center font-bold <?php echo ($page == 'chartsofaccounts' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Charts of Accounts
          </a>
         </li>
         <li class="mb-2">
         <a href="journal_entry.php?page=journalentry" class="flex items-center font-bold <?php echo ($page == 'journalentry' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Journal Entry
          </a>
         </li>
         <li class="mb-2">
         <a href="ledger.php?page=ledger" class="flex items-center font-bold <?php echo ($page == 'ledger' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Ledger
          </a>
         </li>
         <li class="mb-2">
         <a href="trial_balance.php?page=trialbalance" class="flex items-center font-bold <?php echo ($page == 'trialbalance' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Trial Balance
          </a>
         </li>
         <li class="mb-2">
         <a href="financial_statement.php?page=financialstatement" class="flex items-center font-bold <?php echo ($page == 'financialstatement' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Financial Statement
          </a>
         </li>
        
         <li class="mb-2">
         <a href="audit_reports.php?page=auditreports" class="flex items-center font-bold <?php echo ($page == 'auditreports' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Audit Reports
          </a>
         </li>
        </ul>
       </div>
       </li>
      <li>
	<li>
<li class="mb-4">
       <div>
        <a class="flex items-center text-gray-700 font-bold cursor-pointer shadow-lg pb-2" onclick="toggleDropdown('taxDropdown')">
         <i class="fas fa-file-invoice mr-2">
         </i>
         Tax Management
         <i class="fas fa-chevron-right ml-auto transition-transform duration-300 <?php echo ($activeDropdown == 'tax' ? 'rotate-90' : ''); ?>"></i>
        </a>
        <ul class="<?php echo ($activeDropdown == 'tax' ? '' : 'hidden'); ?> pl-8 mt-2" id="taxDropdown">
         <li class="mb-2">
         <a href="tax_employees.php?page=taxemployees" class="flex items-center font-bold <?php echo ($page == 'taxemployees' ? 'text-blue-600' : 'text-gray-600'); ?>">
           Employees Tax Records
          </a>
         </li>
         <li class="mb-2">
         <a href="paid_tax.php?page=taxpaidrecords" class="flex items-center font-bold <?php echo ($page == 'taxpaidrecords' ? 'text-blue-600' : 'text-gray-600'); ?>">
            Tax Records
          </a>
         </li>
        </ul>
       </div>
      </li>
      
    
     </ul>
    </nav>
   </div>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">

        <!-- Header (Navbar) -->
        <header class="flex items-center justify-between bg-white p-4 border-b-2 border-gray-300">
            <div class="flex items-center ">
                <button class="text-2xl mr-4" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="text-xl font-bold text-blue-600">Finance</h1>
            </div>

            <!-- Search Bar -->
              <div class="relative flex-grow mx-4 max-w-70">
                  <input 
                      type="text" 
                      id="searchInputnavbar" 
                      placeholder="Search" 
                      class="border rounded-full py-2 px-4 w-full text-lg text-gray-700"
                      oninput="showSuggestions()" 
                  />
                  <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 text-lg"></i>
                  
                  <!-- Suggestions Dropdown -->
                  <div id="suggestions" class="absolute left-0 right-0 bg-white border rounded-lg shadow-lg mt-2 hidden">
                      <!-- Suggestions will appear here dynamically -->
                  </div>
              </div>
<script>

              // Define the suggestions and their corresponding pages
              const suggestionsData = {
                  "Dashboard": "TNVSFinance.php",
                  "Budget Requests": "budget_request.php",
                  "Rejected Requests": "rejected_request.php",
                  "Budget Allocation": "budget_allocation.php",
                  "Budget Estimation": "budget_estimation.php",
                  "Payout Approval": "payout_approval.php",
                  "Bank Transfer Payout": "banktransfer.php",
                  "Ecash Payout": "ecash.php",
                  "Cheque Payout": "cheque.php",
                  "Cash Payout": "cash.php",
                  "Disbursed Records": "disbursedrecords.php",
                  "Payment Records": "paymentrecords.php",
                  "Payables Receipts": "payables_receipts.php",
                  "Receivables Receipts": "receivables_receipts.php",
                  "Invoice Approval (Payables)": "payables_ia.php",
                  "Payables": "payables.php",
                  "Payables Records": "payables_records.php",
                  "Invoice Approval (Receivables)": "receivables_ia.php",
                  "Receivables": "receivables.php",
                  "Receivables Records": "receivables_records.php",
                  "Charts of Accounts": "charts_of_accounts.php",
                  "Journal Entry": "journal_entry.php",
                  "Ledger": "ledger.php",
                  "Trial Balance": "trial_balance.php",
                  "Financial Statement": "financial_statement.php",
                  "Audit Reports": "audit_reports.php",
                  "Employees Tax Records": "tax_employees.php",
                  "Tax Records": "paid_tax.php",
                  "Add Budget Request":"add_ap.php"

                  // Add all other mappings here
              };

              function showSuggestions() {
                  const input = document.getElementById("searchInputnavbar");
                  const suggestionsBox = document.getElementById("suggestions");
                  const query = input.value.toLowerCase().trim();
                  
                  // Clear previous suggestions
                  suggestionsBox.innerHTML = "";
                  suggestionsBox.classList.add("hidden");

                  if (query === "") return; // Do not show suggestions for an empty input

                  // Filter suggestions based on the query
                  const filteredSuggestions = Object.keys(suggestionsData).filter(name =>
                      name.toLowerCase().includes(query)
                  );

                  // Display suggestions
                  if (filteredSuggestions.length > 0) {
                      suggestionsBox.classList.remove("hidden");
                      filteredSuggestions.forEach(name => {
                          const suggestionItem = document.createElement("div");
                          suggestionItem.textContent = name;
                          suggestionItem.classList.add("py-2", "px-4", "hover:bg-gray-100", "cursor-pointer");

                          // Add click event to redirect to the corresponding page
                          suggestionItem.onclick = () => {
                              window.location.href = suggestionsData[name]; // Navigate to the page
                          };

                          suggestionsBox.appendChild(suggestionItem);
                      });
                  }
              }


</script>

            <!-- User and Notifications Section -->
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <div class="relative">
                    <button class="flex items-center" onclick="toggleDropdown('notificationDropdown')">
                        <i class="fas fa-bell text-2xl text-gray-700"></i>
                    </button>
                    
                    <!-- Notifications Dropdown -->
                    <div id="notificationDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg py-2 hidden">
                        <p class="block px-4 py-2 text-gray-700 text-sm">No notifications</p>
                    </div>
                </div>

                <!-- User Avatar -->
                <div class="relative">
                    <button class="flex items-center" onclick="toggleDropdown('userDropdown')">
                        <img alt="User avatar" class="rounded-full" height="40" src="user.jpg" width="40"/>
                    </button>
                    <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 hidden">
                        <a class="block px-4 py-2 text-gray-700 font-bold hover:bg-blue-800 hover:text-white" href="#">Profile</a>
                        <a class="block px-4 py-2 text-gray-700 font-bold hover:bg-blue-800 hover:text-white" href="#">Settings</a>
                        <a class="block px-4 py-2 text-gray-700 font-bold hover:bg-blue-800 hover:text-white" href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </header>