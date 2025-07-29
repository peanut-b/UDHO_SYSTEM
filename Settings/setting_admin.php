<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "udho_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /UDHO%20SYSTEM/index.php');
    exit();
}

// Get current user data
$user_id = $_SESSION['user_id'];
$user_data = [];
$users_list = [];

// Fetch current user data with proper error handling
$stmt = $conn->prepare("SELECT id, username, password, role, created_at, profile_picture, email, phone FROM users WHERE id = ?");
if (!$stmt) {
    $_SESSION['error_message'] = "Database error: " . $conn->error;
} else {
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        $_SESSION['error_message'] = "Error executing query: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc() ?? [];
    }
    $stmt->close();
}

// Set default values
$user_data['role'] = $user_data['role'] ?? 'User';
$user_data['profile_picture'] = $user_data['profile_picture'] ?? 'default_profile.jpg';
$user_data['email'] = $user_data['email'] ?? '';
$user_data['phone'] = $user_data['phone'] ?? '';

// For admin users, fetch all users
if ($user_data['role'] === 'Admin') {
    $result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
    if (!$result) {
        $_SESSION['error_message'] = "Error fetching users: " . $conn->error;
    } else {
        while ($row = $result->fetch_assoc()) {
            $users_list[] = $row;
        }
    }
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update profile
    if (isset($_POST['update_profile'])) {
        $username = $conn->real_escape_string($_POST['username'] ?? '');
        $email = $conn->real_escape_string($_POST['email'] ?? '');
        $phone = $conn->real_escape_string($_POST['phone'] ?? '');
        $role = $conn->real_escape_string($_POST['role'] ?? $user_data['role']);
        
        // Only allow role change if admin
        if ($user_data['role'] !== 'Admin') {
            $role = $user_data['role'];
        }
        
        // Handle profile picture upload
        $profile_picture = $user_data['profile_picture'];
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
            $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/UDHO SYSTEM/assets/profile_pictures/";
            
            // Create directory if it doesn't exist
            if (!file_exists($target_dir)) {
                if (!mkdir($target_dir, 0755, true)) {
                    $_SESSION['error_message'] = "Failed to create directory for profile pictures";
                    header("Location: ".$_SERVER['PHP_SELF']);
                    exit();
                }
            }
            
            // Validate file type and size
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            $file_info = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($file_info, $_FILES['profile_picture']['tmp_name']);
            finfo_close($file_info);
            
            if (!in_array($mime_type, $allowed_types)) {
                $_SESSION['error_message'] = "Only JPG, PNG, and GIF files are allowed";
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            }
            
            if ($_FILES['profile_picture']['size'] > $max_size) {
                $_SESSION['error_message'] = "File size must be less than 2MB";
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            }
            
            $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                // Delete old profile picture if it's not the default
                if ($profile_picture !== 'default_profile.jpg' && file_exists($target_dir . $profile_picture)) {
                    @unlink($target_dir . $profile_picture);
                }
                $profile_picture = $new_filename;
            } else {
                $_SESSION['error_message'] = "Error uploading file";
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            }
        }
        
        $stmt = $conn->prepare("UPDATE users SET 
                username = ?, 
                email = ?, 
                phone = ?, 
                role = ?,
                profile_picture = ?
                WHERE id = ?");
        
        if (!$stmt) {
            $_SESSION['error_message'] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("sssssi", $username, $email, $phone, $role, $profile_picture, $user_id);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Profile updated successfully!";
                // Refresh user data
                $refresh_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $refresh_stmt->bind_param("i", $user_id);
                $refresh_stmt->execute();
                $result = $refresh_stmt->get_result();
                $user_data = $result->fetch_assoc() ?? [];
                $refresh_stmt->close();
            } else {
                $_SESSION['error_message'] = "Error updating profile: " . $stmt->error;
            }
            $stmt->close();
        }
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
    
    
    // Password change request
    if (isset($_POST['request_password_change'])) {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $change_reason = $conn->real_escape_string($_POST['change_reason'] ?? '');
        
        // Verify current password
        if (!password_verify($current_password, $user_data['password'])) {
            $_SESSION['error_message'] = "Current password is incorrect";
        } elseif ($new_password !== $confirm_password) {
            $_SESSION['error_message'] = "New passwords do not match";
        } elseif (strlen($new_password) < 8) {
            $_SESSION['error_message'] = "Password must be at least 8 characters";
        } else {
            $stmt = $conn->prepare("INSERT INTO password_change_requests (user_id, new_password_hash, reason, requested_at, status) 
                                   VALUES (?, ?, ?, NOW(), 'pending')");
            
            if (!$stmt) {
                $_SESSION['error_message'] = "Database error: " . $conn->error;
            } else {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt->bind_param("iss", $user_id, $new_password_hash, $change_reason);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Password change request submitted!";
                } else {
                    $_SESSION['error_message'] = "Error submitting request: " . $stmt->error;
                }
                $stmt->close();
            }
        }
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
    
    // User management actions (admin only)
    if (isset($_POST['user_action']) && $user_data['role'] === 'Admin') {
        $action = $_POST['user_action'];
        $target_user_id = $_POST['user_id'];
        
        try {
            switch ($action) {
                case 'edit':
                    // Handle edit action
                    break;
                    
                case 'copy':
                    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->bind_param("i", $target_user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user_to_copy = $result->fetch_assoc();
                    $stmt->close();
                    
                    if ($user_to_copy) {
                        $new_username = $user_to_copy['username'] . '_copy';
                        $new_email = str_replace('@', '_copy@', $user_to_copy['email']);
                        
                        $stmt = $conn->prepare("INSERT INTO users (username, password, role, email, phone, profile_picture) 
                                              VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssssss", 
                            $new_username,
                            $user_to_copy['password'],
                            $user_to_copy['role'],
                            $new_email,
                            $user_to_copy['phone'],
                            $user_to_copy['profile_picture']
                        );
                        
                        if ($stmt->execute()) {
                            $_SESSION['success_message'] = "User copied successfully!";
                            $result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
                            $users_list = [];
                            while ($row = $result->fetch_assoc()) {
                                $users_list[] = $row;
                            }
                        } else {
                            throw new Exception("Error copying user: " . $conn->error);
                        }
                        $stmt->close();
                    }
                    break;
                    
                case 'delete':
                    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->bind_param("i", $target_user_id);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = "User deleted successfully!";
                        $result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
                        $users_list = [];
                        while ($row = $result->fetch_assoc()) {
                            $users_list[] = $row;
                        }
                    } else {
                        throw new Exception("Error deleting user: " . $conn->error);
                    }
                    $stmt->close();
                    break;
            }
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Operation Panel - Settings</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    .scrollable-content::-webkit-scrollbar {
      width: 8px;
    }
    .scrollable-content::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }
    .scrollable-content::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 4px;
    }
    .scrollable-content::-webkit-scrollbar-thumb:hover {
      background: #555;
    }
    .profile-image-container {
      width: 120px;
      height: 120px;
      border: 3px solid #4f46e5;
    }
    input:focus, textarea:focus, select:focus {
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
    }
    select:disabled {
      background-color: #f3f4f6;
      color: #6b7280;
      cursor: not-allowed;
    }
    .sidebar-link {
      transition: all 0.2s ease;
    }
    .sidebar-link:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 100;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.4);
    }
    
    .modal-content {
      background-color: #fefefe;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 500px;
      border-radius: 8px;
      position: relative;
    }
    
    .close {
      color: #aaa;
      position: absolute;
      right: 15px;
      top: 10px;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }
    
    .close:hover {
      color: black;
    }
    
    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 4px;
    }
    
    .error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
    .success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
  </style>
</head>

<body class="bg-gray-100 min-h-screen flex">
 
    <!-- Error Modal -->
    <div id="errorModal" class="modal" style="<?php echo isset($_SESSION['error_message']) ? 'display:block;' : 'display:none;' ?>">
    <div class="modal-content">
      <span class="close" onclick="closeModal('errorModal')">&times;</span>
      <div class="flex items-center">
        <i class="fas fa-exclamation-circle text-red-500 text-2xl mr-3"></i>
        <h3 class="text-xl font-bold text-red-600">Error</h3>
      </div>
      <p class="mt-3"><?php echo $_SESSION['error_message'] ?? ''; ?></p>
      <div class="mt-4 text-right">
        <button onclick="closeModal('errorModal')" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">OK</button>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div id="successModal" class="modal" style="<?php echo isset($_SESSION['success_message']) ? 'display:block;' : 'display:none;' ?>">
    <div class="modal-content">
      <span class="close" onclick="closeModal('successModal')">&times;</span>
      <div class="flex items-center">
        <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
        <h3 class="text-xl font-bold text-green-600">Success</h3>
      </div>
      <p class="mt-3"><?php echo $_SESSION['success_message'] ?? ''; ?></p>
      <div class="mt-4 text-right">
        <button onclick="closeModal('successModal')" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">OK</button>
      </div>
    </div>
  </div>

  <!-- Sidebar -->
  <div class="w-64 bg-gray-800 text-white flex flex-col">
    <div class="flex items-center justify-center h-24">
      <div class="rounded-full bg-gray-200 w-20 h-20 flex items-center justify-center overflow-hidden border-2 border-white shadow-md">
        <img src="/UDHO%20SYSTEM/assets/profile_pictures/<?php echo htmlspecialchars($user_data['profile_picture']); ?>" 
             alt="Profile Picture" 
             class="w-full h-full object-cover"
             onerror="this.src='/UDHO%20SYSTEM/assets/PROFILE_SAMPLE.jpg'">
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
  <div class="flex-1 p-4 md:p-10">
    <header class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
      <h1 class="text-2xl font-bold text-gray-800">Account Settings</h1>
      <div class="flex items-center gap-2">
        <img src="/UDHO%20SYSTEM/assets/UDHOLOGO.png" alt="Logo" class="h-8">
        <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
      </div>
    </header>

    <div class="bg-white p-6 rounded-lg shadow-md">
      <div class="flex flex-col md:flex-row gap-8">
        <!-- Profile Picture Section -->
        <div class="flex flex-col items-center md:w-1/3">
          <div class="profile-image-container rounded-full overflow-hidden mb-4">
            <img id="profileImage" src="/UDHO SYSTEM/assets/profile_pictures/<?php echo htmlspecialchars($user_data['profile_picture']); ?>" 
                 alt="Profile" class="w-full h-full object-cover"
                 onerror="this.src='/UDHO SYSTEM/assets/PROFILE_SAMPLE.jpg'">
          </div>
          <form id="profileForm" method="POST" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="w-full">
            <input type="file" id="profileUpload" name="profile_picture" accept="image/*" class="hidden" />
            <button type="button" onclick="document.getElementById('profileUpload').click()" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition w-full">
              <i class="fas fa-camera mr-2"></i> Change Photo
            </button>
            <button type="submit" name="update_profile" class="hidden" id="profileSubmit"></button>
          </form>
          <p class="text-sm text-gray-500 mt-2">JPG, GIF or PNG. Max size 2MB</p>
        </div>
        
        <!-- Account Settings Form -->
        <div class="flex-1">
          <h2 class="text-xl font-semibold border-b pb-2 mb-4">Personal Information</h2>
          
          <form id="settingsForm" class="space-y-4" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block mb-1 font-medium">Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                       <?php echo ($user_data['role'] !== 'Admin') ? 'disabled' : ''; ?> />
              </div>
              
              <div>
                <label class="block mb-1 font-medium">Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                       required />
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block mb-1 font-medium">Phone Number</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user_data['phone']); ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                       required />
              </div>
              
              <div>
                <label class="block mb-1 font-medium">Designated Position</label>
                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                        <?php echo ($user_data['role'] !== 'Admin') ? 'disabled' : ''; ?>>
                  <option value="Admin" <?php echo ($user_data['role'] === 'Admin') ? 'selected' : ''; ?>>Admin</option>
                  <option value="Operation" <?php echo ($user_data['role'] === 'Operation') ? 'selected' : ''; ?>>Operation</option>
                  <option value="Admin Executive" <?php echo ($user_data['role'] === 'Admin Executive') ? 'selected' : ''; ?>>Admin Executive</option>
                  <option value="HOA" <?php echo ($user_data['role'] === 'HOA') ? 'selected' : ''; ?>>HOA</option>
                  <option value="Enumerator" <?php echo ($user_data['role'] === 'Enumerator') ? 'selected' : ''; ?>>Enumerator</option>
                </select>
                <?php if ($user_data['role'] !== 'Admin'): ?>
                  <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i> Only administrators can modify this field
                  </p>
                <?php endif; ?>
              </div>
            </div>
            
            <div class="pt-4 border-t mt-6">
              <h2 class="text-xl font-semibold mb-4">Security</h2>
              <div class="bg-blue-50 p-4 rounded-md mb-4">
                <p class="text-blue-800 flex items-center">
                  <i class="fas fa-shield-alt mr-2"></i> Last password change: <?php 
                  echo isset($user_data['password_changed_at']) ? 
                      date('F j, Y', strtotime($user_data['password_changed_at'])) : 
                      'Never'; ?>
                </p>
                <p class="text-blue-800 text-sm mt-2">
                  <i class="fas fa-info-circle mr-2"></i> Password changes require administrator approval
                </p>
              </div>
              
              <button type="button" onclick="openPasswordModal()" 
                      class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition flex items-center">
                <i class="fas fa-key mr-2"></i> Request Password Change
              </button>
            </div>
            
            <div class="flex justify-end gap-2 pt-6">
              <button type="button" onclick="resetForm()" 
                      class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">
                Cancel
              </button>
              <button type="submit" name="update_profile"
                      class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition flex items-center">
                <i class="fas fa-save mr-2"></i> Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Users Table (Admin Only) -->
    <?php if ($user_data['role'] === 'Admin' && !empty($users_list)): ?>
    <div class="bg-white p-6 rounded-lg shadow-md mt-8">
      <h2 class="text-xl font-semibold border-b pb-2 mb-4">User Management</h2>
      
      <div class="flex justify-between items-center mb-4">
        <div class="flex items-center">
          <input type="checkbox" id="selectAll" class="mr-2">
          <label for="selectAll">Check all</label>
        </div>
        <div class="flex items-center">
          <span class="mr-2">With selected:</span>
          <select id="bulkAction" class="border rounded px-2 py-1 mr-2">
            <option value="">Select action</option>
            <option value="edit">Edit</option>
            <option value="copy">Copy</option>
            <option value="delete">Delete</option>
            <option value="export">Export</option>
          </select>
          <button onclick="applyBulkAction()" class="bg-blue-600 text-white px-3 py-1 rounded">Apply</button>
        </div>
      </div>
      
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
          <thead>
            <tr>
              <th class="py-2 px-4 border-b"></th>
              <th class="py-2 px-4 border-b">ID</th>
              <th class="py-2 px-4 border-b">Username</th>
              <th class="py-2 px-4 border-b">Email</th>
              <th class="py-2 px-4 border-b">Role</th>
              <th class="py-2 px-4 border-b">Created At</th>
              <th class="py-2 px-4 border-b">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users_list as $user): ?>
            <tr>
              <td class="py-2 px-4 border-b text-center"><input type="checkbox" class="user-checkbox" value="<?php echo $user['id']; ?>"></td>
              <td class="py-2 px-4 border-b text-center"><?php echo htmlspecialchars($user['id']); ?></td>
              <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['username']); ?></td>
              <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
              <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['role']); ?></td>
              <td class="py-2 px-4 border-b"><?php echo date('Y-m-d H:i:s', strtotime($user['created_at'])); ?></td>
              <td class="py-2 px-4 border-b">
                <form method="POST" class="inline">
                  <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                  <button type="submit" name="user_action" value="edit" class="text-blue-600 hover:text-blue-800 mr-2">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                  <button type="submit" name="user_action" value="copy" class="text-green-600 hover:text-green-800 mr-2">
                    <i class="fas fa-copy"></i> Copy
                  </button>
                  <button type="submit" name="user_action" value="delete" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i> Delete
                  </button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Password Change Request Modal -->
  <div id="passwordModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('passwordModal')">&times;</span>
        <h2 class="text-xl font-bold mb-4">Request Password Change</h2>
        
        <div class="bg-yellow-50 p-3 rounded-md mb-4">
            <p class="text-yellow-800 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i> 
                Password changes must be approved by an administrator.
            </p>
        </div>
        
        <form id="passwordForm" class="space-y-4" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div>
                <label class="block mb-1 font-medium">Current Password</label>
                <input type="password" name="current_password" id="currentPassword"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md" required />
            </div>
            
            <div>
                <label class="block mb-1 font-medium">New Password</label>
                <input type="password" name="new_password" id="newPassword"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md" required />
                <p class="text-xs text-gray-500 mt-1">Must be at least 8 characters</p>
            </div>
            
            <div>
                <label class="block mb-1 font-medium">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirmPassword"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md" required />
            </div>
            
            <div>
                <label class="block mb-1 font-medium">Reason for Change</label>
                <textarea name="change_reason" rows="3" id="changeReason"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md" required></textarea>
            </div>
            
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" onclick="closeModal('passwordModal')" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                    Cancel
                </button>
                <button type="submit" name="request_password_change"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-paper-plane mr-2"></i> Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

  <script>
    // DOM Elements
    const profileImage = document.getElementById('profileImage');
    const profileUpload = document.getElementById('profileUpload');
    const profileForm = document.getElementById('profileForm');
    const profileSubmit = document.getElementById('profileSubmit');
    const passwordModal = document.getElementById('passwordModal');
    const successModal = document.getElementById('successModal');
    const settingsForm = document.getElementById('settingsForm');
    const passwordForm = document.getElementById('passwordForm');
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkActionSelect = document.getElementById('bulkAction');

    // Modal functions
    function openPasswordModal() {
        document.getElementById('passwordModal').style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Automatically show error/success modals if they have content
    window.onload = function() {
        <?php if (isset($_SESSION['error_message'])): ?>
            document.getElementById('errorModal').style.display = 'block';
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            document.getElementById('successModal').style.display = 'block';
        <?php endif; ?>
    };

    // Form validation for password change
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        if (newPassword.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long!');
            return false;
        }
        
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('New passwords do not match!');
            return false;
        }
        
        return true;
    });

    // Profile Image Upload
    document.getElementById('profileUpload').addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            // Client-side validation
            if (this.files[0].size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                return;
            }
            
            // Submit the form
            document.getElementById('profileSubmit').click();
        }
    });

    // Modal Functions
    function openPasswordModal() {
        document.getElementById('passwordModal').style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = 'none';
        }
    }

    function closeSuccessModal() {
        document.getElementById('successModal').style.display = 'none';
    }

    // Form Handling
    function resetForm() {
        document.getElementById('settingsForm').reset();
    }

    // Checkbox handling for user selection
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
    }

    function validatePassword(password) {
        // At least 8 characters, one uppercase, one lowercase, one number
        const re = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
        return re.test(password);
    }

    // Bulk action handling
    function applyBulkAction() {
        const action = bulkActionSelect.value;
        const selectedUsers = Array.from(userCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
        
        if (!action) {
            alert('Please select an action');
            return;
        }
        
        if (selectedUsers.length === 0) {
            alert('Please select at least one user');
            return;
        }
        
        // In a real app, you would implement this with AJAX
        alert(`Performing ${action} on selected users: ${selectedUsers.join(', ')}`);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Settings page loaded');
        
        // Initialize checkbox functionality
        if (userCheckboxes.length > 0) {
            userCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Check if all checkboxes are selected
                    const allChecked = Array.from(userCheckboxes).every(cb => cb.checked);
                    selectAllCheckbox.checked = allChecked;
                });
            });
        }
    });
  </script>
</body>
</html>

<?php
// Close database connection
$conn->close();

// Clear the messages after displaying them
unset($_SESSION['error_message']);
unset($_SESSION['success_message']);
?>