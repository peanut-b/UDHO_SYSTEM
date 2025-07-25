<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Housing Survey Form</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Add barcode and QR code libraries -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .form-container {
            max-width: 1000px;
            margin: 0 auto;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3), 0 2px 6px 2px rgba(60,64,67,0.15);
            width: 100%;
        }
        
        .form-header {
            border-bottom: 8px solid #673ab7;
            padding-bottom: 8px;
        }
        
        .form-title {
            color: #202124;
            font-size: clamp(24px, 3vw, 32px);
            font-weight: 400;
        }
        
        .form-description {
            color: #5f6368;
            font-size: clamp(12px, 2vw, 14px);
        }
        
        .section-title {
            color: #202124;
            font-size: clamp(18px, 2.5vw, 20px);
            font-weight: 500;
            border-bottom: 1px solid #dadce0;
            padding-bottom: 6px;
            margin-bottom: 16px;
        }
        
        .question-title {
            color: #202124;
            font-size: clamp(14px, 2vw, 16px);
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 1px solid #dadce0;
            border-radius: 4px;
            padding: 8px 12px;
            width: 100%;
            font-size: 14px;
        }
        
        .form-control:focus {
            border-color: #673ab7;
            box-shadow: 0 0 0 2px rgba(103,58,183,0.2);
        }
        
        #signature-pad {
            border: 1px solid #dadce0;
            cursor: crosshair;
            background-color: white;
            touch-action: none;
            width: 100%;
            min-height: 150px;
            border-radius: 4px;
        }
        
        #map {
            height: 300px;
            width: 100%;
            border-radius: 4px;
            border: 1px solid #dadce0;
        }
        
        .location-confirm-btn {
            background-color: #f1f3f4;
            color: #3c4043;
            border: 1px solid #dadce0;
            border-radius: 4px;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            margin-right: 8px;
        }
        
        .location-confirm-btn:hover {
            background-color: #e8eaed;
        }
        
        .location-confirm-btn.confirmed {
            background-color: #e6f4ea;
            color: #137333;
            border-color: #137333;
        }
        
        .location-confirm-btn.denied {
            background-color: #fce8e6;
            color: #d93025;
            border-color: #d93025;
        }
        
        .required-field::after {
            content: " *";
            color: #d93025;
        }
        
        .photo-container {
            position: relative;
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .delete-photo-btn {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: #d93025;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            font-weight: bold;
        }
        
        .photo-thumbnail {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 2px solid #dadce0;
            border-radius: 4px;
        }
        
        .photos-container {
            display: flex;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        
        .map-instructions {
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 8px;
            font-size: 14px;
            color: #5f6368;
        }
        
        .location-accuracy {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
        
        .location-accuracy.high {
            color: #137333;
        }
        
        .location-accuracy.medium {
            color: #E67C00;
        }
        
        .location-accuracy.low {
            color: #D93025;
        }
        
        .location-loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(0,0,0,0.1);
            border-radius: 50%;
            border-top-color: #4285F4;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive table styles */
        .responsive-table {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Button styles for better mobile experience */
        .form-button {
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap;
        }
        
        .form-button:active {
            transform: scale(0.98);
        }
        
        /* Camera modal styles */
        .camera-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
            z-index: 1000;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .camera-container {
            width: 100%;
            max-width: 600px;
            position: relative;
        }
        
        .camera-view {
            width: 100%;
            background-color: black;
        }
        
        .camera-preview {
            width: 100%;
            display: none;
        }
        
        .camera-controls {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        
        .camera-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .capture-btn {
            background-color: #fff;
        }
        
        .switch-camera-btn, .retake-btn, .confirm-btn {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }
        
        /* Confirmation modal */
        .confirmation-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .confirmation-box {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        /* Adjust padding for smaller screens */
        @media (max-width: 768px) {
            .form-container {
                padding: 15px;
            }
            
            .photo-thumbnail {
                width: 100px;
                height: 100px;
            }
            
            #map {
                height: 250px;
            }
            
            #signature-pad {
                min-height: 120px;
            }
        }
        
        @media (max-width: 480px) {
            .form-container {
                padding: 10px;
            }
            
            .photo-thumbnail {
                width: 80px;
                height: 80px;
            }
            
            #map {
                height: 200px;
            }
            
            .form-button {
                padding: 8px 16px;
                font-size: 14px;
            }
            
            .grid-cols-1 > div {
                margin-bottom: 10px;
            }
        }
        .member-row {
            transition: all 0.3s ease;
        }
        .member-row:hover {
            background-color: #f8f9fa;
        }
        .remove-member-btn {
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 2px 8px;
            cursor: pointer;
            font-size: 12px;
        }
        .location-details {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
            border: 1px solid #e0e0e0;
        }
        
        /* Code display styles */
        .code-display-container {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .code-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        
        .code-input-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .code-input-group {
            display: flex;
            flex-direction: column;
        }
        
        .code-label {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 5px;
            color: #555;
        }
        
        .code-input {
            padding: 8px 12px;
            border: 1px solid #dadce0;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .barcode-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }
        
        .barcode-display {
            text-align: center;
            padding: 10px;
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
        }
        
        .barcode-label {
            font-size: 14px;
            margin-bottom: 5px;
            color: #555;
        }
        
        .barcode-svg {
            width: 100%;
            height: 50px;
        }
        
        .qr-code-container {
            margin-top: 15px;
            text-align: center;
            padding: 10px;
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
        }
        
        .qr-code-label {
            font-size: 14px;
            margin-bottom: 5px;
            color: #555;
        }
        
        .qr-code-canvas {
            margin: 0 auto;
        }
        
        @media (max-width: 768px) {
            .code-input-container,
            .barcode-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="bg-gray-100 p-2 md:p-4 lg:p-8">

    <!-- Camera Modal -->
    <div id="camera-modal" class="camera-modal">
        <div class="camera-container">
            <video id="camera-view" class="camera-view" autoplay playsinline></video>
            <canvas id="camera-preview" class="camera-preview"></canvas>
            
            <div class="camera-controls">
                <button id="switch-camera-btn" class="camera-btn switch-camera-btn" title="Switch Camera">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h7a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7M5 16H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1"></path>
                        <polyline points="16 16 12 12 16 8"></polyline>
                        <polyline points="8 8 12 12 8 16"></polyline>
                    </svg>
                </button>
                <button id="capture-btn" class="camera-btn capture-btn" title="Take Photo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                    </svg>
                </button>
                <button id="retake-btn" class="camera-btn retake-btn" title="Retake" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="1 4 1 10 7 10"></polyline>
                        <polyline points="23 20 23 14 17 14"></polyline>
                        <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path>
                    </svg>
                </button>
                <button id="confirm-btn" class="camera-btn confirm-btn" title="Confirm" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal" class="confirmation-modal">
        <div class="confirmation-box">
            <h3 class="text-lg font-medium mb-4">Confirm Submission</h3>
            <p class="mb-4">Are you sure all the information you provided is accurate and complete?</p>
            <div class="flex justify-end gap-3">
                <button id="cancel-submission-btn" class="form-button bg-gray-400 hover:bg-gray-500 text-white font-medium">
                    Cancel
                </button>
                <button id="confirm-submission-btn" class="form-button bg-indigo-600 hover:bg-indigo-700 text-white font-medium">
                    Confirm Submission
                </button>
            </div>
        </div>
    </div>

    <div id="form-container" class="form-container bg-white rounded-lg mx-2 md:mx-auto">
        <div class="p-4 md:p-6 lg:p-8">
            <div class="form-header mb-6 md:mb-8">
                <h1 class="form-title mb-2">Housing Survey Form</h1>
                <p class="form-description">This survey collects information about housing conditions and needs for urban development planning.</p>
            </div>

            <!-- UD Code and TAG Number Section -->
            <div id="tag-number-section" class="mb-6">
                <div class="code-display-container">
                    <h2 class="section-title">Identification Codes</h2>
                    
                    <div class="code-input-container">
                        <div class="code-input-group">
                            <label for="ud-code" class="code-label required-field">UD Code (YEAR-BRGY-NO.)</label>
                            <input type="text" id="ud-code" class="code-input" placeholder="e.g., 2023-BRGY-001" required>
                        </div>
                        
                        <div class="code-input-group">
                            <label for="tag-number" class="code-label required-field">TAG Number</label>
                            <input type="text" id="tag-number" class="code-input" placeholder="e.g., 2025-BRGY-001" required 
                                pattern="^20[2-9][0-9]-\d{3}-\d{3}$"
                                title="Format: YYYY-BBB-NNN (e.g., 2025-147-001)">
                            <small class="text-gray-500">Format: YYYY-BBB-NNN (e.g., 2025-147-001)</small>
                        </div>
                    </div>
                    
                    <div class="barcode-container">
                        <div class="barcode-display">
                            <div class="barcode-label">UD Code Barcode</div>
                            <svg id="ud-code-barcode" class="barcode-svg"></svg>
                        </div>
                        
                        <div class="barcode-display">
                            <div class="barcode-label">TAG Number Barcode</div>
                            <svg id="tag-number-barcode" class="barcode-svg"></svg>
                        </div>
                    </div>
            
                    <div class="qr-code-container">
                        <div class="qr-code-label">Survey QR Code</div>
                        <div id="survey-qr-code" class="qr-code-canvas"></div>
                    </div>
                </div>
            </div>
            
            <!-- Location Section (Moved to top) -->
            <div id="location-section" class="mb-6 md:mb-8 p-4 md:p-6 bg-white rounded-lg border border-gray-200">
                <h2 class="section-title">Location Information</h2>
                <div class="mb-4">
                    <p class="question-title">Please confirm your location:</p>
                    <div class="map-instructions">
                        <p>We'll first try to get your device's location. If it's not accurate, you can manually place a marker.</p>
                    </div>
                    <div id="map" class="mb-4"></div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center mb-4">
                        <div class="flex-1 mb-2 sm:mb-0">
                            <p id="location-text" class="text-sm text-gray-600 mr-4">
                                <span id="location-status">Getting your location...</span>
                                <span id="location-accuracy" class="location-accuracy"></span>
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button id="get-location-btn" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded mr-2">
                                <span id="location-loading" class="location-loading hidden"></span>
                                Get My Location
                            </button>
                            <button id="confirm-location-btn" class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded">
                                Confirm Location
                            </button>
                        </div>
                    </div>
                    <div id="manual-location" class="hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                            <div>
                                <label for="manual-address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <input type="text" id="manual-address" class="form-control">
                            </div>
                            <div>
                                <label for="manual-city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" id="manual-city" class="form-control" value="Pasay City" readonly>
                            </div>
                            <div>
                                <label for="manual-barangay" class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                                <input type="text" id="manual-barangay" class="form-control">
                            </div>
                            <div>
                                <label for="manual-zone" class="block text-sm font-medium text-gray-700 mb-1">Zone</label>
                                <input type="text" id="manual-zone" class="form-control">
                            </div>
                        </div>
                        <button id="save-manual-location-btn" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded">
                            Save Location Details
                        </button>
                    </div>
                </div>
            </div>

            <div id="location-details" class="location-details hidden">
                <h3 class="font-medium mb-2">Confirmed Location Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div>
                        <p class="text-sm font-medium">Coordinates:</p>
                        <p id="confirmed-coords" class="text-sm"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Address:</p>
                        <p id="confirmed-address" class="text-sm"></p>
                    </div>
                </div>
            </div>

            <!-- Form Pages -->
            <div id="form-page-1" class="form-page">
                <!-- Section I: Personal Data -->
                        

                <!-- Personal Data Section -->
                <div class="mb-6 md:mb-8">
                    <h2 class="section-title">I. Personal Data</h2>
                    
                    <div class="mb-4 md:mb-6">
                        <p class="question-title required-field">Name of Household Head</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4">
                            <div>
                                <label for="hh-surname" class="block text-xs text-gray-600 mb-1">Surname</label>
                                <input type="text" id="hh-surname" class="form-control" required>
                            </div>
                            <div>
                                <label for="hh-firstname" class="block text-xs text-gray-600 mb-1">First Name</label>
                                <input type="text" id="hh-firstname" class="form-control" required>
                            </div>
                            <div>
                                <label for="hh-middlename" class="block text-xs text-gray-600 mb-1">Middle Name</label>
                                <input type="text" id="hh-middlename" class="form-control">
                            </div>
                            <div>
                                <label for="hh-mi" class="block text-xs text-gray-600 mb-1">MI</label>
                                <input type="text" id="hh-mi" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 md:mb-6">
                        <p class="question-title">Name of Spouse</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4">
                            <div>
                                <label for="spouse-surname" class="block text-xs text-gray-600 mb-1">Surname</label>
                                <input type="text" id="spouse-surname" class="form-control">
                            </div>
                            <div>
                                <label for="spouse-firstname" class="block text-xs text-gray-600 mb-1">First Name</label>
                                <input type="text" id="spouse-firstname" class="form-control">
                            </div>
                            <div>
                                <label for="spouse-middlename" class="block text-xs text-gray-600 mb-1">Middle Name</label>
                                <input type="text" id="spouse-middlename" class="form-control">
                            </div>
                            <div>
                                <label for="spouse-mi" class="block text-xs text-gray-600 mb-1">MI</label>
                                <input type="text" id="spouse-mi" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div>
                            <p class="question-title required-field">Household Head Data</p>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-y-2 gap-x-3 mb-3 md:mb-4">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Sex</label>
                                    <select id="hh-sex" class="form-control" required>
                                        <option value="">Select</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Age</label>
                                    <input type="number" id="hh-age" class="form-control" required>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Birthdate</label>
                                    <input type="date" id="hh-birthdate" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3 md:mb-4">
                                <label class="block text-xs text-gray-600 mb-1">Civil Status</label>
                                <div class="flex flex-wrap gap-x-3 gap-y-1 text-sm">
                                    <label class="inline-flex items-center"><input type="radio" name="hh-civil-status" class="form-radio h-4 w-4 text-indigo-600" required> <span class="ml-2">Single</span></label>
                                    <label class="inline-flex items-center"><input type="radio" name="hh-civil-status" class="form-radio h-4 w-4 text-indigo-600"> <span class="ml-2">Married</span></label>
                                    <label class="inline-flex items-center"><input type="radio" name="hh-civil-status" class="form-radio h-4 w-4 text-indigo-600"> <span class="ml-2">Widow/Widower</span></label>
                                    <label class="inline-flex items-center"><input type="radio" name="hh-civil-status" class="form-radio h-4 w-4 text-indigo-600"> <span class="ml-2">Solo Parent</span></label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Vulnerability</label>
                                <div class="flex flex-wrap gap-x-3 gap-y-1 text-sm">
                                    <label class="inline-flex items-center"><input type="checkbox" id="hh-senior-citizen" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Senior Citizen</span></label>
                                    <label class="inline-flex items-center"><input type="checkbox" id="hh-pwd" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">PWD</span></label>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="question-title">Spouse Data</p>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-y-2 gap-x-3 mb-3 md:mb-4">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Sex</label>
                                    <select id="spouse-sex" class="form-control">
                                        <option value="">Select</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Age</label>
                                    <input type="number" id="spouse-age" class="form-control">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Birthdate</label>
                                    <input type="date" id="spouse-birthdate" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3 md:mb-4">
                                <label class="block text-xs text-gray-600 mb-1">Civil Status</label>
                                <div class="flex flex-wrap gap-x-3 gap-y-1 text-sm">
                                    <label class="inline-flex items-center"><input type="radio" name="spouse-civil-status" class="form-radio h-4 w-4 text-indigo-600"> <span class="ml-2">Single</span></label>
                                    <label class="inline-flex items-center"><input type="radio" name="spouse-civil-status" class="form-radio h-4 w-4 text-indigo-600"> <span class="ml-2">Married</span></label>
                                    <label class="inline-flex items-center"><input type="radio" name="spouse-civil-status" class="form-radio h-4 w-4 text-indigo-600"> <span class="ml-2">Widow/Widower</span></label>
                                    <label class="inline-flex items-center"><input type="radio" name="spouse-civil-status" class="form-radio h-4 w-4 text-indigo-600"> <span class="ml-2">Solo Parent</span></label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Vulnerability</label>
                                <div class="flex flex-wrap gap-x-3 gap-y-1 text-sm">
                                    <label class="inline-flex items-center"><input type="checkbox" id="spouse-senior-citizen" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Senior Citizen</span></label>
                                    <label class="inline-flex items-center"><input type="checkbox" id="spouse-pwd" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">PWD</span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section II: Tenurial Status -->
                <div class="mb-6 md:mb-8">
                    <h2 class="section-title">II. Tenurial Status</h2>
                    
                    <div class="mb-4 md:mb-6">
                        <p class="question-title required-field">Residential Address</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-4 mb-3 md:mb-4 text-sm">
                            <div>
                                <label for="house-no" class="block text-xs text-gray-600 mb-1">House No.</label>
                                <input type="text" id="house-no" class="form-control">
                            </div>
                            <div>
                                <label for="lot-no" class="block text-xs text-gray-600 mb-1">Lot No.</label>
                                <input type="text" id="lot-no" class="form-control">
                            </div>
                            <div>
                                <label for="building" class="block text-xs text-gray-600 mb-1">Building</label>
                                <input type="text" id="building" class="form-control">
                            </div>
                            <div>
                                <label for="block" class="block text-xs text-gray-600 mb-1">Block</label>
                                <input type="text" id="block" class="form-control">
                            </div>
                            <div>
                                <label for="street" class="block text-xs text-gray-600 mb-1">Street</label>
                                <input type="text" id="street" class="form-control" required>
                            </div>
                            <div>
                                <label for="barangay" class="block text-xs text-gray-600 mb-1">Barangay</label>
                                <input type="text" id="barangay" class="form-control" required>
                            </div>
                            <div>
                                <label for="city" class="block text-xs text-gray-600 mb-1">City</label>
                                <input type="text" id="city" class="form-control" value="Pasay City" readonly>
                            </div>
                            <div>
                                <label for="region" class="block text-xs text-gray-600 mb-1">Region</label>
                                <input type="text" id="region" class="form-control" value="NCR" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-4 md:mb-6">
                        <div>
                            <p class="question-title required-field">Nature of Land Occupied</p>
                            <div class="flex flex-col gap-1 md:gap-2">
                                <label class="inline-flex items-center"><input type="radio" name="land-nature" class="form-radio h-4 w-4 text-indigo-600" required> <span class="ml-2">Private</span></label>
                                <label class="inline-flex items-center"><input type="radio" name="land-nature" class="form-radio h-4 w-4 text-indigo-600"> <span class="ml-2">Government</span></label>
                                <label class="inline-flex items-center"><input type="radio" name="land-nature" class="form-radio h-4 w-4 text-indigo-600"> <span class="ml-2">Others</span></label>
                            </div>
                        </div>
                        <div>
                            <p class="question-title required-field">Lot Status</p>
                            <div class="flex flex-col gap-1 md:gap-2">
                                <label class="inline-flex items-center"><input type="radio" name="lot-status" class="form-radio h-4 w-4 text-indigo-600" required> <span class="ml-2">Lot Owner</span></label>
                                <label class="inline-flex items-center"><input type="radio" name="lot-status" class="form-radio h-4 w-4 text-indigo-600"> <span class="ml-2">Lot not occupied (sqm/ha)</span></label>
                                <label class="inline-flex items-center"><input type="radio" name="lot-status" class="form-radio h-4 w-4 text-indigo-600"> <span class="ml-2">Renter (< 5 years)</span></label>
                                <label class="inline-flex items-center"><input type="radio" name="lot-status" class="form-radio h-4 w-4 text-indigo-600"> <span class="ml-2">Renter (> 5 years)</span></label>
                                <label class="inline-flex items-center"><input type="radio" name="lot-status" class="form-radio h-4 w-4 text-indigo-600"> <span class="ml-2">Rent Free Owner</span></label>
                                <label class="inline-flex items-center"><input type="radio" name="lot-status" class="form-radio h-4 w-4 text-indigo-600"> <span class="ml-2">Co-Owner</span></label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="name-rfo-renter" class="question-title">Name of Owner (for RFO/Renter):</label>
                        <input type="text" id="name-rfo-renter" class="form-control">
                    </div>
                </div>

                <div class="mt-4 md:mt-6 flex justify-end">
                    <button id="next-page-1-btn" class="form-button bg-blue-500 hover:bg-blue-600 text-white font-medium">
                        Next
                    </button>
                </div>
            </div>

            <div id="form-page-2" class="form-page hidden">
                <!-- Section III: Membership -->
                <div class="mb-6 md:mb-8">
                    <h2 class="section-title">III. Membership</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div>
                            <p class="question-title">Fund</p>
                            <div class="flex flex-col gap-1 md:gap-2">
                                <label class="inline-flex items-center"><input type="checkbox" id="pagibig" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">PAG-IBIG</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="sss" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">SSS</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="gsis" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">GSIS</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="philhealth" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">PhilHealth</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="none-fund" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">None</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="other-fund" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Others</span></label>
                            </div>
                        </div>
                        <div>
                            <p class="question-title">Organization</p>
                            <div class="flex flex-col gap-1 md:gap-2">
                                <label class="inline-flex items-center"><input type="checkbox" id="cso" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">CSO</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="hoa" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">HOA</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="cooperative" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Cooperative</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="none-org" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">None</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="other-org" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Others</span></label>
                            </div>
                            <div class="mt-3 md:mt-4">
                                <label for="name-organization" class="question-title">Name of Organization</label>
                                <input type="text" id="name-organization" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section IV: Household Member Data -->
                <div class="mb-6 md:mb-8">
                    <h2 class="section-title">IV. Household Member Data</h2>
                    <p class="text-sm text-gray-600 mb-3 md:mb-4">List all household members including the head and spouse</p>

                </div>
                    
                    <!-- Replace the existing table in Section IV with this -->
                <div class="responsive-table mb-4">
                    <table id="member-table" class="min-w-full bg-white border border-gray-200 text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="py-2 px-3 border-b text-left text-gray-600 font-medium">NAME</th>
                                <th class="py-2 px-3 border-b text-left text-gray-600 font-medium">RELATIONSHIP TO HEAD</th>
                                <th class="py-2 px-3 border-b text-left text-gray-600 font-medium">AGE</th>
                                <th class="py-2 px-3 border-b text-left text-gray-600 font-medium">SEX</th>
                                <th class="py-2 px-3 border-b text-left text-gray-600 font-medium">BIRTHDATE</th>
                                <th class="py-2 px-3 border-b text-left text-gray-600 font-medium">EDUCATION</th>
                                <th class="py-2 px-3 border-b text-left text-gray-600 font-medium">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody id="member-table-body">
                            <!-- Initial row will be added by JavaScript -->
                        </tbody>
                    </table>
                </div>
                <button id="add-member-btn" type="button" class="form-button bg-green-500 hover:bg-green-600 text-white font-medium mb-4">
                    Add Household Member
                </button>

                <div class="mt-4 md:mt-6 flex justify-between">
                    <button id="prev-page-2-btn" class="form-button bg-gray-400 hover:bg-gray-500 text-white font-medium">
                        Previous
                    </button>
                    <button id="next-page-2-btn" class="form-button bg-blue-500 hover:bg-blue-600 text-white font-medium">
                        Next
                    </button>
                </div>
            </div>

            <div id="form-page-3" class="form-page hidden">
                <!-- Section V: Remarks -->
                <div class="mb-6 md:mb-8">
                    <h2 class="section-title">V. Remarks</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-4 md:mb-6">
                        <div>
                            <p class="question-title">SHELTER NEEDS</p>
                            <div class="flex flex-col gap-1 md:gap-2">
                                <label class="inline-flex items-center"><input type="checkbox" id="security-upgrading" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Security Upgrading</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="shelter-provision" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Shelter Provision</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="structural-upgrading" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Structural Upgrading</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="infrastructure-upgrading" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Infrastructure Upgrading</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="other-remarks-checkbox" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Other Remarks</span></label>
                                <input type="text" id="other-remarks-text" class="form-control mt-1 hidden" placeholder="Specify other remarks">
                            </div>
                        </div>
                        <div>
                            <p class="question-title">HOUSEHOLD CLASSIFICATION</p>
                            <div class="flex flex-col gap-1 md:gap-2">
                                <label class="inline-flex items-center"><input type="checkbox" id="single-hh" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Single HH</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="displaced-unit" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Displaced Unit</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="doubled-up" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Doubled Up HH</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="displacement-concern" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Displacement Concern</span></label>
                            </div>
                        </div>
                        <div>
                            <p class="question-title">CENSUS REMARKS</p>
                            <div class="flex flex-col gap-1 md:gap-2">
                                <label class="inline-flex items-center"><input type="checkbox" id="odc" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Out During Census (ODC)</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="aho" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Absentee House Owner (AHO)</span></label>
                                <label class="inline-flex items-center"><input type="checkbox" id="census-others-checkbox" class="form-checkbox h-4 w-4 text-indigo-600"> <span class="ml-2">Others</span></label>
                                <input type="text" id="census-others-text" class="form-control mt-1 hidden" placeholder="Specify other census remarks">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Photo Capture Section -->
                <div class="mb-6 md:mb-8 p-4 md:p-6 border rounded-lg bg-gray-50">
                    <h2 class="section-title">Photo Capture</h2>
                    
                    <div class="mb-4">
                        <label class="question-title">Household Head Photo:</label>
                        <div class="flex flex-col items-center">
                            <div id="photos-container" class="photos-container">
                                <!-- Photos will be added here -->
                            </div>
                            <button id="open-camera-btn" class="form-button bg-blue-500 hover:bg-blue-600 text-white font-medium mt-4">
                                Open Camera to Take Photo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Signature Pad Section -->
                <div class="mb-6 md:mb-8 p-4 md:p-6 border rounded-lg bg-gray-50">
                    <h2 class="section-title">Digital Signature</h2>
                    
                    <div class="mb-4">
                        <label class="question-title">Draw your signature below:</label>
                        <div class="border-2 border-gray-300 rounded-lg p-2 bg-white">
                            <canvas id="signature-pad" class="w-full h-40 md:h-48 lg:h-64 bg-white"></canvas>
                        </div>
                        <div class="flex flex-col sm:flex-row justify-between gap-2 mt-3 md:mt-4">
                            <button id="clear-signature-btn" class="form-button bg-red-500 hover:bg-red-600 text-white font-medium">
                                Clear Signature
                            </button>
                            <button id="save-signature-btn" class="form-button bg-blue-500 hover:bg-blue-600 text-white font-medium">
                                Save Signature
                            </button>
                        </div>
                        <div class="mt-3 md:mt-4">
                            <p class="text-sm text-gray-600 mb-1 md:mb-2">Your saved signature:</p>
                            <img id="saved-signature-img" class="max-w-xs border border-gray-300 rounded-md bg-white p-2" alt="Saved Signature">
                        </div>
                    </div>
                </div>

                <!-- Data Confirmation -->
                <div class="mb-6 md:mb-8 p-4 md:p-6 border rounded-lg bg-gray-100">
                    <h2 class="section-title">Data Confirmation</h2>
                    
                    <div class="mb-4">
                        <div class="flex items-center mb-4">
                            <input type="checkbox" id="data-accuracy-checkbox" class="form-checkbox h-5 w-5 text-indigo-600" required>
                            <label for="data-accuracy-checkbox" class="ml-2 block text-sm text-gray-700">
                                I confirm that all the information provided in this form is accurate to the best of my knowledge.
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="privacy-policy-checkbox" class="form-checkbox h-5 w-5 text-indigo-600" required>
                            <label for="privacy-policy-checkbox" class="ml-2 block text-sm text-gray-700">
                                I agree to the collection and processing of my personal data in accordance with the Data Privacy Act of 2012.
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 md:mt-6 flex justify-between">
                    <button id="prev-page-3-btn" class="form-button bg-gray-400 hover:bg-gray-500 text-white font-medium">
                        Previous
                    </button>
                    <button id="submit-form-btn" class="form-button bg-indigo-600 hover:bg-indigo-700 text-white font-medium">
                        Submit Form
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Thank You Page -->
    <div id="thank-you-page" class="form-page hidden">
        <div class="text-center p-8 md:p-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">Thank You!</h2>
            <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
            Thank you for completing the survey. Your TAG number is:
            </p>
            
            <!-- TAG Number Display -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 max-w-md mx-auto mb-6">
                <h3 class="text-xl font-bold mb-2" id="final-tag-number"></h3>
                <div class="mb-4">
                    <div class="barcode-label mb-2">TAG Number Barcode</div>
                    <svg id="final-tag-barcode" class="w-full h-16"></svg>
                </div>
                <div>
                    <div class="qr-code-label mb-2">Survey QR Code</div>
                    <div id="final-qr-code" class="flex justify-center"></div>
                </div>
            </div>

            <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
            Please save or take a screenshot of your TAG number for future reference.
            </p>
        

            <button id="return-home-btn" class="form-button bg-indigo-600 hover:bg-indigo-700 text-white font-medium">
                Return to Home
            </button>
        </div>
    </div>

    <!-- Load Leaflet JS for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <script>
        // Form navigation
        const formPages = [
            document.getElementById('form-page-1'),
            document.getElementById('form-page-2'),
            document.getElementById('form-page-3')
        ];
        const locationSection = document.getElementById('location-section');
        const tagNumberSection = document.getElementById('tag-number-section');
        const locationDetails = document.getElementById('location-details');
        const thankYouPage = document.getElementById('thank-you-page');
        let currentPage = 0;

        const nextPage1Btn = document.getElementById('next-page-1-btn');
        const prevPage2Btn = document.getElementById('prev-page-2-btn');
        const nextPage2Btn = document.getElementById('next-page-2-btn');
        const prevPage3Btn = document.getElementById('prev-page-3-btn');
        const submitFormBtn = document.getElementById('submit-form-btn');
        const returnHomeBtn = document.getElementById('return-home-btn');

        // Camera elements
        const cameraModal = document.getElementById('camera-modal');
        const cameraView = document.getElementById('camera-view');
        const cameraPreview = document.getElementById('camera-preview');
        const openCameraBtn = document.getElementById('open-camera-btn');
        const captureBtn = document.getElementById('capture-btn');
        const retakeBtn = document.getElementById('retake-btn');
        const confirmBtn = document.getElementById('confirm-btn');
        const switchCameraBtn = document.getElementById('switch-camera-btn');
        const photosContainer = document.getElementById('photos-container');
        const previewContext = cameraPreview.getContext('2d');
        let cameraStream = null;
        let capturedPhotos = [];
        let currentFacingMode = 'environment'; // Default to back camera

        // Signature pad elements
        const signaturePadCanvas = document.getElementById('signature-pad');
        const clearSignatureBtn = document.getElementById('clear-signature-btn');
        const saveSignatureBtn = document.getElementById('save-signature-btn');
        const savedSignatureImg = document.getElementById('saved-signature-img');
        let signaturePadContext = null;
        let drawing = false;

        // Location elements
        const getLocationBtn = document.getElementById('get-location-btn');
        const confirmLocationBtn = document.getElementById('confirm-location-btn');
        const manualLocationDiv = document.getElementById('manual-location');
        const saveManualLocationBtn = document.getElementById('save-manual-location-btn');
        const locationText = document.getElementById('location-text');
        const locationStatus = document.getElementById('location-status');
        const locationAccuracy = document.getElementById('location-accuracy');
        const locationLoading = document.getElementById('location-loading');
        let map;
        let marker;
        let userLocation = null;
        let locationConfirmed = false;

        // Confirmation modal elements
        const confirmationModal = document.getElementById('confirmation-modal');
        const cancelSubmissionBtn = document.getElementById('cancel-submission-btn');
        const confirmSubmissionBtn = document.getElementById('confirm-submission-btn');

        // UD Code and TAG Number elements
        const udCodeInput = document.getElementById('ud-code');
        const tagNumberInput = document.getElementById('tag-number');
        const udCodeBarcode = document.getElementById('ud-code-barcode');
        const tagNumberBarcode = document.getElementById('tag-number-barcode');
        const surveyQrCode = document.getElementById('survey-qr-code');

        // Initialize signature pad
        function initializeSignaturePad() {
            signaturePadCanvas.width = signaturePadCanvas.offsetWidth;
            signaturePadCanvas.height = signaturePadCanvas.offsetHeight;
            
            signaturePadContext = signaturePadCanvas.getContext('2d');
            signaturePadContext.fillStyle = '#FFFFFF';
            signaturePadContext.fillRect(0, 0, signaturePadCanvas.width, signaturePadCanvas.height);
            signaturePadContext.strokeStyle = '#000000';
            signaturePadContext.lineWidth = 2.5;
            signaturePadContext.lineCap = 'round';
            signaturePadContext.lineJoin = 'round';
        }

        // Handle window resizing
        function resizeSignaturePad() {
            const canvas = signaturePadCanvas;
            const container = canvas.parentElement;
            const imageData = signaturePadContext.getImageData(0, 0, canvas.width, canvas.height);
            
            canvas.width = container.offsetWidth;
            canvas.height = container.offsetHeight;
            
            signaturePadContext.putImageData(imageData, 0, 0);
        }

        // Initialize map centered on Pasay City
        function initMap() {
            // Default to Pasay City coordinates
            const defaultLat = 14.5378;
            const defaultLng = 121.0014;
            
            map = L.map('map').setView([defaultLat, defaultLng], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Add click event to place marker
            map.on('click', function(e) {
                if (!locationConfirmed) {
                    placeMarker(e.latlng);
                    updateLocationText(e.latlng);
                } else {
                    alert('Location is already confirmed. To change your location, please contact the administrator.');
                }
            });
            
            // Try to get device location automatically
            getDeviceLocation();
        }

        function placeMarker(latlng) {
            if (marker) {
                map.removeLayer(marker);
            }
            
            marker = L.marker(latlng, {
                draggable: !locationConfirmed
            }).addTo(map)
                .bindPopup('You are here.')
                .openPopup();
            
            userLocation = latlng;
            
            // Update position when marker is dragged
            marker.on('dragend', function(e) {
                if (!locationConfirmed) {
                    userLocation = e.target.getLatLng();
                    updateLocationText(userLocation);
                }
            });
        }

        function updateLocationText(latlng) {
            // First show coordinates while we fetch address
            locationText.innerHTML = `<span id="location-status">Selected location:</span> Latitude: ${latlng.lat.toFixed(6)}, Longitude: ${latlng.lng.toFixed(6)}`;
            
            // Reverse geocode to get address
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}`)
                .then(response => response.json())
                .then(data => {
                    const address = data.display_name || 'Selected location';
                    locationText.innerHTML = `<span id="location-status">Selected location:</span> ${address}`;
                    
                    // Try to get barangay from reverse geocoding
                    if (data.address) {
                        const barangay = data.address.suburb || data.address.neighbourhood || 
                                        data.address.village || data.address.city_district || '';
                        if (barangay) {
                            document.getElementById('barangay').value = barangay;
                            document.getElementById('manual-barangay').value = barangay;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching address:', error);
                });
        }

        function getDeviceLocation() {
            locationLoading.classList.remove('hidden');
            getLocationBtn.disabled = true;
            locationStatus.textContent = "Getting your location...";
            locationAccuracy.textContent = "";
            
            if (!navigator.geolocation) {
                locationStatus.textContent = "Geolocation is not supported by your browser";
                locationLoading.classList.add('hidden');
                getLocationBtn.disabled = false;
                return;
            }
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const latlng = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    
                    // Place marker at device location
                    placeMarker(latlng);
                    map.setView(latlng, 18); // Zoom in closer for accuracy
                    
                    // Update status
                    locationStatus.textContent = "Device location found:";
                    
                    // Show accuracy information
                    const accuracy = position.coords.accuracy;
                    let accuracyText = "";
                    let accuracyClass = "";
                    
                    if (accuracy < 50) {
                        accuracyText = `(High accuracy: within ${Math.round(accuracy)} meters)`;
                        accuracyClass = "high";
                    } else if (accuracy < 200) {
                        accuracyText = `(Medium accuracy: within ${Math.round(accuracy)} meters)`;
                        accuracyClass = "medium";
                    } else {
                        accuracyText = `(Low accuracy: within ${Math.round(accuracy)} meters)`;
                        accuracyClass = "low";
                    }
                    
                    locationAccuracy.textContent = accuracyText;
                    locationAccuracy.className = `location-accuracy ${accuracyClass}`;
                    
                    locationLoading.classList.add('hidden');
                    getLocationBtn.disabled = false;
                    
                    // Auto-confirm if accuracy is good
                    if (accuracy < 100) {
                        confirmLocation();
                    }
                },
                function(error) {
                    let errorMessage = "Error getting location: ";
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage += "Permission denied. Please manually select your location.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage += "Location information unavailable. Please manually select your location.";
                            break;
                        case error.TIMEOUT:
                            errorMessage += "The request to get location timed out. Please manually select your location.";
                            break;
                        case error.UNKNOWN_ERROR:
                            errorMessage += "An unknown error occurred. Please manually select your location.";
                            break;
                    }
                    
                    locationStatus.textContent = errorMessage;
                    locationLoading.classList.add('hidden');
                    getLocationBtn.disabled = false;
                    
                    // Show manual location option
                    manualLocationDiv.classList.remove('hidden');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        function confirmLocation() {
            if (!marker) {
                alert('Please select your location on the map first');
                return;
            }
            
            if (confirm('Location confirmed. You will not be able to change this location after confirmation. To make changes, please contact the administrator.')) {
                confirmLocationBtn.classList.add('confirmed');
                locationConfirmed = true;
                marker.dragging.disable();
                
                // Update address fields
                const address = locationText.textContent.replace('Selected location:', '').trim();
                document.getElementById('street').value = address;
                
                // Show confirmed location details
                document.getElementById('confirmed-coords').textContent = 
                    `Latitude: ${userLocation.lat.toFixed(6)}, Longitude: ${userLocation.lng.toFixed(6)}`;
                document.getElementById('confirmed-address').textContent = address;
                locationDetails.classList.remove('hidden');
                
                // Try to get barangay from reverse geocoding
                if (userLocation) {
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${userLocation.lat}&lon=${userLocation.lng}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.address) {
                                const barangay = data.address.suburb || data.address.neighbourhood || 
                                                data.address.village || data.address.city_district || '';
                                if (barangay) {
                                    document.getElementById('barangay').value = barangay;
                                    document.getElementById('manual-barangay').value = barangay;
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching detailed address:', error);
                        });
                }
            }
        }

        // Calculate age from birthdate
        function calculateAge(birthdate) {
            const birthDate = new Date(birthdate);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            return age;
        }

        // Reset form to default state
        function resetForm() {
            // Reset form fields
            document.getElementById('hh-surname').value = '';
            document.getElementById('hh-firstname').value = '';
            document.getElementById('hh-middlename').value = '';
            document.getElementById('hh-mi').value = '';
            document.getElementById('spouse-surname').value = '';
            document.getElementById('spouse-firstname').value = '';
            document.getElementById('spouse-middlename').value = '';
            document.getElementById('spouse-mi').value = '';
            
            // Reset household head data
            document.getElementById('hh-sex').value = '';
            document.getElementById('hh-age').value = '';
            document.getElementById('hh-birthdate').value = '';
            document.querySelector('input[name="hh-civil-status"]').checked = true;
            document.getElementById('hh-senior-citizen').checked = false;
            document.getElementById('hh-pwd').checked = false;
            
            // Reset spouse data
            document.getElementById('spouse-sex').value = '';
            document.getElementById('spouse-age').value = '';
            document.getElementById('spouse-birthdate').value = '';
            document.querySelector('input[name="spouse-civil-status"]').checked = true;
            document.getElementById('spouse-senior-citizen').checked = false;
            document.getElementById('spouse-pwd').checked = false;
            
            // Reset address fields
            document.getElementById('house-no').value = '';
            document.getElementById('lot-no').value = '';
            document.getElementById('building').value = '';
            document.getElementById('block').value = '';
            document.getElementById('street').value = '';
            document.getElementById('barangay').value = '';
            document.getElementById('name-rfo-renter').value = '';
            
            // Reset membership checkboxes
            document.getElementById('pagibig').checked = false;
            document.getElementById('sss').checked = false;
            document.getElementById('gsis').checked = false;
            document.getElementById('philhealth').checked = false;
            document.getElementById('none-fund').checked = false;
            document.getElementById('other-fund').checked = false;
            document.getElementById('cso').checked = false;
            document.getElementById('hoa').checked = false;
            document.getElementById('cooperative').checked = false;
            document.getElementById('none-org').checked = false;
            document.getElementById('other-org').checked = false;
            document.getElementById('name-organization').value = '';
            
            // Reset remarks checkboxes
            document.getElementById('security-upgrading').checked = false;
            document.getElementById('shelter-provision').checked = false;
            document.getElementById('structural-upgrading').checked = false;
            document.getElementById('infrastructure-upgrading').checked = false;
            document.getElementById('other-remarks-checkbox').checked = false;
            document.getElementById('other-remarks-text').value = '';
            document.getElementById('other-remarks-text').classList.add('hidden');
            document.getElementById('single-hh').checked = false;
            document.getElementById('displaced-unit').checked = false;
            document.getElementById('doubled-up').checked = false;
            document.getElementById('displacement-concern').checked = false;
            document.getElementById('odc').checked = false;
            document.getElementById('aho').checked = false;
            document.getElementById('census-others-checkbox').checked = false;
            document.getElementById('census-others-text').value = '';
            document.getElementById('census-others-text').classList.add('hidden');
            
            // Reset confirmation checkboxes
            document.getElementById('data-accuracy-checkbox').checked = false;
            document.getElementById('privacy-policy-checkbox').checked = false;
            
            // Reset location
            if (marker) {
                map.removeLayer(marker);
                marker = null;
            }
            locationConfirmed = false;
            confirmLocationBtn.classList.remove('confirmed');
            locationDetails.classList.add('hidden');
            locationStatus.textContent = "Getting your location...";
            locationAccuracy.textContent = "";
            manualLocationDiv.classList.add('hidden');
            
            // Reset photos and signature
            capturedPhotos = [];
            renderPhotos();
            initializeSignaturePad();
            savedSignatureImg.src = '';
            savedSignatureImg.classList.add('hidden');
            
            // Reset member table
            memberTableBody.innerHTML = '';
            addMemberRow(); // Add initial row for household head
            
            // Reset UD Code and TAG Number
            document.getElementById('ud-code').value = '';
            document.getElementById('tag-number').value = '';
            document.getElementById('ud-code-barcode').innerHTML = '';
            document.getElementById('tag-number-barcode').innerHTML = '';
            document.getElementById('survey-qr-code').innerHTML = '';
            
            // Get new location
            getDeviceLocation();
        }

        // Camera functions
        async function startCamera(facingMode = 'environment') {
            try {
                if (cameraStream) {
                    stopCamera();
                }
                
                cameraStream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        facingMode: facingMode 
                    } 
                });
                
                cameraView.srcObject = cameraStream;
                cameraModal.style.display = 'flex';
                cameraView.classList.remove('hidden');
                cameraPreview.classList.add('hidden');
                captureBtn.style.display = 'flex';
                retakeBtn.style.display = 'none';
                confirmBtn.style.display = 'none';
            } catch (err) {
                console.error("Error accessing camera: ", err);
                alert("Could not access camera. Please ensure camera permissions are granted.");
            }
        }

        function stopCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
            cameraModal.style.display = 'none';
        }

        function capturePhoto() {
            cameraPreview.width = cameraView.videoWidth;
            cameraPreview.height = cameraView.videoHeight;
            previewContext.drawImage(cameraView, 0, 0, cameraPreview.width, cameraPreview.height);
            
            cameraView.classList.add('hidden');
            cameraPreview.classList.remove('hidden');
            captureBtn.style.display = 'none';
            retakeBtn.style.display = 'flex';
            confirmBtn.style.display = 'flex';
        }

        function retakePhoto() {
            cameraView.classList.remove('hidden');
            cameraPreview.classList.add('hidden');
            captureBtn.style.display = 'flex';
            retakeBtn.style.display = 'none';
            confirmBtn.style.display = 'none';
        }

        function confirmPhoto() {
            const dataURL = cameraPreview.toDataURL('image/png');
            capturedPhotos.push(dataURL);
            renderPhotos();
            stopCamera();
        }

        function switchCamera() {
            currentFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
            startCamera(currentFacingMode);
        }

        // Delete photo function
        function deletePhoto(index) {
            capturedPhotos.splice(index, 1);
            renderPhotos();
        }

        // Render all captured photos
        function renderPhotos() {
            photosContainer.innerHTML = '';
            capturedPhotos.forEach((photo, index) => {
                const photoContainer = document.createElement('div');
                photoContainer.className = 'photo-container';
                
                const img = document.createElement('img');
                img.src = photo;
                img.className = 'photo-thumbnail';
                
                const deleteBtn = document.createElement('button');
                deleteBtn.className = 'delete-photo-btn';
                deleteBtn.innerHTML = '×';
                deleteBtn.onclick = () => deletePhoto(index);
                
                photoContainer.appendChild(img);
                photoContainer.appendChild(deleteBtn);
                photosContainer.appendChild(photoContainer);
            });
        }

        // Generate barcode for UD Code and TAG Number
        function generateBarcodes() {
            const udCode = document.getElementById('ud-code').value;
            const tagNumber = document.getElementById('tag-number').value;
            
            if (udCode) {
                JsBarcode("#ud-code-barcode", udCode, {
                    format: "CODE128",
                    lineColor: "#000",
                    width: 2,
                    height: 50,
                    displayValue: false
                });
            }
            
            if (tagNumber) {
                JsBarcode("#tag-number-barcode", tagNumber, {
                    format: "CODE128",
                    lineColor: "#000",
                    width: 2,
                    height: 50,
                    displayValue: false
                });
            }
            
            if (udCode && tagNumber) {
                const qrCodeData = `UD Code: ${udCode}\nTAG Number: ${tagNumber}\nSurvey Date: ${new Date().toLocaleDateString()}`;
                QRCode.toCanvas(document.getElementById('survey-qr-code'), qrCodeData, {
                    width: 150,
                    margin: 1,
                    color: {
                        dark: "#000000",
                        light: "#ffffff"
                    }
                }, function(error) {
                    if (error) console.error(error);
                });
            }
        }

        // Form navigation functions
        function showPage(pageIndex) {
            console.log(`Showing page ${pageIndex}`);
            
            // Hide thank you page when showing other pages
            thankYouPage.classList.add('hidden');
            
            // Log before hiding
            console.log('Hiding all pages');
            formPages.forEach((page, i) => {
                console.log(`Page ${i} hidden state before: ${page.classList.contains('hidden')}`);
                page.classList.add('hidden');
                console.log(`Page ${i} hidden state after: ${page.classList.contains('hidden')}`);
            });
            
            // Show the requested page
            console.log(`Showing page ${pageIndex}`);
            formPages[pageIndex].classList.remove('hidden');
            console.log(`Page ${pageIndex} hidden state after show: ${formPages[pageIndex].classList.contains('hidden')}`);

            // Show location and TAG number sections only on first page
            if (pageIndex === 0) {
                locationSection.classList.remove('hidden');
                tagNumberSection.classList.remove('hidden');
                if (locationConfirmed) {
                    locationDetails.classList.remove('hidden');
                }
            } else {
                locationSection.classList.add('hidden');
                tagNumberSection.classList.add('hidden');
                locationDetails.classList.add('hidden');
            }
            
            currentPage = pageIndex;
            
            // Initialize signature pad when showing page 3
            if (pageIndex === 2) {
                initializeSignaturePad();
            }
        }

        function nextPage() {
            if (currentPage < formPages.length - 1) {
                showPage(currentPage + 1);
            }
        }

        function prevPage() {
            if (currentPage > 0) {
                showPage(currentPage - 1);
            }
        }

        // Event listeners for form navigation
        nextPage1Btn.addEventListener('click', nextPage);
        prevPage2Btn.addEventListener('click', prevPage);
        nextPage2Btn.addEventListener('click', nextPage);
        prevPage3Btn.addEventListener('click', prevPage);

        // Camera event listeners
        openCameraBtn.addEventListener('click', () => startCamera(currentFacingMode));
        captureBtn.addEventListener('click', capturePhoto);
        retakeBtn.addEventListener('click', retakePhoto);
        confirmBtn.addEventListener('click', confirmPhoto);
        switchCameraBtn.addEventListener('click', switchCamera);

        // Signature pad functionality
        function getCanvasPoint(canvas, clientX, clientY) {
            const rect = canvas.getBoundingClientRect();
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        }

        signaturePadCanvas.addEventListener('mousedown', (e) => {
            drawing = true;
            const pos = getCanvasPoint(signaturePadCanvas, e.clientX, e.clientY);
            signaturePadContext.beginPath();
            signaturePadContext.moveTo(pos.x, pos.y);
            savedSignatureImg.classList.add('hidden');
        });

        signaturePadCanvas.addEventListener('mouseup', () => {
            drawing = false;
            signaturePadContext.closePath();
        });

        signaturePadCanvas.addEventListener('mousemove', (e) => {
            if (!drawing) return;
            const pos = getCanvasPoint(signaturePadCanvas, e.clientX, e.clientY);
            signaturePadContext.lineTo(pos.x, pos.y);
            signaturePadContext.stroke();
        });

        signaturePadCanvas.addEventListener('mouseleave', () => {
            if (drawing) {
                drawing = false;
                signaturePadContext.closePath();
            }
        });

        // Touch events for mobile
        signaturePadCanvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            drawing = true;
            const touch = e.touches[0];
            const pos = getCanvasPoint(signaturePadCanvas, touch.clientX, touch.clientY);
            signaturePadContext.beginPath();
            signaturePadContext.moveTo(pos.x, pos.y);
            savedSignatureImg.classList.add('hidden');
        });

        signaturePadCanvas.addEventListener('touchend', () => {
            drawing = false;
            signaturePadContext.closePath();
        });

        signaturePadCanvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            if (!drawing) return;
            const touch = e.touches[0];
            const pos = getCanvasPoint(signaturePadCanvas, touch.clientX, touch.clientY);
            signaturePadContext.lineTo(pos.x, pos.y);
            signaturePadContext.stroke();
        });

        // Clear signature button
        clearSignatureBtn.addEventListener('click', () => {
            initializeSignaturePad();
            savedSignatureImg.classList.add('hidden');
        });

        // Save signature button
        saveSignatureBtn.addEventListener('click', () => {
            if (signaturePadContext.getImageData(0, 0, signaturePadCanvas.width, signaturePadCanvas.height).data.every(channel => channel === 255)) {
                alert("Signature pad is empty! Please draw your signature.");
                return;
            }
            const dataURL = signaturePadCanvas.toDataURL('image/png');
            savedSignatureImg.src = dataURL;
            savedSignatureImg.classList.remove('hidden');
        });

        // Location confirmation button
        confirmLocationBtn.addEventListener('click', confirmLocation);
        
        saveManualLocationBtn.addEventListener('click', () => {
            const address = document.getElementById('manual-address').value;
            const barangay = document.getElementById('manual-barangay').value;
            const zone = document.getElementById('manual-zone').value;
            
            if (!address || !barangay) {
                alert('Please enter at least address and barangay');
                return;
            }
            
            document.getElementById('street').value = address;
            document.getElementById('barangay').value = barangay;
            
            manualLocationDiv.classList.add('hidden');
            locationText.textContent = `${address}, ${barangay}, Pasay City`;
        });

        // Confirmation modal
        submitFormBtn.addEventListener('click', () => {
            // Validate required fields
            const requiredFields = document.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value) {
                    field.style.borderColor = 'red';
                    isValid = false;
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                alert('Please fill in all required fields marked with *');
                return;
            }
            
            if (!marker) {
                alert('Please select your location on the map');
                return;
            }
            
            if (capturedPhotos.length === 0) {
                alert('Please capture at least one photo');
                return;
            }
            
            if (!savedSignatureImg.src || savedSignatureImg.classList.contains('hidden')) {
                alert('Please provide your signature');
                return;
            }
            
            if (!document.getElementById('data-accuracy-checkbox').checked) {
                alert('Please confirm that all data is accurate');
                return;
            }
            
            if (!document.getElementById('privacy-policy-checkbox').checked) {
                alert('Please agree to the privacy policy');
                return;
            }
            
            // Show confirmation modal
            confirmationModal.style.display = 'flex';
        });

        cancelSubmissionBtn.addEventListener('click', () => {
            confirmationModal.style.display = 'none';
        });

        confirmSubmissionBtn.addEventListener('click', () => {
            confirmationModal.style.display = 'none';

            const tagNumber = document.getElementById('tag-number').value;
            const tagNumberRegex = /^\d{4}-\d{3}-\d{3}$/;
            
            if (!tagNumberRegex.test(tagNumber)) {
                alert('Please enter a valid TAG number in the format YEAR-BRGY No.-000 (e.g., 2023-000-001)');
                return;
            }
            
            // Gather form data
            const formData = {
                // Location data
                location: userLocation,
                address: document.getElementById('street').value,
                barangay: document.getElementById('barangay').value,
                
                // Identification codes
                udCode: document.getElementById('ud-code').value,
                tagNumber: document.getElementById('tag-number').value,
                
                // Personal data
                householdHead: {
                    name: {
                        surname: document.getElementById('hh-surname').value,
                        firstname: document.getElementById('hh-firstname').value,
                        middlename: document.getElementById('hh-middlename').value,
                        mi: document.getElementById('hh-mi').value
                    },
                    // ... other household head data
                },
                
                // Capture data
                photos: capturedPhotos,
                signature: savedSignatureImg.src,
                
                // Timestamp
                submittedAt: new Date().toISOString()
            };
            
            console.log('Form data submitted:', formData);

            // Hide all form pages and show thank you page
            formPages.forEach(page => page.classList.add('hidden'));
            thankYouPage.classList.remove('hidden');
            
            // Display the final TAG number
            document.getElementById('final-tag-number').textContent = tagNumber;
            
            // Generate barcode
            JsBarcode("#final-tag-barcode", tagNumber, {
                format: "CODE128",
                lineColor: "#000",
                width: 2,
                height: 50,
                displayValue: false
            });
            
            // Generate QR code
            const qrCodeData = `TAG Number: ${tagNumber}\nSurvey Date: ${new Date().toLocaleDateString()}`;
            QRCode.toCanvas(document.getElementById('final-qr-code'), qrCodeData, {
                width: 150,
                margin: 1,
                color: {
                    dark: "#000000",
                    light: "#ffffff"
                }
            }, function(error) {
                if (error) console.error(error);
            });
    
            // Reset form after submission
            resetForm();
        });

        // Toggle other remarks fields
        document.getElementById('other-remarks-checkbox').addEventListener('change', function() {
            document.getElementById('other-remarks-text').classList.toggle('hidden', !this.checked);
        });

        document.getElementById('census-others-checkbox').addEventListener('change', function() {
            document.getElementById('census-others-text').classList.toggle('hidden', !this.checked);
        });

        // Age calculation when birthdate changes
        document.getElementById('hh-birthdate').addEventListener('change', function() {
            if (this.value) {
                const age = calculateAge(this.value);
                document.getElementById('hh-age').value = age;
            }
        });

        document.getElementById('spouse-birthdate').addEventListener('change', function() {
            if (this.value) {
                const age = calculateAge(this.value);
                document.getElementById('spouse-age').value = age;
            }
        });

        // UD Code and TAG Number input listeners
        udCodeInput.addEventListener('input', generateBarcodes);
        tagNumberInput.addEventListener('input', generateBarcodes);

        // Auto-generate codes when barangay is entered
        document.getElementById('barangay').addEventListener('change', function() {
            if (!udCodeInput.value && !tagNumberInput.value) {
                const currentYear = new Date().getFullYear();
                const barangay = this.value || '000'; // Default to 000 if no barangay
                
                // Extract numbers from barangay name or use 000
                let barangayNum = '000';
                const numMatch = barangay.match(/\d+/);
                if (numMatch) {
                    barangayNum = numMatch[0].padStart(3, '0').substring(0, 3);
                }
                
                // Format: YEAR-BARANGAYNUM-001 (starting with 001)
                const sequenceNum = '001';
                udCodeInput.value = `${currentYear}-${barangayNum}-${sequenceNum}`;
                tagNumberInput.value = `${currentYear}-${barangayNum}-${sequenceNum}`;
                
                generateBarcodes();
            }
        });

        // Member table functionality
        const memberTableBody = document.getElementById('member-table-body');
        const addMemberBtn = document.getElementById('add-member-btn');

        function addMemberRow() {
            const row = document.createElement('tr');
            row.className = 'member-row';
            
            row.innerHTML = `
                <td class="py-1 px-2 border-b"><input type="text" class="form-control border-none p-1" required></td>
                <td class="py-1 px-2 border-b"><input type="text" class="form-control border-none p-1" required></td>
                <td class="py-1 px-2 border-b"><input type="number" class="form-control border-none p-1" required></td>
                <td class="py-1 px-2 border-b">
                    <select class="form-control border-none p-1" required>
                        <option value=""></option>
                        <option>M</option>
                        <option>F</option>
                    </select>
                </td>
                <td class="py-1 px-2 border-b"><input type="date" class="form-control border-none p-1" required></td>
                <td class="py-1 px-2 border-b"><input type="text" class="form-control border-none p-1" required></td>
                <td class="py-1 px-2 border-b">
                    <button type="button" class="remove-member-btn">Remove</button>
                </td>
            `;
            
            memberTableBody.appendChild(row);
            
            // Add age calculation when birthdate changes
            const birthdateInput = row.querySelector('input[type="date"]');
            const ageInput = row.querySelector('input[type="number"]');
            
            birthdateInput.addEventListener('change', function() {
                if (this.value) {
                    const age = calculateAge(this.value);
                    ageInput.value = age;
                }
            });
            
            // Add event listener to the remove button
            row.querySelector('.remove-member-btn').addEventListener('click', () => {
                if (memberTableBody.children.length > 1) {
                    row.remove();
                } else {
                    alert('You must have at least one household member (the head)');
                }
            });
        }

        addMemberBtn.addEventListener('click', addMemberRow);

        // Add initial row for household head
        addMemberRow();

        returnHomeBtn.addEventListener('click', () => {
            // Reset form and show first page
            thankYouPage.classList.add('hidden');
            showPage(0);
        });

        // Initialize the form
        document.addEventListener('DOMContentLoaded', () => {
            showPage(0);
            initMap();
            
            // Update signature pad when window resizes
            window.addEventListener('resize', () => {
                if (currentPage === 2) {
                    resizeSignaturePad();
                }
            });
        });
    </script>
</body>
</html>