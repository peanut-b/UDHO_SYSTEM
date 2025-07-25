<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UDHO Login</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .login-container {
      background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    }
    .form-input:focus {
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
  <div class="login-container rounded-2xl shadow-2xl overflow-hidden w-full max-w-md">
    <div class="p-8 bg-white">
      <div class="flex justify-center mb-6">
        <img src="\UDHO%20SYSTEM\assets\UDHOLOGO.png" alt="UDHO Logo" class="h-16">
      </div>
      
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Welcome Back</h2>
      <p class="text-gray-600 text-center mb-8">Please login to your account</p>
      
      <form id="loginForm" class="space-y-4">
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-user text-gray-400"></i>
            </div>
            <input type="text" id="username" required
                   class="pl-10 form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-4 border">
          </div>
        </div>
        
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-lock text-gray-400"></i>
            </div>
            <input type="password" id="password" required
                   class="pl-10 form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-4 border">
          </div>
        </div>
        
        <div>
          <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
          <select id="role" required
                  class="form-select block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-4 border">
            <option value="" disabled selected>Select your role</option>
            <!-- Options will be populated by JavaScript -->
          </select>
        </div>
        
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input id="remember-me" name="remember-me" type="checkbox"
                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
          </div>
          <div class="text-sm">
            <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Forgot password?</a>
          </div>
        </div>
        
        <div>
          <button type="submit" id="loginButton"
                  class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <span id="buttonText">Sign in</span>
            <span id="spinner" class="ml-2 hidden">
              <i class="fas fa-spinner fa-spin"></i>
            </span>
          </button>
        </div>
        
        <div id="errorMessage" class="hidden text-center text-sm text-red-600 mt-2"></div>
      </form>
    </div>
    
    <div class="px-8 py-4 bg-gray-50 text-center">
      <p class="text-sm text-gray-600">
        Don't have an account? 
        <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Contact admin</a>
      </p>
    </div>
  </div>

  <script>
    // Fetch available roles when page loads
    document.addEventListener('DOMContentLoaded', async () => {
      try {
        const response = await fetch('http://localhost:3001/api/roles');
        if (!response.ok) throw new Error('Failed to fetch roles');
        
        const roles = await response.json();
        const roleSelect = document.getElementById('role');
        
        roles.forEach(role => {
          const option = document.createElement('option');
          option.value = role;
          option.textContent = role;
          roleSelect.appendChild(option);
        });
      } catch (error) {
        showError('Failed to load roles. Please refresh the page.');
      }
    });

    // Handle login form submission
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value;
      const role = document.getElementById('role').value;
      const rememberMe = document.getElementById('remember-me').checked;
      
      // Validate inputs
      if (!username || !password || !role) {
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
      
      try {
        const response = await fetch('http://localhost:3001/api/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ username, password, role })
        });
        
        const data = await response.json();
        
        if (response.ok) {
          // Store the token (use sessionStorage for more security if rememberMe is false)
          const storage = rememberMe ? localStorage : sessionStorage;
          storage.setItem('authToken', data.token);
          storage.setItem('userData', JSON.stringify(data.user));
          
          // Redirect based on role
          redirectUser(data.user.role);
        } else {
          showError(data.message || 'Login failed. Please try again.');
        }
      } catch (error) {
        showError('Error connecting to server. Please try again.');
      } finally {
        // Reset button state
        loginButton.disabled = false;
        buttonText.textContent = 'Sign in';
        spinner.classList.add('hidden');
      }
    });

    // Redirect user based on role
    function redirectUser(role) {
      switch(role) {
        case 'Admin':
          window.location.href = 'admin_dashboard.php';
          break;
        case 'Operation':
          window.location.href = 'operation_dashboard.php';
          break;
        case 'Admin Executive':
          window.location.href = 'adminexecutive_dashboard.php';
          break;
        case 'HOA':
          window.location.href = 'hoa_dashboard.php';
          break;
        default:
          window.location.href = 'dashboard.php';
      }
    }

    // Show error message
    function showError(message) {
      const errorElement = document.getElementById('errorMessage');
      errorElement.textContent = message;
      errorElement.classList.remove('hidden');
      
      // Hide error after 5 seconds
      setTimeout(() => {
        errorElement.classList.add('hidden');
      }, 5000);
    }

    // Check if user is already logged in
    function checkExistingLogin() {
      const token = localStorage.getItem('authToken') || sessionStorage.getItem('authToken');
      if (token) {
        // Verify token and redirect if valid
        // You would typically make an API call to verify the token
        const userData = JSON.parse(localStorage.getItem('userData') || 
                         JSON.parse(sessionStorage.getItem('userData')));
        if (userData) {
          redirectUser(userData.role);
        }
      }
    }

    // Check for existing login when page loads
    checkExistingLogin();
  </script>
</body>
</html>