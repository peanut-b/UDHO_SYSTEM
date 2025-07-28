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
                    <a href="\UDHO%20SYSTEM\Settings\setting_operation.php" class="block py-2.5 px-4 hover:bg-gray-700 flex items-center">
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
      <h1 class="text-2xl font-bold">Operation Panel</h1>
      <div class="flex items-center gap-2">
        <img src="\UDHO%20SYSTEM\assets\UDHOLOGO.png" alt="Logo" class="h-8">
        <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
      </div>
    </header>

    <!-- Tailwind Grid Buttons -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <!-- IDSAP Database -->
      <a href="operation_IDSAP.php" class="block">
        <div class="bg-white p-6 text-center rounded shadow-sm border transition-all duration-300 custom-card">
          <i class="fas fa-users icon-style mb-2"></i>
          <h6 class="mt-2 text-lg font-medium text-gray-800">IDSAP Database</h6>
        </div>
      </a>

      <!-- PDC Cases -->
      <a href="operation_panel.php" class="block">
        <div class="bg-white p-6 text-center rounded shadow-sm border transition-all duration-300 custom-card">
          <i class="fas fa-scale-balanced icon-style mb-2"></i>
          <h6 class="mt-2 text-lg font-medium text-gray-800">PDC Cases</h6>
        </div>
      </a>

      <!-- Meralco Certificate -->
      <a href="meralco.php" class="block">
        <div class="bg-white p-6 text-center rounded shadow-sm border transition-all duration-300 custom-card">
          <i class="fas fa-file-alt icon-style mb-2"></i>
          <h6 class="mt-2 text-lg font-medium text-gray-800">Meralco Certificate</h6>
        </div>
      </a>
    </div>
  </div>

</body>
</html>
