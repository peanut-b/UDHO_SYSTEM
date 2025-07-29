<?php
session_start();
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "udho_db";

// Create connection with error reporting
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . ". Please check your database credentials and make sure the database exists.");
}

// Function to get latest routing number with error handling
function getLatestRoutingNumber($conn) {
    $query = "SELECT control_no, created_at FROM routing_slips ORDER BY created_at DESC LIMIT 1";
    $result = $conn->query($query);
    
    if (!$result) {
        error_log("Database error: " . $conn->error);
        return [
            'number' => 'UDHO-'.date('Y').'-0000',
            'date' => date('M d, Y h:i A')
        ];
    }
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return [
            'number' => $row['control_no'],
            'date' => date('M d, Y h:i A', strtotime($row['created_at']))
        ];
    }
    return [
        'number' => 'UDHO-'.date('Y').'-0000',
        'date' => date('M d, Y h:i A')
    ];
}

function getIncomingPapersToday($conn) {
    $today = date('Y-m-d');
    $query = "SELECT COUNT(*) as count FROM routing_slips 
              WHERE DATE(created_at) = '$today' 
              AND direction = 'Incoming'";
    $result = $conn->query($query);
    
    if (!$result) {
        error_log("Database error: " . $conn->error);
        return 0;
    }
    
    $row = $result->fetch_assoc();
    return $row['count'];
}

function getOutgoingPapersToday($conn) {
    $today = date('Y-m-d');
    $query = "SELECT COUNT(*) as count FROM routing_slips 
              WHERE DATE(created_at) = '$today' 
              AND direction = 'Outgoing'";
    $result = $conn->query($query);
    
    if (!$result) {
        error_log("Database error: " . $conn->error);
        return 0;
    }
    
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Helper function for date comparisons
function getCountForDate($conn, $direction, $date) {
    $query = "SELECT COUNT(*) as count FROM routing_slips 
              WHERE DATE(created_at) = '$date' 
              AND direction = '$direction'";
    $result = $conn->query($query);
    
    if (!$result) {
        error_log("Database error: " . $conn->error);
        return 0;
    }
    
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Get data from database
try {
    $latestRouting = getLatestRoutingNumber($conn);
    $incomingToday = getIncomingPapersToday($conn);
    $outgoingToday = getOutgoingPapersToday($conn);

    // Calculate differences from yesterday
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $incomingYesterday = getCountForDate($conn, 'Incoming', $yesterday);
    $outgoingYesterday = getCountForDate($conn, 'Outgoing', $yesterday);

    $incomingDiff = $incomingToday - $incomingYesterday;
    $outgoingDiff = $outgoingToday - $outgoingYesterday;
} catch (Exception $e) {
    error_log("Error fetching dashboard data: " . $e->getMessage());
    // Set default values if there's an error
    $latestRouting = ['number' => 'UDHO-'.date('Y').'-0000', 'date' => date('M d, Y h:i A')];
    $incomingToday = 0;
    $outgoingToday = 0;
    $incomingDiff = 0;
    $outgoingDiff = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Operation Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
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
    .main-content {
      flex: 1;
      padding: 2.5rem;
    }
    .metrics-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }
  </style>
</head>

<body class="bg-gray-100 min-h-screen flex">
  <!-- Sidebar -->
  <div class="w-64 bg-gray-800 text-white flex flex-col">
    <div class="flex items-center justify-center h-24">
      <div class="rounded-full bg-gray-200 w-20 h-20 flex items-center justify-center overflow-hidden border-2 border-white shadow-md">
        <img src="/UDHO%20SYSTEM/assets/PROFILE_SAMPLE.jpg" 
             alt="Profile Picture" 
             class="w-full h-full object-cover">
      </div>
    </div>
    <nav class="mt-6">
      <ul>
        <li>
          <a href="/UDHO%20SYSTEM/Admin/admin_dashboard.php" class="block py-2.5 px-4 hover:bg-gray-700 flex items-center">
            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
          </a>
        </li>
        <li>
          <a href="/UDHO%20SYSTEM/Settings/setting_admin.php" class="block py-2.5 px-4 hover:bg-gray-700 flex items-center">
            <i class="fas fa-cog mr-3"></i> Settings
          </a>
        </li>
        <li>
          <a href="/UDHO%20SYSTEM/logout.php" class="block py-2.5 px-4 hover:bg-gray-700 mt-10 flex items-center">
            <i class="fas fa-sign-out-alt mr-3"></i> Logout
          </a>
        </li>
      </ul>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <header class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Operation Dashboard</h1>
      <div class="flex items-center gap-2">
        <img src="/UDHO%20SYSTEM/assets/UDHOLOGO.png" alt="Logo" class="h-8">
        <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
      </div>
    </header>

    <!-- Dashboard Metrics -->
    <div class="metrics-grid">
      <!-- Latest Routing Number -->
      <div class="bg-white rounded-lg shadow p-6 metric-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">Latest Routing Number</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo htmlspecialchars($latestRouting['number']); ?></p>
            <p class="mt-2 text-sm text-gray-500">Created: <?php echo htmlspecialchars($latestRouting['date']); ?></p>
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
            <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo $incomingToday; ?></p>
            <p class="mt-2 text-sm <?php echo $incomingDiff > 0 ? 'text-green-600' : ($incomingDiff < 0 ? 'text-red-600' : 'text-gray-500'); ?>">
              <?php 
              if ($incomingDiff > 0) {
                  echo "<i class='fas fa-arrow-up'></i> +$incomingDiff from yesterday";
              } elseif ($incomingDiff < 0) {
                  echo "<i class='fas fa-arrow-down'></i> $incomingDiff from yesterday";
              } else {
                  echo "Same as yesterday";
              }
              ?>
            </p>
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
            <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo $outgoingToday; ?></p>
            <p class="mt-2 text-sm <?php echo $outgoingDiff > 0 ? 'text-green-600' : ($outgoingDiff < 0 ? 'text-red-600' : 'text-gray-500'); ?>">
              <?php 
              if ($outgoingDiff > 0) {
                  echo "<i class='fas fa-arrow-up'></i> +$outgoingDiff from yesterday";
              } elseif ($outgoingDiff < 0) {
                  echo "<i class='fas fa-arrow-down'></i> $outgoingDiff from yesterday";
              } else {
                  echo "Same as yesterday";
              }
              ?>
            </p>
          </div>
          <div class="bg-purple-100 p-3 rounded-full">
            <i class="fas fa-paper-plane text-purple-600 text-2xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-8">
      <a href="/UDHO%20SYSTEM/Admin/admin_panel.php" class="block">
        <div class="bg-white p-6 text-center rounded shadow-sm border transition-all duration-300 custom-card">
          <i class="fas fa-file-alt icon-style mb-2"></i>
          <h6 class="mt-2 text-lg font-medium text-gray-800">Routing Slip</h6>
        </div>
      </a>
      <a href="/UDHO%20SYSTEM/Admin/admin_records.php" class="block">
        <div class="bg-white p-6 text-center rounded shadow-sm border transition-all duration-300 custom-card">
          <i class="fas fa-archive icon-style mb-2"></i>
          <h6 class="mt-2 text-lg font-medium text-gray-800">Records</h6>
        </div>
      </a>
    </div>
  </div>

</body>
</html>
<?php
$conn->close();
?>