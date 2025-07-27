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
    /* Improved alignment for copy type section */
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
          <a href="#" class="sidebar-link flex items-center py-3 px-4 active-link bg-gray-700">
            <i class="fas fa-file-alt mr-3"></i> Document Tracking
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
  

  <!-- Separate Document Tracking Log File (would normally be in a separate HTML file) -->
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
          <a href="#" class="sidebar-link flex items-center py-3 px-4 active-link bg-gray-700">
            <i class="fas fa-file-alt mr-3"></i> Document Tracking
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

  <div class="flex-1 p-4 md:p-10">
    <!-- Document Tracking Log -->
    <div class="document-section">
      <div class="document-header">
        <div>
          <i class="fas fa-file-alt mr-2"></i> Document Tracking Log
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

    // Initialize the page
    document.addEventListener('DOMContentLoaded', () => {
      // No need for auto-generation now
    });
  </script>
</body>
</html>