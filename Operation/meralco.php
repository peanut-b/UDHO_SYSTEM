
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

// Add this at the top of your script

date_default_timezone_set('Asia/Manila'); // Set to Philippines timezone

// Database operations
// Database operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'save_certificate':
                $name = $conn->real_escape_string($_POST['name']);
                $address = $conn->real_escape_string($_POST['address']);
                $controlNumber = $conn->real_escape_string($_POST['control_number']);
                
                // First check if control number exists
                $checkControlSql = "SELECT COUNT(*) as count FROM certificates WHERE control_number = '$controlNumber'";
                $checkResult = $conn->query($checkControlSql);
                $row = $checkResult->fetch_assoc();
                
                if ($row['count'] > 0) {
                    echo json_encode(['success' => false, 'message' => 'Control number already exists']);
                    exit;
                }
                
                // Check if applicant has reached limit (2 certificates)
                $checkApplicantSql = "SELECT COUNT(*) as count FROM certificates WHERE name = '$name' AND address = '$address'";
                $checkApplicantResult = $conn->query($checkApplicantSql);
                $applicantRow = $checkApplicantResult->fetch_assoc();
                
                if ($applicantRow['count'] >= 2) {
                    echo json_encode(['success' => false, 'message' => 'Applicant has reached maximum certificate requests (2)']);
                    exit;
                }
                
                // Get current date in MySQL format
                $currentDate = date('Y-m-d H:i:s');
                error_log("Attempting to save certificate with date: " . $currentDate); // Debug logging
                
                // Insert new certificate with current date
                $sql = "INSERT INTO certificates (
                            name, 
                            address, 
                            control_number, 
                            date_issued, 
                            created_at, 
                            request_count, 
                            previous_request_date
                        ) VALUES (
                            '$name', 
                            '$address', 
                            '$controlNumber', 
                            '$currentDate',
                            '$currentDate',
                            (SELECT COUNT(*) + 1 FROM (SELECT * FROM certificates WHERE name = '$name' AND address = '$address') AS temp),
                            (SELECT IFNULL(MAX(date_issued), NULL) FROM (SELECT * FROM certificates WHERE name = '$name' AND address = '$address') AS temp2)
                        )";
                
                if ($conn->query($sql) === TRUE) {
                    error_log("Certificate saved successfully with date: " . $currentDate); // Debug logging
                    echo json_encode(['success' => true, 'message' => 'Certificate saved successfully']);
                } else {
                    error_log("Database error: " . $conn->error); // Debug logging
                    echo json_encode(['success' => false, 'message' => 'Error saving certificate: ' . $conn->error]);
                }
                exit;
        }
    }
}

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check for duplicate control number
    if (isset($_GET['check_duplicate']) && isset($_GET['control_number'])) {
        $controlNumber = $conn->real_escape_string($_GET['control_number']);
        $sql = "SELECT COUNT(*) as count FROM certificates WHERE control_number = '$controlNumber'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        
        echo json_encode(['exists' => $row['count'] > 0]);
        exit;
    }
    
    // Get all certificates for database view
    if (isset($_GET['get_certificates'])) {
        $sql = "SELECT id, name, address, control_number, DATE_FORMAT(date_issued, '%M %d, %Y') as date_issued, 
                created_at FROM certificates ORDER BY created_at DESC";
        $result = $conn->query($sql);
        $certificates = [];
        
        while ($row = $result->fetch_assoc()) {
            $certificates[] = $row;
        }
        
        echo json_encode($certificates);
        exit;
    }
    
    // Verify applicant
    if (isset($_GET['verify_applicant'])) {
        $name = $conn->real_escape_string($_GET['name']);
        
        
        // Get applicant history
        $sql = "SELECT id, name, address, control_number, 
                DATE_FORMAT(date_issued, '%M %d, %Y') as date_issued 
                FROM certificates 
                WHERE name LIKE '%$name%' AND address LIKE '%$address%' 
                ORDER BY date_issued DESC";
        
        $result = $conn->query($sql);
        $history = [];
        $count = 0;
        
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
            $count++;
        }
        
        echo json_encode([
            'count' => $count,
            'history' => $history
        ]);
        exit;
    }
}
    
?>

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
        line-height: 1.6; /* Add this for better readability */
    }

.justified-text p {
    margin-bottom: 20px; /* Add space between paragraphs */
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

        /* Error modal styles */
        .error-modal {
            background-color: #f8d7da;
            border-left: 6px solid #dc3545;
            max-width: 500px;
        }

        .error-icon {
            color: #dc3545;
            font-size: 2rem;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            transition: opacity 0.3s ease;
        }
        
        .modal-content {
            margin: 15% auto;
            animation: modalFadeIn 0.3s;
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .modal.show {
            display: block;
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
        
        /* Add these to your existing CSS */
        #verificationStatus {
            padding: 8px 12px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #4CAF50;
        }
        
        #applicantHistoryModal .modal-content {
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .history-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .history-table th, .history-table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .history-table th {
            background-color: #f3f4f6;
            font-weight: 600;
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
              <img src="/assets/profile_pictures/<?php echo htmlspecialchars($profilePicture); ?>" 
                  alt="Profile Picture" 
                  class="w-full h-full object-cover"
                  onerror="this.src='/assets/PROFILE_SAMPLE.jpg'">
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
                    <a href="meralco.php" class="flex items-center py-2.5 px-4 hover:bg-gray-700">
                        <i class="fa-solid fa-file mr-3"></i> Certificate
                    </a>
                <li>
                    <a href="meralco_database.php" class="flex items-center py-2.5 px-4 hover:bg-gray-700">
                        <i class="fa-solid fa-database mr-3"></i> Database
                    </a>
                </li>
                <li>
                    <a href="Settings/setting_operation.php" class="flex items-center py-2.5 px-4 hover:bg-gray-700">
                        <i class="fas fa-cog mr-3"></i> Settings
                    </a>
                </li>
                <li>
                    <a href="#" id="logoutBtn" class="flex items-center py-2.5 px-4 hover:bg-gray-700 mt-10">
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
                <img src="/assets/UDHOLOGO.png" alt="Logo" class="h-8">
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
            
                        <!-- Add this div for verification status -->
                    <div id="verificationStatus" class="hidden">
                        <div class="flex items-center gap-2 text-sm">
                            <span id="certificateCount" class="font-semibold">0</span>
                            <span>certificates previously issued to this applicant</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-between">
                    <button onclick="verifyApplicant()" 
                            class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <i class="fas fa-search mr-2"></i> Verify Applicant
                    </button>
                    
                    <button onclick="generateCertificate()" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-file-certificate mr-2"></i> Generate Certificate
                    </button>
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
                <img src="/assets/PILIPINASLOGO.png" alt="Pasay Logo" class="logo">
                <img src="/assets/PASAYLOGO.png" alt="Bagong Pilipinas" class="logo">
                <img src="/assets/UDHOLOGO.png" alt="UDHO Logo" class="logo">
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
    
    <!-- Error Modal for missing fields -->
    <div id="errorModal" class="modal">
        <div class="modal-content error-modal">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle error-icon mr-3"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Missing Required Fields</h3>
                    <p id="errorMessage" class="text-gray-700">Please fill in all required fields before generating the certificate.</p>
                    <div class="modal-buttons mt-4">
                        <button onclick="closeErrorModal()" 
                                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            <i class="fas fa-times mr-2"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal">
    <div class="modal-content bg-white rounded-lg shadow-xl max-w-md mx-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Confirm Logout</h3>
            <span class="close-modal text-gray-500 hover:text-gray-700 cursor-pointer text-2xl">&times;</span>
        </div>
        <div class="mb-6">
            <p class="text-gray-700">Are you sure you want to logout?</p>
        </div>
        <div class="flex justify-end space-x-3">
            <button id="cancelLogout" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                Cancel
            </button>
            <button id="confirmLogout" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                Logout
            </button>
        </div>
    </div>
</div>

<!-- Add this modal before your existing scripts -->
<div id="applicantHistoryModal" class="modal">
    <div class="modal-content bg-white rounded-lg shadow-xl max-w-2xl mx-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Applicant Verification</h3>
            <span class="close-modal text-gray-500 hover:text-gray-700 cursor-pointer text-2xl" 
                  onclick="closeApplicantHistoryModal()">&times;</span>
        </div>
        
        <div class="mb-4">
            <h4 class="font-medium text-gray-700" id="applicantName"></h4>
            <p class="text-sm text-gray-600" id="applicantAddress"></p>
            <p class="mt-2 font-medium" id="certificateCountBadge"></p>
        </div>
        
        <div class="border rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Control Number</th>
                        <th class="px-4 py-2 text-left">Date Issued</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody" class="divide-y divide-gray-200">
                    <!-- History will be populated here -->
                </tbody>
            </table>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button onclick="closeApplicantHistoryModal()" 
                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                <i class="fas fa-times mr-2"></i> Close
            </button>
        </div>
    </div>
</div>
    
    <!-- Database Modal -->
    <!-- Add these before your existing scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // Set current year in the form
        document.getElementById('currentYear').textContent = new Date().getFullYear();
        
        const { jsPDF } = window.jspdf;
        
        // Show error modal
        function showErrorModal(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').style.display = 'block';
        }
        
        // Close error modal
        function closeErrorModal() {
            document.getElementById('errorModal').style.display = 'none';
        }
        
        // Generate certificate function
        function generateCertificate() {
            const name = document.getElementById('name').value;
            const address = document.getElementById('address').value;
            const controlNumberSuffix = document.getElementById('controlNumber').value;
            const currentYear = new Date().getFullYear();
            
            // Validate required fields
            if (!name) {
                showErrorModal('Please enter the full name of the applicant');
                document.getElementById('name').focus();
                return;
            }
            
            if (!address) {
                showErrorModal('Please enter the complete address of the applicant');
                document.getElementById('address').focus();
                return;
            }
            
            if (!controlNumberSuffix) {
                showErrorModal('Please enter the control number suffix');
                document.getElementById('controlNumber').focus();
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
        
        // Print certificate with logo fixes
        function printCertificate() {
            const certificate = document.getElementById('certificateOutput').cloneNode(true);
            certificate.id = 'printable-certificate';
            certificate.style.display = 'block';

            // Update image paths to absolute URLs
            const logos = certificate.querySelectorAll('.logo');
            logos[0].src = window.location.origin + '/assets/PILIPINASLOGO.png';
            logos[1].src = window.location.origin + '/assets/PASAYLOGO.png';
            logos[2].src = window.location.origin + '/assets/UDHOLOGO.png';

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
        // Initialize jsPDF
     
                // Save as PDF using html2canvas and jsPDF
async function saveAsPDF() {
    const element = document.getElementById('certificateOutput');
    const saveBtn = document.querySelector('button[onclick="saveAsPDF()"]');
    let originalText = '';
    
    try {
        // Store original button state
        if (saveBtn) {
            originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generating PDF...';
            saveBtn.disabled = true;
        }

        // Create a hidden container for processing
        const hiddenContainer = document.createElement('div');
        hiddenContainer.style.position = 'fixed';
        hiddenContainer.style.left = '-9999px';
        hiddenContainer.style.top = '0';
        hiddenContainer.style.width = '8.5in';
        hiddenContainer.style.height = '14in';
        document.body.appendChild(hiddenContainer);

        // Clone the certificate and add to hidden container
        const clone = element.cloneNode(true);
        clone.style.display = 'block';
        clone.style.visibility = 'visible';
        clone.style.position = 'relative';
        clone.style.left = '0';
        clone.style.width = '100%';
        clone.style.height = '100%';
        hiddenContainer.appendChild(clone);

        // Wait for images to load
        await loadAllImages(clone);

        // Generate canvas with proper dimensions
        const canvas = await html2canvas(clone, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#FFFFFF',
            logging: false,
            width: 850,  // 8.5in * 100dpi
            height: 1400, // 14in * 100dpi
            windowWidth: 850,
            windowHeight: 1400
        });

        // Remove the hidden container
        document.body.removeChild(hiddenContainer);

        // Create PDF with correct dimensions
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'in',
            format: [8.5, 14]
        });

        // Add image to PDF with proper scaling
        const imgData = canvas.toDataURL('image/png');
        pdf.addImage(imgData, 'PNG', 0, 0, 8.5, 14);

        // Generate filename
        const name = document.getElementById('name').value.replace(/\s+/g, '_');
        const controlNumber = document.getElementById('outputControlNumber').textContent;
        const filename = `MERALCO_Certificate_${name}_${controlNumber}.pdf`;

        // Save PDF directly
        pdf.save(filename);

    } catch (error) {
        console.error('PDF generation failed:', error);
        showErrorModal('PDF generation failed. Please try again or use the print option.');
    } finally {
        // Restore button state
        if (saveBtn) {
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        }
    }
}

// Helper function to ensure all images are loaded
        function loadAllImages(element) {
            const images = element.getElementsByTagName('img');
            const promises = [];
            
            for (let img of images) {
                if (!img.complete) {
                    promises.push(new Promise((resolve) => {
                        img.onload = resolve;
                        img.onerror = resolve; // Continue even if some images fail
                    }));
                }
            }
            
            return Promise.all(promises);
        }
                // Save to database
        function saveToDatabase() {
            const name = document.getElementById('name').value;
            const address = document.getElementById('address').value;
            const currentYear = new Date().getFullYear();
            const controlNumberSuffix = document.getElementById('controlNumber').value;
            const controlNumber = `${currentYear}-${controlNumberSuffix}`;

            // Check for duplicates first
            fetch(`?check_duplicate=1&control_number=${controlNumber}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        showErrorModal('This control number already exists in the database. Please use a different number.');
                        return;
                    }

                    // Proceed with saving
                    const formData = new FormData();
                    formData.append('action', 'save_certificate');
                    formData.append('name', name);
                    formData.append('address', address);
                    formData.append('control_number', controlNumber);

                    return fetch('', {
                        method: 'POST',
                        body: formData
                    });
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSaveSuccess('Certificate data saved to database!');
                    } else {
                        showErrorModal(data.message || 'Error saving certificate to database');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorModal('An error occurred while saving to database');
                });
        }

        // Show save success message
        function showSaveSuccess(message) {
            const popup = document.getElementById('saveWarning');
            const messageSpan = document.getElementById('saveMessage');
            
            messageSpan.textContent = message;
            popup.style.display = 'block';
            
            setTimeout(() => {
                popup.classList.add('fade-out');
                setTimeout(() => {
                    popup.style.display = 'none';
                    popup.classList.remove('fade-out');
                }, 500);
            }, 3000);
        }
        // Logout functionality
        // Logout Modal Functionality
        const logoutBtn = document.getElementById('logoutBtn');
        const logoutModal = document.getElementById('logoutModal');
        const closeModalBtn = document.querySelector('.close-modal');
        const cancelLogoutBtn = document.getElementById('cancelLogout');
        const confirmLogoutBtn = document.getElementById('confirmLogout');
        
        // Open modal when logout button is clicked
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            logoutModal.classList.add('show');
        });
        
        // Close modal when X is clicked
        closeModalBtn.addEventListener('click', function() {
            logoutModal.classList.remove('show');
        });
        
        // Close modal when Cancel is clicked
        cancelLogoutBtn.addEventListener('click', function() {
            logoutModal.classList.remove('show');
        });
        
        // Handle logout confirmation
        confirmLogoutBtn.addEventListener('click', function() {
            // Create a form to submit the logout request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/logout.php';
            
            // Submit the form
            document.body.appendChild(form);
            form.submit();
        });
        
        // Close modal when clicking outside the modal content
        window.addEventListener('click', function(e) {
            if (e.target === logoutModal) {
                logoutModal.classList.remove('show');
            }
        });
        
        confirmLogoutBtn.addEventListener('click', function() {
            // Show loading spinner
            confirmLogoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Logging out...';
            confirmLogoutBtn.disabled = true;
            
            setTimeout(() => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/logout.php';
                document.body.appendChild(form);
                form.submit();
            }, 500); // Small delay to show the spinner
        });
        
                document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && logoutModal.classList.contains('show')) {
                logoutModal.classList.remove('show');
            }
        });
        
        // Add these functions to your existing JavaScript

// Verify applicant function
    function verifyApplicant() {
        const name = document.getElementById('name').value;
        
        
        if (!name) {
            showErrorModal('Please enter Full Name to verify');
            return;
        }
        
        const verifyBtn = document.querySelector('button[onclick="verifyApplicant()"]');
        const originalText = verifyBtn.innerHTML;
        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Verifying...';
        verifyBtn.disabled = true;
        
        fetch(`?verify_applicant=1&name=${encodeURIComponent(name)}&address=${encodeURIComponent(address)}`)
            .then(response => response.json())
            .then(data => {
                // Update verification status
                document.getElementById('verificationStatus').classList.remove('hidden');
                document.getElementById('certificateCount').textContent = data.count;
                
                // Show detailed history in modal
                showApplicantHistory(name, address, data);
            })
            .catch(error => {
                console.error('Verification failed:', error);
                showErrorModal('Verification failed. Please try again.');
            })
            .finally(() => {
                verifyBtn.innerHTML = originalText;
                verifyBtn.disabled = false;
            });
    }

// Show applicant history modal
function showApplicantHistory(name, address, data) {
    document.getElementById('applicantName').textContent = name;

    
    const countBadge = document.getElementById('certificateCountBadge');
    if (data.count > 0) {
        countBadge.innerHTML = `<span class="px-2 py-1 rounded-full ${data.count > 1 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}">
            ${data.count} certificate(s) found
        </span>`;
    } else {
        countBadge.innerHTML = '<span class="px-2 py-1 rounded-full bg-blue-100 text-blue-800">No previous certificates found</span>';
    }
    
    const tbody = document.getElementById('historyTableBody');
    tbody.innerHTML = '';
    
    if (data.count > 0) {
        data.history.forEach(cert => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-4 py-2">${cert.control_number}</td>
                <td class="px-4 py-2">${cert.date_issued}</td>
            `;
            tbody.appendChild(row);
        });
    } else {
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="2" class="px-4 py-2 text-center text-gray-500">No previous certificates found for this applicant</td>';
        tbody.appendChild(row);
    }
    
    document.getElementById('applicantHistoryModal').style.display = 'block';
}

// Close applicant history modal
function closeApplicantHistoryModal() {
    document.getElementById('applicantHistoryModal').style.display = 'none';
}
    </script>
</body>
</html>