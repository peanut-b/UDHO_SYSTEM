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

// Get current user data from session
$current_user_id = $_SESSION['user_id'] ?? null;
$current_user_role = $_SESSION['role'] ?? '';

// Initialize messages
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);

// Handle employee details view
if (isset($_GET['view_employee'])) {
    $employee_id = intval($_GET['view_employee']);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee_details = $result->fetch_assoc();
    
    if (!$employee_details) {
        $_SESSION['error'] = "Employee not found";
        header("Location: employee.php");
        exit();
    }
    
    // Display employee details page
    displayEmployeeDetails($employee_details);
    exit();
}

// Fetch all employees from database
$employees = [];
$password_requests = 0;

$query = "SELECT id, username, password, role, created_at, profile_picture, email, phone, password_changed_at 
          FROM users 
          WHERE role IN ('Admin', 'Operation', 'Admin Executive', 'HOA', 'Enumerator')
          ORDER BY created_at DESC";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
        
        // Count password reset requests (users who haven't changed their password yet)
        if ($row['password_changed_at'] === null) {
            $password_requests++;
        }
    }
}

// Count active/inactive employees
$active_employees = count($employees);
$inactive_employees = 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Reset password
    if (isset($_POST['reset_password'])) {
        $employee_id = $_POST['employee_id'];
        
        // Verify this user actually needs a password reset
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND password_changed_at IS NULL");
        $check_stmt->bind_param("i", $employee_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $new_password = bin2hex(random_bytes(4)); // Temporary password (plain text)
            
            $stmt = $conn->prepare("UPDATE users SET password = ?, password_changed_at = NOW() WHERE id = ?");
            $stmt->bind_param("si", $new_password, $employee_id);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = "Password reset successfully for user ID $employee_id. New password: $new_password";
            } else {
                $_SESSION['error'] = "Error resetting password: " . $conn->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "This employee has already set their password. They must request a reset.";
        }
        $check_stmt->close();
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
    
    // Add new employee
    if (isset($_POST['add_employee'])) {
        $id = intval($_POST['id']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']); // Plain text password
        $role = trim($_POST['role']);
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $profile_picture = 'PROFILE_SAMPLE.jpg';
        
        // Validate inputs
        $errors = [];
        
        if (empty($id) || $id <= 0) {
            $errors[] = "Valid Employee ID is required";
        }
        
        if (empty($username)) {
            $errors[] = "Username is required";
        }
        
        if (empty($password) || strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters";
        }
        
        if (empty($role)) {
            $errors[] = "Role is required";
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode("<br>", $errors);
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }
        
        // Check if ID exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $_SESSION['error'] = "Employee ID already exists. Please choose a different ID.";
            $check_stmt->close();
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }
        $check_stmt->close();
        
        // Insert new employee with plain text password
        $stmt = $conn->prepare("INSERT INTO users (id, username, password, role, email, phone, profile_picture, created_at, password_changed_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("issssss", $id, $username, $password, $role, $email, $phone, $profile_picture);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Employee added successfully! Password: $password";
        } else {
            $_SESSION['error'] = "Error adding employee: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
    
    // Update employee
    if (isset($_POST['update_employee'])) {
        $id = intval($_POST['id']);
        $username = trim($_POST['username']);
        $role = trim($_POST['role']);
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        if (empty($username) || empty($role)) {
            $_SESSION['error'] = "Username and role are required fields.";
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }
        
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $username, $role, $email, $phone, $id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Employee updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating employee: " . $conn->error;
        }
        $stmt->close();
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

// Handle AJAX request to get employee info
if (isset($_GET['get_employee'])) {
    $id = intval($_GET['get_employee']);

    $stmt = $conn->prepare("SELECT id, username, role, email, phone FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc());
    exit();
}

$conn->close();

// Function to display employee details page
function displayEmployeeDetails($employee) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Employee Details</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <style>
            .profile-picture {
                width: 150px;
                height: 150px;
                border-radius: 50%;
                object-fit: cover;
                border: 4px solid #ddd;
            }
            .detail-card {
                background-color: white;
                border-radius: 0.5rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                padding: 1.5rem;
                margin-bottom: 1rem;
            }
        </style>
    </head>
    <body class="bg-gray-100">
        <div class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Employee Details</h1>
                <a href="employee.php" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Employees
                </a>
            </div>
            
            <div class="max-w-4xl mx-auto">
                <div class="flex flex-col md:flex-row gap-8">
                    <!-- Profile Picture and Basic Info -->
                    <div class="w-full md:w-1/3">
                        <div class="detail-card text-center">
                            <img src="assets/<?php echo htmlspecialchars($employee['profile_picture']); ?>" 
                                 alt="Profile Picture" 
                                 class="profile-picture mx-auto mb-4"
                                 onerror="this.src='assets/PROFILE_SAMPLE.jpg'">
                            <h2 class="text-2xl font-semibold"><?php echo htmlspecialchars($employee['username']); ?></h2>
                            <p class="text-gray-600"><?php echo htmlspecialchars($employee['role']); ?></p>
                        </div>
                    </div>
                    
                    <!-- Detailed Information -->
                    <div class="w-full md:w-2/3">
                        <div class="detail-card">
                            <h3 class="text-xl font-semibold mb-4">Personal Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-600">Employee ID</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($employee['id']); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Username</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($employee['username']); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Role</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($employee['role']); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Account Created</p>
                                    <p class="font-medium"><?php echo date('M d, Y h:i A', strtotime($employee['created_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-card">
                            <h3 class="text-xl font-semibold mb-4">Contact Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-600">Email</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($employee['email'] ?? 'Not provided'); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Phone</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($employee['phone'] ?? 'Not provided'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-card">
                            <h3 class="text-xl font-semibold mb-4">Account Status</h3>
                            <div class="flex items-center">
                                <?php if ($employee['password_changed_at'] === null): ?>
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm mr-2">Initial Setup Required</span>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="employee_id" value="<?php echo $employee['id']; ?>">
                                        <button type="submit" name="reset_password" class="text-blue-600 hover:text-blue-800 text-sm">
                                            <i class="fas fa-key mr-1"></i> Set Password
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Active</span>
                                    <span class="text-gray-600 text-sm ml-2">Password last changed: <?php echo date('M d, Y', strtotime($employee['password_changed_at'])); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Main page display
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Employee Management</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
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
    .profile-picture {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #ddd;
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 50;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.4);
    }
    .modal-content {
      background-color: #fefefe;
      margin: 5% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 800px;
      border-radius: 8px;
    }
    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1000;
      animation: slideIn 0.5s, fadeOut 0.5s 2.5s forwards;
    }
    @keyframes slideIn {
      from { transform: translateX(100%); }
      to { transform: translateX(0); }
    }
    @keyframes fadeOut {
      from { opacity: 1; }
      to { opacity: 0; }
    }
  </style>
</head>

<body class="bg-gray-100 min-h-screen flex">
  <!-- Sidebar -->
  <div class="w-64 bg-gray-800 text-white flex flex-col">
    <div class="flex items-center justify-center h-24">
      <div class="rounded-full bg-gray-200 w-20 h-20 flex items-center justify-center overflow-hidden border-2 border-white shadow-md">
        <?php
        $profilePicture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'PROFILE_SAMPLE.jpg';
        ?>
        <img src="assets/<?php echo htmlspecialchars($profilePicture); ?>" 
            alt="Profile Picture" 
            class="w-full h-full object-cover"
            onerror="this.src='assets/PROFILE_SAMPLE.jpg'">
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
          <a href="logout.php" class="sidebar-link flex items-center py-3 px-4 mt-10">
            <i class="fas fa-sign-out-alt mr-3"></i> Logout
          </a>
        </li>
      </ul>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="flex-1 p-6 overflow-auto">
    <!-- Notification messages -->
    <?php if ($message): ?>
      <div class="notification">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
          <span class="block sm:inline"><?php echo $message; ?></span>
          <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
              <title>Close</title>
              <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
          </span>
        </div>
      </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
      <div class="notification">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
          <span class="block sm:inline"><?php echo $error; ?></span>
          <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
              <title>Close</title>
              <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
          </span>
        </div>
      </div>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
      <h1 class="text-2xl font-bold text-gray-800">Employee Management</h1>
      <div class="flex items-center gap-2">
        <img src="assets/UDHOLOGO.png" alt="Logo" class="h-8">
        <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
      </div>
    </div>

    <!-- Notification and Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <!-- Password Reset Requests -->
      <div class="bg-white p-6 rounded-lg shadow-md custom-card relative">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-gray-500">Initial Password Setup</p>
            <h3 class="text-2xl font-bold"><?php echo $password_requests; ?></h3>
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
        <?php if ($password_requests > 0): ?>
          <span class="badge badge-danger absolute -top-2 -right-2"><?php echo $password_requests; ?> New</span>
        <?php endif; ?>
      </div>
      
      <!-- Active Employees -->
      <div class="bg-white p-6 rounded-lg shadow-md custom-card">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-gray-500">Active Employees</p>
            <h3 class="text-2xl font-bold"><?php echo $active_employees; ?></h3>
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
            <h3 class="text-2xl font-bold"><?php echo $inactive_employees; ?></h3>
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
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profile</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200" id="employeeTableBody">
            <?php foreach ($employees as $employee): ?>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($employee['id']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <img src="assets/<?php echo htmlspecialchars($employee['profile_picture']); ?>" 
                       alt="Profile" 
                       class="profile-picture"
                       onerror="this.src='assets/PROFILE_SAMPLE.jpg'">
                </td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($employee['username']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($employee['role']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo date('M d, Y h:i A', strtotime($employee['created_at'])); ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <?php if ($employee['password_changed_at'] === null): ?>
                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Initial Setup</span>
                  <?php else: ?>
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Active</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                  <button onclick="viewEmployeeDetails(<?php echo $employee['id']; ?>)" class="text-blue-600 hover:text-blue-900" title="View">
                    <i class="fas fa-eye"></i>
                  </button>
                  
                  <button onclick="editEmployee(<?php echo $employee['id']; ?>)" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                    <i class="fas fa-edit"></i>
                  </button>
                  
                  <?php if ($employee['password_changed_at'] === null): ?>
                    <form method="POST" style="display: inline;">
                      <input type="hidden" name="employee_id" value="<?php echo $employee['id']; ?>">
                      <button type="submit" name="reset_password" class="text-blue-600 hover:text-blue-900" title="Set Initial Password">
                        <i class="fas fa-key"></i>
                      </button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="flex justify-between items-center mt-4">
        <div class="text-sm text-gray-500">
          Showing <span class="font-medium">1</span> to <span class="font-medium"><?php echo count($employees); ?></span> of <span class="font-medium"><?php echo count($employees); ?></span> employees
        </div>
        <div class="flex gap-1">
          <button class="px-3 py-1 bg-gray-200 rounded-md">Previous</button>
          <button class="px-3 py-1 bg-blue-600 text-white rounded-md">1</button>
          <button class="px-3 py-1 bg-gray-200 rounded-md">Next</button>
        </div>
      </div>
    </div>

    <!-- Password Reset Requests Modal -->
    <div id="passwordRequestsModal" class="modal">
      <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold">Initial Password Setup (<?php echo $password_requests; ?>)</h3>
          <button onclick="closeModal('passwordRequestsModal')" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="overflow-y-auto max-h-96">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profile</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php foreach ($employees as $employee): ?>
                <?php if ($employee['password_changed_at'] === null): ?>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($employee['id']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <img src="assets/<?php echo htmlspecialchars($employee['profile_picture']); ?>" 
                           alt="Profile" 
                           class="profile-picture"
                           onerror="this.src='assets/PROFILE_SAMPLE.jpg'">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($employee['username']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($employee['role']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Initial Setup</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <form method="POST" style="display: inline;">
                        <input type="hidden" name="employee_id" value="<?php echo $employee['id']; ?>">
                        <button type="submit" name="reset_password" class="text-blue-600 hover:text-blue-900 mr-2" title="Set Initial Password">
                          <i class="fas fa-key"></i> Set Password
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="mt-4 flex justify-end">
          <button onclick="closeModal('passwordRequestsModal')" class="px-4 py-2 bg-gray-200 rounded-md mr-2">Close</button>
        </div>
      </div>
    </div>

    <!-- Add Employee Modal -->
    <div id="addEmployeeModal" class="modal">
      <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold">Add New Employee</h3>
          <button onclick="closeModal('addEmployeeModal')" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="addEmployeeForm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Employee ID *</label>
              <input type="number" name="id" required min="1" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
              <p class="text-xs text-gray-500 mt-1">Must be a positive number</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Username *</label>
              <input type="text" name="username" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Initial Password *</label>
              <input type="text" name="password" required minlength="6" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '' ?>">
              <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Role *</label>
              <select name="role" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select Role</option>
                <option value="Admin">Admin</option>
                <option value="Operation">Operation</option>
                <option value="Admin Executive">Admin Executive</option>
                <option value="HOA">HOA</option>
                <option value="Enumerator">Enumerator</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input type="email" name="email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Phone</label>
              <input type="text" name="phone" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
          </div>
          
          <div class="mt-6">
            <p class="text-sm text-gray-500">* Required fields</p>
          </div>
          
          <div class="mt-6 flex justify-end">
            <button type="button" onclick="closeModal('addEmployeeModal')" class="px-4 py-2 bg-gray-200 rounded-md mr-2">Cancel</button>
            <button type="submit" name="add_employee" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Add Employee</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Employee Modal -->
    <div id="editEmployeeModal" class="modal">
      <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold">Edit Employee</h3>
          <button onclick="closeModal('editEmployeeModal')" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="editEmployeeForm">
          <input type="hidden" name="id" id="edit_id">
          <input type="hidden" name="update_employee" value="1">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Username *</label>
              <input type="text" name="username" id="edit_username" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Role *</label>
              <select name="role" id="edit_role" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select Role</option>
                <option value="Admin">Admin</option>
                <option value="Operation">Operation</option>
                <option value="Admin Executive">Admin Executive</option>
                <option value="HOA">HOA</option>
                <option value="Enumerator">Enumerator</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input type="email" name="email" id="edit_email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Phone</label>
              <input type="text" name="phone" id="edit_phone" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
          </div>
          
          <div class="mt-6 flex justify-end">
            <button type="button" onclick="closeModal('editEmployeeModal')" class="px-4 py-2 bg-gray-200 rounded-md mr-2">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Changes</button>
          </div>
        </form>
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
      document.getElementById('passwordRequestsModal').style.display = 'block';
    }

    function closeModal(modalId) {
      document.getElementById(modalId).style.display = 'none';
    }

    function viewEmployeeDetails(employeeId) {
      window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>?view_employee=" + employeeId;
    }

    function editEmployee(employeeId) {
      fetch(`<?php echo $_SERVER['PHP_SELF']; ?>?get_employee=${employeeId}`)
        .then(response => response.json())
        .then(data => {
          if (data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_username').value = data.username;
            document.getElementById('edit_role').value = data.role;
            document.getElementById('edit_email').value = data.email || '';
            document.getElementById('edit_phone').value = data.phone || '';
            document.getElementById('editEmployeeModal').style.display = 'block';
          } else {
            alert('Employee not found');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error loading employee data');
        });
    }

    function exportEmployeeData() {
      console.log('Exporting employee data');
      alert('Employee data export initiated. You will receive an email with the download link.');
    }

    function showAddEmployeeModal() {
      // Reset the form before showing
      document.getElementById('addEmployeeForm').reset();
      document.getElementById('addEmployeeModal').style.display = 'block';
    }

    // Add form validation
    document.getElementById('addEmployeeForm').addEventListener('submit', function(e) {
        const password = document.querySelector('input[name="password"]').value;
        
        if (password.length < 6) {
            alert('Password must be at least 6 characters long');
            e.preventDefault();
            return false;
        }
        
        return true;
    });

    // Close modal when clicking outside of it
    window.onclick = function(event) {
      if (event.target.className === 'modal') {
        event.target.style.display = 'none';
      }
    }

    // Auto-hide notifications after 3 seconds
    setTimeout(() => {
      const notifications = document.querySelectorAll('.notification');
      notifications.forEach(notification => {
        if (notification) {
          notification.style.display = 'none';
        }
      });
    }, 3000);
  </script>
</body>
</html>