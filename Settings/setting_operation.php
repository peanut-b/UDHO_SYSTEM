<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Operation Panel - Settings</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    /* Custom scrollbar */
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
    
    /* Profile image styling */
    .profile-image-container {
      width: 120px;
      height: 120px;
      border: 3px solid #4f46e5;
    }
    
    /* Input focus styling */
    input:focus, textarea:focus, select:focus {
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
    }
    
    /* Disabled select styling */
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
  </style>
</head>

<body class="bg-gray-100 min-h-screen flex">
  <!-- Sidebar (Retained Design) -->
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
          <a href="\UDHO%20SYSTEM\Operation\operation_dashboard.php" class="flex items-center py-2.5 px-4 hover:bg-gray-700">
            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
          </a>
        </li>

        <li>
          <a href="\UDHO%20SYSTEM\Settings\setting.php" class="flex items-center py-2.5 px-4 bg-gray-700">
            <i class="fas fa-cog mr-3"></i> Settings
          </a>
        </li>
        <li>
          <a href="#" class="flex items-center py-2.5 px-4 hover:bg-gray-700 mt-10">
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
        <img src="\UDHO%20SYSTEM\assets\UDHOLOGO.png" alt="Logo" class="h-8">
        <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
      </div>
    </header>

    <div class="bg-white p-6 rounded-lg shadow-md">
      <div class="flex flex-col md:flex-row gap-8">
        <!-- Profile Picture Section -->
        <div class="flex flex-col items-center md:w-1/3">
          <div class="profile-image-container rounded-full overflow-hidden mb-4">
            <img id="profileImage" src="https://via.placeholder.com/120" alt="Profile" class="w-full h-full object-cover">
          </div>
          <input type="file" id="profileUpload" accept="image/*" class="hidden" />
          <button onclick="document.getElementById('profileUpload').click()" 
                  class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition">
            <i class="fas fa-camera mr-2"></i> Change Photo
          </button>
          <p class="text-sm text-gray-500 mt-2">JPG, GIF or PNG. Max size 2MB</p>
        </div>
        
        <!-- Account Settings Form -->
        <div class="flex-1">
          <h2 class="text-xl font-semibold border-b pb-2 mb-4">Personal Information</h2>
          
          <form id="settingsForm" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block mb-1 font-medium">Username</label>
                <input type="text" value="admin_user" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                       disabled />
              </div>
              
              <div>
                <label class="block mb-1 font-medium">Email Address</label>
                <input type="email" value="admin@udho.gov.ph" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                       required />
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block mb-1 font-medium">Phone Number</label>
                <input type="tel" value="+639123456789" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                       required />
              </div>
              
              <div>
                <label class="block mb-1 font-medium">Designated Position</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                        disabled>
                  <option>Admin</option>
                  <option>HOA</option>
                  <option>Operation</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                  <i class="fas fa-info-circle mr-1"></i> Only administrators can modify this field
                </p>
              </div>
            </div>
            
            <div>
              <label class="block mb-1 font-medium">Account Type</label>
              <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                      disabled>
                <option>Administrator</option>
                <option>Staff</option>
                <option>Viewer</option>
              </select>
            </div>
            
            <div class="pt-4 border-t mt-6">
              <h2 class="text-xl font-semibold mb-4">Security</h2>
              <div class="bg-blue-50 p-4 rounded-md mb-4">
                <p class="text-blue-800 flex items-center">
                  <i class="fas fa-shield-alt mr-2"></i> Last password change: 15 days ago
                </p>
                <p class="text-blue-800 text-sm mt-2">
                  <i class="fas fa-info-circle mr-2"></i> Only administrators can change passwords
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

  <!-- Password Change Request Modal -->
  <div id="passwordModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
      <div class="p-4 border-b">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-bold">Request Password Change</h3>
          <button onclick="closePasswordModal()" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      
      <div class="p-4">
        <form id="passwordForm" class="space-y-4">
          <div class="bg-yellow-50 p-3 rounded-md mb-4">
            <p class="text-yellow-800 text-sm">
              <i class="fas fa-exclamation-circle mr-2"></i> 
              Password changes must be approved by an administrator. Please provide your current password and reason for the request.
            </p>
          </div>
          
          <div>
            <label class="block mb-1 font-medium">Current Password</label>
            <input type="password" id="currentPassword"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                   required />
          </div>
          
          <div>
            <label class="block mb-1 font-medium">Reason for Change</label>
            <textarea rows="3" id="changeReason"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                   required></textarea>
          </div>
          
          <div class="flex justify-end gap-2 pt-4">
            <button type="button" onclick="closePasswordModal()" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">
              Cancel
            </button>
            <button type="submit" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition flex items-center">
              <i class="fas fa-paper-plane mr-2"></i> Submit Request
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div id="successModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
      <div class="p-6 text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
          <i class="fas fa-check text-green-600 text-xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Password Change Request Submitted</h3>
        <div class="mt-2">
          <p class="text-sm text-gray-500">
            Your request has been successfully submitted. Please contact your system administrator to approve the password change.
          </p>
        </div>
        <div class="mt-4">
          <button type="button" onclick="closeSuccessModal()"
                  class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
            OK
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // DOM Elements
    const profileImage = document.getElementById('profileImage');
    const profileUpload = document.getElementById('profileUpload');
    const passwordModal = document.getElementById('passwordModal');
    const successModal = document.getElementById('successModal');
    const settingsForm = document.getElementById('settingsForm');
    const passwordForm = document.getElementById('passwordForm');

    // Profile Image Upload
    profileUpload.addEventListener('change', function(e) {
      if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(event) {
          profileImage.src = event.target.result;
          // Here you would typically upload the image to your server
        };
        reader.readAsDataURL(e.target.files[0]);
      }
    });

    // Modal Functions
    function openPasswordModal() {
      passwordModal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closePasswordModal() {
      passwordModal.classList.add('hidden');
      document.body.style.overflow = 'auto';
    }

    function showSuccessModal() {
      successModal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeSuccessModal() {
      successModal.classList.add('hidden');
      document.body.style.overflow = 'auto';
      closePasswordModal();
    }

    // Form Handling
    function resetForm() {
      settingsForm.reset();
      // In a real app, you would reset to original values from server
    }

    settingsForm.addEventListener('submit', function(e) {
      e.preventDefault();
      // Here you would send the form data to your server
      alert('Settings saved successfully!');
    });

    passwordForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const currentPassword = document.getElementById('currentPassword').value;
      const changeReason = document.getElementById('changeReason').value;
      
      // Here you would send the password change request to your server
      console.log('Password change request submitted:', {
        currentPassword,
        changeReason
      });
      
      // Show success modal
      closePasswordModal();
      showSuccessModal();
      
      // Reset form
      passwordForm.reset();
    });

    // Sample data initialization
    document.addEventListener('DOMContentLoaded', function() {
      // In a real app, you would fetch user data from your server here
      console.log('Settings page loaded');
    });
  </script>
</body>

</html>