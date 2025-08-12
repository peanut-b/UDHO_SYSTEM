


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HOA Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .card {
      transition: all 0.3s ease;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
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
    .status-inactive {
      background-color: #f3f4f6;
      color: #6b7280;
    }
    .status-abolished {
      background-color: #fee2e2;
      color: #b91c1c;
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
          <a href="hoa_dashboard.php" class="sidebar-link flex items-center py-3 px-4 active-link">
            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
          </a>
        </li>
        <li>
          <a href="hoa_records.php" class="sidebar-link flex items-center py-3 px-4">
            <i class="fas fa-home mr-3"></i> HOA Management
          </a>
        </li>
        <li>
          <a href="hoa_payment.php" class="sidebar-link flex items-center py-3 px-4 active-link">
            <i class="fas fa-money-bill-wave mr-3"></i> Payment Records
          </a>
        </li>
        <li>
          <a href="/Settings/setting_hoa.php" class="sidebar-link flex items-center py-3 px-4">
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
      <h1 class="text-2xl font-bold text-gray-800">HOA Dashboard Overview</h1>
      <div class="flex items-center gap-2">
        <img src="/assets/UDHOLOGO.png" alt="Logo" class="h-8">
        <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
      </div>
    </header>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <!-- Active HOAs Card -->
      <div class="card bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm font-medium text-gray-500">Active HOAs</p>
            <p class="text-3xl font-bold mt-2">12</p>
            <p class="text-sm text-green-600 mt-1">
              <span class="font-medium">+2</span> from last month
            </p>
          </div>
          <div class="p-3 rounded-full bg-green-100 text-green-600">
            <i class="fas fa-check-circle text-xl"></i>
          </div>
        </div>
        <div class="mt-4">
          <a href="hoa_records.php?status=active" class="text-blue-600 text-sm font-medium hover:text-blue-800">
            View all active HOAs <i class="fas fa-arrow-right ml-1"></i>
          </a>
        </div>
      </div>

      <!-- Inactive HOAs Card -->
      <div class="card bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm font-medium text-gray-500">Inactive HOAs</p>
            <p class="text-3xl font-bold mt-2">3</p>
            <p class="text-sm text-yellow-600 mt-1">
              <span class="font-medium">No change</span> from last month
            </p>
          </div>
          <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
            <i class="fas fa-exclamation-circle text-xl"></i>
          </div>
        </div>
        <div class="mt-4">
          <a href="hoa_records.php?status=inactive" class="text-blue-600 text-sm font-medium hover:text-blue-800">
            View all inactive HOAs <i class="fas fa-arrow-right ml-1"></i>
          </a>
        </div>
      </div>

      <!-- Abolished HOAs Card -->
      <div class="card bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm font-medium text-gray-500">Abolished HOAs</p>
            <p class="text-3xl font-bold mt-2">2</p>
            <p class="text-sm text-red-600 mt-1">
              <span class="font-medium">+1</span> from last month
            </p>
          </div>
          <div class="p-3 rounded-full bg-red-100 text-red-600">
            <i class="fas fa-times-circle text-xl"></i>
          </div>
        </div>
        <div class="mt-4">
          <a href="hoa_management.html?status=abolished" class="text-blue-600 text-sm font-medium hover:text-blue-800">
            View all abolished HOAs <i class="fas fa-arrow-right ml-1"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="bg-white p-6 rounded-lg shadow mb-8">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Recent HOA Activities</h2>
        <a href="hoa_management.html" class="text-blue-600 text-sm font-medium hover:text-blue-800">
          View all activities <i class="fas fa-arrow-right ml-1"></i>
        </a>
      </div>
      
      <div class="space-y-4">
        <!-- Activity Item 1 -->
        <div class="flex items-start pb-4 border-b border-gray-100">
          <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-4">
            <i class="fas fa-home"></i>
          </div>
          <div class="flex-1">
            <p class="font-medium">New HOA registered: <span class="text-blue-600">Sunrise Village Association</span></p>
            <p class="text-sm text-gray-500 mt-1">Barangay 2 • Registered on July 15, 2023</p>
          </div>
          <span class="status-badge status-active ml-4">Active</span>
        </div>
        
        <!-- Activity Item 2 -->
        <div class="flex items-start pb-4 border-b border-gray-100">
          <div class="p-2 rounded-full bg-yellow-100 text-yellow-600 mr-4">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="flex-1">
            <p class="font-medium">HOA status changed: <span class="text-blue-600">Pine Crest Community</span></p>
            <p class="text-sm text-gray-500 mt-1">Changed from Active to Inactive on July 10, 2023</p>
          </div>
          <span class="status-badge status-inactive ml-4">Inactive</span>
        </div>
        
        <!-- Activity Item 3 -->
        <div class="flex items-start">
          <div class="p-2 rounded-full bg-red-100 text-red-600 mr-4">
            <i class="fas fa-trash-alt"></i>
          </div>
          <div class="flex-1">
            <p class="font-medium">HOA abolished: <span class="text-blue-600">Old Town Residents</span></p>
            <p class="text-sm text-gray-500 mt-1">Barangay 7 • Abolished on June 28, 2023</p>
          </div>
          <span class="status-badge status-abolished ml-4">Abolished</span>
        </div>
      </div>
    </div>

    <!-- Quick Links Section -->
    <div class="bg-white p-6 rounded-lg shadow">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="hoa_management.html?action=add" class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
              <i class="fas fa-plus"></i>
            </div>
            <div>
              <p class="font-medium">Register New HOA</p>
              <p class="text-sm text-gray-500">Add a new homeowners association</p>
            </div>
          </div>
        </a>
        
        <a href="hoa_management.html?filter=recent" class="p-4 border border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
              <i class="fas fa-history"></i>
            </div>
            <div>
              <p class="font-medium">View Recent Updates</p>
              <p class="text-sm text-gray-500">See recently modified HOAs</p>
            </div>
          </div>
        </a>
        
        <a href="Backup_data/backup.php" class="p-4 border border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
              <i class="fas fa-database"></i>
            </div>
            <div>
              <p class="font-medium">Backup HOA Data</p>
              <p class="text-sm text-gray-500">Create a backup of all HOA records</p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>

<script>
          // Function to filter HOA records by status
          function filterHOA(status) {
            // Get all HOA record elements
            const hoaRecords = document.querySelectorAll('.hoa-record');
            
            // Loop through all records
            hoaRecords.forEach(record => {
              // Get the status of the current record
              const recordStatus = record.getAttribute('data-status');
              
              // Show or hide based on the selected filter
              if (status === 'all' || recordStatus === status) {
                record.style.display = 'block';
              } else {
                record.style.display = 'none';
              }
            });
            
            // Update active button styling
            document.querySelectorAll('.filter-btn').forEach(btn => {
              if (btn.getAttribute('data-filter') === status) {
                btn.classList.add('bg-blue-500', 'text-white');
                btn.classList.remove('bg-gray-200', 'text-gray-700');
              } else {
                btn.classList.remove('bg-blue-500', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
              }
            });
          }

          // Check URL parameters on page load to apply initial filter
          document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const statusParam = urlParams.get('status');
            
            if (statusParam) {
              filterHOA(statusParam);
            } else {
              // Default to showing all if no filter specified
              filterHOA('all');
            }
          });
          
         
// Nuclear option for back button
history.pushState(null, null, location.href);
window.onpopstate = function() {
    history.go(1);
};

</script>
</body>
</html>