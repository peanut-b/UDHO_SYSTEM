<?php
session_start();

// Handle all form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../db_connection.php';
    
    // Profile picture upload
    if (isset($_FILES['profile_picture'])) {
        $uploadDir = '../assets/profile_pictures/';
        $filename = 'user_' . $_SESSION['user_id'] . '_' . time() . '.jpg';
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $filename, $_SESSION['user_id']);
            $stmt->execute();
            $_SESSION['profile_picture'] = $filename;
            $_SESSION['success'] = "Profile picture updated!";
        } else {
            $_SESSION['error'] = "Failed to upload image";
        }
        header("Location: settings.php");
        exit();
    }

    // Settings update
    if (isset($_POST['email'])) {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $phone = preg_replace('/[^0-9+]/', '', $_POST['phone']);
        
        if ($email && strlen($phone) >= 5) {
            $stmt = $conn->prepare("UPDATE users SET email = ?, phone = ? WHERE id = ?");
            $stmt->bind_param("ssi", $email, $phone, $_SESSION['user_id']);
            $stmt->execute();
            $_SESSION['email'] = $email;
            $_SESSION['phone'] = $phone;
            $_SESSION['success'] = "Settings updated!";
        } else {
            $_SESSION['error'] = "Invalid input";
        }
        header("Location: settings.php");
        exit();
    }
}

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
require_once '../db_connection.php';

// Fetch user data from database
$stmt = $conn->prepare("SELECT username, email, phone, role, profile_picture, last_password_change FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found");
}

// Set user data
$profilePicture = $user['profile_picture'] ?? 'default.jpg';
$username = $user['username'] ?? '';
$email = $user['email'] ?? '';
$phone = $user['phone'] ?? '';
$userType = $user['role'] ?? '';
$lastPasswordChange = $user['last_password_change'] ?? 'Never';

// Automatically determine dashboard based on account type
$accountTypes = [
    'admin' => 'adminexecutive_dashboard.php',
    'operation' => 'operation_dashboard.php', 
    'hoa' => 'hoa_dashboard.php',
    'staff' => 'staff_dashboard.php'
];
$dashboardFile = $accountTypes[$userType] ?? 'index.php';
$dashboardUrl = dirname($_SERVER['PHP_SELF']) . '/../' . $dashboardFile;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head content remains the same -->
    <!-- ... -->
</head>
<body class="bg-gray-100 min-h-screen flex">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 text-white flex flex-col">
        <div class="flex items-center justify-center h-24">
            <div class="rounded-full bg-gray-200 w-20 h-20 flex items-center justify-center overflow-hidden border-2 border-white shadow-md">
                <img src="../assets/profile_pictures/<?= htmlspecialchars($profilePicture) ?>" 
                     alt="Profile Picture" 
                     class="w-full h-full object-cover"
                     onerror="this.src='../assets/PROFILE_SAMPLE.jpg'">
            </div>
        </div>
        <nav class="mt-6">
            <ul>
                <li>
                    <a href="<?= htmlspecialchars($dashboardUrl) ?>" class="sidebar-link flex items-center py-3 px-4">
                        <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                    </a>
                </li>
                <?php if ($userType === 'admin'): ?>
                <li>
                    <a href="../Admin_executive/backup.php" class="sidebar-link flex items-center py-3 px-4">
                        <i class="fas fa-database mr-3"></i> Backup Data
                    </a>
                </li>
                <?php endif; ?>
                <li>
                    <a href="../employee/employee.php" class="sidebar-link flex items-center py-3 px-4">
                        <i class="fas fa-users mr-3"></i> Employees
                    </a>
                </li>
                <li>
                    <a href="settings.php" class="sidebar-link flex items-center py-3 px-4 active-link">
                        <i class="fas fa-cog mr-3"></i> Settings
                    </a>
                </li>
                <li>
                    <a href="../logout.php" class="sidebar-link flex items-center py-3 px-4 mt-10">
                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-4 md:p-10">
        <!-- Header and success message display -->
        <!-- ... -->

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Profile Picture Section -->
                <div class="flex flex-col items-center md:w-1/3">
                    <!-- Profile picture upload form -->
                    <!-- ... -->
                </div>
                
                <!-- Account Settings Form -->
                <div class="flex-1">
                    <h2 class="text-xl font-semibold border-b pb-2 mb-4">Personal Information</h2>
                    
                    <form id="settingsForm" class="space-y-4" action="settings.php" method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1 font-medium">Username</label>
                                <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                                       disabled />
                            </div>
                            
                            <div>
                                <label class="block mb-1 font-medium">Email Address</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                                       required />
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1 font-medium">Phone Number</label>
                                <input type="tel" name="phone" value="<?= htmlspecialchars($phone) ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                                       required />
                            </div>
                            
                            <div>
                                <label class="block mb-1 font-medium">Account Type</label>
                                <input type="text" value="<?= ucfirst(htmlspecialchars($userType)) ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                                       disabled />
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i> Only administrators can modify this field
                                </p>
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t mt-6">
                            <h2 class="text-xl font-semibold mb-4">Security</h2>
                            <div class="bg-blue-50 p-4 rounded-md mb-4">
                                <p class="text-blue-800 flex items-center">
                                    <i class="fas fa-shield-alt mr-2"></i> Last password change: <?= htmlspecialchars($lastPasswordChange) ?>
                                </p>
                                <?php if ($userType !== 'admin'): ?>
                                <p class="text-blue-800 text-sm mt-2">
                                    <i class="fas fa-info-circle mr-2"></i> Only administrators can change passwords
                                </p>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($userType === 'admin'): ?>
                                <a href="../Admin_executive/change_password.php" 
                                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition flex items-center inline-block">
                                    <i class="fas fa-key mr-2"></i> Change Password
                                </a>
                            <?php else: ?>
                                <button type="button" onclick="openPasswordModal()" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition flex items-center">
                                    <i class="fas fa-key mr-2"></i> Request Password Change
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex justify-end gap-2 pt-6">
                            <a href="<?= htmlspecialchars($dashboardUrl) ?>" 
                               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition flex items-center">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals and JavaScript remain the same -->
    <!-- ... -->
</body>
</html>
<?php
// Clear messages
unset($_SESSION['error']);
?>