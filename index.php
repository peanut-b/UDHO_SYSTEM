<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'udho_db');

// Error reporting (turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to establish database connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    $rememberMe = isset($_POST['remember-me']);
    
    // Validate inputs
    if (empty($username) || empty($password) || empty($role)) {
        $error = "Please fill in all fields";
    } else {
        // Authenticate user
        $conn = getDBConnection();
        
        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? AND role = ?");
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password (use password_verify() if passwords are hashed)
            if ($password === $user['password']) { // In production, use: password_verify($password, $user['password'])
                // Authentication successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                
                // Set remember me cookie if selected
                if ($rememberMe) {
                    $cookie_value = base64_encode($user['id'] . ':' . hash('sha256', $user['password']));
                    setcookie('remember_me', $cookie_value, time() + (86400 * 30), "/"); // 30 days
                }
                
                // Redirect based on role
                switch ($user['role']) {
                    case 'Admin':
                        header("Location: \UDHO%20SYSTEM\Admin\admin_dashboard.php");
                        break;
                    case 'Operation':
                        header("Location: \UDHO%20SYSTEM\Operation\operation_dashboard.php");
                        break;
                    case 'Admin Executive':
                        header("Location: \UDHO%20SYSTEM\Admin%20executive\adminexecutive_dashboard.php");
                        break;
                    case 'HOA':
                        header("Location: \UDHO%20SYSTEM\HOA\hoa_dashboard.php");
                        break;
                    case 'Enumerator':
                        header("Location: \UDHO%20SYSTEM\Operation\survey.php");
                        break;
                    default:
                        header("Location: index.php");
                }
                exit();
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "User not found or invalid role";
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Check for remember me cookie
if (!isset($_SESSION['logged_in']) && isset($_COOKIE['remember_me'])) {
    $cookie_data = base64_decode($_COOKIE['remember_me']);
    list($user_id, $token) = explode(':', $cookie_data);
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, username, role, password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (hash('sha256', $user['password']) === $token) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            
            // Redirect based on role
            switch ($user['role']) {
                case 'Admin':
                    header("Location: \UDHO%20SYSTEM\Admin\admin_dashboard.php");
                    break;
                case 'Operation':
                    header("Location: \UDHO%20SYSTEM\Operation\operation_dashboard.php");
                    break;
                case 'Admin Executive':
                    header("Location: \UDHO%20SYSTEM\Admin%20executive\adminexecutive_dashboard.php");
                    break;
                case 'HOA':
                    header("Location: \UDHO%20SYSTEM\HOA\hoa_dashboard.php");
                    break;
                case 'Enumerator':
                    header("Location: \UDHO%20SYSTEM\Operation\survey.php");
                    break;
                default:
                    header("Location: index.php");
            }
            exit();
        }
    }
    
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UDHO Login</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
     /* Background Image Styles */
    body {
      background-image: url('assets/BG_LOGIN.png');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-attachment: fixed;
    }
    .login-container {
      background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }
    .form-input:focus {
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
    }
    .survey-btn {
      transition: all 0.3s ease;
    }
    .survey-btn:hover {
      transform: translateY(-2px);
    }
    .logo-box {
      background: white;
      border-radius: 1rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.0);
      padding: 2rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    /* Loader Styles */
    .loader {
      width: 50px;
      aspect-ratio: 1;
      border-radius: 50%;
      border: 8px solid lightblue;
      border-right-color: orange;
      animation: l2 1s infinite linear;
    }
    .loader:before,
    .loader:after {
      content: "";
      width: 50%;
      background: #514b82;
      clip-path: polygon(0 0,100% 50%,0% 100%);
      animation: inherit;
      animation-name: l10-1;
      transform-origin: bottom left;
    }
    .loader:before {
      clip-path: polygon(0 50%,100% 0,100% 100%);
      transform-origin: bottom right;
      --s:-1;
    }
    @keyframes l10-0 {
      0%,34.99% {transform: scaley(1)}
      35%,70%   {transform: scaley(-1)}
      90%,100%  {transform: scaley(-1) rotate(180deg)}
    }
    @keyframes l10-1 {
      0%,10%,70%,100%{transform:translateY(-100%) rotate(calc(var(--s,1)*135deg))}
      35%            {transform:translateY(0%)    rotate(0deg)}
    }
    @keyframes l2 {
      to {
        transform: rotate(1turn);
      }
    }

    /* Full page loader overlay */
    .loader-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(255, 255, 255, 0.8);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
  <!-- Page Loader -->
  <div id="pageLoader" class="loader-overlay">
    <div class="loader"></div>
  </div>

  <!-- Main Content -->
  <div class="flex w-full max-w-4xl bg-white rounded-xl shadow-xl overflow-hidden">
    <!-- Logo Box on Left -->
    <div class="w-1/2 p-8 flex items-center justify-center">
      <div class="logo-box">
        <img src="assets/bg_1.png" alt="UDHO Logo" class="h-100 w-100 object-cover">
      </div>
    </div>
    
    <!-- Login Form on Right -->
    <div class="w-1/2 p-8">
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Welcome Back</h2>
      <p class="text-gray-600 text-center mb-8">Please login to your account</p>
      
      <?php if (isset($error)): ?>
        <div id="errorMessage" class="text-center text-sm text-red-600 mb-4">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
      
      <form id="loginForm" method="POST" class="space-y-4">
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-user text-gray-400"></i>
            </div>
            <input type="text" id="username" name="username" required
                   class="pl-10 form-input block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-4 border"
                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
          </div>
        </div>
        
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-lock text-gray-400"></i>
            </div>
            <input type="password" id="password" name="password" required
                   class="pl-10 form-input block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-4 border">
          </div>
        </div>
        
        <div>
          <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
          <select id="role" name="role" required
                  class="form-select block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-4 border">
            <option value="" disabled selected>Select your role</option>
            <option value="Admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'Admin') ? 'selected' : ''; ?>>Admin</option>
            <option value="Operation" <?php echo (isset($_POST['role']) && $_POST['role'] === 'Operation') ? 'selected' : ''; ?>>Operation</option>
            <option value="Admin Executive" <?php echo (isset($_POST['role']) && $_POST['role'] === 'Admin Executive') ? 'selected' : ''; ?>>Admin Executive</option>
            <option value="HOA" <?php echo (isset($_POST['role']) && $_POST['role'] === 'HOA') ? 'selected' : ''; ?>>HOA</option>
            <option value="Enumerator" <?php echo (isset($_POST['role']) && $_POST['role'] === 'Enumerator') ? 'selected' : ''; ?>>Enumerator</option>
          </select>
        </div>
        
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input id="remember-me" name="remember-me" type="checkbox"
                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                   <?php echo (isset($_POST['remember-me']) && $_POST['remember-me'] === 'on') ? 'checked' : ''; ?>>
            <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
          </div>
          <div class="text-sm">
            <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">Forgot password?</a>
          </div>
        </div>
        
        <div>
          <button type="submit" id="loginButton"
                  class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <span id="buttonText">Sign in</span>
            <span id="spinner" class="ml-2 hidden">
              <div class="loader" style="width: 20px; height: 20px;"></div>
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Hide page loader when DOM is fully loaded
    document.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => {
        document.getElementById('pageLoader').style.display = 'none';
      }, 1000);
    });

    // Handle login form submission
    document.getElementById('loginForm').addEventListener('submit', (e) => {
      // Client-side validation
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value;
      const role = document.getElementById('role').value;
      
      if (!username || !password || !role) {
        e.preventDefault();
        showError('Please fill in all fields');
        return;
      }
      
      // Show loading state
      const loginButton = document.getElementById('loginButton');
      const buttonText = document.getElementById('buttonText');
      const spinner = document.getElementById('spinner');
      
      loginButton.disabled = true;
      buttonText.textContent = 'Signing in...';
      spinner.classList.remove('hidden');
    });

    // Show error message
    function showError(message) {
      const errorElement = document.createElement('div');
      errorElement.id = 'errorMessage';
      errorElement.className = 'text-center text-sm text-red-600 mt-2';
      errorElement.textContent = message;
      
      const form = document.getElementById('loginForm');
      const existingError = document.getElementById('errorMessage');
      
      if (existingError) {
        existingError.remove();
      }
      
      form.insertBefore(errorElement, form.firstChild);
      
      // Hide error after 5 seconds
      setTimeout(() => {
        errorElement.remove();
      }, 5000);
    }
  </script>
</body>
</html>