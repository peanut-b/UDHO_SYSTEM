<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Database Management</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    /* Custom scrollbar for tables */
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
    
    /* Database table styling */
    .database-section {
      margin-bottom: 2rem;
      border: 1px solid #e2e8f0;
      border-radius: 0.5rem;
      overflow: hidden;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .database-header {
      background-color: #4c51bf;
      color: white;
      padding: 0.75rem 1rem;
      font-weight: bold;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .database-count {
      background-color: rgba(255, 255, 255, 0.2);
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.875rem;
    }
    .database-table {
      width: 100%;
      border-collapse: collapse;
    }
    .database-table th {
      background-color: #edf2f7;
      padding: 0.75rem;
      text-align: left;
      position: sticky;
      top: 0;
    }
    .database-table td {
      padding: 0.75rem;
      border-top: 1px solid #e2e8f0;
    }
    .database-table tr:hover {
      background-color: #f8fafc;
    }
    
    /* Action buttons */
    .action-buttons {
      display: flex;
      gap: 0.5rem;
      margin-bottom: 2rem;
    }
    .btn-backup {
      background-color: #10b981;
    }
    .btn-backup:hover {
      background-color: #059669;
    }
    .btn-delete {
      background-color: #ef4444;
    }
    .btn-delete:hover {
      background-color: #dc2626;
    }
    .btn-show-all {
      background-color: #3b82f6;
    }
    .btn-show-all:hover {
      background-color: #2563eb;
    }
    
    
    
    /* Status badges */
    .status-badge {
      display: inline-block;
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    .status-active {
      background-color: #d1fae5;
      color: #065f46;
    }
    .status-pending {
      background-color: #fef3c7;
      color: #92400e;
    }
    .status-completed {
      background-color: #dbeafe;
      color: #1e40af;
    }
    .status-inactive {
      background-color: #f3f4f6;
      color: #6b7280;
    }
    .status-high {
      background-color: #fee2e2;
      color: #b91c1c;
    }
    .status-medium {
      background-color: #fef3c7;
      color: #92400e;
    }
    .status-low {
      background-color: #d1fae5;
      color: #065f46;
    }
    .custom-card {
      transition: all 0.3s ease;
    }
    .custom-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    .sidebar-link {
      transition: all 0.2s ease;
    }
    .sidebar-link:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }
    .active-link {
      background-color: rgba(255, 255, 255, 0.2);
    }
    .time-period-btn.active {
      background-color: #3b82f6;
      color: white;
    }
    
    /* Hidden rows */
    .hidden-row {
      display: none;
    }
  </style>
</head>

<body class="bg-gray-100 min-h-screen flex">
  <!-- Sidebar -->
  <div class="w-64 bg-gray-800 text-white flex flex-col">
    <div class="flex items-center justify-center h-24">
          <!-- Profile Picture Container -->
          <div class="rounded-full bg-gray-200 w-20 h-20 flex items-center justify-center overflow-hidden border2 border-white shadow-md">
              <?php
              // Assuming you have a user profile picture path stored in a session or variable
              $profilePicture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default_profile.jpg';
              ?>
              <img src="/assets/profile_pictures/<?php echo htmlspecialchars($profilePicture); ?>" 
                  alt="Profile Picture" 
                  class="w-full h-full object-cover"
                  onerror="this.src='/assets/PROFILE_SAMPLE.jpg'">
          </div>
  </div>
    <nav class="mt-6">
      <ul>
        <li>
          <a href="adminexecutive_dashboard.php" class="sidebar-link flex items-center py-3 px-4 active-link">
            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
          </a>
        </li>
        <li>
          <a href="backup.php" class="sidebar-link flex items-center py-3 px-4">
            <i class="fas fa-database mr-3"></i> Backup Data
          </a>
        </li>
        <li>
          <a href="employee.php" class="sidebar-link flex items-center py-3 px-4">
            <i class="fas fa-users mr-3"></i> Employees
          </a>
        </li>
        <li>
          <a href="Settings/setting_executive.php" class="sidebar-link flex items-center py-3 px-4">
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
      <h1 class="text-2xl font-bold text-gray-800">Database Management</h1>
      <div class="flex items-center gap-2">
        <img src="/assets/UDHOLOGO.png" alt="Logo" class="h-8">
        <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
      </div>
    </header>

    <!-- Action Buttons -->
    <div class="action-buttons">
      <button onclick="showConfirmation('backup')" class="btn-backup text-white px-4 py-2 rounded-md transition flex items-center">
        <i class="fas fa-database mr-2"></i> Backup All Data
      </button>
      <button onclick="showConfirmation('delete')" class="btn-delete text-white px-4 py-2 rounded-md transition flex items-center">
        <i class="fas fa-trash-alt mr-2"></i> Delete All Data
      </button>
    </div>

    <!-- Database Tables -->
    <div class="database-section" id="meralco-db">
      <div class="database-header">
        <div>
          <i class="fas fa-bolt mr-2"></i> Meralco Database
          <span class="database-count">8 records</span>
        </div>
        <button class="text-white hover:text-gray-200">
          <i class="fas fa-ellipsis-v"></i>
        </button>
      </div>
      <div class="table-container overflow-x-auto max-h-96">
        <table class="database-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Customer Name</th>
              <th>Account Number</th>
              <th>Address</th>
              <th>Status</th>
              <th>Last Updated</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>MER-001</td>
              <td>Juan Dela Cruz</td>
              <td>1234567890</td>
              <td>123 Main St, Barangay 1</td>
              <td><span class="status-badge status-active">Active</span></td>
              <td>2023-06-15 14:30</td>
              <td>
                <button class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="text-yellow-600 hover:text-yellow-800">
                  <i class="fas fa-edit"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td>MER-002</td>
              <td>Maria Santos</td>
              <td>0987654321</td>
              <td>456 Oak St, Barangay 2</td>
              <td><span class="status-badge status-pending">Pending</span></td>
              <td>2023-07-20 09:15</td>
              <td>
                <button class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="text-yellow-600 hover:text-yellow-800">
                  <i class="fas fa-edit"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td>MER-003</td>
              <td>Robert Johnson</td>
              <td>1122334455</td>
              <td>789 Pine St, Barangay 3</td>
              <td><span class="status-badge status-active">Active</span></td>
              <td>2023-07-18 11:20</td>
              <td>
                <button class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="text-yellow-600 hover:text-yellow-800">
                  <i class="fas fa-edit"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td>MER-004</td>
              <td>Sarah Williams</td>
              <td>5566778899</td>
              <td>321 Elm St, Barangay 4</td>
              <td><span class="status-badge status-active">Active</span></td>
              <td>2023-07-15 16:45</td>
              <td>
                <button class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="text-yellow-600 hover:text-yellow-800">
                  <i class="fas fa-edit"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td>MER-005</td>
              <td>Michael Brown</td>
              <td>3344556677</td>
              <td>654 Maple St, Barangay 5</td>
              <td><span class="status-badge status-pending">Pending</span></td>
              <td>2023-07-10 10:10</td>
              <td>
                <button class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="text-yellow-600 hover:text-yellow-800">
                  <i class="fas fa-edit"></i>
                </button>
              </td>
            </tr>
            <tr class="hidden-row">
              <td>MER-006</td>
              <td>Jennifer Davis</td>
              <td>7788990011</td>
              <td>987 Cedar St, Barangay 6</td>
              <td><span class="status-badge status-active">Active</span></td>
              <td>2023-07-05 13:25</td>
              <td>
                <button class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="text-yellow-600 hover:text-yellow-800">
                  <i class="fas fa-edit"></i>
                </button>
              </td>
            </tr>
            <tr class="hidden-row">
              <td>MER-007</td>
              <td>David Wilson</td>
              <td>2233445566</td>
              <td>147 Birch St, Barangay 7</td>
              <td><span class="status-badge status-pending">Pending</span></td>
              <td>2023-06-28 15:30</td>
              <td>
                <button class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="text-yellow-600 hover:text-yellow-800">
                  <i class="fas fa-edit"></i>
                </button>
              </td>
            </tr>
            <tr class="hidden-row">
              <td>MER-008</td>
              <td>Lisa Martinez</td>
              <td>6677889900</td>
              <td>258 Walnut St, Barangay 8</td>
              <td><span class="status-badge status-active">Active</span></td>
              <td>2023-06-20 09:45</td>
              <td>
                <button class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="text-yellow-600 hover:text-yellow-800">
                  <i class="fas fa-edit"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="flex justify-center p-3 bg-gray-50">
        <button onclick="toggleAllRows('meralco-db')" class="btn-show-all text-white px-4 py-2 rounded-md transition flex items-center">
          <i class="fas fa-list mr-2"></i> Show All Records
        </button>
      </div>
    </div>

    <div class="database-section" id="operation-dashboard">
      <div class="database-header">
        <div>
          <i class="fas fa-tachometer-alt mr-2"></i> Operation Dashboard
          <span class="database-count">7 records</span>
        </div>
        <button class="text-white hover:text-gray-200">
          <i class="fas fa-ellipsis-v"></i>
        </button>
      </div>
      <div class="table-container overflow-x-auto max-h-96">
        <table class="database-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Project Name</th>
              <th>Description</th>
              <th>Status</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Budget</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>OPD-001</td>
              <td>Road Expansion</td>
              <td>Main road widening project</td>
              <td><span class="status-badge status-active">In Progress</span></td>
              <td>2023-01-15</td>
              <td>2023-12-31</td>
              <td>₱5,000,000</td>
            </tr>
            <tr>
              <td>OPD-002</td>
              <td>Drainage System</td>
              <td>Barangay 3 drainage improvement</td>
              <td><span class="status-badge status-completed">Completed</span></td>
              <td>2022-11-01</td>
              <td>2023-05-30</td>
              <td>₱2,500,000</td>
            </tr>
            <tr>
              <td>OPD-003</td>
              <td>Street Lighting</td>
              <td>Installation of solar street lights</td>
              <td><span class="status-badge status-pending">Pending</span></td>
              <td>2023-09-01</td>
              <td>2023-11-30</td>
              <td>₱1,200,000</td>
            </tr>
            <tr>
              <td>OPD-004</td>
              <td>Community Center</td>
              <td>Construction of multi-purpose hall</td>
              <td><span class="status-badge status-active">In Progress</span></td>
              <td>2023-03-10</td>
              <td>2023-10-15</td>
              <td>₱3,800,000</td>
            </tr>
            <tr>
              <td>OPD-005</td>
              <td>Water System</td>
              <td>Barangay 5 water pipe installation</td>
              <td><span class="status-badge status-completed">Completed</span></td>
              <td>2023-02-01</td>
              <td>2023-06-30</td>
              <td>₱1,750,000</td>
            </tr>
            <tr class="hidden-row">
              <td>OPD-006</td>
              <td>Health Center</td>
              <td>Renovation of barangay health station</td>
              <td><span class="status-badge status-pending">Pending</span></td>
              <td>2023-08-15</td>
              <td>2023-12-15</td>
              <td>₱2,100,000</td>
            </tr>
            <tr class="hidden-row">
              <td>OPD-007</td>
              <td>Playground</td>
              <td>Children's playground equipment</td>
              <td><span class="status-badge status-active">In Progress</span></td>
              <td>2023-05-20</td>
              <td>2023-09-30</td>
              <td>₱950,000</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="flex justify-center p-3 bg-gray-50">
        <button onclick="toggleAllRows('operation-dashboard')" class="btn-show-all text-white px-4 py-2 rounded-md transition flex items-center">
          <i class="fas fa-list mr-2"></i> Show All Records
        </button>
      </div>
    </div>

    <div class="database-section" id="operation-idsap">
      <div class="database-header">
        <div>
          <i class="fas fa-id-card mr-2"></i> Operation IDSAP
          <span class="database-count">9 records</span>
        </div>
        <button class="text-white hover:text-gray-200">
          <i class="fas fa-ellipsis-v"></i>
        </button>
      </div>
      <div class="table-container overflow-x-auto max-h-96">
        <table class="database-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Beneficiary</th>
              <th>Program</th>
              <th>Contact</th>
              <th>Status</th>
              <th>Date Approved</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>IDSAP-001</td>
              <td>Barangay 1 Residents</td>
              <td>Housing Assistance</td>
              <td>09123456789</td>
              <td><span class="status-badge status-completed">Completed</span></td>
              <td>2023-05-10</td>
              <td>₱3,000,000</td>
            </tr>
            <tr>
              <td>IDSAP-002</td>
              <td>Barangay 2 Farmers</td>
              <td>Livelihood Program</td>
              <td>09234567890</td>
              <td><span class="status-badge status-active">In Progress</span></td>
              <td>2023-07-15</td>
              <td>₱1,500,000</td>
            </tr>
            <tr>
              <td>IDSAP-003</td>
              <td>Senior Citizens</td>
              <td>Medical Assistance</td>
              <td>09345678901</td>
              <td><span class="status-badge status-pending">Pending</span></td>
              <td>-</td>
              <td>₱750,000</td>
            </tr>
            <tr>
              <td>IDSAP-004</td>
              <td>Youth Organization</td>
              <td>Educational Support</td>
              <td>09456789012</td>
              <td><span class="status-badge status-completed">Completed</span></td>
              <td>2023-04-22</td>
              <td>₱500,000</td>
            </tr>
            <tr>
              <td>IDSAP-005</td>
              <td>Women's Group</td>
              <td>Skills Training</td>
              <td>09567890123</td>
              <td><span class="status-badge status-active">In Progress</span></td>
              <td>2023-06-05</td>
              <td>₱650,000</td>
            </tr>
            <tr class="hidden-row">
              <td>IDSAP-006</td>
              <td>PWD Association</td>
              <td>Mobility Aids</td>
              <td>09678901234</td>
              <td><span class="status-badge status-completed">Completed</span></td>
              <td>2023-03-18</td>
              <td>₱350,000</td>
            </tr>
            <tr class="hidden-row">
              <td>IDSAP-007</td>
              <td>Fisherfolk</td>
              <td>Fishing Equipment</td>
              <td>09789012345</td>
              <td><span class="status-badge status-pending">Pending</span></td>
              <td>-</td>
              <td>₱420,000</td>
            </tr>
            <tr class="hidden-row">
              <td>IDSAP-008</td>
              <td>Market Vendors</td>
              <td>Market Stalls</td>
              <td>09890123456</td>
              <td><span class="status-badge status-active">In Progress</span></td>
              <td>2023-07-01</td>
              <td>₱1,200,000</td>
            </tr>
            <tr class="hidden-row">
              <td>IDSAP-009</td>
              <td>Transport Group</td>
              <td>Vehicle Repair</td>
              <td>09901234567</td>
              <td><span class="status-badge status-completed">Completed</span></td>
              <td>2023-02-14</td>
              <td>₱850,000</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="flex justify-center p-3 bg-gray-50">
        <button onclick="toggleAllRows('operation-idsap')" class="btn-show-all text-white px-4 py-2 rounded-md transition flex items-center">
          <i class="fas fa-list mr-2"></i> Show All Records
        </button>
      </div>
    </div>

    <div class="database-section" id="operation-panel">
      <div class="database-header">
        <div>
          <i class="fas fa-tasks mr-2"></i> Operation Panel
          <span class="database-count">10 records</span>
        </div>
        <button class="text-white hover:text-gray-200">
          <i class="fas fa-ellipsis-v"></i>
        </button>
      </div>
      <div class="table-container overflow-x-auto max-h-96">
        <table class="database-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Task Name</th>
              <th>Description</th>
              <th>Assigned To</th>
              <th>Priority</th>
              <th>Status</th>
              <th>Due Date</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>OP-001</td>
              <td>Site Inspection</td>
              <td>Road expansion area</td>
              <td>Team A</td>
              <td><span class="status-badge status-high">High</span></td>
              <td><span class="status-badge status-pending">Pending</span></td>
              <td>2023-08-15</td>
            </tr>
            <tr>
              <td>OP-002</td>
              <td>Document Preparation</td>
              <td>IDSAP program requirements</td>
              <td>Team B</td>
              <td><span class="status-badge status-medium">Medium</span></td>
              <td><span class="status-badge status-completed">Completed</span></td>
              <td>2023-07-30</td>
            </tr>
            <tr>
              <td>OP-003</td>
              <td>Beneficiary Interview</td>
              <td>HOA representatives</td>
              <td>Team C</td>
              <td><span class="status-badge status-high">High</span></td>
              <td><span class="status-badge status-active">In Progress</span></td>
              <td>2023-08-20</td>
            </tr>
            <tr>
              <td>OP-004</td>
              <td>Budget Review</td>
              <td>Q3 financial report</td>
              <td>Team D</td>
              <td><span class="status-badge status-low">Low</span></td>
              <td><span class="status-badge status-completed">Completed</span></td>
              <td>2023-07-10</td>
            </tr>
            <tr>
              <td>OP-005</td>
              <td>Community Meeting</td>
              <td>Barangay 4 residents</td>
              <td>Team A</td>
              <td><span class="status-badge status-medium">Medium</span></td>
              <td><span class="status-badge status-pending">Pending</span></td>
              <td>2023-09-05</td>
            </tr>
            <tr class="hidden-row">
              <td>OP-006</td>
              <td>Equipment Maintenance</td>
              <td>Office vehicles and tools</td>
              <td>Team E</td>
              <td><span class="status-badge status-medium">Medium</span></td>
              <td><span class="status-badge status-active">In Progress</span></td>
              <td>2023-08-25</td>
            </tr>
            <tr class="hidden-row">
              <td>OP-007</td>
              <td>Report Submission</td>
              <td>Monthly accomplishment report</td>
              <td>Team B</td>
              <td><span class="status-badge status-high">High</span></td>
              <td><span class="status-badge status-pending">Pending</span></td>
              <td>2023-08-05</td>
            </tr>
            <tr class="hidden-row">
              <td>OP-008</td>
              <td>Training Session</td>
              <td>New software implementation</td>
              <td>All Teams</td>
              <td><span class="status-badge status-medium">Medium</span></td>
              <td><span class="status-badge status-completed">Completed</span></td>
              <td>2023-07-22</td>
            </tr>
            <tr class="hidden-row">
              <td>OP-009</td>
              <td>Inventory Check</td>
              <td>Office supplies and materials</td>
              <td>Team D</td>
              <td><span class="status-badge status-low">Low</span></td>
              <td><span class="status-badge status-active">In Progress</span></td>
              <td>2023-08-30</td>
            </tr>
            <tr class="hidden-row">
              <td>OP-010</td>
              <td>Project Evaluation</td>
              <td>Road expansion progress</td>
              <td>Team C</td>
              <td><span class="status-badge status-high">High</span></td>
              <td><span class="status-badge status-pending">Pending</span></td>
              <td>2023-09-10</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="flex justify-center p-3 bg-gray-50">
        <button onclick="toggleAllRows('operation-panel')" class="btn-show-all text-white px-4 py-2 rounded-md transition flex items-center">
          <i class="fas fa-list mr-2"></i> Show All Records
        </button>
      </div>
    </div>

    <div class="database-section" id="admin-database">
      <div class="database-header">
        <div>
          <i class="fas fa-user-shield mr-2"></i> Admin Database
          <span class="database-count">6 records</span>
        </div>
        <button class="text-white hover:text-gray-200">
          <i class="fas fa-ellipsis-v"></i>
        </button>
      </div>
      <div class="table-container overflow-x-auto max-h-96">
        <table class="database-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Status</th>
              <th>Last Login</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>ADM-001</td>
              <td>admin1</td>
              <td>John Smith</td>
              <td>admin1@udho.gov</td>
              <td>Super Admin</td>
              <td><span class="status-badge status-active">Active</span></td>
              <td>2023-08-10 09:15</td>
            </tr>
            <tr>
              <td>ADM-002</td>
              <td>manager1</td>
              <td>Sarah Johnson</td>
              <td>manager1@udho.gov</td>
              <td>Manager</td>
              <td><span class="status-badge status-active">Active</span></td>
              <td>2023-08-15 14:20</td>
            </tr>
            <tr>
              <td>ADM-003</td>
              <td>staff1</td>
              <td>Michael Brown</td>
              <td>staff1@udho.gov</td>
              <td>Staff</td>
              <td><span class="status-badge status-inactive">Inactive</span></td>
              <td>2023-07-28 11:45</td>
            </tr>
            <tr>
              <td>ADM-004</td>
              <td>staff2</td>
              <td>Emily Davis</td>
              <td>staff2@udho.gov</td>
              <td>Staff</td>
              <td><span class="status-badge status-active">Active</span></td>
              <td>2023-08-16 08:30</td>
            </tr>
            <tr>
              <td>ADM-005</td>
              <td>manager2</td>
              <td>Robert Wilson</td>
              <td>manager2@udho.gov</td>
              <td>Manager</td>
              <td><span class="status-badge status-active">Active</span></td>
              <td>2023-08-14 16:45</td>
            </tr>
            <tr class="hidden-row">
              <td>ADM-006</td>
              <td>staff3</td>
              <td>Jennifer Martinez</td>
              <td>staff3@udho.gov</td>
              <td>Staff</td>
              <td><span class="status-badge status-inactive">Inactive</span></td>
              <td>2023-06-15 10:20</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="flex justify-center p-3 bg-gray-50">
        <button onclick="toggleAllRows('admin-database')" class="btn-show-all text-white px-4 py-2 rounded-md transition flex items-center">
          <i class="fas fa-list mr-2"></i> Show All Records
        </button>
      </div>
    </div>

    <div class="database-section" id="hoa-database">
      <div class="database-header">
        <div>
          <i class="fas fa-home mr-2"></i> HOA Database
          <span class="database-count">5 records</span>
        </div>
        <button class="text-white hover:text-gray-200">
          <i class="fas fa-ellipsis-v"></i>
        </button>
      </div>
      <div class="table-container overflow-x-auto max-h-96">
        <table class="database-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Association Name</th>
              <th>President</th>
              <th>Vice President</th>
              <th>Contact</th>
              <th>Members</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>HOA-001</td>
              <td>Green Valley Homeowners</td>
              <td>Maria Santos</td>
              <td>Juan Dela Cruz</td>
              <td>09123456789</td>
              <td>150</td>
              <td><span class="status-badge status-active">Active</span></td>
            </tr>
            <tr>
              <td>HOA-002</td>
              <td>Sunrise Village Association</td>
              <td>Robert Garcia</td>
              <td>Anna Reyes</td>
              <td>09234567890</td>
              <td>85</td>
              <td><span class="status-badge status-active">Active</span></td>
            </tr>
            <tr>
              <td>HOA-003</td>
              <td>Maple Grove Residents</td>
              <td>David Wilson</td>
              <td>Lisa Brown</td>
              <td>09345678901</td>
              <td>120</td>
              <td><span class="status-badge status-active">Active</span></td>
            </tr>
            <tr>
              <td>HOA-004</td>
              <td>Pine Crest Community</td>
              <td>James Johnson</td>
              <td>Sarah Miller</td>
              <td>09456789012</td>
              <td>95</td>
              <td><span class="status-badge status-active">Active</span></td>
            </tr>
            <tr>
              <td>HOA-005</td>
              <td>Riverbend Homeowners</td>
              <td>Michael Davis</td>
              <td>Jennifer Taylor</td>
              <td>09567890123</td>
              <td>110</td>
              <td><span class="status-badge status-active">Active</span></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="flex justify-center p-3 bg-gray-50">
        <button onclick="toggleAllRows('hoa-database')" class="btn-show-all text-white px-4 py-2 rounded-md transition flex items-center">
          <i class="fas fa-list mr-2"></i> Show All Records
        </button>
      </div>
    </div>
  </div>

  

  <script>
    // Current action type (backup or delete)
    let currentAction = '';
    let messageTimer;
    let countdownInterval;

    // Detailed messages for each action
    const actionMessages = {
      backup: {
        title: "Confirm Data Backup",
        message: `
          <p>You are about to back up all database information. This action will:</p>
          <ul class="list-disc pl-5 mt-2 space-y-1">
            <li>Generate Excel files for each database table</li>
            <li>Include all current data in the backup</li>
            <li>Create a timestamped record of this backup</li>
          </ul>
          <p class="mt-3">Please verify this is what you intend to do before proceeding.</p>
        `,
        button: "Backup Data",
        buttonClass: "bg-green-600 hover:bg-green-700"
      },
      delete: {
        title: "Confirm Data Deletion",
        message: `
          <p class="text-red-600 font-medium">WARNING: This is a highly destructive action!</p>
          <p class="mt-2">You are about to permanently delete all data from the system. This action will:</p>
          <ul class="list-disc pl-5 mt-2 space-y-1">
            <li>Remove all records from all database tables</li>
            <li>Not be recoverable through normal means</li>
            <li>Be recorded with your user account as the responsible party</li>
            <li>Require manual intervention to restore from backup</li>
          </ul>
          <p class="mt-3 font-bold">This action cannot be undone.</p>
          <p class="mt-2">Are you sure to delete all data? All data will be erased and you will be recorded as the user who deleted this data.</p>
        `,
        button: "Delete All Data",
        buttonClass: "bg-red-600 hover:bg-red-700"
      }
    };

    
    // Toggle visibility of all rows in a table
    function toggleAllRows(tableId) {
      const section = document.getElementById(tableId);
      if (!section) return;
      
      const table = section.querySelector('tbody');
      const button = section.querySelector('.btn-show-all');
      const hiddenRows = table.querySelectorAll('.hidden-row');
      
      if (hiddenRows.length === 0) return;
      
      const isShowingAll = hiddenRows[0].style.display === 'table-row';
      
      hiddenRows.forEach(row => {
        row.style.display = isShowingAll ? 'none' : 'table-row';
      });
      
      button.innerHTML = isShowingAll ? 
        '<i class="fas fa-list mr-2"></i> Show All Records' : 
        '<i class="fas fa-eye-slash mr-2"></i> Show Less';
    }

    // Perform the selected action
    function performAction() {
      clearInterval(countdownInterval);
      document.getElementById('confirmationModal').classList.add('hidden');
      
      if (currentAction === 'backup') {
        backupData();
      } else {
        deleteData();
      }
    }

    // Backup data function
    function backupData() {
      // Show loading state
      const loadingMessage = `
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white p-6 rounded-lg shadow-lg max-w-md">
            <div class="flex items-center">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mr-3"></div>
              <div>
                <h3 class="text-lg font-bold">Creating Backup</h3>
                <p class="mt-1">Generating Excel files for all databases...</p>
              </div>
            </div>
          </div>
        </div>
      `;
      
      document.body.insertAdjacentHTML('beforeend', loadingMessage);
      
      // Simulate backup process (replace with actual API call)
      setTimeout(() => {
        // Remove loading indicator
        document.querySelector('.fixed.inset-0').remove();
        
        // Show success message
        alert('Backup completed successfully! Files have been downloaded.');
      }, 2000);
    }

    // Delete data function
    function deleteData() {
      // Show warning loading state
      const loadingMessage = `
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white p-6 rounded-lg shadow-lg max-w-md">
            <div class="flex items-center">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mr-3"></div>
              <div>
                <h3 class="text-lg font-bold text-red-600">Deleting All Data</h3>
                <p class="mt-1">This action cannot be undone. Please wait...</p>
              </div>
            </div>
          </div>
        </div>
      `;
      
      document.body.insertAdjacentHTML('beforeend', loadingMessage);
      
      // Simulate deletion process (replace with actual API call)
      setTimeout(() => {
        // Remove loading indicator
        document.querySelector('.fixed.inset-0').remove();
        
        // Show success message
        alert('All data has been deleted. This action has been logged in the system.');
      }, 2000);
    }

    // Event listeners for confirmation modal
    document.getElementById('cancelAction').addEventListener('click', function() {
      clearInterval(countdownInterval);
      document.getElementById('confirmationModal').classList.add('hidden');
    });

    document.getElementById('confirmAction').addEventListener('click', function() {
      performAction();
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
      // Hide all rows beyond the first 5 in each table
      document.querySelectorAll('.database-section').forEach(section => {
        const table = section.querySelector('tbody');
        if (table) {
          const rows = table.querySelectorAll('tr');
          rows.forEach((row, index) => {
            if (index >= 5) {
              row.classList.add('hidden-row');
              row.style.display = 'none';
            }
          });
        }
      });
    });
  </script>
</body>
</html>