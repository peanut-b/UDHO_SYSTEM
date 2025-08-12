<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HOA Records</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .table-container::-webkit-scrollbar {
      height: 8px;
      width: 8px;
    }
    .table-container::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }
    .table-container::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 4px;
    }
    .table-container::-webkit-scrollbar-thumb:hover {
      background: #555;
    }
    .database-section {
      margin-bottom: 2rem;
      border: 1px solid #e2e8f0;
      border-radius: 0.5rem;
      overflow: hidden;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .database-header {
      background-color: #4c51bf;
      color: white;
      padding: 0.75rem 1rem;
      font-weight: bold;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .database-count {
      background-color: rgba(255, 255, 255, 0.2);
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.875rem;
    }
    .database-table {
      width: 100%;
      border-collapse: collapse;
    }
    .database-table th {
      background-color: #edf2f7;
      padding: 0.75rem;
      text-align: left;
      position: sticky;
      top: 0;
    }
    .database-table td {
      padding: 0.75rem;
      border-top: 1px solid #e2e8f0;
    }
    .database-table tr:hover {
      background-color: #f8fafc;
    }
    .status-badge {
      display: inline-block;
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    .status-active {
      background-color: #d1fae5;
      color: #065f46;
    }
    .status-inactive {
      background-color: #f3f4f6;
      color: #6b7280;
    }
    .status-abolished {
      background-color: #fee2e2;
      color: #b91c1c;
    }
    .hoa-details-panel {
      display: none;
      position: fixed;
      top: 0;
      right: 0;
      width: 40%;
      height: 100%;
      background: white;
      box-shadow: -2px 0 10px rgba(0,0,0,0.1);
      z-index: 1000;
      overflow-y: auto;
      padding: 20px;
    }
    .backdrop {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 999;
    }
    .edit-input {
      width: 100%;
      padding: 0.375rem 0.75rem;
      border: 1px solid #d1d5db;
      border-radius: 0.375rem;
      background-color: #f9fafb;
    }
    .edit-select {
      width: 100%;
      padding: 0.375rem 0.75rem;
      border: 1px solid #d1d5db;
      border-radius: 0.375rem;
      background-color: #f9fafb;
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex">
  <!-- Sidebar -->
  <div class="w-64 bg-gray-800 text-white flex flex-col">
    <div class="flex items-center justify-center h-24">
      <div class="rounded-full bg-gray-200 w-20 h-20 flex items-center justify-center overflow-hidden border-2 border-white shadow-md">
        <img src="/assets/PROFILE_SAMPLE.jpg" alt="Profile Picture" class="w-full h-full object-cover">
      </div>
    </div>
    <nav class="mt-6">
      <ul>
        <li>
          <a href="hoa_dashboard.php" class="sidebar-link flex items-center py-3 px-4 active-link">
            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
          </a>
        </li>
        <li>
          <a href="hoa_records.php" class="sidebar-link flex items-center py-3 px-4">
            <i class="fas fa-home mr-3"></i> HOA Management
          </a>
        </li>
        <li>
          <a href="hoa_payment.php" class="sidebar-link flex items-center py-3 px-4 active-link">
            <i class="fas fa-money-bill-wave mr-3"></i> Payment Records
          </a>
        </li>
        <li>
          <a href="/Settings/setting_hoa.php" class="sidebar-link flex items-center py-3 px-4">
            <i class="fas fa-cog mr-3"></i> Settings
          </a>
        </li>
        <li>
          <a href="#" class="sidebar-link flex items-center py-3 px-4 mt-10">
            <i class="fas fa-sign-out-alt mr-3"></i> Logout
          </a>
        </li>
      </ul>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="flex-1 p-4 md:p-10">
    <header class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
      <h1 class="text-2xl font-bold text-gray-800">Homeowners Association Management</h1>
      <div class="flex items-center gap-2">
        <img src="/assets/UDHOLOGO.png" alt="Logo" class="h-8">
        <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
      </div>
    </header>

    <div class="flex flex-wrap gap-2 mb-6">
      <button onclick="filterHOA('all')" data-filter="all" class="filter-btn px-4 py-2 rounded-md bg-blue-500 text-white">
        All HOAs
      </button>
      <button onclick="filterHOA('active')" data-filter="active" class="filter-btn px-4 py-2 rounded-md bg-gray-200 text-gray-700">
        Active
      </button>
      <button onclick="filterHOA('inactive')" data-filter="inactive" class="filter-btn px-4 py-2 rounded-md bg-gray-200 text-gray-700">
        Inactive
      </button>
      <button onclick="filterHOA('abolished')" data-filter="abolished" class="filter-btn px-4 py-2 rounded-md bg-gray-200 text-gray-700">
        Abolished
      </button>
    </div>

    <!-- HOA List Table -->
    <div class="database-section">
      <div class="database-header">
        <div>
          <i class="fas fa-home mr-2"></i> Homeowners Associations
          <span class="database-count">5 records</span>
        </div>
        <div class="flex items-center space-x-2">
          <button class="bg-green-600 text-white px-3 py-1 rounded text-sm">
            <i class="fas fa-plus mr-1"></i> Add HOA
          </button>
          <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm">
            <i class="fas fa-filter mr-1"></i> Filter
          </button>
        </div>
      </div>
      <div class="table-container overflow-x-auto max-h-96">
        <table class="database-table">
          <thead>
            <tr>
              <th>HOA ID</th>
              <th>Association Name</th>
              <th>Barangay</th>
              <th>President</th>
              <th>Contact</th>
              <th>Members</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>HOA-001</td>
              <td>Green Valley Homeowners</td>
              <td>Barangay 1</td>
              <td>Maria Santos</td>
              <td>09123456789</td>
              <td>150</td>
              <td><span class="status-badge status-active">Active</span></td>
              <td>
                <button onclick="showHOADetails('HOA-001')" class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-eye"></i> View
                </button>
              </td>
            </tr>
            <tr>
              <td>HOA-002</td>
              <td>Sunrise Village Association</td>
              <td>Barangay 2</td>
              <td>Robert Garcia</td>
              <td>09234567890</td>
              <td>85</td>
              <td><span class="status-badge status-active">Active</span></td>
              <td>
                <button onclick="showHOADetails('HOA-002')" class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-eye"></i> View
                </button>
              </td>
            </tr>
            <tr>
              <td>HOA-003</td>
              <td>Maple Grove Residents</td>
              <td>Barangay 3</td>
              <td>David Wilson</td>
              <td>09345678901</td>
              <td>120</td>
              <td><span class="status-badge status-active">Active</span></td>
              <td>
                <button onclick="showHOADetails('HOA-003')" class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-eye"></i> View
                </button>
              </td>
            </tr>
            <tr>
              <td>HOA-004</td>
              <td>Pine Crest Community</td>
              <td>Barangay 4</td>
              <td>James Johnson</td>
              <td>09456789012</td>
              <td>95</td>
              <td><span class="status-badge status-inactive">Inactive</span></td>
              <td>
                <button onclick="showHOADetails('HOA-004')" class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-eye"></i> View
                </button>
              </td>
            </tr>
            <tr>
              <td>HOA-005</td>
              <td>Riverbend Homeowners</td>
              <td>Barangay 5</td>
              <td>Michael Davis</td>
              <td>09567890123</td>
              <td>110</td>
              <td><span class="status-badge status-active">Active</span></td>
              <td>
                <button onclick="showHOADetails('HOA-005')" class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-eye"></i> View
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- HOA Details Panel (hidden by default) -->
    <div class="backdrop" id="hoaBackdrop" onclick="closeHOADetails()"></div>
    <div class="hoa-details-panel" id="hoaDetailsPanel">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold" id="hoaDetailsTitle">HOA Details</h2>
        <div>
          <button id="editHoaBtn" onclick="toggleEditMode(true)" class="text-blue-600 hover:text-blue-800 mr-2">
            <i class="fas fa-edit mr-1"></i> Edit
          </button>
          <button id="saveHoaBtn" onclick="saveHOAChanges()" class="text-green-600 hover:text-green-800 mr-2 hidden">
            <i class="fas fa-save mr-1"></i> Save
          </button>
          <button id="cancelEditBtn" onclick="toggleEditMode(false)" class="text-gray-600 hover:text-gray-800 hidden">
            <i class="fas fa-times mr-1"></i> Cancel
          </button>
          <button onclick="closeHOADetails()" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Association Information</h3>
        <div class="bg-gray-50 p-4 rounded">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-500">HOA ID</p>
              <p class="font-medium" id="hoaId">-</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Barangay</p>
              <p class="font-medium" id="hoaBarangay">-</p>
              <input type="text" id="hoaBarangayInput" class="edit-input hidden" value="">
            </div>
            <div>
              <p class="text-sm text-gray-500">Registration Date</p>
              <p class="font-medium" id="hoaRegDate">-</p>
              <input type="date" id="hoaRegDateInput" class="edit-input hidden" value="">
            </div>
            <div>
              <p class="text-sm text-gray-500">Status</p>
              <p class="font-medium" id="hoaStatus">-</p>
              <select id="hoaStatusInput" class="edit-select hidden">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Abolished">Abolished</option>
              </select>
            </div>
            <div class="col-span-2">
              <p class="text-sm text-gray-500">Address</p>
              <p class="font-medium" id="hoaAddress">-</p>
              <input type="text" id="hoaAddressInput" class="edit-input hidden" value="">
            </div>
          </div>
        </div>
      </div>
      
      <div class="mb-6">
        <div class="flex justify-between items-center mb-2">
          <h3 class="text-lg font-semibold">Officials</h3>
          <div>
            <button id="addOfficialBtn" class="text-blue-600 text-sm">
              <i class="fas fa-plus mr-1"></i> Add Official
            </button>
            <button id="saveOfficialsBtn" onclick="saveOfficialsChanges()" class="text-green-600 text-sm hidden">
              <i class="fas fa-save mr-1"></i> Save Changes
            </button>
          </div>
        </div>
        <div class="table-container overflow-x-auto">
          <table class="database-table">
            <thead>
              <tr>
                <th>Position</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Term Start</th>
                <th>Term End</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="hoaOfficialsTable">
              <!-- Will be populated by JavaScript -->
            </tbody>
          </table>
        </div>
      </div>
      
      <div>
        <div class="flex justify-between items-center mb-2">
          <h3 class="text-lg font-semibold">Members (<span id="membersCount">0</span>)</h3>
          <div class="flex space-x-2">
            <button id="addMemberBtn" class="text-blue-600 text-sm">
              <i class="fas fa-plus mr-1"></i> Add Member
            </button>
            <button id="saveMembersBtn" onclick="saveMembersChanges()" class="text-green-600 text-sm hidden">
              <i class="fas fa-save mr-1"></i> Save Changes
            </button>
            <button class="text-gray-600 text-sm">
              <i class="fas fa-download mr-1"></i> Export
            </button>
          </div>
        </div>
        <div class="table-container overflow-x-auto max-h-96">
          <table class="database-table">
            <thead>
              <tr>
                <th>Member ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Contact</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="hoaMembersTable">
              <!-- Will be populated by JavaScript -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Sample data for HOAs
    const hoaData = {
      'HOA-001': {
        name: 'Green Valley Homeowners',
        barangay: 'Barangay 1',
        regDate: '2022-01-15',
        status: 'Active',
        address: '123 Green Valley, Barangay 1',
        officials: [
          { position: 'President', name: 'Maria Santos', contact: '09123456789', termStart: '2022-01-01', termEnd: '2024-01-01' },
          { position: 'Vice President', name: 'Juan Dela Cruz', contact: '09234567890', termStart: '2022-01-01', termEnd: '2024-01-01' },
          { position: 'Secretary', name: 'Anna Reyes', contact: '09345678901', termStart: '2022-01-01', termEnd: '2024-01-01' }
        ],
        members: [
          { id: 'MEM-001', name: 'Robert Johnson', address: '123 Green Valley', contact: '09456789012', status: 'Active' },
          { id: 'MEM-002', name: 'Sarah Williams', address: '456 Green Valley', contact: '09567890123', status: 'Active' },
          { id: 'MEM-003', name: 'Michael Brown', address: '789 Green Valley', contact: '09678901234', status: 'Inactive' }
        ]
      },
      'HOA-002': {
        name: 'Sunrise Village Association',
        barangay: 'Barangay 2',
        regDate: '2021-11-20',
        status: 'Active',
        address: '456 Sunrise St, Barangay 2',
        officials: [
          { position: 'President', name: 'Robert Garcia', contact: '09234567890', termStart: '2021-12-01', termEnd: '2023-12-01' },
          { position: 'Vice President', name: 'Anna Reyes', contact: '09345678901', termStart: '2021-12-01', termEnd: '2023-12-01' }
        ],
        members: [
          { id: 'MEM-004', name: 'Emily Davis', address: '101 Sunrise St', contact: '09789012345', status: 'Active' },
          { id: 'MEM-005', name: 'David Wilson', address: '202 Sunrise St', contact: '09890123456', status: 'Active' }
        ]
      },
      'HOA-003': {
        name: 'Maple Grove Residents',
        barangay: 'Barangay 3',
        regDate: '2020-05-10',
        status: 'Active',
        address: '789 Maple Grove, Barangay 3',
        officials: [
          { position: 'President', name: 'David Wilson', contact: '09345678901', termStart: '2020-06-01', termEnd: '2022-06-01' }
        ],
        members: [
          { id: 'MEM-006', name: 'Jennifer Lee', address: '101 Maple Grove', contact: '09901234567', status: 'Active' },
          { id: 'MEM-007', name: 'Christopher Martin', address: '202 Maple Grove', contact: '09112345678', status: 'Active' }
        ]
      },
      'HOA-004': {
        name: 'Pine Crest Community',
        barangay: 'Barangay 4',
        regDate: '2019-08-25',
        status: 'Inactive',
        address: '321 Pine Crest, Barangay 4',
        officials: [],
        members: []
      },
      'HOA-005': {
        name: 'Riverbend Homeowners',
        barangay: 'Barangay 5',
        regDate: '2023-02-14',
        status: 'Active',
        address: '555 Riverbend, Barangay 5',
        officials: [
          { position: 'President', name: 'Michael Davis', contact: '09567890123', termStart: '2023-03-01', termEnd: '2025-03-01' }
        ],
        members: [
          { id: 'MEM-008', name: 'Jessica Taylor', address: '101 Riverbend', contact: '09223456789', status: 'Active' }
        ]
      }
    };

    let currentHoaId = null;
    let isEditMode = false;
    let originalHoaData = null;

    function showHOADetails(hoaId) {
      currentHoaId = hoaId;
      const hoa = hoaData[hoaId] || {
        name: hoaId,
        barangay: 'N/A',
        regDate: 'N/A',
        status: 'N/A',
        address: 'N/A',
        officials: [],
        members: []
      };

      // Store original data
      originalHoaData = JSON.parse(JSON.stringify(hoa));

      // Update basic info
      document.getElementById('hoaDetailsTitle').textContent = hoa.name;
      document.getElementById('hoaId').textContent = hoaId;
      document.getElementById('hoaBarangay').textContent = hoa.barangay;
      document.getElementById('hoaRegDate').textContent = hoa.regDate;
      document.getElementById('hoaStatus').textContent = hoa.status;
      document.getElementById('hoaAddress').textContent = hoa.address;
      
      // Set input values
      document.getElementById('hoaBarangayInput').value = hoa.barangay;
      document.getElementById('hoaRegDateInput').value = hoa.regDate;
      document.getElementById('hoaStatusInput').value = hoa.status;
      document.getElementById('hoaAddressInput').value = hoa.address;

      // Update officials table
      updateOfficialsTable(hoa.officials);

      // Update members table
      updateMembersTable(hoa.members);

      // Show panel and backdrop
      document.getElementById('hoaDetailsPanel').style.display = 'block';
      document.getElementById('hoaBackdrop').style.display = 'block';
    }

    function updateOfficialsTable(officials) {
      const officialsTable = document.getElementById('hoaOfficialsTable');
      officialsTable.innerHTML = '';
      
      if (officials.length === 0) {
        officialsTable.innerHTML = `
          <tr>
            <td colspan="6" class="text-center py-4 text-gray-500">
              No officials found
            </td>
          </tr>
        `;
        return;
      }

      officials.forEach((official, index) => {
        officialsTable.innerHTML += `
          <tr data-index="${index}">
            <td>
              <span class="official-position">${official.position}</span>
              ${isEditMode ? `
                <select class="edit-select official-position-input hidden" value="${official.position}">
                  <option value="President">President</option>
                  <option value="Vice President">Vice President</option>
                  <option value="Secretary">Secretary</option>
                  <option value="Treasurer">Treasurer</option>
                  <option value="Auditor">Auditor</option>
                  <option value="PRO">PRO</option>
                  <option value="Board Member">Board Member</option>
                </select>
              ` : ''}
            </td>
            <td>
              <span class="official-name">${official.name}</span>
              ${isEditMode ? `<input type="text" class="edit-input official-name-input hidden" value="${official.name}">` : ''}
            </td>
            <td>
              <span class="official-contact">${official.contact}</span>
              ${isEditMode ? `<input type="text" class="edit-input official-contact-input hidden" value="${official.contact}">` : ''}
            </td>
            <td>
              <span class="official-term-start">${official.termStart}</span>
              ${isEditMode ? `<input type="date" class="edit-input official-term-start-input hidden" value="${official.termStart}">` : ''}
            </td>
            <td>
              <span class="official-term-end">${official.termEnd}</span>
              ${isEditMode ? `<input type="date" class="edit-input official-term-end-input hidden" value="${official.termEnd}">` : ''}
            </td>
            <td>
              ${isEditMode ? `
                <button onclick="editOfficialRow(this)" class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-edit"></i>
                </button>
                <button onclick="saveOfficialRow(this)" class="text-green-600 hover:text-green-800 mr-2 hidden">
                  <i class="fas fa-save"></i>
                </button>
                <button onclick="deleteOfficialRow(this)" class="text-red-600 hover:text-red-800">
                  <i class="fas fa-trash"></i>
                </button>
              ` : `
                <button class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-eye"></i>
                </button>
              `}
            </td>
          </tr>
        `;
        
        // Set the select value for position
        if (isEditMode) {
          const row = officialsTable.querySelector(`tr[data-index="${index}"]`);
          if (row) {
            const select = row.querySelector('.official-position-input');
            if (select) select.value = official.position;
          }
        }
      });
      
      // Add "Add Official" row if in edit mode
      if (isEditMode) {
        officialsTable.innerHTML += `
          <tr id="addOfficialRow">
            <td>
              <select class="edit-select" id="newOfficialPosition">
                <option value="President">President</option>
                <option value="Vice President">Vice President</option>
                <option value="Secretary">Secretary</option>
                <option value="Treasurer">Treasurer</option>
                <option value="Auditor">Auditor</option>
                <option value="PRO">PRO</option>
                <option value="Board Member">Board Member</option>
              </select>
            </td>
            <td><input type="text" class="edit-input" id="newOfficialName" placeholder="Name"></td>
            <td><input type="text" class="edit-input" id="newOfficialContact" placeholder="Contact"></td>
            <td><input type="date" class="edit-input" id="newOfficialTermStart"></td>
            <td><input type="date" class="edit-input" id="newOfficialTermEnd"></td>
            <td>
              <button onclick="addNewOfficial()" class="text-green-600 hover:text-green-800">
                <i class="fas fa-plus"></i> Add
              </button>
            </td>
          </tr>
        `;
      }
    }

    function updateMembersTable(members) {
      const membersTable = document.getElementById('hoaMembersTable');
      membersTable.innerHTML = '';
      document.getElementById('membersCount').textContent = members.length;
      
      if (members.length === 0) {
        membersTable.innerHTML = `
          <tr>
            <td colspan="6" class="text-center py-4 text-gray-500">
              No members found
            </td>
          </tr>
        `;
        return;
      }

      members.forEach((member, index) => {
        const statusClass = member.status === 'Active' ? 'status-active' : 'status-inactive';
        
        membersTable.innerHTML += `
          <tr data-index="${index}">
            <td>${member.id}</td>
            <td>
              <span class="member-name">${member.name}</span>
              ${isEditMode ? `<input type="text" class="edit-input member-name-input hidden" value="${member.name}">` : ''}
            </td>
            <td>
              <span class="member-address">${member.address}</span>
              ${isEditMode ? `<input type="text" class="edit-input member-address-input hidden" value="${member.address}">` : ''}
            </td>
            <td>
              <span class="member-contact">${member.contact}</span>
              ${isEditMode ? `<input type="text" class="edit-input member-contact-input hidden" value="${member.contact}">` : ''}
            </td>
            <td>
              <span class="status-badge ${statusClass} member-status">${member.status}</span>
              ${isEditMode ? `
                <select class="edit-select member-status-input hidden">
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              ` : ''}
            </td>
            <td>
              ${isEditMode ? `
                <button onclick="editMemberRow(this)" class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-edit"></i>
                </button>
                <button onclick="saveMemberRow(this)" class="text-green-600 hover:text-green-800 mr-2 hidden">
                  <i class="fas fa-save"></i>
                </button>
                <button onclick="deleteMemberRow(this)" class="text-red-600 hover:text-red-800">
                  <i class="fas fa-trash"></i>
                </button>
              ` : `
                <button class="text-blue-600 hover:text-blue-800 mr-2">
                  <i class="fas fa-eye"></i>
                </button>
              `}
            </td>
          </tr>
        `;
        
        // Set the select value for status
        if (isEditMode) {
          const row = membersTable.querySelector(`tr[data-index="${index}"]`);
          if (row) {
            const select = row.querySelector('.member-status-input');
            if (select) select.value = member.status;
          }
        }
      });
      
      // Add "Add Member" row if in edit mode
      if (isEditMode) {
        membersTable.innerHTML += `
          <tr id="addMemberRow">
            <td>MEM-${String(members.length + 1).padStart(3, '0')}</td>
            <td><input type="text" class="edit-input" id="newMemberName" placeholder="Name"></td>
            <td><input type="text" class="edit-input" id="newMemberAddress" placeholder="Address"></td>
            <td><input type="text" class="edit-input" id="newMemberContact" placeholder="Contact"></td>
            <td>
              <select class="edit-select" id="newMemberStatus">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </td>
            <td>
              <button onclick="addNewMember()" class="text-green-600 hover:text-green-800">
                <i class="fas fa-plus"></i> Add
              </button>
            </td>
          </tr>
        `;
      }
    }

    function closeHOADetails() {
      document.getElementById('hoaDetailsPanel').style.display = 'none';
      document.getElementById('hoaBackdrop').style.display = 'none';
      toggleEditMode(false); // Reset edit mode when closing
    }

    function toggleEditMode(enable) {
      isEditMode = enable;
      
      // Toggle edit buttons
      document.getElementById('editHoaBtn').classList.toggle('hidden', enable);
      document.getElementById('saveHoaBtn').classList.toggle('hidden', !enable);
      document.getElementById('cancelEditBtn').classList.toggle('hidden', !enable);
      document.getElementById('saveOfficialsBtn').classList.toggle('hidden', !enable);
      document.getElementById('saveMembersBtn').classList.toggle('hidden', !enable);
      
      // Toggle input fields
      document.getElementById('hoaBarangay').classList.toggle('hidden', enable);
      document.getElementById('hoaBarangayInput').classList.toggle('hidden', !enable);
      document.getElementById('hoaRegDate').classList.toggle('hidden', enable);
      document.getElementById('hoaRegDateInput').classList.toggle('hidden', !enable);
      document.getElementById('hoaStatus').classList.toggle('hidden', enable);
      document.getElementById('hoaStatusInput').classList.toggle('hidden', !enable);
      document.getElementById('hoaAddress').classList.toggle('hidden', enable);
      document.getElementById('hoaAddressInput').classList.toggle('hidden', !enable);
      
      // Re-render tables with edit controls
      if (currentHoaId) {
        const hoa = hoaData[currentHoaId];
        updateOfficialsTable(hoa.officials);
        updateMembersTable(hoa.members);
      }
    }

    function saveHOAChanges() {
      // Get updated values
      const updatedBarangay = document.getElementById('hoaBarangayInput').value;
      const updatedRegDate = document.getElementById('hoaRegDateInput').value;
      const updatedStatus = document.getElementById('hoaStatusInput').value;
      const updatedAddress = document.getElementById('hoaAddressInput').value;
      
      // Update the data
      hoaData[currentHoaId].barangay = updatedBarangay;
      hoaData[currentHoaId].regDate = updatedRegDate;
      hoaData[currentHoaId].status = updatedStatus;
      hoaData[currentHoaId].address = updatedAddress;
      
      // Update the display
      document.getElementById('hoaBarangay').textContent = updatedBarangay;
      document.getElementById('hoaRegDate').textContent = updatedRegDate;
      document.getElementById('hoaStatus').textContent = updatedStatus;
      document.getElementById('hoaAddress').textContent = updatedAddress;
      
      // Update status badge in main table
      const statusBadge = document.querySelector(`table.database-table td:has(span:contains(${currentHoaId})) ~ td ~ td ~ td ~ td span.status-badge`);
      if (statusBadge) {
        statusBadge.className = 'status-badge ' + 
          (updatedStatus === 'Active' ? 'status-active' : 
           updatedStatus === 'Inactive' ? 'status-inactive' : 'status-abolished');
        statusBadge.textContent = updatedStatus;
      }
      
      // Exit edit mode
      toggleEditMode(false);
      
      // Show success message
      alert('HOA information updated successfully!');
    }

    function editOfficialRow(button) {
      const row = button.closest('tr');
      if (!row) return;
      
      // Hide spans and show inputs
      row.querySelector('.official-position').classList.add('hidden');
      row.querySelector('.official-position-input').classList.remove('hidden');
      row.querySelector('.official-name').classList.add('hidden');
      row.querySelector('.official-name-input').classList.remove('hidden');
      row.querySelector('.official-contact').classList.add('hidden');
      row.querySelector('.official-contact-input').classList.remove('hidden');
      row.querySelector('.official-term-start').classList.add('hidden');
      row.querySelector('.official-term-start-input').classList.remove('hidden');
      row.querySelector('.official-term-end').classList.add('hidden');
      row.querySelector('.official-term-end-input').classList.remove('hidden');
      
      // Hide edit button and show save button
      button.classList.add('hidden');
      row.querySelector('button[onclick="saveOfficialRow(this)"]').classList.remove('hidden');
    }

    function saveOfficialRow(button) {
      const row = button.closest('tr');
      if (!row) return;
      const index = row.dataset.index;
      
      // Get updated values
      const updatedPosition = row.querySelector('.official-position-input').value;
      const updatedName = row.querySelector('.official-name-input').value;
      const updatedContact = row.querySelector('.official-contact-input').value;
      const updatedTermStart = row.querySelector('.official-term-start-input').value;
      const updatedTermEnd = row.querySelector('.official-term-end-input').value;
      
      // Update the data
      hoaData[currentHoaId].officials[index].position = updatedPosition;
      hoaData[currentHoaId].officials[index].name = updatedName;
      hoaData[currentHoaId].officials[index].contact = updatedContact;
      hoaData[currentHoaId].officials[index].termStart = updatedTermStart;
      hoaData[currentHoaId].officials[index].termEnd = updatedTermEnd;
      
      // Update the display
      row.querySelector('.official-position').textContent = updatedPosition;
      row.querySelector('.official-name').textContent = updatedName;
      row.querySelector('.official-contact').textContent = updatedContact;
      row.querySelector('.official-term-start').textContent = updatedTermStart;
      row.querySelector('.official-term-end').textContent = updatedTermEnd;
      
      // Show spans and hide inputs
      row.querySelector('.official-position').classList.remove('hidden');
      row.querySelector('.official-position-input').classList.add('hidden');
      row.querySelector('.official-name').classList.remove('hidden');
      row.querySelector('.official-name-input').classList.add('hidden');
      row.querySelector('.official-contact').classList.remove('hidden');
      row.querySelector('.official-contact-input').classList.add('hidden');
      row.querySelector('.official-term-start').classList.remove('hidden');
      row.querySelector('.official-term-start-input').classList.add('hidden');
      row.querySelector('.official-term-end').classList.remove('hidden');
      row.querySelector('.official-term-end-input').classList.add('hidden');
      
      // Hide save button and show edit button
      button.classList.add('hidden');
      row.querySelector('button[onclick="editOfficialRow(this)"]').classList.remove('hidden');
    }

    function deleteOfficialRow(button) {
      if (!confirm('Are you sure you want to delete this official?')) return;
      
      const row = button.closest('tr');
      if (!row) return;
      const index = row.dataset.index;
      
      // Remove from data
      hoaData[currentHoaId].officials.splice(index, 1);
      
      // Re-render table
      updateOfficialsTable(hoaData[currentHoaId].officials);
    }

    function addNewOfficial() {
      const position = document.getElementById('newOfficialPosition').value;
      const name = document.getElementById('newOfficialName').value;
      const contact = document.getElementById('newOfficialContact').value;
      const termStart = document.getElementById('newOfficialTermStart').value;
      const termEnd = document.getElementById('newOfficialTermEnd').value;
      
      if (!name || !contact) {
        alert('Please fill in all required fields');
        return;
      }
      
      // Add to data
      hoaData[currentHoaId].officials.push({
        position,
        name,
        contact,
        termStart,
        termEnd
      });
      
      // Re-render table
      updateOfficialsTable(hoaData[currentHoaId].officials);
      
      // Clear inputs
      document.getElementById('newOfficialName').value = '';
      document.getElementById('newOfficialContact').value = '';
      document.getElementById('newOfficialTermStart').value = '';
      document.getElementById('newOfficialTermEnd').value = '';
    }

    function editMemberRow(button) {
      const row = button.closest('tr');
      if (!row) return;
      
      // Hide spans and show inputs
      row.querySelector('.member-name').classList.add('hidden');
      row.querySelector('.member-name-input').classList.remove('hidden');
      row.querySelector('.member-address').classList.add('hidden');
      row.querySelector('.member-address-input').classList.remove('hidden');
      row.querySelector('.member-contact').classList.add('hidden');
      row.querySelector('.member-contact-input').classList.remove('hidden');
      row.querySelector('.member-status').classList.add('hidden');
      row.querySelector('.member-status-input').classList.remove('hidden');
      
      // Hide edit button and show save button
      button.classList.add('hidden');
      row.querySelector('button[onclick="saveMemberRow(this)"]').classList.remove('hidden');
    }

    function saveMemberRow(button) {
      const row = button.closest('tr');
      if (!row) return;
      const index = row.dataset.index;
      
      // Get updated values
      const updatedName = row.querySelector('.member-name-input').value;
      const updatedAddress = row.querySelector('.member-address-input').value;
      const updatedContact = row.querySelector('.member-contact-input').value;
      const updatedStatus = row.querySelector('.member-status-input').value;
      
      // Update the data
      hoaData[currentHoaId].members[index].name = updatedName;
      hoaData[currentHoaId].members[index].address = updatedAddress;
      hoaData[currentHoaId].members[index].contact = updatedContact;
      hoaData[currentHoaId].members[index].status = updatedStatus;
      
      // Update the display
      row.querySelector('.member-name').textContent = updatedName;
      row.querySelector('.member-address').textContent = updatedAddress;
      row.querySelector('.member-contact').textContent = updatedContact;
      row.querySelector('.member-status').textContent = updatedStatus;
      row.querySelector('.member-status').className = 'status-badge ' + 
        (updatedStatus === 'Active' ? 'status-active' : 'status-inactive');
      
      // Show spans and hide inputs
      row.querySelector('.member-name').classList.remove('hidden');
      row.querySelector('.member-name-input').classList.add('hidden');
      row.querySelector('.member-address').classList.remove('hidden');
      row.querySelector('.member-address-input').classList.add('hidden');
      row.querySelector('.member-contact').classList.remove('hidden');
      row.querySelector('.member-contact-input').classList.add('hidden');
      row.querySelector('.member-status').classList.remove('hidden');
      row.querySelector('.member-status-input').classList.add('hidden');
      
      // Hide save button and show edit button
      button.classList.add('hidden');
      row.querySelector('button[onclick="editMemberRow(this)"]').classList.remove('hidden');
    }

    function deleteMemberRow(button) {
      if (!confirm('Are you sure you want to delete this member?')) return;
      
      const row = button.closest('tr');
      if (!row) return;
      const index = row.dataset.index;
      
      // Remove from data
      hoaData[currentHoaId].members.splice(index, 1);
      
      // Re-render table
      updateMembersTable(hoaData[currentHoaId].members);
    }

    function addNewMember() {
      const name = document.getElementById('newMemberName').value;
      const address = document.getElementById('newMemberAddress').value;
      const contact = document.getElementById('newMemberContact').value;
      const status = document.getElementById('newMemberStatus').value;
      
      if (!name || !address || !contact) {
        alert('Please fill in all required fields');
        return;
      }
      
      // Generate new ID
      const memberCount = hoaData[currentHoaId].members.length;
      const newId = 'MEM-' + String(memberCount + 1).padStart(3, '0');
      
      // Add to data
      hoaData[currentHoaId].members.push({
        id: newId,
        name,
        address,
        contact,
        status
      });
      
      // Re-render table
      updateMembersTable(hoaData[currentHoaId].members);
      
      // Clear inputs
      document.getElementById('newMemberName').value = '';
      document.getElementById('newMemberAddress').value = '';
      document.getElementById('newMemberContact').value = '';
    }

    function saveOfficialsChanges() {
      alert('Officials changes saved successfully!');
      toggleEditMode(false);
    }

    function saveMembersChanges() {
      alert('Members changes saved successfully!');
      toggleEditMode(false);
    }

    function filterHOA(status) {
      const rows = document.querySelectorAll('.database-table tbody tr');
      const filterButtons = document.querySelectorAll('.filter-btn');
      
      // Update button styles
      filterButtons.forEach(btn => {
        if (btn.dataset.filter === status) {
          btn.classList.remove('bg-gray-200', 'text-gray-700');
          btn.classList.add('bg-blue-500', 'text-white');
        } else {
          btn.classList.remove('bg-blue-500', 'text-white');
          btn.classList.add('bg-gray-200', 'text-gray-700');
        }
      });
      
      // Filter rows
      rows.forEach(row => {
        const statusBadge = row.querySelector('td:nth-child(7) span');
        if (!statusBadge) return;
        
        const rowStatus = statusBadge.textContent.toLowerCase();
        if (status === 'all' || rowStatus === status.toLowerCase()) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    // Close panel when clicking outside
    document.addEventListener('click', function(event) {
      const panel = document.getElementById('hoaDetailsPanel');
      const backdrop = document.getElementById('hoaBackdrop');
      if (event.target === backdrop) {
        panel.style.display = 'none';
        backdrop.style.display = 'none';
      }
    });
  </script>
</body>
</html>