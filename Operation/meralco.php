<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MERALCO Certification Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
        @import url('https://fonts.cdnfonts.com/css/bookman-old-style');
        
        .certificate {
            font-family: 'Bookman Old Style', serif;
            width: 8.5in;
            height: 14in;
            padding: 2.54cm;
            border: 1px solid #ccc;
            margin: 20px auto;
            display: none;
            position: relative;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .justified-text {
            text-align: justify;
            margin: 15px 0;
            text-indent: 50px;
        }

        .header-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo-container {
                display: flex;
                justify-content: center; /* Center the logos horizontally */
                align-items: center; /* Center the logos vertically */
                width: 100%;
                margin-bottom: 15px;
                gap: 20px; /* Add space between logos */
            }

            .logo {
                height: 80px;
                width: auto; /* Maintain aspect ratio */
                max-width: 150px; /* Limit maximum width */
                object-fit: contain;
            }

        .header-text {
            text-align: center;
        }

        .footer-right {
            text-align: right;
            margin-top: 50px;
            margin-right: 100px;
        }

        .conforme-signature {
            font-style: italic;
        }

        .signature-block {
            margin-left: 30px;
        }

        table {
            margin-bottom: 20px;
            margin-left: auto;
            margin-right: auto;
        }

        table td {
            padding-right: 10px;
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
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-buttons {
            text-align: center;
            margin-top: 20px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        /* Database table styles */
        .applicant-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .applicant-table th, .applicant-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .applicant-table th {
            background-color: #4CAF50;
            color: white;
        }
        
        .applicant-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .applicant-table tr:hover {
            background-color: #ddd;
        }
        
        .duplicate-applicant {
            background-color: #ffcccc !important;
        }

        /* Warning modal styles */
        .warning-modal {
            background-color: #fff3cd;
            border-left: 6px solid #ffc107;
        }
        
        .warning-icon {
            color: #ffc107;
            font-size: 2rem;
        }
        
        .countdown {
            font-weight: bold;
            color: #dc3545;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            #printable-certificate {
                width: 8.5in;
                height: 14in;
                padding: 1in;
                box-sizing: border-box;
                page-break-after: avoid;
            }

            body * {
                visibility: hidden;
            }

            #printable-certificate, #printable-certificate * {
                visibility: visible;
            }

            #printable-certificate {
                position: absolute;
                left: 0;
                top: 0;
            }
        }

        @page {
            size: legal portrait;
            margin: 1in;
        }

        /* Small warning popup */
        .warning-popup {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 100;
            display: none;
            animation: slideIn 0.5s forwards;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }

        .warning-popup.fade-out {
            animation: fadeOut 0.5s forwards;
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex">
    <!-- Small warning popup -->
    <div id="saveWarning" class="warning-popup">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="saveMessage">Certificate data saved to database!</span>
    </div>

    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 text-white flex flex-col">
        <div class="flex items-center justify-center h-24">
          <div class="rounded-full bg-gray-200 w-20 h-20 flex items-center justify-center overflow-hidden border2 border-white shadow-md">
              <?php
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
                    <a href="operation_dashboard.php" class="flex items-center py-2.5 px-4 hover:bg-gray-700">
                        <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="/UDHO%20SYSTEM/Settings/setting_operation.php" class="flex items-center py-2.5 px-4 hover:bg-gray-700">
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
    <div class="flex-1 p-10">
        <header class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">MERALCO Certification Generator</h1>
            <div class="flex items-center gap-2">
                <img src="\UDHO%20SYSTEM\assets\UDHOLOGO.png" alt="Logo" class="h-8">
                <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
            </div>
        </header>

        <!-- Form Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Applicant Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="name" placeholder="Juan Dela Cruz" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
                
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Complete Address</label>
                    <input type="text" id="address" placeholder="123 Main Street, Barangay 123, Pasay City" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Control Number</label>
                    <div class="flex items-center">
                        <span id="currentYear" class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-l-md"></span>
                        <span class="px-2">-</span>
                        <input type="text" id="controlNumber" placeholder="001" 
                               class="w-20 px-4 py-2 border border-gray-300 rounded-r-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button onclick="showWarningModal()" 
                        class="px-6 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition flex items-center">
                    <i class="fas fa-database mr-2"></i> View Applicants Database
                </button>
                <button onclick="generateCertificate()" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition flex items-center">
                    <i class="fas fa-file-certificate mr-2"></i> Generate Certificate
                </button>
            </div>
        </div>
        
        <!-- Instructions Section -->
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <h3 class="text-lg font-medium text-blue-800 mb-2 flex items-center">
                <i class="fas fa-info-circle mr-2"></i> Instructions
            </h3>
            <ul class="text-sm text-gray-700 space-y-1 list-disc pl-5">
                <li>Fill in all required fields above</li>
                <li>Click "Generate Certificate" to preview the document</li>
                <li>In the preview window, you can print or save as PDF</li>
                <li>Use legal size paper (8.5" x 14") when printing</li>
                <li>Check database for duplicate applications before issuing new certificates</li>
            </ul>
        </div>
    </div>
    
    <!-- Certificate template (hidden by default) -->
    <div id="certificateOutput" class="certificate">
        <div class="header-container">
            <div class="logo-container">
                <img src="\UDHO%20SYSTEM\assets\PILIPINASLOGO.png" alt="Pasay Logo" class="logo">
                <img src="\UDHO%20SYSTEM\assets\PASAYLOGO.png" alt="Bagong Pilipinas" class="logo">
                <img src="\UDHO%20SYSTEM\assets\UDHOLOGO.png" alt="UDHO Logo" class="logo">
            </div>
            <div class="header-text">
                <p>Republika ng Pilipinas<br>
                Lungsod ng Pasay, Kalakhang Maynila<br>
                <strong><span style="font-size: 0.8em;"></span>URBAND DEVELOPMENT AND HOUSING OFFICE</strong><br>
                <span style="font-size: 0.8em; padding-top: 0em">Room 209, Pasay City Hall, F.B. Harrison St., Pasay City</span></p><br>
            </div>
        </div>
        
        <hr>
        
        <h2 style="text-align: center;">CERTIFICATION</h2>
        
        <div class="justified-text">
            <p>This is to certify that <strong><span id="outputName"></span></strong> is a verified resident of <strong><span id="outputAddress"></span></strong>, as evident in the attached barangay certificate. This certification is issued in relation to the <em>first-time application</em> for MERALCO electric service.</p>
            
            <p>Through this certificate, the above-mentioned individual is hereby permitted to connect to the service of the <strong>MANILA ELECTRIC RAILROAD AND LIGHT COMPANY (MERALCO)</strong> after complying with all the requirements.</p>
            
            <p>In the event that the property owner exercises his/her right of ownership by demanding the immediate removal of the structure, the applicant, as identified in this certification, shall promptly and voluntarily vacate the premises upon receiving proper notice.</p>
            
            <p>This certification is issued solely for the aforementioned purpose and shall not be used for any other purpose or in connection with any other matters, unless expressly authorized. Any use of this certification beyond its intended purpose is strictly prohibited.</p>
        </div>
        
        <p><em>Issued this <span id="currentDate"></span>.</em></p>
        
        <div style="margin-left: 0; padding-left: 0;">
                <table style="margin-left: 0; padding-left: 0;">
                    <tr>
                        <td colspan="2" style="height: 1em;">&nbsp;</td>
                    </tr>

                    <tr>
                        <td style="padding-left: 0;">Control Number</td>
                        <td style="padding-left: 0;">: <span id="outputControlNumber"></span></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 0;">Conforme</td>
                        <td style="padding-left: 0; vertical-align: bottom;">
                            : <span class="conforme-signature">___________________________</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-left: 0;"></td>
                        <td style="padding-left: 0; text-align: left; font-style: italic; padding-top: 0;">
                            Signature Over Printed Name
                        </td>
                    </tr>
                </table>
            </div>
        
            <div class="footer-left">
                <table>
                    
                    <td style="vertical-align: top; text-align: right; padding-right: 2.5em;" colspan="2">
                        <div style="height: 4em;"></div>
                        <span>By the authority of the City Mayor,</span><br><br>
                    </td>
                    <tr>
                        <td style="vertical-align: top; padding-left: 20em; ">
                        </td>
                        <td style="vertical-align: top; padding-right: 0em;">
                            <div style="height: 4em;"></div>
                            <span style="font-weight: bold; padding-right: 5em;">MARGARITA G. IGNACIO</span><br>
                            <span style="padding-right: 0em;">Officer-in-Charge</span><br>
                            <span style="padding-right: 0em; white-space: nowrap;">Urban Development and Housing Office</span><br>
                        </td>
                    </tr>
                    <td style="vertical-align: top; text-align: right; padding-right: 0.2em;" colspan="2">
                        <div style="height: 2em;"></div>
                        <span style="font-style: italic; font-size: smaller;">This document is not valid without dry seal</span><br><br>
                    </td>
                    
                </table>
            </div>
    </div>
    
    <!-- Modal for preview -->
    <div id="previewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Certificate Preview</h2>
            <div id="modalPreviewContent" class="bg-white p-4 rounded shadow"></div>
            <div class="modal-buttons mt-6 space-x-3">
                <button onclick="printCertificate()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
                <button onclick="saveAsPDF()" 
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <i class="fas fa-file-pdf mr-2"></i> Save as PDF
                </button>
                <button onclick="saveToDatabase()" 
                        class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <i class="fas fa-save mr-2"></i> Save to Database
                </button>
                <button onclick="closeModal()" 
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <i class="fas fa-times mr-2"></i> Exit
                </button>
            </div>
        </div>
    </div>
    
    <!-- Warning Modal -->
    <div id="warningModal" class="modal">
        <div class="modal-content warning-modal">
            <span class="close" onclick="closeWarningModal()">&times;</span>
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle warning-icon mr-3"></i>
                <h2 class="text-2xl font-bold text-gray-800">Warning: Sensitive Data Access</h2>
            </div>
            <p class="mb-4">You are about to access the MERALCO applicants database which contains sensitive personal information.</p>
            <p class="mb-4"><strong>Please ensure you have proper authorization before proceeding.</strong></p>
            <p class="mb-4">The database will open in <span id="countdown" class="countdown">5</span> seconds.</p>
            <div class="modal-buttons mt-6 space-x-3">
                <button onclick="closeWarningModal()" 
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <i class="fas fa-ban mr-2"></i> Cancel
                </button>
                <button onclick="proceedToDatabase()" 
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <i class="fas fa-check mr-2"></i> I Understand, Proceed
                </button>
            </div>
        </div>
    </div>
    
    <!-- Database Modal -->
    <div id="databaseModal" class="modal">
        <div class="modal-content" style="max-width: 90%;">
            <span class="close" onclick="closeDatabaseModal()">&times;</span>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">MERALCO Applicants Database</h2>
            <div class="mb-4 flex justify-between">
                <div>
                    <input type="text" id="searchDatabase" placeholder="Search applicants..." 
                           class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
                <div>
                    <button onclick="checkDuplicates()" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition flex items-center">
                        <i class="fas fa-search mr-2"></i> Check for Duplicates
                    </button>
                </div>
            </div>
            <div style="max-height: 60vh; overflow-y: auto;">
                <table class="applicant-table">
                    <thead>
                        <tr>
                            <th>Control No.</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Date Issued</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="databaseBody">
                        <tr>
                            <td>2023-001</td>
                            <td>JUAN DELA CRUZ</td>
                            <td>123 Main Street, Pasay City</td>
                            <td>2023-06-15</td>
                            <td>Approved</td>
                            <td>
                                <button class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2023-002</td>
                            <td>MARIA SANTOS</td>
                            <td>456 Oak Avenue, Pasay City</td>
                            <td>2023-06-16</td>
                            <td>Pending</td>
                            <td>
                                <button class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">View</button>
                            </td>
                        </tr>
                        <tr class="duplicate-applicant">
                            <td>2023-003</td>
                            <td>JUAN DELA CRUZ</td>
                            <td>123 Main Street, Pasay City</td>
                            <td>2023-07-01</td>
                            <td>Duplicate</td>
                            <td>
                                <button class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2023-004</td>
                            <td>PEDRO REYES</td>
                            <td>789 Pine Road, Pasay City</td>
                            <td>2023-07-05</td>
                            <td>Approved</td>
                            <td>
                                <button class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-buttons mt-6">
                <button onclick="closeDatabaseModal()" 
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <i class="fas fa-times mr-2"></i> Close Database
                </button>
            </div>
        </div>
    </div>

    <script>
        // Set current year in the form
        document.getElementById('currentYear').textContent = new Date().getFullYear();
        
        // Generate certificate function
        function generateCertificate() {
            const name = document.getElementById('name').value;
            const address = document.getElementById('address').value;
            const controlNumberSuffix = document.getElementById('controlNumber').value;
            const currentYear = new Date().getFullYear();
            
            if (!name || !address || !controlNumberSuffix) {
                alert('Please fill in all fields');
                return;
            }
            
            // Format date as "16th day of June 2025"
            const date = new Date();
            const day = date.getDate();
            const monthNames = ["January", "February", "March", "April", "May", "June", 
                               "July", "August", "September", "October", "November", "December"];
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear();
            
            // Add ordinal suffix to day
            let daySuffix;
            if (day > 3 && day < 21) daySuffix = 'th';
            else {
                switch (day % 10) {
                    case 1: daySuffix = 'st'; break;
                    case 2: daySuffix = 'nd'; break;
                    case 3: daySuffix = 'rd'; break;
                    default: daySuffix = 'th'; break;
                }
            }
            
            const formattedDate = `${day}${daySuffix} day of ${month} ${year}`;
            
            // Update certificate
            document.getElementById('outputName').textContent = name.toUpperCase();
            document.getElementById('outputAddress').textContent = address.toUpperCase();
            document.getElementById('outputControlNumber').textContent = `${currentYear}-${controlNumberSuffix}`;
            document.getElementById('currentDate').textContent = formattedDate;
            
            // Show preview modal
            showPreview();
        }
        
        // Show preview modal
        function showPreview() {
            const modal = document.getElementById('previewModal');
            const certificate = document.getElementById('certificateOutput').cloneNode(true);
            certificate.style.display = 'block';
            certificate.style.margin = '0 auto';
            
            document.getElementById('modalPreviewContent').innerHTML = '';
            document.getElementById('modalPreviewContent').appendChild(certificate);
            
            modal.style.display = 'block';
        }
        
        // Close modal
        function closeModal() {
            document.getElementById('previewModal').style.display = 'none';
        }
        
        // Show warning modal
        function showWarningModal() {
            const modal = document.getElementById('warningModal');
            modal.style.display = 'block';
            
            // Start countdown
            let seconds = 5;
            const countdownElement = document.getElementById('countdown');
            countdownElement.textContent = seconds;
            
            const countdownInterval = setInterval(() => {
                seconds--;
                countdownElement.textContent = seconds;
                
                if (seconds <= 0) {
                    clearInterval(countdownInterval);
                    proceedToDatabase();
                }
            }, 1000);
            
            // Store interval to clear if user cancels
            modal.dataset.countdownInterval = countdownInterval;
        }
        
        // Close warning modal
        function closeWarningModal() {
            const modal = document.getElementById('warningModal');
            clearInterval(modal.dataset.countdownInterval);
            modal.style.display = 'none';
        }
        
        // Proceed to database after warning
        function proceedToDatabase() {
            closeWarningModal();
            const modal = document.getElementById('databaseModal');
            modal.style.display = 'block';
        }
        
        // Close database modal
        function closeDatabaseModal() {
            document.getElementById('databaseModal').style.display = 'none';
        }
        
        // Check for duplicates in the database
        function checkDuplicates() {
            const name = document.getElementById('name').value.toUpperCase();
            const address = document.getElementById('address').value;
            
            if (!name && !address) {
                alert('Please enter name and address to check for duplicates');
                return;
            }
            
            // In a real app, this would query a database
            // For demo, we'll just highlight existing matches in the sample data
            const rows = document.querySelectorAll('#databaseBody tr');
            let foundDuplicate = false;
            
            rows.forEach(row => {
                const rowName = row.cells[1].textContent;
                const rowAddress = row.cells[2].textContent;
                
                if ((name && rowName.includes(name)) || (address && rowAddress.includes(address))) {
                    row.classList.add('duplicate-applicant');
                    foundDuplicate = true;
                } else {
                    row.classList.remove('duplicate-applicant');
                }
            });
            
            if (foundDuplicate) {
                alert('Potential duplicates found! Highlighted in red.');
            } else {
                alert('No duplicates found for this applicant.');
            }
        }
        
        // Save certificate data to database
        function saveToDatabase() {
            const name = document.getElementById('name').value;
            const address = document.getElementById('address').value;
            const controlNumber = document.getElementById('controlNumber').value;
            const currentYear = new Date().getFullYear();
            const fullControlNumber = `${currentYear}-${controlNumber}`;
            const issueDate = new Date().toISOString().split('T')[0];
            
            // In a real application, you would send this data to your backend
            // Here we'll just simulate it and show a success message
            
            // Simulate AJAX call
            setTimeout(() => {
                showSaveSuccess();
                
                // In a real app, you would add the new record to the database table
                // For demo purposes, we'll just add it to the visible table
                const tableBody = document.getElementById('databaseBody');
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${fullControlNumber}</td>
                    <td>${name.toUpperCase()}</td>
                    <td>${address}</td>
                    <td>${issueDate}</td>
                    <td>Approved</td>
                    <td>
                        <button class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">View</button>
                    </td>
                `;
                tableBody.prepend(newRow);
            }, 1000);
        }
        
        // Show save success message
        function showSaveSuccess() {
            const popup = document.getElementById('saveWarning');
            popup.style.display = 'block';
            
            // Hide after 5 seconds
            setTimeout(() => {
                popup.classList.add('fade-out');
                setTimeout(() => {
                    popup.style.display = 'none';
                    popup.classList.remove('fade-out');
                }, 500);
            }, 5000);
        }
        
        // Print certificate with logo fixes
        function printCertificate() {
            const certificate = document.getElementById('certificateOutput').cloneNode(true);
            certificate.id = 'printable-certificate';
            certificate.style.display = 'block';

            // Update image paths to absolute URLs
            const logos = certificate.querySelectorAll('.logo');
            logos[0].src = window.location.origin + '/UDHO%20SYSTEM/assets/PILIPINASLOGO.png';
            logos[1].src = window.location.origin + '/UDHO%20SYSTEM/assets/PASAYLOGO.png';
            logos[2].src = window.location.origin + '/UDHO%20SYSTEM/assets/UDHOLOGO.png';

            const printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.open();
            printWindow.document.write(`
                <html>
                    <head>
                        <title>MERALCO Certification</title>
                        <style>
                            @import url('https://fonts.cdnfonts.com/css/bookman-old-style');

                            @page {
                                size: legal portrait;
                                margin: 1in;
                            }

                            body {
                                font-family: 'Bookman Old Style', serif;
                                margin: 0;
                                padding: 0;
                            }

                            #printable-certificate {
                                width: 100%;
                                height: auto;
                                padding: 0;
                                margin: 0 auto;
                                box-sizing: border-box;
                                page-break-after: avoid;
                            }

                            .header-container {
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                margin-bottom: 10px;
                            }

                            .logo-container {
                                    display: flex;
                                    justify-content: center; /* Center the logos horizontally */
                                    align-items: center; /* Center the logos vertically */
                                    width: 100%;
                                    margin-bottom: 15px;
                                    gap: 20px; /* Add space between logos */
                                }

                                .logo {
                                    height: 80px;
                                    width: auto; /* Maintain aspect ratio */
                                    max-width: 150px; /* Limit maximum width */
                                    object-fit: contain;
                                }

                            .header-text {
                                text-align: center;
                            }

                            .justified-text {
                                text-align: justify;
                                margin: 15px 0;
                                text-indent: 50px;
                            }

                            table {
                                margin-left: auto;
                                margin-right: auto;
                                margin-bottom: 20px;
                            }

                            .footer-right {
                                text-align: right;
                                margin-top: 50px;
                                margin-right: 100px;
                            }
                        </style>
                    </head>
                    <body>
                        ${certificate.outerHTML}
                        <script>
                            // Wait for images to load before printing
                            window.onload = function() {
                                const images = document.querySelectorAll('img');
                                let loadedImages = 0;
                                
                                function tryPrint() {
                                    loadedImages++;
                                    if (loadedImages === images.length) {
                                        setTimeout(function() {
                                            window.print();
                                            setTimeout(function() {
                                                window.close();
                                            }, 500);
                                        }, 200);
                                    }
                                }

                                images.forEach(img => {
                                    if (img.complete) {
                                        tryPrint();
                                    } else {
                                        img.onload = tryPrint;
                                        img.onerror = tryPrint;
                                    }
                                });

                                // Fallback in case there are no images
                                if (images.length === 0) {
                                    setTimeout(function() {
                                        window.print();
                                        setTimeout(function() {
                                            window.close();
                                        }, 500);
                                    }, 200);
                                }
                            };
                        <\/script>
                    </body>
                </html>
            `);
            printWindow.document.close();
        }
        
        // Save as PDF (simulated - in a real implementation you would use a PDF library)
        function saveAsPDF() {
            printCertificate();
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modals = ['previewModal', 'warningModal', 'databaseModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target == modal) {
                    if (modalId === 'warningModal') {
                        closeWarningModal();
                    } else if (modalId === 'databaseModal') {
                        closeDatabaseModal();
                    } else {
                        closeModal();
                    }
                }
            });
        }
        
        // Search functionality for database
        document.getElementById('searchDatabase').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#databaseBody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>