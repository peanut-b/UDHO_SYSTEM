<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Employee Management</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
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
    .badge {
      display: inline-block;
      padding: 0.25em 0.4em;
      font-size: 75%;
      font-weight: 700;
      line-height: 1;
      text-align: center;
      white-space: nowrap;
      vertical-align: baseline;
      border-radius: 0.25rem;
    }
    .badge-danger {
      color: #fff;
      background-color: #dc3545;
    }
    .message-preview {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 200px;
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
              <img src="assets/profile_pictures/<?php echo htmlspecialchars($profilePicture); ?>" 
                  alt="Profile Picture" 
                  class="w-full h-full object-cover"
                  onerror="this.src='/UDHO%20SYSTEM/assets/PROFILE_SAMPLE.jpg'">
          </div>
    </div>
    <nav class="mt-6">
      <ul>
        <li>
          <a href="/UDHO%20SYSTEM/Admin%20executive/adminexecutive_dashboard.php" class="sidebar-link flex items-center py-3 px-4 active-link">
            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
          </a>
        </li>
        <li>
          <a href="/UDHO%20SYSTEM/Admin%20executive/backup.php" class="sidebar-link flex items-center py-3 px-4">
            <i class="fas fa-database mr-3"></i> Backup Data
          </a>
        </li>
        <li>
          <a href="/UDHO%20SYSTEM/Admin%20executive/employee.php" class="sidebar-link flex items-center py-3 px-4">
            <i class="fas fa-users mr-3"></i> Employees
          </a>
        </li>
        <li>
          <a href="/UDHO%20SYSTEM/Settings/setting_executive.php" class="sidebar-link flex items-center py-3 px-4">
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
  <div class="flex-1 p-6 overflow-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
      <h1 class="text-2xl font-bold text-gray-800">Employee Management</h1>
      <div class="flex items-center gap-2">
        <img src="\UDHO%20SYSTEM\assets\UDHOLOGO.png" alt="Logo" class="h-8">
        <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
      </div>
    </div>

    <!-- Notification and Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <!-- Password Reset Requests -->
      <div class="bg-white p-6 rounded-lg shadow-md custom-card relative">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-gray-500">Password Reset Requests</p>
            <h3 class="text-2xl font-bold">5</h3>
          </div>
          <div class="bg-red-100 p-3 rounded-full">
            <i class="fas fa-key text-red-600 text-xl"></i>
          </div>
        </div>
        <div class="mt-4">
          <button onclick="showPasswordRequests()" class="w-full bg-red-100 hover:bg-red-200 text-red-800 py-2 rounded-md transition">
            View Requests
          </button>
        </div>
        <span class="badge badge-danger absolute -top-2 -right-2">5 New</span>
      </div>
      
      <!-- Active Employees -->
      <div class="bg-white p-6 rounded-lg shadow-md custom-card">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-gray-500">Active Employees</p>
            <h3 class="text-2xl font-bold">42</h3>
          </div>
          <div class="bg-green-100 p-3 rounded-full">
            <i class="fas fa-user-check text-green-600 text-xl"></i>
          </div>
        </div>
        <div class="mt-4">
          <button onclick="filterEmployees('active')" class="w-full bg-green-100 hover:bg-green-200 text-green-800 py-2 rounded-md transition">
            View Active
          </button>
        </div>
      </div>
      
      <!-- Inactive/Locked Employees -->
      <div class="bg-white p-6 rounded-lg shadow-md custom-card">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-gray-500">Inactive/Locked Employees</p>
            <h3 class="text-2xl font-bold">8</h3>
          </div>
          <div class="bg-yellow-100 p-3 rounded-full">
            <i class="fas fa-user-lock text-yellow-600 text-xl"></i>
          </div>
        </div>
        <div class="mt-4">
          <button onclick="filterEmployees('inactive')" class="w-full bg-yellow-100 hover:bg-yellow-200 text-yellow-800 py-2 rounded-md transition">
            View Inactive
          </button>
        </div>
      </div>
    </div>

    <!-- Employee Management Section -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        <h2 class="text-xl font-semibold">Employee Records</h2>
        <div class="flex flex-wrap gap-2">
          <button onclick="filterEmployees('all')" class="px-4 py-2 bg-blue-600 text-white rounded-md transition">
            All Employees
          </button>
          <button onclick="showAddEmployeeModal()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition flex items-center">
            <i class="fas fa-plus mr-2"></i> Add Employee
          </button>
          <button onclick="exportEmployeeData()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md transition flex items-center">
            <i class="fas fa-file-export mr-2"></i> Export Data
          </button>
        </div>
      </div>

      <!-- Employee Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200" id="employeeTableBody">
            <!-- Sample data rows will be loaded here -->
            <tr>
              <td class="px-6 py-4 whitespace-nowrap">EMP-001</td>
              <td class="px-6 py-4 whitespace-nowrap">Juan Dela Cruz</td>
              <td class="px-6 py-4 whitespace-nowrap">Administration</td>
              <td class="px-6 py-4 whitespace-nowrap">System Admin</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Active</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">Today, 10:45 AM</td>
              <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                <button onclick="viewEmployeeDetails('EMP-001')" class="text-blue-600 hover:text-blue-900" title="View">
                  <i class="fas fa-eye"></i>
                </button>
                <button onclick="editEmployee('EMP-001')" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                  <i class="fas fa-edit"></i>
                </button>
                <button onclick="toggleEmployeeLock('EMP-001')" class="text-red-600 hover:text-red-900" title="Lock/Unlock">
                  <i class="fas fa-lock"></i>
                </button>
                <button onclick="showLoginHistory('EMP-001')" class="text-purple-600 hover:text-purple-900" title="Login History">
                  <i class="fas fa-history"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap">EMP-002</td>
              <td class="px-6 py-4 whitespace-nowrap">Maria Santos</td>
              <td class="px-6 py-4 whitespace-nowrap">Operations</td>
              <td class="px-6 py-4 whitespace-nowrap">Field Officer</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Locked</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">3 days ago</td>
              <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                <button onclick="viewEmployeeDetails('EMP-002')" class="text-blue-600 hover:text-blue-900" title="View">
                  <i class="fas fa-eye"></i>
                </button>
                <button onclick="editEmployee('EMP-002')" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                  <i class="fas fa-edit"></i>
                </button>
                <button onclick="toggleEmployeeLock('EMP-002')" class="text-green-600 hover:text-green-900" title="Lock/Unlock">
                  <i class="fas fa-unlock"></i>
                </button>
                <button onclick="showLoginHistory('EMP-002')" class="text-purple-600 hover:text-purple-900" title="Login History">
                  <i class="fas fa-history"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap">EMP-003</td>
              <td class="px-6 py-4 whitespace-nowrap">Pedro Reyes</td>
              <td class="px-6 py-4 whitespace-nowrap">HOA Relations</td>
              <td class="px-6 py-4 whitespace-nowrap">Coordinator</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Active</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">Yesterday, 2:30 PM</td>
              <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                <button onclick="viewEmployeeDetails('EMP-003')" class="text-blue-600 hover:text-blue-900" title="View">
                  <i class="fas fa-eye"></i>
                </button>
                <button onclick="editEmployee('EMP-003')" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                  <i class="fas fa-edit"></i>
                </button>
                <button onclick="toggleEmployeeLock('EMP-003')" class="text-red-600 hover:text-red-900" title="Lock/Unlock">
                  <i class="fas fa-lock"></i>
                </button>
                <button onclick="showLoginHistory('EMP-003')" class="text-purple-600 hover:text-purple-900" title="Login History">
                  <i class="fas fa-history"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap">EMP-004</td>
              <td class="px-6 py-4 whitespace-nowrap">Ana Lopez</td>
              <td class="px-6 py-4 whitespace-nowrap">Administration</td>
              <td class="px-6 py-4 whitespace-nowrap">Data Entry</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Password Reset Requested</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">1 hour ago</td>
              <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                <button onclick="viewEmployeeDetails('EMP-004')" class="text-blue-600 hover:text-blue-900" title="View">
                  <i class="fas fa-eye"></i>
                </button>
                <button onclick="editEmployee('EMP-004')" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                  <i class="fas fa-edit"></i>
                </button>
                
                <button onclick="resetPassword('EMP-004')" class="text-blue-600 hover:text-blue-900" title="Reset Password">
                  <i class="fas fa-key"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="flex justify-between items-center mt-4">
        <div class="text-sm text-gray-500">
          Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">50</span> employees
        </div>
        <div class="flex gap-1">
          <button class="px-3 py-1 bg-gray-200 rounded-md">Previous</button>
          <button class="px-3 py-1 bg-blue-600 text-white rounded-md">1</button>
          <button class="px-3 py-1 bg-gray-200 rounded-md">2</button>
          <button class="px-3 py-1 bg-gray-200 rounded-md">3</button>
          <button class="px-3 py-1 bg-gray-200 rounded-md">Next</button>
        </div>
      </div>
    </div>

    <!-- Password Reset Requests Modal -->
    <div id="passwordRequestsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
      <div class="bg-white rounded-lg p-6 w-full max-w-4xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold">Password Reset Requests (5)</h3>
          <button onclick="closeModal('passwordRequestsModal')" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="overflow-y-auto max-h-96">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Date</th>
              
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">Ana Lopez</td>
                <td class="px-6 py-4 whitespace-nowrap">Administration</td>
                <td class="px-6 py-4 whitespace-nowrap">Today, 9:15 AM</td>
                
                <td class="px-6 py-4 whitespace-nowrap">
                  
                  <button onclick="resetPassword('EMP-004')" class="text-blue-600 hover:text-blue-900 mr-2" title="Reset Password">
                    <i class="fas fa-key"></i>
                  </button>
                  <button onclick="denyPasswordRequest('EMP-004')" class="text-red-600 hover:text-red-900" title="Deny Request">
                    <i class="fas fa-times"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">Carlos Garcia</td>
                <td class="px-6 py-4 whitespace-nowrap">Operations</td>
                <td class="px-6 py-4 whitespace-nowrap">Yesterday, 4:30 PM</td>
                
                <td class="px-6 py-4 whitespace-nowrap">
                 
                  <button onclick="resetPassword('EMP-005')" class="text-blue-600 hover:text-blue-900 mr-2" title="Reset Password">
                    <i class="fas fa-key"></i>
                  </button>
                  <button onclick="denyPasswordRequest('EMP-005')" class="text-red-600 hover:text-red-900" title="Deny Request">
                    <i class="fas fa-times"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mt-4 flex justify-end">
          <button onclick="closeModal('passwordRequestsModal')" class="px-4 py-2 bg-gray-200 rounded-md mr-2">Close</button>
          <button onclick="resetAllPasswords()" class="px-4 py-2 bg-blue-600 text-white rounded-md">Reset All</button>
        </div>
      </div>
    </div>

    <!-- Message View Modal -->
    <div id="messageModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold">Password Reset Request from <span id="messageEmployeeName">Employee</span></h3>
          <button onclick="closeModal('messageModal')" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="bg-gray-50 p-4 rounded-md mb-4">
          <p id="messageContent" class="whitespace-pre-line">Message content will appear here...</p>
        </div>
        <div class="flex justify-end">
          <button onclick="closeModal('messageModal')" class="px-4 py-2 bg-gray-200 rounded-md mr-2">Close</button>
          <button onclick="resetPasswordFromMessage()" class="px-4 py-2 bg-blue-600 text-white rounded-md">Reset Password</button>
        </div>
      </div>
    </div>

    <!-- Add Employee Modal -->
    <div id="addEmployeeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold">Add New Employee</h3>
          <button onclick="closeModal('addEmployeeModal')" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <form id="employeeForm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">First Name</label>
              <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Last Name</label>
              <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Division</label>
              <select class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option>Administrative</option>
                <option>Operations</option>
                <option>HOA</option>
                <option>Admin Executive</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Position</label>
              <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input type="email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Username</label>
              <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Initial Password</label>
              <input type="password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Access Level</label>
              <select class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option>Administrative</option>
                <option>Operations</option>
                <option>HOA</option>
                <option>Admin Executive</option>
              </select>
            </div>
          </div>
          <div class="mt-6 flex justify-end">
            <button type="button" onclick="closeModal('addEmployeeModal')" class="px-4 py-2 bg-gray-200 rounded-md mr-2">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Add Employee</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Login History Modal -->
    <div id="loginHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
  <div class="bg-white rounded-lg p-6 w-full max-w-3xl">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-xl font-semibold">Login History for <span id="historyEmployeeName">Employee Name</span></h3>
      <button onclick="closeModal('loginHistoryModal')" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div class="overflow-y-auto max-h-96">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200" id="loginHistoryBody">
          <!-- Login history will be loaded here via AJAX -->
        </tbody>
      </table>
    </div>
    <div class="mt-4 flex justify-between items-center">
      <div class="text-sm text-gray-500" id="historyCount">Showing 0 entries</div>
      <div>
        <button onclick="closeModal('loginHistoryModal')" class="px-4 py-2 bg-gray-200 rounded-md">Close</button>
      </div>
    </div>
  </div>
</div>
  </div>

  <script>
    // Employee Management Functions
    function filterEmployees(status) {
      // This would filter the employee table based on status
      console.log(`Filtering employees by status: ${status}`);
      // For demo, we're just showing all employees
      document.querySelectorAll('#employeeTableBody tr').forEach(row => {
        row.style.display = '';
      });
    }

    function showPasswordRequests() {
      document.getElementById('passwordRequestsModal').classList.remove('hidden');
    }

    function showFullMessage(name, message) {
      document.getElementById('messageEmployeeName').textContent = name;
      document.getElementById('messageContent').textContent = message;
      document.getElementById('messageModal').classList.remove('hidden');
    }

    function showRequestMessage(employeeId, name, message) {
      document.getElementById('messageEmployeeName').textContent = name;
      document.getElementById('messageContent').textContent = message;
      document.getElementById('messageModal').classList.remove('hidden');
    }

    function resetPasswordFromMessage() {
      // This would reset the password for the employee in the message
      alert('Password reset initiated for this employee.');
      closeModal('messageModal');
    }

    function closeModal(modalId) {
      document.getElementById(modalId).classList.add('hidden');
    }

    function resetPassword(employeeId) {
      console.log(`Resetting password for employee: ${employeeId}`);
      alert(`Password reset initiated for employee ${employeeId}. They will receive an email with instructions.`);
      closeModal('passwordRequestsModal');
    }

    function denyPasswordRequest(employeeId) {
      console.log(`Denying password reset for employee: ${employeeId}`);
      alert(`Password reset request denied for employee ${employeeId}.`);
      closeModal('passwordRequestsModal');
    }

    function resetAllPasswords() {
      console.log('Resetting all pending password requests');
      alert('All pending password reset requests have been processed.');
      closeModal('passwordRequestsModal');
    }

    function toggleEmployeeLock(employeeId) {
      console.log(`Toggling lock status for employee: ${employeeId}`);
      alert(`Employee ${employeeId} lock status has been updated.`);
    }

    function viewEmployeeDetails(employeeId) {
      console.log(`Viewing details for employee: ${employeeId}`);
      window.location.href = `/UDHO%20SYSTEM/employee/employee_details.php?id=${employeeId}`;
    }

    function editEmployee(employeeId) {
      console.log(`Editing employee: ${employeeId}`);
      window.location.href = `/UDHO%20SYSTEM/employee/edit_employee.php?id=${employeeId}`;
    }

    function showLoginHistory(employeeId) {
      console.log(`Showing login history for employee: ${employeeId}`);
      document.getElementById('loginHistoryModal').classList.remove('hidden');
    }

    function exportEmployeeData() {
      console.log('Exporting employee data');
      alert('Employee data export initiated. You will receive an email with the download link.');
    }

    // Form submission
    document.getElementById('employeeForm').addEventListener('submit', function(e) {
      e.preventDefault();
      console.log('Submitting new employee form');
      alert('New employee added successfully!');
      closeModal('addEmployeeModal');
    });
    
  </script>
</body>

</html>