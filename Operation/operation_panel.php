<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Operation Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    textarea#subject {
      min-height: 80px;
      max-height: 200px;
      resize: vertical;
    }
    
    /* Custom scrollbar for modal */
    #modalContent::-webkit-scrollbar {
      width: 8px;
    }
    #modalContent::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }
    #modalContent::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 4px;
    }
    #modalContent::-webkit-scrollbar-thumb:hover {
      background: #555;
    }
    
    /* Compact table styling */
    .compact-table th, .compact-table td {
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
    }
    
    .action-buttons {
      white-space: nowrap;
    }
    
    .action-buttons button {
      padding: 0.25rem 0.5rem;
      margin: 0 0.125rem;
    }
    
    /* Landscape modal styling */
    .landscape-modal {
      width: 90%;
      max-width: 900px;
    }
    
    /* Photo preview with delete button */
    .photo-container {
      position: relative;
      display: inline-block;
      margin: 0.25rem;
    }
    
    .delete-photo {
      position: absolute;
      top: -8px;
      right: -8px;
      background: #ef4444;
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
      cursor: pointer;
      border: 1px solid white;
    }
    
    /* Confirmation modal styles */
    .confirmation-modal {
      max-width: 400px;
      width: 90%;
    }
    
    .confirmation-buttons {
      display: flex;
      justify-content: flex-end;
      gap: 0.5rem;
      margin-top: 1rem;
    }
  </style>
</head>

<body class="bg-gray-100 min-h-screen flex">
  <!-- Sidebar -->
  <div class="w-64 bg-gray-800 text-white flex flex-col">
    <div class="flex items-center justify-center h-24">
      <div class="rounded-full bg-gray-200 w-20 h-20 flex items-center justify-center overflow-hidden border2 border-white shadow-md">
        <img src="/UDHO%20SYSTEM/assets/PROFILE_SAMPLE.jpg" 
             alt="Profile Picture" 
             class="w-full h-full object-cover">
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
          <a href="\UDHO%20SYSTEM\Settings\setting_operation.php" class="flex items-center py-2.5 px-4 hover:bg-gray-700">
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
  <div class="flex-1 p-4 md:p-6">
    <header class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
      <h1 class="text-xl font-bold text-gray-800">Operation Panel</h1>
      <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
        <input type="text" placeholder="Search" class="w-full md:w-64 px-3 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm" />
        <div class="flex items-center gap-2">
          <img src="\UDHO%20SYSTEM\assets\UDHOLOGO.png" alt="Logo" class="h-6">
          <span class="font-medium text-gray-700 text-sm">Urban Development and Housing Office</span>
        </div>
      </div>
    </header>

    <div class="bg-white p-3 rounded-lg shadow-md">
      <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg border-b-2 border-purple-600 pb-1">PDC Database</h2>
        <button onclick="openModal()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md transition flex items-center text-sm">
          <i class="fas fa-plus mr-1"></i> Add Data
        </button>
      </div>

      <div class="overflow-x-auto">
        <table id="dataTable" class="w-full border compact-table">
          <thead>
            <tr class="bg-purple-600 text-white">
              <th class="border px-2 py-1 text-left">Date Issued</th>
              <th class="border px-2 py-1 text-left">Subject</th>
              <th class="border px-2 py-1 text-left">Case File No.</th>
              <th class="border px-2 py-1 text-left">Branch</th>
              <th class="border px-2 py-1 text-left">Barangay</th>
              <th class="border px-2 py-1 text-left">Households</th>
              <th class="border px-2 py-1 text-left">Status</th>
              <th class="border px-2 py-1 text-left">Activities</th>
              <th class="border px-2 py-1 text-center">Actions</th>
            </tr>
          </thead>
          <tbody id="dataBody"></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Add/Edit Modal -->
  <div id="addDataModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-lg shadow-lg landscape-modal max-h-[90vh] flex flex-col border border-gray-300">
      <!-- Modal Header -->
      <div class="p-3 border-b bg-gray-50">
        <div class="flex justify-between items-center">
          <h3 class="text-md font-bold" id="modalTitle">Add New Record</h3>
          <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      
      <!-- Scrollable Content Area -->
      <div id="modalContent" class="overflow-y-auto flex-1 p-4">
        <form id="addDataForm" class="space-y-3">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
              <label class="block mb-1 font-medium text-sm">Date Issued</label>
              <input type="date" id="dateIssued" class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm" required />
            </div>
            <div>
              <label class="block mb-1 font-medium text-sm">Case File No.</label>
              <input type="text" id="caseFile" class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm" required />
            </div>
            <div>
              <label class="block mb-1 font-medium text-sm">Branch</label>
              <input type="text" id="branch" class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm" required />
            </div>
            <div>
              <label class="block mb-1 font-medium text-sm">Affected Barangay</label>
              <input type="text" id="affectedBarangay" class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm" required />
            </div>
            <div>
              <label class="block mb-1 font-medium text-sm">No. of Household Affected</label>
              <input type="number" id="householdAffected" class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm" required />
            </div>
            <div>
              <label class="block mb-1 font-medium text-sm">Status</label>
              <select id="status" class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm" required>
                <option value="">Select Status</option>
                <option value="CENSUS">CENSUS</option>
                <option value="DEMOLISHED">DEMOLISHED</option>
                <option value="EVICTED">EVICTED</option>
                <option value="DEMOLISHED AND EVICTED">DEMOLISHED AND EVICTED</option>
              </select>
            </div>
            <div>
              <label class="block mb-1 font-medium text-sm">Activities</label>
              <select id="activities" class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm" required>
                <option value="">Select Activity</option>
                <option value="PENDING">PENDING</option>
                <option value="ONGOING">ONGOING</option>
                <option value="CANCELLED">CANCELLED</option>
                <option value="EXECUTED">EXECUTED</option>
              </select>
            </div>
          </div>
          
          <div>
            <label class="block mb-1 font-medium text-sm">Subject</label>
            <textarea id="subject" rows="3" class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm" required></textarea>
          </div>
          
          <div>
            <label class="block mb-1 font-medium text-sm">Attach Documents</label>
            <input type="file" id="docScanner" accept="image/*" multiple class="block mb-2 w-full text-sm text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            <div class="mb-2">
              <button type="button" onclick="startCamera()" class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded-md transition flex items-center text-xs">
                <i class="fas fa-camera mr-1"></i> Capture from Camera
              </button>
              <video id="cameraStream" autoplay class="w-full h-40 mt-2 rounded-md hidden"></video>
              <button type="button" onclick="capturePhoto()" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded-md mt-2 hidden transition flex items-center text-xs" id="captureBtn">
                <i class="fas fa-camera-retro mr-1"></i> Capture
              </button>
            </div>
            <div id="docPreview" class="flex flex-wrap gap-2 mt-2"></div>
          </div>
        </form>
      </div>
      
      <!-- Modal Footer -->
      <div class="p-3 border-t bg-gray-50 flex justify-end gap-2">
        <button type="button" onclick="closeModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded-md transition text-sm">Cancel</button>
        <button type="submit" form="addDataForm" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md transition flex items-center text-sm">
          <i class="fas fa-save mr-1"></i> Save
        </button>
      </div>
    </div>
  </div>

  <!-- View Modal -->
  <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white max-w-2xl w-full p-4 rounded-lg shadow-lg max-h-[90vh] flex flex-col border border-gray-300">
      <div class="flex justify-between items-center border-b pb-2 bg-gray-50">
        <h3 class="text-md font-bold">Record Details</h3>
        <button onclick="closeViewModal()" class="text-gray-500 hover:text-gray-700">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div id="viewDetails" class="flex-1 overflow-y-auto p-3 space-y-3 text-sm">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="font-medium">Date Issued:</label>
            <p id="viewDateIssued" class="mt-0.5"></p>
          </div>
          <div>
            <label class="font-medium">Case File No.:</label>
            <p id="viewCaseFile" class="mt-0.5"></p>
          </div>
          <div>
            <label class="font-medium">Branch:</label>
            <p id="viewBranch" class="mt-0.5"></p>
          </div>
          <div>
            <label class="font-medium">Affected Barangay:</label>
            <p id="viewAffectedBarangay" class="mt-0.5"></p>
          </div>
          <div>
            <label class="font-medium">No. of Household Affected:</label>
            <p id="viewHouseholdAffected" class="mt-0.5"></p>
          </div>
          <div>
            <label class="font-medium">Status:</label>
            <p id="viewStatus" class="mt-0.5"></p>
          </div>
          <div>
            <label class="font-medium">Activities:</label>
            <p id="viewActivities" class="mt-0.5"></p>
          </div>
        </div>
        <div>
          <label class="font-medium">Subject:</label>
          <p id="viewSubject" class="mt-0.5"></p>
        </div>
        <div class="mt-3">
          <label class="font-medium block mb-1">Attached Documents</label>
          <div id="viewImages" class="grid grid-cols-2 sm:grid-cols-3 gap-2"></div>
        </div>
      </div>
      <div class="p-3 border-t bg-gray-50">
        <button onclick="closeViewModal()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md transition text-sm float-right">
          Close
        </button>
      </div>
    </div>
  </div>

  <!-- HD Image Modal with Save -->
  <div id="hdImageModal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center hidden z-50 p-4">
    <div class="relative flex flex-col items-center justify-center max-w-full max-h-full">
      <img id="hdImage" class="max-w-full max-h-[80vh] border-4 border-white rounded-lg mb-2" />
      <div class="flex gap-2">
        <a id="downloadLink" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md transition flex items-center text-sm" href="#" download target="_blank">
          <i class="fas fa-download mr-1"></i> Save
        </a>
        <button onclick="closeHDImage()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md transition text-sm">
          Close
        </button>
      </div>
    </div>
  </div>

  <!-- Delete Record Confirmation Modal -->
  <div id="deleteRecordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-lg shadow-lg p-4 confirmation-modal">
      <div class="flex justify-between items-center border-b pb-2 mb-3">
        <h3 class="text-md font-bold">Confirm Deletion</h3>
        <button onclick="closeDeleteRecordModal()" class="text-gray-500 hover:text-gray-700">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <p>Are you sure you want to delete this record? This action cannot be undone.</p>
      <div class="confirmation-buttons">
        <button onclick="closeDeleteRecordModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded-md transition text-sm">
          Cancel
        </button>
        <button id="confirmDeleteRecord" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md transition text-sm">
          Delete
        </button>
      </div>
    </div>
  </div>

  <!-- Delete Photo Confirmation Modal -->
  <div id="deletePhotoModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-lg shadow-lg p-4 confirmation-modal">
      <div class="flex justify-between items-center border-b pb-2 mb-3">
        <h3 class="text-md font-bold">Confirm Photo Deletion</h3>
        <button onclick="closeDeletePhotoModal()" class="text-gray-500 hover:text-gray-700">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <p>Are you sure you want to delete this photo? This action cannot be undone.</p>
      <div class="confirmation-buttons">
        <button onclick="closeDeletePhotoModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded-md transition text-sm">
          Cancel
        </button>
        <button id="confirmDeletePhoto" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md transition text-sm">
          Delete
        </button>
      </div>
    </div>
  </div>

  <script>
    // DOM Elements
    const modal = document.getElementById("addDataModal");
    const form = document.getElementById("addDataForm");
    const modalTitle = document.getElementById("modalTitle");
    const docScanner = document.getElementById("docScanner");
    const docPreview = document.getElementById("docPreview");
    const dataBody = document.getElementById("dataBody");
    const viewModal = document.getElementById("viewModal");
    const viewDetails = document.getElementById("viewDetails");
    const viewImages = document.getElementById("viewImages");
    const video = document.getElementById("cameraStream");
    const captureBtn = document.getElementById("captureBtn");
    const hdImageModal = document.getElementById("hdImageModal");
    const hdImage = document.getElementById("hdImage");
    const downloadLink = document.getElementById("downloadLink");
    const deleteRecordModal = document.getElementById("deleteRecordModal");
    const deletePhotoModal = document.getElementById("deletePhotoModal");
    const confirmDeleteRecordBtn = document.getElementById("confirmDeleteRecord");
    const confirmDeletePhotoBtn = document.getElementById("confirmDeletePhoto");

    // Variables
    let editTargetRow = null;
    let attachedDocs = [];
    let stream = null;
    let deleteTargetRow = null;
    let deletePhotoIndex = null;

    // Modal Functions
    function openModal(editing = false) {
      modal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
      if (!editing) {
        form.reset();
        docPreview.innerHTML = "";
        attachedDocs = [];
        modalTitle.textContent = "Add New Record";
        stopCamera();
      }
    }

    function closeModal() {
      modal.classList.add("hidden");
      document.body.style.overflow = "auto";
      stopCamera();
    }

    function closeViewModal() {
      viewModal.classList.add("hidden");
      document.body.style.overflow = "auto";
    }

    function closeHDImage() {
      hdImageModal.classList.add("hidden");
      document.body.style.overflow = "auto";
    }

    function openDeleteRecordModal(row) {
      deleteTargetRow = row;
      deleteRecordModal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    }

    function closeDeleteRecordModal() {
      deleteRecordModal.classList.add("hidden");
      document.body.style.overflow = "auto";
      deleteTargetRow = null;
    }

    function openDeletePhotoModal(index) {
      deletePhotoIndex = index;
      deletePhotoModal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    }

    function closeDeletePhotoModal() {
      deletePhotoModal.classList.add("hidden");
      document.body.style.overflow = "auto";
      deletePhotoIndex = null;
    }

    function showHDImage(src) {
      hdImage.src = src;
      downloadLink.href = src;
      downloadLink.download = `document_${Date.now()}.png`;
      hdImageModal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    }

    // Delete photo function
    function deletePhoto(index) {
      openDeletePhotoModal(index);
    }

    // Delete row function
    function deleteRow(button) {
      const row = button.closest("tr");
      openDeleteRecordModal(row);
    }

    // Confirm photo deletion
    confirmDeletePhotoBtn.addEventListener("click", function() {
      if (deletePhotoIndex !== null) {
        attachedDocs.splice(deletePhotoIndex, 1);
        renderPhotos();
        closeDeletePhotoModal();
      }
    });

    // Confirm record deletion
    confirmDeleteRecordBtn.addEventListener("click", function() {
      if (deleteTargetRow) {
        deleteTargetRow.remove();
        closeDeleteRecordModal();
      }
    });

    // Render photos with delete buttons
    function renderPhotos() {
      docPreview.innerHTML = "";
      attachedDocs.forEach((src, index) => {
        const container = document.createElement("div");
        container.className = "photo-container";
        
        const img = document.createElement("img");
        img.src = src;
        img.className = "w-16 h-16 object-cover border cursor-pointer rounded";
        img.onclick = () => showHDImage(src);
        
        const deleteBtn = document.createElement("span");
        deleteBtn.className = "delete-photo";
        deleteBtn.innerHTML = "×";
        deleteBtn.onclick = (e) => {
          e.stopPropagation();
          deletePhoto(index);
        };
        
        container.appendChild(img);
        container.appendChild(deleteBtn);
        docPreview.appendChild(container);
      });
    }

    // Data Table Functions
    function editRow(button) {
      const row = button.closest("tr");
      const cells = row.querySelectorAll("td");
      document.getElementById("dateIssued").value = cells[0].textContent;
      document.getElementById("subject").value = cells[1].textContent;
      document.getElementById("caseFile").value = cells[2].textContent;
      document.getElementById("branch").value = cells[3].textContent;
      document.getElementById("affectedBarangay").value = cells[4].textContent;
      document.getElementById("householdAffected").value = cells[5].textContent;
      document.getElementById("status").value = cells[6].textContent;
      document.getElementById("activities").value = cells[7].textContent;
      
      attachedDocs = JSON.parse(row.dataset.docs || "[]");
      renderPhotos();

      editTargetRow = row;
      modalTitle.textContent = "Edit Record";
      openModal(true);
    }

    function viewRow(button) {
      const row = button.closest("tr");
      const cells = row.querySelectorAll("td");
      
      // Update view modal with all details
      document.getElementById("viewDateIssued").textContent = cells[0].textContent;
      document.getElementById("viewSubject").textContent = cells[1].textContent;
      document.getElementById("viewCaseFile").textContent = cells[2].textContent;
      document.getElementById("viewBranch").textContent = cells[3].textContent;
      document.getElementById("viewAffectedBarangay").textContent = cells[4].textContent;
      document.getElementById("viewHouseholdAffected").textContent = cells[5].textContent;
      document.getElementById("viewStatus").textContent = cells[6].textContent;
      document.getElementById("viewActivities").textContent = cells[7].textContent;
      
      const docs = JSON.parse(row.dataset.docs || "[]");
      viewImages.innerHTML = "";
      docs.forEach(src => {
        const img = document.createElement("img");
        img.src = src;
        img.className = "w-full h-32 object-contain border cursor-pointer rounded";
        img.onclick = () => showHDImage(src);
        viewImages.appendChild(img);
      });
      
      viewModal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    }

    // Document Handling
    function previewDocs(files) {
      [...files].forEach(file => {
        const reader = new FileReader();
        reader.onload = function (e) {
          attachedDocs.push(e.target.result);
          renderPhotos();
        };
        reader.readAsDataURL(file);
      });
    }

    docScanner.addEventListener("change", () => previewDocs(docScanner.files));

    // Camera Functions
    function startCamera() {
      navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
        .then(s => {
          stream = s;
          video.srcObject = stream;
          video.classList.remove("hidden");
          captureBtn.classList.remove("hidden");
        })
        .catch(err => {
          console.error("Camera error:", err);
          alert("Could not access camera. Please check permissions.");
        });
    }

    function stopCamera() {
      if (stream) {
        stream.getTracks().forEach(track => track.stop());
        video.srcObject = null;
        stream = null;
      }
      video.classList.add("hidden");
      captureBtn.classList.add("hidden");
    }

    function capturePhoto() {
      const canvas = document.createElement("canvas");
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      canvas.getContext("2d").drawImage(video, 0, 0);
      const imageData = canvas.toDataURL("image/png");

      attachedDocs.push(imageData);
      renderPhotos();
    }

    // Form Submission
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const date = document.getElementById("dateIssued").value;
      const subject = document.getElementById("subject").value;
      const caseFile = document.getElementById("caseFile").value;
      const branch = document.getElementById("branch").value;
      const affectedBarangay = document.getElementById("affectedBarangay").value;
      const householdAffected = document.getElementById("householdAffected").value;
      const status = document.getElementById("status").value;
      const activities = document.getElementById("activities").value;

      if (!date || !subject || !caseFile || !branch || !affectedBarangay || !householdAffected || !status || !activities) {
        alert("Please fill in all required fields");
        return;
      }

      if (editTargetRow) {
        // Update existing row
        const cells = editTargetRow.querySelectorAll("td");
        cells[0].textContent = date;
        cells[1].textContent = subject;
        cells[2].textContent = caseFile;
        cells[3].textContent = branch;
        cells[4].textContent = affectedBarangay;
        cells[5].textContent = householdAffected;
        cells[6].textContent = status;
        cells[7].textContent = activities;
        editTargetRow.dataset.docs = JSON.stringify(attachedDocs);
      } else {
        // Create new row
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td class="border px-2 py-1">${date}</td>
          <td class="border px-2 py-1">${subject}</td>
          <td class="border px-2 py-1">${caseFile}</td>
          <td class="border px-2 py-1">${branch}</td>
          <td class="border px-2 py-1">${affectedBarangay}</td>
          <td class="border px-2 py-1">${householdAffected}</td>
          <td class="border px-2 py-1">${status}</td>
          <td class="border px-2 py-1">${activities}</td>
          <td class="border px-2 py-1 action-buttons">
            <button class="bg-blue-500 hover:bg-blue-600 text-white rounded-md transition" onclick="viewRow(this)">
              <i class="fas fa-eye"></i>
            </button>
            <button class="bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition" onclick="editRow(this)">
              <i class="fas fa-edit"></i>
            </button>
            <button class="bg-red-500 hover:bg-red-600 text-white rounded-md transition" onclick="deleteRow(this)">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        `;
        tr.dataset.docs = JSON.stringify(attachedDocs);
        dataBody.appendChild(tr);
      }

      closeModal();
    });

    // Sample data for demonstration
    document.addEventListener("DOMContentLoaded", function() {
      // Add some sample data
      const sampleData = [
        {
          date: "2023-06-15",
          subject: "Land survey for new housing project",
          caseFile: "CF-2023-001",
          branch: "Main",
          affectedBarangay: "Brgy. 1",
          householdAffected: 25,
          status: "CENSUS",
          activities: "PENDING",
          docs: []
        },
        {
          date: "2023-07-20",
          subject: "Road expansion survey results",
          caseFile: "CF-2023-002",
          branch: "East",
          affectedBarangay: "Brgy. 2",
          householdAffected: 42,
          status: "DEMOLISHED",
          activities: "EXECUTED",
          docs: []
        }
      ];

      sampleData.forEach(data => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td class="border px-2 py-1">${data.date}</td>
          <td class="border px-2 py-1">${data.subject}</td>
          <td class="border px-2 py-1">${data.caseFile}</td>
          <td class="border px-2 py-1">${data.branch}</td>
          <td class="border px-2 py-1">${data.affectedBarangay}</td>
          <td class="border px-2 py-1">${data.householdAffected}</td>
          <td class="border px-2 py-1">${data.status}</td>
          <td class="border px-2 py-1">${data.activities}</td>
          <td class="border px-2 py-1 action-buttons">
            <button class="bg-blue-500 hover:bg-blue-600 text-white rounded-md transition" onclick="viewRow(this)">
              <i class="fas fa-eye"></i>
            </button>
            <button class="bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition" onclick="editRow(this)">
              <i class="fas fa-edit"></i>
            </button>
            <button class="bg-red-500 hover:bg-red-600 text-white rounded-md transition" onclick="deleteRow(this)">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        `;
        tr.dataset.docs = JSON.stringify(data.docs);
        dataBody.appendChild(tr);
      });
    });
  </script>
</body>

</html>