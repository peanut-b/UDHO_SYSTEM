<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDSAP Survey System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .custom-card {
            transition: all 0.3s ease;
        }
        .custom-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-color: #3B82F6;
        }
        .survey-btn {
            transition: all 0.2s ease;
        }
        .survey-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-completed {
            background-color: #D1FAE5;
            color: #065F46;
        }
        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }
        .status-ongoing {
            background-color: #DBEAFE;
            color: #1E40AF;
        }
        
        /* Responsive sidebar */
        @media (max-width: 768px) {
            .sidebar-collapsed {
                width: 4rem;
            }
            .sidebar-collapsed .sidebar-text {
                display: none;
            }
            .sidebar-collapsed .logo-text {
                display: none;
            }
            .sidebar-collapsed .sidebar-toggle {
                justify-content: center;
            }
            .main-content {
                margin-left: 4rem;
            }
        }
        
        /* Mobile-friendly modals */
        @media (max-width: 640px) {
            .responsive-modal {
                width: 95%;
                margin: 0.5rem auto;
                max-height: 95vh;
            }
            .responsive-modal-content {
                padding: 1rem;
            }
        }
        
        /* Better table responsiveness */
        .responsive-table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Larger tap targets for mobile */
        @media (max-width: 640px) {
            .mobile-tap-target {
                padding: 1rem;
                min-height: 3rem;
            }
            .mobile-tap-target i {
                font-size: 1.25rem;
            }
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
              <img src="/assets/profile_pictures/<?php echo htmlspecialchars($profilePicture); ?>" 
                  alt="Profile Picture" 
                  class="w-full h-full object-cover"
                  onerror="this.src='/assets/PROFILE_SAMPLE.jpg'">
          </div>
      </div>
        <nav class="mt-6">
            <ul>
                <li>
                    <a href="operation_dashboard.php" class="block py-3 px-4 hover:bg-gray-700 flex items-center mobile-tap-target">
                        <i class="fas fa-tachometer-alt mr-3"></i> 
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="block py-3 px-4 bg-gray-700 flex items-center mobile-tap-target">
                        <i class="fas fa-database mr-3"></i> 
                        <span class="sidebar-text">Survey Database</span>
                    </a>
                </li>
                <li>
                    <a href="Settings/setting_operation.php" class="block py-3 px-4 hover:bg-gray-700 flex items-center mobile-tap-target">
                        <i class="fas fa-cog mr-3"></i> 
                        <span class="sidebar-text">Settings</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="block py-3 px-4 hover:bg-gray-700 mt-10 flex items-center mobile-tap-target">
                        <i class="fas fa-sign-out-alt mr-3"></i> 
                        <span class="sidebar-text">Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-4 md:p-6 overflow-auto main-content">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">IDSAP Survey System</h1>
            <div class="flex items-center gap-2">
                <img src="/assets/UDHOLOGO.png" alt="Logo" class="h-6 md:h-8">
                <span class="font-medium text-gray-700 text-sm md:text-base">Urban Development and Housing Office</span>
            </div>
        </div>

        <!-- Barangay Survey Records Panel (Initially hidden) -->
        <div id="recordsPanel" class="bg-white p-4 md:p-6 rounded-lg shadow-md mb-6 hidden">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                <h2 class="text-lg md:text-xl font-semibold" id="barangayTitle">Survey Records - Barangay </h2>
                <button onclick="backToBarangayList()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 flex items-center w-full md:w-auto justify-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Barangays
                </button>
            </div>
            
            <div class="responsive-table-container">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="recordsTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Records will be populated here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Survey Panel (Initially visible) -->
        <div id="barangayPanel" class="bg-white p-4 md:p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-lg md:text-xl font-semibold mb-4 md:mb-6 border-b pb-2">Barangay Survey Selection</h2>
            
            <div class="mb-4 flex flex-col md:flex-row items-start md:items-center gap-2">
                <input type="text" id="searchBarangay" placeholder="Search barangay..." class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition w-full">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 w-full md:w-auto flex items-center justify-center">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                <?php for ($i = 1; $i <= 201; $i++): ?>
                    <button onclick="showBarangayRecords(<?php echo $i; ?>)" class="survey-btn bg-white p-3 md:p-4 text-center rounded-lg border border-gray-200 hover:border-blue-400">
                        <i class="fas fa-map-marker-alt text-blue-500 text-lg md:text-xl mb-1 md:mb-2"></i>
                        <h6 class="text-xs md:text-sm font-medium text-gray-800">Barangay <?php echo $i; ?></h6>
                    </button>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Quick Stats and Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <!-- System Statistics -->
            <div class="bg-white p-4 md:p-6 rounded-lg shadow-md">
                <h3 class="text-base md:text-lg font-semibold mb-3 md:mb-4">Survey Statistics</h3>
                <div class="grid grid-cols-2 gap-3 md:gap-4">
                    <div class="bg-blue-50 p-3 md:p-4 rounded-lg">
                        <div class="text-blue-600 font-bold text-lg md:text-xl" id="completedSurveys">0</div>
                        <div class="text-gray-600 text-xs md:text-sm">Completed Surveys</div>
                    </div>
                    <div class="bg-green-50 p-3 md:p-4 rounded-lg">
                        <div class="text-green-600 font-bold text-lg md:text-xl" id="totalBarangays">201</div>
                        <div class="text-gray-600 text-xs md:text-sm">Barangays Covered</div>
                    </div>
                    <div class="bg-yellow-50 p-3 md:p-4 rounded-lg">
                        <div class="text-yellow-600 font-bold text-lg md:text-xl" id="pendingSurveys">0</div>
                        <div class="text-gray-600 text-xs md:text-sm">Pending Surveys</div>
                    </div>
                    <div class="bg-purple-50 p-3 md:p-4 rounded-lg">
                        <div class="text-purple-600 font-bold text-lg md:text-xl" id="todaySurveys">0</div>
                        <div class="text-gray-600 text-xs md:text-sm">Today's Surveys</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white p-4 md:p-6 rounded-lg shadow-md">
                <h3 class="text-base md:text-lg font-semibold mb-3 md:mb-4">Quick Actions</h3>
                <div class="space-y-3 md:space-y-4">
                    <button onclick="generateReport()" class="w-full text-left">
                        <div class="bg-white p-3 md:p-4 rounded shadow-sm border border-gray-200 custom-card flex items-center mobile-tap-target">
                            <i class="fas fa-file-export text-green-500 text-lg md:text-xl mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Export Survey Data</span>
                        </div>
                    </button>
                    <button onclick="viewSummary()" class="w-full text-left">
                        <div class="bg-white p-3 md:p-4 rounded shadow-sm border border-gray-200 custom-card flex items-center mobile-tap-target">
                            <i class="fas fa-chart-pie text-purple-500 text-lg md:text-xl mr-3"></i>
                            <span class="font-medium text-sm md:text-base">View Survey Summary</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Survey Details Modal -->
    <div id="surveyDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-2 md:p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto responsive-modal">
            <div class="p-4 md:p-6 responsive-modal-content">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg md:text-xl font-bold" id="surveyDetailsTitle">Survey Details</h3>
                    <button onclick="closeSurveyDetails()" class="text-gray-500 hover:text-gray-700 text-xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-4 md:mb-6">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2 text-sm md:text-base">Basic Information</h4>
                        <div class="space-y-1 md:space-y-2 text-sm md:text-base">
                            <p><span class="font-medium">Barangay:</span> <span id="detailBarangay"></span></p>
                            <p><span class="font-medium">Date of Census:</span> <span id="detailDate"></span></p>
                            <p><span class="font-medium">Surveyor:</span> <span id="detailSurveyor"></span></p>
                            <p><span class="font-medium">Households:</span> <span id="detailHouseholds"></span></p>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2 text-sm md:text-base">Classification & Status</h4>
                        <div class="space-y-1 md:space-y-2 text-sm md:text-base">
                            <p><span class="font-medium">Subject:</span> <span id="detailSubject"></span></p>
                            <p><span class="font-medium">Classification:</span> <span id="detailClassification"></span></p>
                            <p><span class="font-medium">Status:</span> <span id="detailStatus" class="status-badge"></span></p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4 md:mb-6">
                    <h4 class="font-medium text-gray-700 mb-2 text-sm md:text-base">Survey Notes</h4>
                    <div id="detailNotes" class="bg-gray-50 p-3 md:p-4 rounded-md text-sm md:text-base"></div>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-700 mb-2 text-sm md:text-base">Attached Documents</h4>
                    <div id="detailDocuments" class="grid grid-cols-2 sm:grid-cols-3 gap-3 md:gap-4">
                        <!-- Documents will be shown here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Survey Form Modal -->
   
<!-- Survey Form Modal -->
<div id="surveyModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-2 md:p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col responsive-modal">
        <!-- Fixed Header -->
        <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
            <h3 class="text-lg md:text-xl font-bold" id="modalBarangayTitle">Survey Form - Barangay </h3>
            <button onclick="closeSurveyModal()" class="text-gray-500 hover:text-gray-700 text-xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Scrollable Content Area -->
        <div class="overflow-y-auto flex-1 p-4 md:p-6" style="max-height: calc(90vh - 120px);">
            <form id="surveyForm" class="space-y-3 md:space-y-4">
                <input type="hidden" id="barangayId">
                
                <div>
                    <label class="block text-sm md:text-base font-medium text-gray-700 mb-1">Date of Census</label>
                    <input type="date" id="surveyDate" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm md:text-base" required>
                </div>
                
                <div>
                    <label class="block text-sm md:text-base font-medium text-gray-700 mb-1">Subject</label>
                    <input type="text" id="surveySubject" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm md:text-base" required>
                </div>
                
                <div>
                    <label class="block text-sm md:text-base font-medium text-gray-700 mb-1">Classification</label>
                    <select id="surveyClassification" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm md:text-base" required>
                        <option value="">Select Classification</option>
                        <option value="Residential">Residential</option>
                        <option value="Commercial">Commercial</option>
                        <option value="Industrial">Industrial</option>
                        <option value="Agricultural">Agricultural</option>
                        <option value="Mixed Use">Mixed Use</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm md:text-base font-medium text-gray-700 mb-1">Status</label>
                    <select id="surveyStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm md:text-base" required>
                        <option value="">Select Status</option>
                        <option value="Completed">Completed</option>
                        <option value="Pending">Pending</option>
                        <option value="Ongoing">Ongoing</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm md:text-base font-medium text-gray-700 mb-1">Surveyor Name</label>
                    <input type="text" id="surveyorName" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm md:text-base" required>
                </div>
                
                <div>
                    <label class="block text-sm md:text-base font-medium text-gray-700 mb-1">Number of Households</label>
                    <input type="number" id="householdCount" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm md:text-base" required>
                </div>
                
                <div>
                    <label class="block text-sm md:text-base font-medium text-gray-700 mb-1">Survey Notes</label>
                    <textarea id="surveyNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm md:text-base"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm md:text-base font-medium text-gray-700 mb-1">Upload Photos</label>
                    <input type="file" id="surveyPhotos" accept="image/*" multiple class="w-full text-sm md:text-base">
                </div>
            </form>
        </div>
        
        <!-- Fixed Footer -->
        <div class="p-4 border-t sticky bottom-0 bg-white">
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeSurveyModal()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 text-sm md:text-base">
                    Cancel
                </button>
                <button type="submit" form="surveyForm" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm md:text-base">
                    Submit Survey
                </button>
            </div>
        </div>
    </div>
</div>

    <script>
        // Sample data - in a real app this would come from your database
        const surveyData = {
            completed: 87,
            pending: 42,
            today: 5,
            records: {}
        };

        // Generate sample records for some barangays
        for (let i = 1; i <= 201; i++) {
            if (i % 5 === 0) { // Every 5th barangay has records for demo
                surveyData.records[i] = [
                    {
                        id: `${i}-1`,
                        date: '2023-06-15',
                        subject: 'Household Census',
                        classification: 'Residential',
                        status: 'Completed',
                        surveyor: 'Juan Dela Cruz',
                        households: 125,
                        notes: 'Completed full census of all households in the area.',
                        photos: ['sample1.jpg', 'sample2.jpg']
                    },
                    {
                        id: `${i}-2`,
                        date: '2023-07-20',
                        subject: 'Business Survey',
                        classification: 'Commercial',
                        status: 'Ongoing',
                        surveyor: 'Maria Santos',
                        households: 42,
                        notes: 'Initial survey of commercial establishments completed. Follow-up needed for 5 businesses.',
                        photos: ['sample3.jpg']
                    }
                ];
            }
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Update statistics
            document.getElementById('completedSurveys').textContent = surveyData.completed;
            document.getElementById('pendingSurveys').textContent = surveyData.pending;
            document.getElementById('todaySurveys').textContent = surveyData.today;

            // Setup search functionality
            document.getElementById('searchBarangay').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const buttons = document.querySelectorAll('.survey-btn');
                
                buttons.forEach(button => {
                    const barangayNum = button.textContent.toLowerCase();
                    if (barangayNum.includes(searchTerm)) {
                        button.classList.remove('hidden');
                    } else {
                        button.classList.add('hidden');
                    }
                });
            });

            // Sidebar toggle for mobile
            document.getElementById('sidebarToggle').addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('sidebar-collapsed');
            });
        });

        // Show records for specific barangay
        function showBarangayRecords(barangayId) {
            document.getElementById('barangayPanel').classList.add('hidden');
            document.getElementById('recordsPanel').classList.remove('hidden');
            document.getElementById('barangayTitle').textContent = `Survey Records - Barangay ${barangayId}`;
            
            // Store current barangay ID for adding new records
            document.getElementById('barangayId').value = barangayId;
            
            // Populate records table
            const tableBody = document.getElementById('recordsTableBody');
            tableBody.innerHTML = '';
            
            const records = surveyData.records[barangayId] || [];
            
            if (records.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-4 md:px-6 py-4 text-center text-gray-500">
                            No survey records found for this barangay.
                        </td>
                    </tr>
                `;
            } else {
                records.forEach(record => {
                    const statusClass = getStatusClass(record.status);
                    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm">${record.date}</td>
                        <td class="px-4 md:px-6 py-4 text-sm">
                            <a href="#" onclick="showSurveyDetails('${record.id}')" class="text-blue-600 hover:text-blue-800">
                                ${record.subject}
                            </a>
                        </td>
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm">${record.classification}</td>
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm">
                            <span class="${statusClass}">${record.status}</span>
                        </td>
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="editSurveyRecord('${record.id}')" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteSurveyRecord('${record.id}')" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            }
            
            // Add "Add New" button if no records exist
            if (records.length === 0) {
                const addRow = document.createElement('tr');
                addRow.innerHTML = `
                    <td colspan="5" class="px-4 md:px-6 py-4 text-center">
                        <button onclick="openSurveyForm(${barangayId})" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm md:text-base">
                            <i class="fas fa-plus mr-2"></i> Add New Survey
                        </button>
                    </td>
                `;
                tableBody.appendChild(addRow);
            }
        }

        // Show detailed survey view
        function showSurveyDetails(recordId) {
            const [barangayId, recordNum] = recordId.split('-');
            const record = surveyData.records[barangayId][recordNum - 1];
            
            document.getElementById('surveyDetailsTitle').textContent = `Survey Details - ${record.subject}`;
            document.getElementById('detailBarangay').textContent = `Barangay ${barangayId}`;
            document.getElementById('detailDate').textContent = record.date;
            document.getElementById('detailSurveyor').textContent = record.surveyor;
            document.getElementById('detailHouseholds').textContent = record.households;
            document.getElementById('detailSubject').textContent = record.subject;
            document.getElementById('detailClassification').textContent = record.classification;
            document.getElementById('detailStatus').textContent = record.status;
            document.getElementById('detailStatus').className = `status-badge ${getStatusClass(record.status)}`;
            document.getElementById('detailNotes').textContent = record.notes;
            
            // Show documents
            const docsContainer = document.getElementById('detailDocuments');
            docsContainer.innerHTML = '';
            
            if (record.photos && record.photos.length > 0) {
                record.photos.forEach(photo => {
                    docsContainer.innerHTML += `
                        <div class="border rounded-md overflow-hidden">
                            <img src="assets/sample-survey.jpg" alt="Survey Document" class="w-full h-24 md:h-32 object-cover">
                            <div class="p-2 text-center bg-gray-50">
                                <a href="#" class="text-blue-600 text-xs md:text-sm">View Full Image</a>
                            </div>
                        </div>
                    `;
                });
            } else {
                docsContainer.innerHTML = '<p class="text-gray-500 text-sm md:text-base">No documents attached</p>';
            }
            
            document.getElementById('surveyDetailsModal').classList.remove('hidden');
        }

        // Close survey details
        function closeSurveyDetails() {
            document.getElementById('surveyDetailsModal').classList.add('hidden');
        }

        // Back to barangay list
        function backToBarangayList() {
            document.getElementById('recordsPanel').classList.add('hidden');
            document.getElementById('barangayPanel').classList.remove('hidden');
        }

        // Open survey form for specific barangay
        function openSurveyForm(barangayId) {
            document.getElementById('modalBarangayTitle').textContent = `Survey Form - Barangay ${barangayId}`;
            document.getElementById('barangayId').value = barangayId;
            document.getElementById('surveyModal').classList.remove('hidden');
        }

        // Close survey form
        function closeSurveyModal() {
            document.getElementById('surveyModal').classList.add('hidden');
            document.getElementById('surveyForm').reset();
        }

        // Edit survey record
        function editSurveyRecord(recordId) {
            const [barangayId, recordNum] = recordId.split('-');
            const record = surveyData.records[barangayId][recordNum - 1];
            
            // Populate form with existing data
            document.getElementById('modalBarangayTitle').textContent = `Edit Survey - ${record.subject}`;
            document.getElementById('barangayId').value = barangayId;
            document.getElementById('surveyDate').value = record.date;
            document.getElementById('surveySubject').value = record.subject;
            document.getElementById('surveyClassification').value = record.classification;
            document.getElementById('surveyStatus').value = record.status;
            document.getElementById('surveyorName').value = record.surveyor;
            document.getElementById('householdCount').value = record.households;
            document.getElementById('surveyNotes').value = record.notes;
            
            document.getElementById('surveyModal').classList.remove('hidden');
        }

        // Delete survey record
        function deleteSurveyRecord(recordId) {
            if (confirm('Are you sure you want to delete this survey record?')) {
                const [barangayId, recordNum] = recordId.split('-');
                surveyData.records[barangayId].splice(recordNum - 1, 1);
                
                // Refresh the view
                showBarangayRecords(barangayId);
                alert('Survey record deleted successfully!');
            }
        }

        // Handle form submission
        document.getElementById('surveyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = {
                barangayId: document.getElementById('barangayId').value,
                date: document.getElementById('surveyDate').value,
                subject: document.getElementById('surveySubject').value,
                classification: document.getElementById('surveyClassification').value,
                status: document.getElementById('surveyStatus').value,
                surveyor: document.getElementById('surveyorName').value,
                households: document.getElementById('householdCount').value,
                notes: document.getElementById('surveyNotes').value
                // In a real app, you would handle file uploads here
            };
            
            // Here you would typically send the data to your server
            console.log('Survey submitted:', formData);
            
            // For demo purposes, add to our sample data
            if (!surveyData.records[formData.barangayId]) {
                surveyData.records[formData.barangayId] = [];
            }
            
            // Generate a unique ID for this record
            const recordId = `${formData.barangayId}-${surveyData.records[formData.barangayId].length + 1}`;
            
            // Add the record
            surveyData.records[formData.barangayId].push({
                id: recordId,
                date: formData.date,
                subject: formData.subject,
                classification: formData.classification,
                status: formData.status,
                surveyor: formData.surveyor,
                households: formData.households,
                notes: formData.notes,
                photos: [] // In a real app, you would store uploaded photos
            });
            
            // Update statistics
            surveyData.completed++;
            surveyData.today++;
            document.getElementById('completedSurveys').textContent = surveyData.completed;
            document.getElementById('todaySurveys').textContent = surveyData.today;
            
            // Show success message
            alert(`Survey for Barangay ${formData.barangayId} submitted successfully!`);
            
            // Close modal and refresh view
            closeSurveyModal();
            showBarangayRecords(formData.barangayId);
        });

        // Helper function to get status badge class
        function getStatusClass(status) {
            switch(status) {
                case 'Completed': return 'status-completed';
                case 'Pending': return 'status-pending';
                case 'Ongoing': return 'status-ongoing';
                default: return '';
            }
        }

        // Quick action functions
        function generateReport() {
            alert('Exporting survey data...');
            // In a real app, this would generate and download a report
        }

        function viewSummary() {
            alert('Showing survey summary...');
            // In a real app, this would show summary charts/reports
        }
    </script>
</body>
</html>