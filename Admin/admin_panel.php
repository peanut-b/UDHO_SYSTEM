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
          <a href="/UDHO%20SYSTEM/Operation/operation_dashboard.php" class="sidebar-link flex items-center py-3 px-4">
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
  <div class="flex-1 p-4 md:p-10">
    <header class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
      <h1 class="text-2xl font-bold text-gray-800">Document Tracking System</h1>
      <div class="flex items-center gap-2">
        <img src="\UDHO%20SYSTEM\assets\UDHOLOGO.png" alt="Logo" class="h-8">
        <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
      </div>
    </header>

    <div class="flex flex-wrap gap-2 mb-6">
      <button onclick="filterDocuments('all')" data-filter="all" class="filter-btn px-4 py-2 rounded-md bg-blue-500 text-white">
        All Documents
      </button>
      <button onclick="filterDocuments('incoming')" data-filter="incoming" class="filter-btn px-4 py-2 rounded-md bg-gray-200 text-gray-700">
        Incoming
      </button>
      <button onclick="filterDocuments('outgoing')" data-filter="outgoing" class="filter-btn px-4 py-2 rounded-md bg-gray-200 text-gray-700">
        Outgoing
      </button>
    </div>

    <!-- Document Tracking Form -->
    <div class="document-form mb-6">
      <h2 class="text-lg font-semibold mb-4">Document Information</h2>
      <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="col-span-2 text-center">
          <h3 class="font-bold">Republic of the Philippines</h3>
          <h4 class="font-medium">Urban Development and Housing Office</h4>
          <p class="text-sm">Pasay City, Metro Manila</p>
        </div>
        
        <div class="col-span-2 border-t border-b py-2">
          <div class="flex justify-between items-center">
            <span class="font-medium">Control No.: <span id="controlNumber">UDHO2025-1042</span></span>
            <button type="button" onclick="generateControlNumber()" class="text-blue-600 text-sm">
              <i class="fas fa-sync-alt mr-1"></i> Generate New
            </button>
          </div>
        </div>
        
        <div>
          <label class="block mb-1 font-medium">Document Type</label>
          <div class="flex flex-wrap gap-2">
            <label class="inline-flex items-center">
              <input type="radio" name="docType" value="Memo Letter" class="form-radio" checked> 
              <span class="ml-2">Memo Letter</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" name="docType" value="Referral Request" class="form-radio"> 
              <span class="ml-2">Referral Request</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" name="docType" value="Report Proposal" class="form-radio"> 
              <span class="ml-2">Report Proposal</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" name="docType" value="Others" class="form-radio"> 
              <span class="ml-2">Others: <input type="text" class="border-b ml-1 w-24"></span>
            </label>
          </div>
        </div>
        
        <div>
          <label class="block mb-1 font-medium">Direction</label>
          <div class="flex gap-4">
            <label class="inline-flex items-center">
              <input type="radio" name="direction" value="Incoming" class="form-radio" checked> 
              <span class="ml-2">Incoming</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" name="direction" value="Outgoing" class="form-radio"> 
              <span class="ml-2">Outgoing</span>
            </label>
          </div>
        </div>
        
        <div>
          <label class="block mb-1 font-medium">Priority</label>
          <div class="flex gap-2">
            <label class="inline-flex items-center">
              <input type="radio" name="priority" value="3 days" class="form-radio"> 
              <span class="ml-2">3 days</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" name="priority" value="7 days" class="form-radio" checked> 
              <span class="ml-2">7 days</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" name="priority" value="15 days" class="form-radio"> 
              <span class="ml-2">15 days</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" name="priority" value="20 days" class="form-radio"> 
              <span class="ml-2">20 days</span>
            </label>
          </div>
        </div>
        
        <div>
          <label class="block mb-1 font-medium">Type of Copy Sent</label>
          <div class="flex gap-2">
            <label class="inline-flex items-center">
              <input type="radio" name="copyType" value="Original" class="form-radio" checked> 
              <span class="ml-2">Original</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" name="copyType" value="Copy" class="form-radio"> 
              <span class="ml-2">Copy</span>
            </label>
          </div>
        </div>
        
        <div>
          <label class="block mb-1 font-medium">Contact No.</label>
          <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
        </div>
        
        <div>
          <label class="block mb-1 font-medium">Date/Time Received</label>
          <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
        </div>
        
        <div>
          <label class="block mb-1 font-medium">Date/Time Released</label>
          <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
        </div>
        
        <div class="col-span-2">
          <label class="block mb-1 font-medium">Subject/Description</label>
          <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"></textarea>
        </div>
        
        <div class="col-span-2">
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition flex items-center justify-center">
            <i class="fas fa-save mr-2"></i> Save Document
          </button>
        </div>
      </form>
    </div>

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

    <!-- Document Details Panel (hidden by default) -->
    <div class="backdrop" id="docBackdrop" onclick="closeDocumentDetails()"></div>
    <div class="document-details-panel" id="documentDetailsPanel">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Document Details</h2>
        <button onclick="closeDocumentDetails()" class="text-gray-500 hover:text-gray-700">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="mb-6">
        <div class="bg-gray-50 p-4 rounded">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-500">Control Number</p>
              <p class="font-medium" id="docControlNo">-</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Document Type</p>
              <p class="font-medium" id="docType">-</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Direction</p>
              <p class="font-medium" id="docDirection">-</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Priority</p>
              <p class="font-medium" id="docPriority">-</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Date/Time Received</p>
              <p class="font-medium" id="docReceived">-</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Date/Time Released</p>
              <p class="font-medium" id="docReleased">-</p>
            </div>
            <div class="col-span-2">
              <p class="text-sm text-gray-500">Subject</p>
              <p class="font-medium" id="docSubject">-</p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Action Log</h3>
        <div class="table-container overflow-x-auto">
          <table class="document-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>From</th>
                <th>To</th>
                <th>Required Actions</th>
                <th>Due Date</th>
                <th>Action Taken</th>
              </tr>
            </thead>
            <tbody id="actionLogTable">
              <!-- Will be populated by JavaScript -->
            </tbody>
          </table>
        </div>
      </div>
      
      <div>
        <h3 class="text-lg font-semibold mb-2">Add New Action</h3>
        <form id="addActionForm" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block mb-1 font-medium">Date</label>
              <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>
            <div>
              <label class="block mb-1 font-medium">From</label>
              <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>
            <div>
              <label class="block mb-1 font-medium">To</label>
              <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>
            <div>
              <label class="block mb-1 font-medium">Due Date</label>
              <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>
            <div class="col-span-2">
              <label class="block mb-1 font-medium">Required Actions/Instructions</label>
              <textarea rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md" required></textarea>
            </div>
            <div class="col-span-2">
              <label class="block mb-1 font-medium">Action Taken</label>
              <textarea rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md" required></textarea>
            </div>
          </div>
          <div class="flex justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition">
              Add Action
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Sample data for documents
    const documentData = {
      'UDHO2025-1042': {
        controlNo: 'UDHO2025-1042',
        docType: 'Memo Letter',
        direction: 'Incoming',
        priority: '7 days',
        received: '2025-05-07 19:28',
        released: '',
        subject: 'Request for housing assistance',
        status: 'Pending',
        actions: [
          {
            date: '2025-05-07',
            from: 'City Housing Office',
            to: 'UDHO Director',
            requiredAction: 'Review and approve housing assistance request',
            dueDate: '2025-05-14',
            actionTaken: 'Received and logged'
          }
        ]
      },
      'UDHO2025-1041': {
        controlNo: 'UDHO2025-1041',
        docType: 'Report Proposal',
        direction: 'Outgoing',
        priority: '15 days',
        received: '',
        released: '2025-05-06 14:15',
        subject: 'Quarterly housing report',
        status: 'Completed',
        actions: [
          {
            date: '2025-05-01',
            from: 'UDHO Director',
            to: 'Planning Department',
            requiredAction: 'Prepare quarterly housing report',
            dueDate: '2025-05-15',
            actionTaken: 'Report prepared and submitted'
          },
          {
            date: '2025-05-06',
            from: 'Planning Department',
            to: 'City Council',
            requiredAction: 'Submit final report',
            dueDate: '2025-05-06',
            actionTaken: 'Report delivered'
          }
        ]
      },
      'UDHO2025-1040': {
        controlNo: 'UDHO2025-1040',
        docType: 'Referral Request',
        direction: 'Incoming',
        priority: '3 days',
        received: '2025-05-05 10:30',
        released: '',
        subject: 'Urgent housing relocation',
        status: 'In Progress',
        actions: [
          {
            date: '2025-05-05',
            from: 'Barangay 12 Office',
            to: 'UDHO Relocation Team',
            requiredAction: 'Process urgent relocation request',
            dueDate: '2025-05-08',
            actionTaken: 'Request received and assigned'
          }
        ]
      }
    };

    function showDocumentDetails(controlNo) {
      const doc = documentData[controlNo] || {
        controlNo: controlNo,
        docType: 'Unknown',
        direction: 'Unknown',
        priority: 'Unknown',
        received: 'Unknown',
        released: 'Unknown',
        subject: 'Unknown',
        status: 'Unknown',
        actions: []
      };

      // Update basic info
      document.getElementById('docControlNo').textContent = doc.controlNo;
      document.getElementById('docType').textContent = doc.docType;
      document.getElementById('docDirection').textContent = doc.direction;
      document.getElementById('docPriority').textContent = doc.priority;
      document.getElementById('docReceived').textContent = doc.received || 'N/A';
      document.getElementById('docReleased').textContent = doc.released || 'N/A';
      document.getElementById('docSubject').textContent = doc.subject;

      // Update action log
      updateActionLogTable(doc.actions);

      // Show panel and backdrop
      document.getElementById('documentDetailsPanel').style.display = 'block';
      document.getElementById('docBackdrop').style.display = 'block';
    }

    function updateActionLogTable(actions) {
      const actionTable = document.getElementById('actionLogTable');
      actionTable.innerHTML = '';
      
      if (actions.length === 0) {
        actionTable.innerHTML = `
          <tr>
            <td colspan="6" class="text-center py-4 text-gray-500">
              No actions logged
            </td>
          </tr>
        `;
        return;
      }

      actions.forEach(action => {
        actionTable.innerHTML += `
          <tr>
            <td>${action.date}</td>
            <td>${action.from}</td>
            <td>${action.to}</td>
            <td>${action.requiredAction}</td>
            <td>${action.dueDate}</td>
            <td>${action.actionTaken}</td>
          </tr>
        `;
      });
    }

    function closeDocumentDetails() {
      document.getElementById('documentDetailsPanel').style.display = 'none';
      document.getElementById('docBackdrop').style.display = 'none';
    }

    function generateControlNumber() {
      const year = new Date().getFullYear();
      const randomNum = Math.floor(1000 + Math.random() * 9000);
      document.getElementById('controlNumber').textContent = `UDHO${year}-${randomNum}`;
    }

    function filterDocuments(filter) {
      const rows = document.querySelectorAll('.document-table tbody tr');
      const filterButtons = document.querySelectorAll('.filter-btn');
      
      // Update button styles
      filterButtons.forEach(btn => {
        if (btn.dataset.filter === filter) {
          btn.classList.remove('bg-gray-200', 'text-gray-700');
          btn.classList.add('bg-blue-500', 'text-white');
        } else {
          btn.classList.remove('bg-blue-500', 'text-white');
          btn.classList.add('bg-gray-200', 'text-gray-700');
        }
      });
      
      // Filter rows
      rows.forEach(row => {
        const direction = row.querySelector('td:nth-child(4)').textContent;
        if (filter === 'all' || direction.toLowerCase() === filter.toLowerCase()) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    // Close panel when clicking outside
    document.addEventListener('click', function(event) {
      const panel = document.getElementById('documentDetailsPanel');
      const backdrop = document.getElementById('docBackdrop');
      if (event.target === backdrop) {
        panel.style.display = 'none';
        backdrop.style.display = 'none';
      }
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      generateControlNumber();
    });
  </script>
</body>
</html>