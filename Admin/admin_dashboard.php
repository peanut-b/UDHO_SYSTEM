<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Operation Panel</title>

  <!-- Tailwind CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

  <style>
    .custom-card:hover {
      border-color: #2563eb !important;
      box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.3);
    }

    .icon-style {
      font-size: 2.2rem;
      color: #111827;
    }
    
    .metric-card {
      transition: all 0.3s ease;
    }
    .metric-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
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
                    <a href="\UDHO%20SYSTEM\Operation\operation_dashboard.php" class="block py-2.5 px-4 hover:bg-gray-700 flex items-center">
                        <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="\UDHO%20SYSTEM\Settings\setting.php" class="block py-2.5 px-4 hover:bg-gray-700 flex items-center">
                        <i class="fas fa-cog mr-3"></i> Settings
                    </a>
                </li>
                <li>
                    <a href="\UDHO%20SYSTEM\404%20not%20found\404.php" class="block py-2.5 px-4 hover:bg-gray-700 mt-10 flex items-center">
                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>
    </div>

  <!-- Main Content -->
  <div class="flex-1 p-10">
    <header class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Operation Dashboard</h1>
      <div class="flex items-center gap-2">
        <img src="\UDHO%20SYSTEM\assets\UDHOLOGO.png" alt="Logo" class="h-8">
        <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
      </div>
    </header>

    <!-- Dashboard Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <!-- Latest Routing Number -->
      <div class="bg-white rounded-lg shadow p-6 metric-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">Latest Routing Number</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">UDHO-2025-444</p>
            <p class="mt-2 text-sm text-gray-500">Created: <?php echo date('M d, Y h:i A'); ?></p>
          </div>
          <div class="bg-blue-100 p-3 rounded-full">
            <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
          </div>
        </div>
      </div>
      
      <!-- Incoming Papers Today -->
      <div class="bg-white rounded-lg shadow p-6 metric-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">Incoming Papers Today</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">24</p>
            <p class="mt-2 text-sm text-gray-500">+3 from yesterday</p>
          </div>
          <div class="bg-green-100 p-3 rounded-full">
            <i class="fas fa-inbox text-green-600 text-2xl"></i>
          </div>
        </div>
      </div>
      
      <!-- Outgoing Papers Today -->
      <div class="bg-white rounded-lg shadow p-6 metric-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">Outgoing Papers Today</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">18</p>
            <p class="mt-2 text-sm text-gray-500">+5 from yesterday</p>
          </div>
          <div class="bg-purple-100 p-3 rounded-full">
            <i class="fas fa-paper-plane text-purple-600 text-2xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions Title -->
    <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>

    <!-- Tailwind Grid Buttons -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <!-- Routing Slip -->
      <a href="\UDHO%20SYSTEM\Admin\admin_panel.php" class="block">
        <div class="bg-white p-6 text-center rounded shadow-sm border transition-all duration-300 custom-card">
          <i class="fas fa-users icon-style mb-2"></i>
          <h6 class="mt-2 text-lg font-medium text-gray-800">Routing Slip</h6>
        </div>
      </a>

      <!-- Records -->
      <a href="\UDHO%20SYSTEM\Admin\admin_records.php" class="block">
        <div class="bg-white p-6 text-center rounded shadow-sm border transition-all duration-300 custom-card">
          <i class="fas fa-scale-balanced icon-style mb-2"></i>
          <h6 class="mt-2 text-lg font-medium text-gray-800">Records</h6>
        </div>
      </a>
    </div>
    
    <!-- Recent Activity Section -->
    <div class="mt-10 bg-white rounded-lg shadow p-6">
      <h2 class="text-xl font-semibold mb-4">Recent Activity</h2>
      <div class="space-y-4">
        <div class="flex items-start">
          <div class="bg-blue-100 p-2 rounded-full mr-4">
            <i class="fas fa-file-import text-blue-600"></i>
          </div>
          <div>
            <p class="font-medium">New routing slip created</p>
            <p class="text-sm text-gray-500">RS-2023-00142 - Just now</p>
          </div>
        </div>
        <div class="flex items-start">
          <div class="bg-green-100 p-2 rounded-full mr-4">
            <i class="fas fa-check-circle text-green-600"></i>
          </div>
          <div>
            <p class="font-medium">Routing slip completed</p>
            <p class="text-sm text-gray-500">RS-2023-00141 - 30 minutes ago</p>
          </div>
        </div>
        <div class="flex items-start">
          <div class="bg-yellow-100 p-2 rounded-full mr-4">
            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
          </div>
          <div>
            <p class="font-medium">Pending action required</p>
            <p class="text-sm text-gray-500">RS-2023-00140 - 2 hours ago</p>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>