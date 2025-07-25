<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
          <a href="/UDHO%20SYSTEM/Backup_data/backup.php" class="sidebar-link flex items-center py-3 px-4">
            <i class="fas fa-database mr-3"></i> Backup Data
          </a>
        </li>
        <li>
          <a href="/UDHO%20SYSTEM/employee/employee.php" class="sidebar-link flex items-center py-3 px-4">
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
      <h1 class="text-2xl font-bold text-gray-800">Admin Executive Dashboard</h1>
      <div class="flex items-center gap-2">
        <img src="\UDHO%20SYSTEM\assets\UDHOLOGO.png" alt="Logo" class="h-8">
        <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
      </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow-md custom-card">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-gray-500">Total Surveys</p>
            <h3 class="text-2xl font-bold">1,248</h3>
          </div>
          <div class="bg-blue-100 p-3 rounded-full">
            <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
          </div>
        </div>
        <div class="mt-4">
          <canvas id="dailySurveyChart" height="100"></canvas>
        </div>
      </div>
      
      <div class="bg-white p-6 rounded-lg shadow-md custom-card">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-gray-500">HOA Groups Status</p>
            <h3 class="text-2xl font-bold">89</h3>
          </div>
          <div class="bg-yellow-100 p-3 rounded-full">
            <i class="fas fa-home text-yellow-600 text-xl"></i>
          </div>
        </div>
        <div class="mt-4">
          <canvas id="hoaStatusChart" height="100"></canvas>
        </div>
      </div>
      
      <div class="bg-white p-6 rounded-lg shadow-md custom-card">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-gray-500">Active Users</p>
            <h3 class="text-2xl font-bold">37</h3>
          </div>
          <div class="bg-green-100 p-3 rounded-full">
            <i class="fas fa-users text-green-600 text-xl"></i>
          </div>
        </div>
        <div class="mt-4">
          <canvas id="userTypeChart" height="100"></canvas>
        </div>
      </div>
    </div>

    <!-- Survey Data Graphics Section -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        <h2 class="text-xl font-semibold">Survey Data Statistics</h2>
        <div class="flex flex-wrap gap-2">
          <button onclick="changeTimePeriod('daily')" class="time-period-btn px-4 py-2 bg-blue-600 text-white rounded-md transition active">
            Daily
          </button>
          <button onclick="changeTimePeriod('weekly')" class="time-period-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-md transition">
            Weekly
          </button>
          <button onclick="changeTimePeriod('monthly')" class="time-period-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-md transition">
            Monthly
          </button>
          <button onclick="changeTimePeriod('yearly')" class="time-period-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-md transition">
            Yearly
          </button>
          <a href="operation_IDSAP.php" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md transition flex items-center ml-auto">
            <i class="fas fa-external-link-alt mr-2"></i> View Full Report
          </a>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Survey Trend Chart -->
        <div class="bg-gray-50 p-4 rounded-lg">
          <h3 class="text-lg font-medium mb-3">Survey Trend</h3>
          <canvas id="surveyTrendChart" height="250"></canvas>
        </div>
        
        <!-- Survey Completion Rate -->
        <div class="bg-gray-50 p-4 rounded-lg">
          <h3 class="text-lg font-medium mb-3">Completion Status</h3>
          <canvas id="completionChart" height="250"></canvas>
        </div>
        
        <!-- Survey by Barangay -->
        <div class="bg-gray-50 p-4 rounded-lg">
          <h3 class="text-lg font-medium mb-3">Surveys by Barangay</h3>
          <canvas id="barangayChart" height="250"></canvas>
        </div>
        
        <!-- Survey Types -->
        <div class="bg-gray-50 p-4 rounded-lg">
          <h3 class="text-lg font-medium mb-3">Survey Types</h3>
          <canvas id="surveyTypeChart" height="250"></canvas>
        </div>
      </div>
    </div>

    <!-- Department Selection -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
      <h2 class="text-xl font-semibold mb-4">Department Data</h2>
      <div class="flex flex-wrap gap-4">
        <button onclick="showData('admin')" class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-md transition flex items-center">
          <i class="fas fa-user-shield mr-2"></i> Admin Data
        </button>
        <button onclick="showData('hoa')" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition flex items-center">
          <i class="fas fa-home mr-2"></i> HOA Data
        </button>
        <button onclick="showData('operation')" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-md transition flex items-center">
          <i class="fas fa-tasks mr-2"></i> Operation Data
        </button>
      </div>
    </div>

    <!-- Data Display Sections -->
    <div id="adminData" class="bg-white p-6 rounded-lg shadow-md mb-6 hidden">
      <h2 class="text-xl font-semibold mb-4">Admin Executive Records</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <!-- Sample data rows -->
            <tr>
              <td class="px-6 py-4 whitespace-nowrap">ADM-001</td>
              <td class="px-6 py-4 whitespace-nowrap">Juan Dela Cruz</td>
              <td class="px-6 py-4 whitespace-nowrap">System Admin</td>
              <td class="px-6 py-4 whitespace-nowrap">Today, 10:45 AM</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <button class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                <button class="text-yellow-600 hover:text-yellow-900">Edit</button>
              </td>
            </tr>
            <!-- More rows would be loaded from database -->
          </tbody>
        </table>
      </div>
    </div>

    <div id="hoaData" class="bg-white p-6 rounded-lg shadow-md mb-6 hidden">
      <h2 class="text-xl font-semibold mb-4">HOA Records</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">HOA ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Association Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">President</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <!-- Sample data rows -->
            <tr>
              <td class="px-6 py-4 whitespace-nowrap">HOA-001</td>
              <td class="px-6 py-4 whitespace-nowrap">Green Valley Homeowners</td>
              <td class="px-6 py-4 whitespace-nowrap">Maria Santos</td>
              <td class="px-6 py-4 whitespace-nowrap">09123456789</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <button class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                <button class="text-yellow-600 hover:text-yellow-900">Edit</button>
              </td>
            </tr>
            <!-- More rows would be loaded from database -->
          </tbody>
        </table>
      </div>
    </div>

    <div id="operationData" class="bg-white p-6 rounded-lg shadow-md mb-6">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        <h2 class="text-xl font-semibold">Operation Records</h2>
        <div class="flex gap-2">
          <button onclick="showOperationData('idsap')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
            IDSAP Data
          </button>
          <button onclick="showOperationData('pdc')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition">
            PDC Data
          </button>
          <button onclick="showOperationData('meralco')" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md transition">
            Meralco Data
          </button>
        </div>
      </div>

      <!-- Operation Data Tables -->
      <div id="idsapData">
        <h3 class="text-lg font-medium mb-3">IDSAP Survey Records</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Survey ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barangay</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <!-- Sample data rows -->
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">IDSAP-001</td>
                <td class="px-6 py-4 whitespace-nowrap">Barangay 1</td>
                <td class="px-6 py-4 whitespace-nowrap">2023-06-15</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Completed</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <button class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                  <button class="text-yellow-600 hover:text-yellow-900">Edit</button>
                </td>
              </tr>
              <!-- More rows would be loaded from database -->
            </tbody>
          </table>
        </div>
      </div>

      <div id="pdcData" class="hidden">
        <h3 class="text-lg font-medium mb-3">PDC Records</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case No.</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Issued</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <!-- Sample data rows -->
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">PDC-2023-001</td>
                <td class="px-6 py-4">Land dispute resolution</td>
                <td class="px-6 py-4 whitespace-nowrap">2023-06-10</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <button class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                  <button class="text-yellow-600 hover:text-yellow-900">Edit</button>
                </td>
              </tr>
              <!-- More rows would be loaded from database -->
            </tbody>
          </table>
        </div>
      </div>

      <div id="meralcoData" class="hidden">
        <h3 class="text-lg font-medium mb-3">Meralco Applications</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">App No.</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <!-- Sample data rows -->
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">MER-2023-001</td>
                <td class="px-6 py-4 whitespace-nowrap">Juan Dela Cruz</td>
                <td class="px-6 py-4">123 Green Valley, Barangay 1</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Processing</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <button class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                  <button class="text-yellow-600 hover:text-yellow-900">Edit</button>
                </td>
              </tr>
              <!-- More rows would be loaded from database -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Initialize charts
    document.addEventListener('DOMContentLoaded', function() {
      // Daily Survey Chart
      const dailySurveyCtx = document.getElementById('dailySurveyChart').getContext('2d');
      new Chart(dailySurveyCtx, {
        type: 'line',
        data: {
          labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
          datasets: [{
            label: 'Surveys Completed',
            data: [12, 19, 15, 27, 22, 18, 5],
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
            tension: 0.3,
            fill: true
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: { beginAtZero: true }
          }
        }
      });

     
      // HOA Status Chart
        const hoaStatusCtx = document.getElementById('hoaStatusChart').getContext('2d');
        new Chart(hoaStatusCtx, {
          type: 'doughnut',
          data: {
            labels: ['Active', 'Inactive', 'Abolished'],
            datasets: [{
              data: [56, 23, 10],
              backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { position: 'bottom' }
            }
          }
        });

      // User Type Chart
      const userTypeCtx = document.getElementById('userTypeChart').getContext('2d');
      new Chart(userTypeCtx, {
        type: 'bar',
        data: {
          labels: ['Admin', 'HOA', 'Operation'],
          datasets: [{
            label: 'Active Users',
            data: [8, 15, 14],
            backgroundColor: ['#8b5cf6', '#3b82f6', '#10b981']
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: { beginAtZero: true }
          }
        }
      });

      // Initialize survey data charts
      initializeSurveyCharts('daily');
    });

    // Initialize survey data charts based on time period
    function initializeSurveyCharts(period) {
      // Data for different time periods
      const periodData = {
        daily: {
          trendLabels: ['6AM', '9AM', '12PM', '3PM', '6PM'],
          trendData: [5, 15, 30, 25, 10],
          barangayLabels: ['Brgy 1', 'Brgy 2', 'Brgy 3', 'Brgy 4', 'Brgy 5'],
          barangayData: [15, 20, 12, 18, 10]
        },
        weekly: {
          trendLabels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
          trendData: [25, 40, 35, 50, 45, 15],
          barangayLabels: ['Brgy 1', 'Brgy 2', 'Brgy 3', 'Brgy 4', 'Brgy 5'],
          barangayData: [45, 60, 35, 55, 30]
        },
        monthly: {
          trendLabels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
          trendData: [120, 150, 180, 90],
          barangayLabels: ['Brgy 1', 'Brgy 2', 'Brgy 3', 'Brgy 4', 'Brgy 5'],
          barangayData: [180, 220, 150, 200, 110]
        },
        yearly: {
          trendLabels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
          trendData: [80, 95, 110, 105, 120, 150, 140, 160, 130, 125, 110, 90],
          barangayLabels: ['Brgy 1', 'Brgy 2', 'Brgy 3', 'Brgy 4', 'Brgy 5'],
          barangayData: [450, 550, 400, 500, 300]
        }
      };

      // Destroy existing charts if they exist
      if (window.surveyTrendChart) window.surveyTrendChart.destroy();
      if (window.completionChart) window.completionChart.destroy();
      if (window.barangayChart) window.barangayChart.destroy();
      if (window.surveyTypeChart) window.surveyTypeChart.destroy();

      // Survey Trend Chart
      const surveyTrendCtx = document.getElementById('surveyTrendChart').getContext('2d');
      window.surveyTrendChart = new Chart(surveyTrendCtx, {
        type: 'line',
        data: {
          labels: periodData[period].trendLabels,
          datasets: [{
            label: 'Surveys Completed',
            data: periodData[period].trendData,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.3,
            fill: true
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: { beginAtZero: true }
          }
        }
      });

      // Completion Chart
      const completionCtx = document.getElementById('completionChart').getContext('2d');
      window.completionChart = new Chart(completionCtx, {
        type: 'doughnut',
        data: {
          labels: ['Completed', 'In Progress', 'Not Started'],
          datasets: [{
            data: [75, 15, 10],
            backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });

      // Barangay Chart
      const barangayCtx = document.getElementById('barangayChart').getContext('2d');
      window.barangayChart = new Chart(barangayCtx, {
        type: 'bar',
        data: {
          labels: periodData[period].barangayLabels,
          datasets: [{
            label: 'Surveys',
            data: periodData[period].barangayData,
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: { beginAtZero: true }
          }
        }
      });

      // Survey Type Chart
      const surveyTypeCtx = document.getElementById('surveyTypeChart').getContext('2d');
      window.surveyTypeChart = new Chart(surveyTypeCtx, {
        type: 'pie',
        data: {
          labels: ['IDSAP', 'PDC', 'Meralco', 'Other'],
          datasets: [{
            data: [45, 25, 20, 10],
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6']
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });
    }

    // Change time period for survey data
    function changeTimePeriod(period) {
      // Update active button
      document.querySelectorAll('.time-period-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
      });
      event.target.classList.add('active', 'bg-blue-600', 'text-white');
      event.target.classList.remove('bg-gray-200', 'text-gray-700');
      
      // Update charts
      initializeSurveyCharts(period);
    }

    // Show department data
    function showData(department) {
      document.getElementById('adminData').classList.add('hidden');
      document.getElementById('hoaData').classList.add('hidden');
      document.getElementById('operationData').classList.add('hidden');
      
      if (department === 'admin') {
        document.getElementById('adminData').classList.remove('hidden');
      } else if (department === 'hoa') {
        document.getElementById('hoaData').classList.remove('hidden');
      } else {
        document.getElementById('operationData').classList.remove('hidden');
      }
    }

    // Show operation sub-data
    function showOperationData(type) {
      document.getElementById('idsapData').classList.add('hidden');
      document.getElementById('pdcData').classList.add('hidden');
      document.getElementById('meralcoData').classList.add('hidden');
      
      document.getElementById(type + 'Data').classList.remove('hidden');
    }

    // Initialize with operation data visible
    showData('operation');
  </script>
</body>

</html>