<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "u687661100_admin";
$password = "Udhodbms01";
$dbname = "u687661100_udho_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_document'])) {
    // Get form data
    $direction = $_POST['direction'];
    $control_no = "UDHO-" . date("Y") . "-" . $_POST['control_number_suffix'];
    
    // Check for duplicate control number in the same direction only
    $check_sql = "SELECT id FROM routing_slips WHERE control_no = ? AND direction = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $control_no, $direction);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        // Duplicate found - store direction for modal display
         $_SESSION['duplicate_direction'] = $direction;
        $duplicate_error = true;
    } else {
        // No duplicate - proceed with saving
        // Process document types (checkboxes)
        $doc_types = isset($_POST['doc_type']) ? $_POST['doc_type'] : [];
        $document_type = implode(", ", $doc_types);
        
        // Handle "Others" document type
        if (in_array("Others", $doc_types) && !empty($_POST['other_doc_type'])) {
            $document_type = str_replace("Others", $_POST['other_doc_type'], $document_type);
        }
        
        $copy_type = $_POST['copy_type'];
        $status = $_POST['status'];
        
        // Process priorities (checkboxes)
        $priorities = isset($_POST['priority']) ? $_POST['priority'] : [];
        $priority = implode(", ", $priorities);
        
        $sender = $_POST['sender'];
        $date_time = $_POST['date_time'];
        $contact_no = $_POST['contact_no'];
        $subject = $_POST['subject'];
        
        // Process routing table data
        $routing_data = [];
        for ($i = 0; $i < 5; $i++) {
            if (!empty($_POST["routing_date_$i"])) {
                $routing_data[] = [
                    'date' => $_POST["routing_date_$i"],
                    'from' => $_POST["routing_from_$i"],
                    'to' => $_POST["routing_to_$i"],
                    'actions' => $_POST["routing_actions_$i"],
                    'due_date' => $_POST["routing_due_date_$i"],
                    'action_taken' => $_POST["routing_action_taken_$i"]
                ];
            }
        }
        $routing_json = json_encode($routing_data);
        
        // Insert into database
        $sql = "INSERT INTO routing_slips (
            control_no, direction, document_type, copy_type, status, priority, 
            sender, date_time, contact_no, subject, routing_data, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssssssss", 
            $control_no, $direction, $document_type, $copy_type, $status, $priority,
            $sender, $date_time, $contact_no, $subject, $routing_json
        );
        
        if ($stmt->execute()) {
            $success_message = "Routing Slip saved successfully.";
        } else {
            $error_message = "Error saving Routing Slip: " . $conn->error;
        }
        
        $stmt->close();
    }
    
    $check_stmt->close();
}

// Fetch existing records
$records = [];
$sql = "SELECT * FROM routing_slips ORDER BY created_at DESC LIMIT 5";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | Document Tracking System</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    /* All your existing CSS styles here */
    .table-container::-webkit-scrollbar {
      height: 8px;
      width: 8px;
    }
    .table-container::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }
    .table-container::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 4px;
    }
    .table-container::-webkit-scrollbar-thumb:hover {
      background: #555;
    }
    .document-section {
      margin-bottom: 2rem;
      border: 1px solid #e2e8f0;
      border-radius: 0.5rem;
      overflow: hidden;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .document-header {
      background-color: #4c51bf;
      color: white;
      padding: 0.75rem 1rem;
      font-weight: bold;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .document-count {
      background-color: rgba(255, 255, 255, 0.2);
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.875rem;
    }
    .document-table {
      width: 100%;
      border-collapse: collapse;
    }
    .document-table th {
      background-color: #edf2f7;
      padding: 0.75rem;
      text-align: left;
      position: sticky;
      top: 0;
    }
    .document-table td {
      padding: 0.75rem;
      border-top: 1px solid #e2e8f0;
    }
    .document-table tr:hover {
      background-color: #f8fafc;
    }
    .priority-badge {
      display: inline-block;
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    .priority-high {
      background-color: #fee2e2;
      color: #b91c1c;
    }
    .priority-medium {
      background-color: #fef3c7;
      color: #92400e;
    }
    .priority-low {
      background-color: #d1fae5;
      color: #065f46;
    }
    .document-details-panel {
      display: none;
      position: fixed;
      top: 0;
      right: 0;
      width: 40%;
      height: 100%;
      background: white;
      box-shadow: -2px 0 10px rgba(0,0,0,0.1);
      z-index: 1000;
      overflow-y: auto;
      padding: 20px;
    }
    .backdrop {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 999;
    }
    .edit-input {
      width: 100%;
      padding: 0.375rem 0.75rem;
      border: 1px solid #d1d5db;
      border-radius: 0.375rem;
      background-color: #f9fafb;
    }
    .edit-select {
      width: 100%;
      padding: 0.375rem 0.75rem;
      border: 1px solid #d1d5db;
      border-radius: 0.375rem;
      background-color: #f9fafb;
    }
    .document-form {
      background-color: white;
      padding: 20px;
      border-radius: 0.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .header-section {
      text-align: center;
      margin-bottom: 1rem;
    }
    .header-section h1 {
      font-size: 1.25rem;
      font-weight: bold;
    }
    .header-section h2 {
      font-size: 1rem;
      font-weight: bold;
    }
    .header-section p {
      font-size: 0.875rem;
    }
    .form-grid {
      display: grid;
      grid-template-columns: 120px 1fr;
      gap: 0.75rem;
      align-items: center;
      margin-bottom: 0.75rem;
    }
    .form-grid-2 {
      grid-column: span 2;
    }
    .form-grid-4 {
      grid-column: span 4;
    }
    .action-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 1rem;
    }
    .action-table th, .action-table td {
      border: 1px solid #e2e8f0;
      padding: 0.5rem;
      text-align: center;
    }
    .action-table th {
      background-color: #edf2f7;
    }
    .reminder-section {
      font-size: 0.75rem;
      margin-top: 1rem;
      padding-top: 0.5rem;
      border-top: 1px solid #e2e8f0;
    }
    .control-number-container {
      display: flex;
      align-items: center;
    }
    .control-number-prefix {
      margin-right: 0.5rem;
      font-weight: 500;
    }
    .copy-type-options {
      display: flex;
      gap: 1rem;
      align-items: center;
    }
    .copy-type-option {
      display: flex;
      align-items: center;
    }
    .copy-type-container {
      display: grid;
      grid-template-columns: 120px 1fr;
      gap: 0.5rem;
      align-items: center;
      margin-bottom: 0.5rem;
    }
    .copy-type-label {
      font-weight: bold;
    }
    .copy-type-inputs {
      display: flex;
      gap: 1rem;
      align-items: center;
    }
    .nav-buttons {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    .nav-button {
      padding: 0.75rem 1.5rem;
      border-radius: 0.375rem;
      font-weight: 600;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .nav-button.active {
      background-color: #4c51bf;
      color: white;
    }
    .nav-button.inactive {
      background-color: #edf2f7;
      color: #4a5568;
    }
    .nav-button.inactive:hover {
      background-color: #e2e8f0;
    }
    .checkbox-group {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
    }
    .checkbox-option {
      display: flex;
      align-items: center;
    }
    .form-checkbox {
      width: 1rem;
      height: 1rem;
      border: 1px solid #d1d5db;
      border-radius: 0.25rem;
      appearance: none;
      -webkit-appearance: none;
      cursor: pointer;
      margin-right: 0.5rem;
    }
    .form-checkbox:checked {
      background-color: #4c51bf;
      background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: center;
    }
    .form-radio {
      width: 1rem;
      height: 1rem;
      border: 1px solid #d1d5db;
      border-radius: 9999px;
      appearance: none;
      -webkit-appearance: none;
      cursor: pointer;
      margin-right: 0.5rem;
    }
    .form-radio:checked {
      background-color: #4c51bf;
      background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='3'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: center;
    }
    .section-title {
      font-weight: bold;
      margin-bottom: 0.5rem;
      color: #2d3748;
    }
    .other-input {
      margin-left: 0.5rem;
      padding: 0.25rem 0.5rem;
      border: 1px solid #d1d5db;
      border-radius: 0.25rem;
      width: 150px;
    }
    .other-input:focus {
      outline: none;
      border-color: #4c51bf;
    }
    .icon-style {
      font-size: 2.2rem;
      color: #111827;
    }
    .status-select {
      padding: 0.375rem 0.75rem;
      border: 1px solid #d1d5db;
      border-radius: 0.375rem;
      background-color: #f9fafb;
      width: 100%;
    }

    .table-container::-webkit-scrollbar {
      height: 8px;
      width: 8px;
    }
    
    /* Modal styles */
    .modal {
      transition: opacity 0.3s ease;
    }
    
    @media (max-width: 640px) {
      .form-grid {
        grid-template-columns: 1fr !important;
      }
      .form-grid-2, .form-grid-4 {
        grid-column: span 1 !important;
      }
      .checkbox-group {
        flex-direction: column;
        gap: 0.5rem;
      }
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex">
  <!-- Sidebar -->
  <div class="w-64 bg-gray-800 text-white flex flex-col">
    <div class="flex items-center justify-center h-24">
      <div class="rounded-full bg-gray-200 w-20 h-20 flex items-center justify-center overflow-hidden border-2 border-white shadow-md">
        <img src="/assets/PROFILE_SAMPLE.jpg" alt="Profile Picture" class="w-full h-full object-cover">
      </div>
    </div>
    <nav class="mt-6">
      <ul>
        <li>
          <a href="admin_dashboard.php" class="sidebar-link flex items-center py-3 px-4">
            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
          </a>
        </li>
        <li>
          <a href="admin_records.php" class="sidebar-link flex items-center py-3 px-4 active-link bg-gray-700">
            <i class="fas fa-file-alt mr-3"></i> Routing Slip
          </a>
        </li>
        <li>
          <a href="Settings/setting_admin.php" class="sidebar-link flex items-center py-3 px-4">
            <i class="fas fa-cog mr-3"></i> Settings
          </a>
        </li>
        <li>
          <a href="#" class="sidebar-link flex items-center py-3 px-4 mt-10">
            <i class="fas fa-sign-out-alt mr-3"></i> Logout
          </a>
        </li>
      </ul>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="flex-1 p-4 md:p-10">
    <header class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
      <h1 class="text-2xl font-bold text-gray-800">Document Tracking System</h1>
      <div class="flex items-center gap-2">
        <img src="/assets/UDHOLOGO.png" alt="Logo" class="h-8">
        <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
      </div>
    </header>
    
 <!-- Display success/error messages -->
     <?php if (isset($success_message)): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline"><?php echo $success_message; ?></span>
      </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline"><?php echo $error_message; ?></span>
      </div>
    <?php endif; ?>
    
    <!-- Navigation Buttons -->
    <div class="nav-buttons">
      <button id="routingSlipBtn" class="nav-button active">
        <i class="fas fa-file-alt mr-2"></i> Routing Slip
      </button>
    </div>
    
    <!-- Routing Slip Section (Visible by default) -->
    <div id="routingSlipSection">
      <!-- Document Tracking Form -->
      <form method="POST" action="" class="document-form mb-6" id="routingForm">
        <div class="header-section">
          <h1>ROUTING SLIP</h1>
        </div>

        <div class="form-grid">
          <!-- Control Number -->
          <div class="flex items-center font-medium">Control No:</div>
          <div class="flex items-center">
            <div class="control-number-container">
              <span class="control-number-prefix">UDHO-<?php echo date("Y"); ?>-</span>
              <input 
                type="text" 
                name="control_number_suffix" 
                id="controlNumberSuffix" 
                class="w-24 px-2 py-1 border border-gray-300 rounded-md"
                placeholder="0001"
                maxlength="4"
                pattern="[0-9]{4}"
                title="Please enter 4 digits"
                oninput="validateControlNumber()"
                required
              >
            </div>
          </div>
        </div>

        <!-- Direction Section -->
        <div class="mb-4">
          <div class="section-title">Direction</div>
          <div class="flex space-x-4">
            <label class="flex items-center">
              <input type="radio" name="direction" value="Incoming" class="form-radio" checked>
              <span class="ml-2">Incoming</span>
            </label>
            <label class="flex items-center">
              <input type="radio" name="direction" value="Outgoing" class="form-radio">
              <span class="ml-2">Outgoing</span>
            </label>
          </div>
        </div>

        <!-- Document Type Section -->
        <div class="mb-4">
          <div class="section-title">Document Type (Check all that apply)</div>
          <div class="checkbox-group">
            <label class="checkbox-option">
              <input type="checkbox" name="doc_type[]" value="Memo Letter" class="form-checkbox" checked>
              <span class="ml-2">Memo Letter</span>
            </label>
            <label class="checkbox-option">
              <input type="checkbox" name="doc_type[]" value="Referral Request" class="form-checkbox">
              <span class="ml-2">Referral Request</span>
            </label>
            <label class="checkbox-option">
              <input type="checkbox" name="doc_type[]" value="Report Proposal" class="form-checkbox">
              <span class="ml-2">Report Proposal</span>
            </label>
            <label class="checkbox-option">
              <input type="checkbox" name="doc_type[]" value="Invitation" class="form-checkbox">
              <span class="ml-2">Invitation</span>
            </label>
            <label class="checkbox-option">
              <input type="checkbox" name="doc_type[]" value="Others" class="form-checkbox" id="othersCheckbox">
              <span class="ml-2">Others:</span>
              <input type="text" name="other_doc_type" class="other-input" placeholder="Specify" id="otherDocType" disabled>
            </label>
          </div>
        </div>

        <!-- Copy Type Section -->
        <div class="mb-4">
          <div class="section-title">Type of Copy Sent</div>
          <div class="flex space-x-4">
            <label class="flex items-center">
              <input type="radio" name="copy_type" value="Original" class="form-radio" checked>
              <span class="ml-2">Original</span>
            </label>
            <label class="flex items-center">
              <input type="radio" name="copy_type" value="Photocopy" class="form-radio">
              <span class="ml-2">Photocopy</span>
            </label>
            <label class="flex items-center">
              <input type="radio" name="copy_type" value="Scanned" class="form-radio">
              <span class="ml-2">Scanned</span>
            </label>
          </div>
        </div>

        <!-- Status Section -->
        <div class="mb-4">
          <div class="section-title">Status</div>
          <select name="status" class="status-select" required>
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="For Review">For Review</option>
            <option value="Completed">Completed</option>
            <option value="On Hold">On Hold</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>

        <!-- Priority Section -->
        <div class="mb-4">
          <div class="section-title">Priority (Check all that apply)</div>
          <div class="checkbox-group">
            <label class="checkbox-option">
              <input type="checkbox" name="priority[]" value="3 days" class="form-checkbox">
              <span class="ml-2">3 days</span>
            </label>
            <label class="checkbox-option">
              <input type="checkbox" name="priority[]" value="7 days" class="form-checkbox">
              <span class="ml-2">7 days</span>
            </label>
            <label class="checkbox-option">
              <input type="checkbox" name="priority[]" value="15 days" class="form-checkbox">
              <span class="ml-2">15 days</span>
            </label>
            <label class="checkbox-option">
              <input type="checkbox" name="priority[]" value="20 days" class="form-checkbox">
              <span class="ml-2">20 days</span>
            </label>
          </div>
        </div>

        <!-- Sender Information -->
        <div class="form-grid">
          <div class="flex items-center font-medium">Sender</div>
          <div>
            <input type="text" name="sender" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Sender Name" required>
          </div>

          <div class="flex items-center font-medium">Date/Time</div>
          <div>
            <input type="datetime-local" name="date_time" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
          </div>

          <div class="flex items-center font-medium">Contact No</div>
          <div>
            <input type="text" name="contact_no" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Contact number" required>
          </div>
        </div>

        <!-- Subject Section -->
        <div class="mt-4 mb-4">
          <div class="section-title">Subject</div>
          <textarea name="subject" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="4" placeholder="Enter subject details" required></textarea>
        </div>

        <!-- Action Table -->
        <div class="mb-4">
          <div class="section-title">Document Routing</div>
          <table class="action-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>From</th>
                <th>To</th>
                <th>Required Actions/ Instructions</th>
                <th>Due Date</th>
                <th>Action Taken</th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i = 0; $i < 5; $i++): ?>
              <tr>
                <td><input type="date" name="routing_date_<?php echo $i; ?>" class="w-full border-none"></td>
                <td><input type="text" name="routing_from_<?php echo $i; ?>" class="w-full border-none"></td>
                <td><input type="text" name="routing_to_<?php echo $i; ?>" class="w-full border-none"></td>
                <td><input type="text" name="routing_actions_<?php echo $i; ?>" class="w-full border-none"></td>
                <td><input type="date" name="routing_due_date_<?php echo $i; ?>" class="w-full border-none"></td>
                <td><input type="text" name="routing_action_taken_<?php echo $i; ?>" class="w-full border-none"></td>
              </tr>
              <?php endfor; ?>
            </tbody>
          </table>
        </div>

        <!-- Reminder Section -->
        <div class="reminder-section">
          <p><strong>Reminders:</strong> Under Sec. 5 of RA 6713, otherwise known as the <em>Code of Conduct and Ethical Standards for Public Officials and Employees</em>, enjoins all public servants to respond to letters, telegrams, and other means of communication sent by the public within fifteen (15) working days from the receipt thereof. The reply must contain the action taken on the request. Likewise, all official papers and documents must be processed and completed within a reasonable time.</p>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end mt-4">
          <button type="submit" name="save_document" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition">
            <i class="fas fa-save mr-2"></i> Save Document
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Duplicate Control Number Modal -->
  <div id="duplicateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-red-600">Duplicate Control Number</h3>
        <button id="closeDuplicateModal" class="text-gray-500 hover:text-gray-700">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <p class="mb-4" id="duplicateMessage">This control number already exists for <?php echo isset($_SESSION['duplicate_direction']) ? $_SESSION['duplicate_direction'] : ''; ?> documents. Please use a different control number suffix.</p>
      <div class="flex justify-end">
        <button id="confirmDuplicateModal" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
          OK
        </button>
      </div>
    </div>
  </div>

  <script>
 function validateControlNumber() {
      const input = document.getElementById('controlNumberSuffix');
      input.value = input.value.replace(/[^0-9]/g, '').slice(0, 4);
    }

    document.getElementById('othersCheckbox').addEventListener('change', function() {
      document.getElementById('otherDocType').disabled = !this.checked;
      if (!this.checked) {
        document.getElementById('otherDocType').value = '';
      }
    });

    document.addEventListener('DOMContentLoaded', () => {
      const now = new Date();
      const dateTimeInput = document.querySelector('input[type="datetime-local"]');
      dateTimeInput.value = now.toISOString().slice(0, 16);
    });

    function showDuplicateModal() {
      document.getElementById('duplicateModal').classList.remove('hidden');
    }

    function hideDuplicateModal() {
      document.getElementById('duplicateModal').classList.add('hidden');
    }

    document.getElementById('closeDuplicateModal').addEventListener('click', hideDuplicateModal);
    document.getElementById('confirmDuplicateModal').addEventListener('click', hideDuplicateModal);

    <?php if (isset($duplicate_error) && $duplicate_error): ?>
      document.addEventListener('DOMContentLoaded', function() {
        showDuplicateModal();
      });
    <?php endif; ?>

    // Updated real-time duplicate checking with direction
    // Updated real-time duplicate checking with direction
document.getElementById('controlNumberSuffix').addEventListener('change', function() {
    const direction = document.querySelector('input[name="direction"]:checked').value;
    const controlNumber = 'UDHO-' + new Date().getFullYear() + '-' + this.value;
    
    if (this.value.length === 4) {
        fetch('check_control_number.php?control_no=' + encodeURIComponent(controlNumber) + '&direction=' + direction)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    document.getElementById('duplicateMessage').textContent = 
                        'This control number already exists for ' + direction + ' documents. Please use a different control number suffix.';
                    showDuplicateModal();
                }
            });
    }
});
  </script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>