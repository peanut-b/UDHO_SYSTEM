<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Tracking System</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
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
      grid-template-columns: 120px 1fr 80px 1fr;
      gap: 0.5rem;
      align-items: center;
      margin-bottom: 0.5rem;
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
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex">
  <!-- Sidebar -->
  <div class="w-64 bg-gray-800 text-white flex flex-col">
    <div class="flex items-center justify-center h-24">
      <div class="rounded-full bg-gray-200 w-20 h-20 flex items-center justify-center overflow-hidden border-2 border-white shadow-md">
        <img src="/UDHO%20SYSTEM/assets/PROFILE_SAMPLE.jpg" alt="Profile Picture" class="w-full h-full object-cover">
      </div>
    </div>
    <nav class="mt-6">
      <ul>
        <li>
          <a href="/UDHO%20SYSTEM/Operation/admin_panel.php" class="sidebar-link flex items-center py-3 px-4">
            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
          </a>
        </li>
        <li>
          <a href="/UDHO%20SYSTEM/Admin/admin_records.php" class="sidebar-link flex items-center py-3 px-4 active-link bg-gray-700">
            <i class="fas fa-file-alt mr-3"></i> Records
          </a>
        </li>
        <li>
          <a href="/UDHO%20SYSTEM/Settings/setting.php" class="sidebar-link flex items-center py-3 px-4">
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
        <img src="\UDHO%20SYSTEM\assets\UDHOLOGO.png" alt="Logo" class="h-8">
        <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
      </div>
    </header>

    <!-- Navigation Buttons -->
    <div class="nav-buttons">
      <button id="routingSlipBtn" class="nav-button active">
        <i class="fas fa-file-alt"></i> Routing Slip
      </button>
      <button id="recordsBtn" class="nav-button inactive">
        <i class="fas fa-archive"></i> Records
      </button>
    </div>

    <!-- Routing Slip Section (Visible by default) -->
    <div id="routingSlipSection">
      <!-- Document Tracking Form -->
      <div class="document-form mb-6">
        <div class="header-section">
          <h1>ROUTING SLIP</h1>
        </div>

        <div class="form-grid">
          <!-- Control Number Row -->
          <div class="flex items-center">Control No:</div>
          <div class="flex items-center">
            <div class="control-number-container">
              <span class="control-number-prefix">UDHO-2025-</span>
              <input 
                type="text" 
                id="controlNumberSuffix" 
                class="w-24 px-2 py-1 border border-gray-300 rounded-md"
                placeholder="0001"
                maxlength="4"
                pattern="[0-9]{4}"
                title="Please enter 4 digits"
                oninput="validateControlNumber()"
              >
            </div>
          </div>
          
          <!-- Direction Radio Buttons -->
          <div class="flex items-center">
            <input type="radio" id="incoming" name="direction" value="Incoming" checked>
            <label for="incoming" class="ml-1">Incoming</label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="outgoing" name="direction" value="Outgoing">
            <label for="outgoing" class="ml-1">Outgoing</label>
          </div>

          <!-- Document Type Row -->
          <div class="flex items-center">Document Type</div>
          <div class="flex items-center">
            <input type="radio" id="memoLetter" name="docType" value="Memo Letter" checked>
            <label for="memoLetter" class="ml-1">Memo Letter</label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="referralRequest" name="docType" value="Referral Request">
            <label for="referralRequest" class="ml-1">Referral Request</label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="reportProposal" name="docType" value="Report Proposal">
            <label for="reportProposal" class="ml-1">Report Proposal</label>
          </div>

          <!-- Document Type Continued -->
          <div class="flex items-center"></div>
          <div class="flex items-center">
            <input type="radio" id="invitation" name="docType" value="Invitation">
            <label for="invitation" class="ml-1">Invitation</label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="others" name="docType" value="Others">
            <label for="others" class="ml-1">Others:</label>
            <input type="text" class="ml-1 border-b w-20">
          </div>
        </div>

        <!-- Improved Copy Type Section -->
        <div class="copy-type-container">
          <div class="copy-type-label">Type of Copy Sent:</div>
          <div class="copy-type-inputs">
            <label class="copy-type-option">
              <input type="radio" name="copyType" value="Original" class="form-radio" checked>
              <span class="ml-2">Original</span>
            </label>
            <label class="copy-type-option">
              <input type="radio" name="copyType" value="Photocopy" class="form-radio">
              <span class="ml-2">Photocopy</span>
            </label>
            <label class="copy-type-option">
              <input type="radio" name="copyType" value="Scanned" class="form-radio">
              <span class="ml-2">Scanned</span>
            </label>
          </div>
        </div>

        <div class="form-grid">
          <!-- Priority Row -->
          <div class="flex items-center">Priority</div>
          <div class="flex items-center">
            <input type="radio" id="priority3" name="priority" value="3 days">
            <label for="priority3" class="ml-1">3 days</label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="priority7" name="priority" value="7 days" checked>
            <label for="priority7" class="ml-1">7 days</label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="priority15" name="priority" value="15 days">
            <label for="priority15" class="ml-1">15 days</label>
          </div>

          <!-- Priority Continued -->
          <div class="flex items-center"></div>
          <div class="flex items-center">
            <input type="radio" id="priority20" name="priority" value="20 days">
            <label for="priority20" class="ml-1">20 days</label>
          </div>
          
          <!-- Sender Information -->
          <div class="flex items-center font-bold">Sender</div>
          <div class="form-grid-2">
            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md">
          </div>
          <div class="flex items-center font-bold">Date/Time</div>
          <div class="flex items-center">
            <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md">
          </div>

          <!-- Contact Number -->
          <div class="flex items-center font-bold">Contact No</div>
          <div class="form-grid-4">
            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md">
          </div>
        </div>

        <!-- Subject Section -->
        <div class="border-t border-b py-2 my-4">
          <h3 class="text-lg font-bold text-center">Subject</h3>
        </div>

        <!-- Action Table -->
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
            <tr>
              <td><input type="date" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
              <td><input type="date" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
            </tr>
            <tr>
              <td><input type="date" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
              <td><input type="date" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
            </tr>
            <tr>
              <td><input type="date" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
              <td><input type="date" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
            </tr>
            <tr>
              <td><input type="date" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
              <td><input type="date" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
            </tr>
            <tr>
              <td><input type="date" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
              <td><input type="date" class="w-full border-none"></td>
              <td><input type="text" class="w-full border-none"></td>
            </tr>
          </tbody>
        </table>

        <!-- Reminder Section -->
        <div class="reminder-section">
          <p><strong>Reminders:</strong> Under Sec. 5 of RA 6713, otherwise known as the <em>Code of Conduct and Ethical Standards for Public Officials and Employees</em>, enjoins all public servants to respond to letters, telegrams, and other means of communication sent by the public within fifteen (15) working days from the receipt thereof. The reply must contain the action taken on the request. Likewise, all official papers and documents must be processed and completed within a reasonable time.</p>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end mt-4">
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition">
            <i class="fas fa-save mr-2"></i> Save Document
          </button>
        </div>
      </div>
    </div>

    <!-- Records Section (Hidden by default) -->
    <div id="recordsSection" class="hidden">
      <div class="document-section">
        <div class="document-header">
          <div>
            <i class="fas fa-archive mr-2"></i> Document Records
            <span class="document-count">5 records</span>
          </div>
          <div class="flex items-center space-x-2">
            <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm">
              <i class="fas fa-filter mr-1"></i> Filter
            </button>
            <button class="bg-purple-600 text-white px-3 py-1 rounded text-sm">
              <i class="fas fa-print mr-1"></i> Print
            </button>
          </div>
        </div>
        <div class="table-container overflow-x-auto">
          <table class="document-table">
            <thead>
              <tr>
                <th>Control No.</th>
                <th>Date/Time</th>
                <th>Document Type</th>
                <th>Direction</th>
                <th>Priority</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>UDHO2025-1042</td>
                <td>2025-05-07 19:28</td>
                <td>Memo Letter</td>
                <td>Incoming</td>
                <td><span class="priority-badge priority-medium">7 days</span></td>
                <td>Request for housing assistance</td>
                <td>Pending</td>
                <td>
                  <button onclick="showDocumentDetails('UDHO2025-1042')" class="text-blue-600 hover:text-blue-800 mr-2">
                    <i class="fas fa-eye"></i> View
                  </button>
                  <button class="text-green-600 hover:text-green-800">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                </td>
              </tr>
              <tr>
                <td>UDHO2025-1041</td>
                <td>2025-05-06 14:15</td>
                <td>Report Proposal</td>
                <td>Outgoing</td>
                <td><span class="priority-badge priority-low">15 days</span></td>
                <td>Quarterly housing report</td>
                <td>Completed</td>
                <td>
                  <button onclick="showDocumentDetails('UDHO2025-1041')" class="text-blue-600 hover:text-blue-800 mr-2">
                    <i class="fas fa-eye"></i> View
                  </button>
                  <button class="text-green-600 hover:text-green-800">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                </td>
              </tr>
              <tr>
                <td>UDHO2025-1040</td>
                <td>2025-05-05 10:30</td>
                <td>Referral Request</td>
                <td>Incoming</td>
                <td><span class="priority-badge priority-high">3 days</span></td>
                <td>Urgent housing relocation</td>
                <td>In Progress</td>
                <td>
                  <button onclick="showDocumentDetails('UDHO2025-1040')" class="text-blue-600 hover:text-blue-800 mr-2">
                    <i class="fas fa-eye"></i> View
                  </button>
                  <button class="text-green-600 hover:text-green-800">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                </td>
              </tr>
              <tr>
                <td>UDHO2025-1039</td>
                <td>2025-05-04 16:45</td>
                <td>Memo Letter</td>
                <td>Outgoing</td>
                <td><span class="priority-badge priority-medium">7 days</span></td>
                <td>Notice of assessment</td>
                <td>Completed</td>
                <td>
                  <button onclick="showDocumentDetails('UDHO2025-1039')" class="text-blue-600 hover:text-blue-800 mr-2">
                    <i class="fas fa-eye"></i> View
                  </button>
                  <button class="text-green-600 hover:text-green-800">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                </td>
              </tr>
              <tr>
                <td>UDHO2025-1038</td>
                <td>2025-05-03 09:20</td>
                <td>Others: Invitation</td>
                <td>Incoming</td>
                <td><span class="priority-badge priority-low">20 days</span></td>
                <td>Community meeting invitation</td>
                <td>Pending</td>
                <td>
                  <button onclick="showDocumentDetails('UDHO2025-1038')" class="text-blue-600 hover:text-blue-800 mr-2">
                    <i class="fas fa-eye"></i> View
                  </button>
                  <button class="text-green-600 hover:text-green-800">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
    function validateControlNumber() {
      const input = document.getElementById('controlNumberSuffix');
      // Only allow numbers and enforce 4-digit length
      input.value = input.value.replace(/[^0-9]/g, '').slice(0, 4);
    }

    // Show document details
    function showDocumentDetails(controlNo) {
      alert(`Showing details for document: ${controlNo}`);
      // In a real implementation, this would open a modal with document details
    }

    // Toggle between Routing Slip and Records sections
    document.addEventListener('DOMContentLoaded', () => {
      const routingSlipBtn = document.getElementById('routingSlipBtn');
      const recordsBtn = document.getElementById('recordsBtn');
      const routingSlipSection = document.getElementById('routingSlipSection');
      const recordsSection = document.getElementById('recordsSection');

      routingSlipBtn.addEventListener('click', () => {
        routingSlipBtn.classList.remove('inactive');
        routingSlipBtn.classList.add('active');
        recordsBtn.classList.remove('active');
        recordsBtn.classList.add('inactive');
        routingSlipSection.classList.remove('hidden');
        recordsSection.classList.add('hidden');
      });

      recordsBtn.addEventListener('click', () => {
        recordsBtn.classList.remove('inactive');
        recordsBtn.classList.add('active');
        routingSlipBtn.classList.remove('active');
        routingSlipBtn.classList.add('inactive');
        recordsSection.classList.remove('hidden');
        routingSlipSection.classList.add('hidden');
      });
    });
  </script>
</body>
</html>