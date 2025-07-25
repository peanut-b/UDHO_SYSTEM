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
          <a href="\UDHO%20SYSTEM\Operation\operation_dashboard.php" class="flex items-center py-2.5 px-4 hover:bg-gray-700">
            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
          </a>
        </li>
        <li>
          <a href="\UDHO%20SYSTEM\Settings\setting.php" class="flex items-center py-2.5 px-4 hover:bg-gray-700">
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
      <h1 class="text-2xl font-bold text-gray-800">Operation Panel</h1>
      <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
        <input type="text" placeholder="Search" class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
        
        
        
        
        <div class="flex items-center gap-2">
          <img src="\UDHO%20SYSTEM\assets\UDHOLOGO.png" alt="Logo" class="h-8">
          <span class="font-medium text-gray-700">Urban Development and Housing Office</span>
        </div>
      </div>
    </header>

    <div class="bg-white p-4 md:p-6 rounded-lg shadow-md">
      <h2 class="text-xl border-b-2 border-purple-600 pb-2">PDC Database</h2>
      <button onclick="openModal()" class="mt-4 mb-5 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition flex items-center">
        <i class="fas fa-plus mr-2"></i> Add Data
      </button>

      <div class="overflow-x-auto">
        <table id="dataTable" class="w-full border">
          <thead>
            <tr class="bg-purple-600 text-white">
              <th class="border px-4 py-2">DATE OF ISSUED</th>
              <th class="border px-4 py-2">SUBJECT</th>
              <th class="border px-4 py-2">CASE FILE No.</th>
              <th class="border px-4 py-2">ARCHIVE No.</th>
              <th class="border px-4 py-2">ACTIONS</th>
            </tr>
          </thead>
          <tbody id="dataBody"></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Add/Edit Modal -->
  <div id="addDataModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md max-h-[90vh] flex flex-col">
      <!-- Modal Header (Fixed) -->
      <div class="p-4 border-b sticky top-0 bg-white z-10">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-bold" id="modalTitle">Add New Record</h3>
          <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      
      <!-- Scrollable Content Area -->
      <div id="modalContent" class="overflow-y-auto flex-1 p-4">
        <form id="addDataForm" class="space-y-4">
          <div>
            <label class="block mb-1 font-medium">Date Issued</label>
            <input type="date" id="dateIssued" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required />
          </div>
          <div>
            <label class="block mb-1 font-medium">Subject</label>
            <textarea id="subject" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required></textarea>
          </div>
          <div>
            <label class="block mb-1 font-medium">Case File No.</label>
            <input type="text" id="caseFile" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required />
          </div>
          <div>
            <label class="block mb-1 font-medium">Archive No.</label>
            <input type="text" id="archiveNo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required />
          </div>
          <div>
            <label class="block mb-1 font-medium">Attach Documents</label>
            <input type="file" id="docScanner" accept="image/*" multiple class="block mb-2 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            <div class="mb-2">
              <button type="button" onclick="startCamera()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md transition flex items-center">
                <i class="fas fa-camera mr-2"></i> Capture from Camera
              </button>
              <video id="cameraStream" autoplay class="w-full h-48 mt-2 rounded-md hidden"></video>
              <button type="button" onclick="capturePhoto()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md mt-2 hidden transition flex items-center" id="captureBtn">
                <i class="fas fa-camera-retro mr-2"></i> Capture
              </button>
            </div>
            <div id="docPreview" class="flex flex-wrap gap-2 mt-2"></div>
          </div>
        </form>
      </div>
      
      <!-- Modal Footer (Fixed) -->
      <div class="p-4 border-t sticky bottom-0 bg-white flex justify-end gap-2">
        <button type="button" onclick="closeModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">Cancel</button>
        <button type="submit" form="addDataForm" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition flex items-center">
          <i class="fas fa-save mr-2"></i> Save
        </button>
      </div>
    </div>
  </div>

  <!-- View Modal -->
  <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white max-w-2xl w-full p-5 rounded-lg shadow-lg max-h-[90vh] flex flex-col">
      <div class="flex justify-between items-center border-b pb-2 sticky top-0 bg-white z-10">
        <h3 class="text-lg font-bold">Attached Documents</h3>
        <button onclick="closeViewModal()" class="text-gray-500 hover:text-gray-700">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div id="viewImages" class="flex-1 overflow-y-auto p-2 grid grid-cols-2 sm:grid-cols-3 gap-4"></div>
      <div class="text-right mt-4 pt-2 border-t sticky bottom-0 bg-white">
        <button onclick="closeViewModal()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
          Close
        </button>
      </div>
    </div>
  </div>

  <!-- HD Image Modal with Save -->
  <div id="hdImageModal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center hidden z-50 p-4">
    <div class="relative flex flex-col items-center justify-center max-w-full max-h-full">
      <img id="hdImage" class="max-w-full max-h-[80vh] border-4 border-white rounded-lg mb-4" />
      <div class="flex gap-4">
        <a id="downloadLink" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition flex items-center" href="#" download target="_blank">
          <i class="fas fa-download mr-2"></i> Save
        </a>
        <button onclick="closeHDImage()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
          Close
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
    const viewImages = document.getElementById("viewImages");
    const video = document.getElementById("cameraStream");
    const captureBtn = document.getElementById("captureBtn");
    const hdImageModal = document.getElementById("hdImageModal");
    const hdImage = document.getElementById("hdImage");
    const downloadLink = document.getElementById("downloadLink");

    // Variables
    let editTargetRow = null;
    let attachedDocs = [];
    let stream = null;

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

    function showHDImage(src) {
      hdImage.src = src;
      downloadLink.href = src;
      downloadLink.download = `document_${Date.now()}.png`;
      hdImageModal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    }

    // Data Table Functions
    function deleteRow(button) {
      if (confirm("Are you sure you want to delete this record?")) {
        button.closest("tr").remove();
      }
    }

    function editRow(button) {
      const row = button.closest("tr");
      const cells = row.querySelectorAll("td");
      document.getElementById("dateIssued").value = cells[0].textContent;
      document.getElementById("subject").value = cells[1].textContent;
      document.getElementById("caseFile").value = cells[2].textContent;
      document.getElementById("archiveNo").value = cells[3].textContent;
      attachedDocs = JSON.parse(row.dataset.docs || "[]");

      docPreview.innerHTML = "";
      attachedDocs.forEach(src => {
        const img = document.createElement("img");
        img.src = src;
        img.className = "w-16 h-16 object-cover border cursor-pointer rounded";
        img.onclick = () => showHDImage(src);
        docPreview.appendChild(img);
      });

      editTargetRow = row;
      modalTitle.textContent = "Edit Record";
      openModal(true);
    }

    function viewRow(button) {
      const row = button.closest("tr");
      const docs = JSON.parse(row.dataset.docs || "[]");
      viewImages.innerHTML = "";
      docs.forEach(src => {
        const img = document.createElement("img");
        img.src = src;
        img.className = "w-full h-48 object-contain border cursor-pointer rounded";
        img.onclick = () => showHDImage(src);
        viewImages.appendChild(img);
      });
      viewModal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    }

    // Document Handling
    function previewDocs(files) {
      docPreview.innerHTML = "";
      attachedDocs = [];
      [...files].forEach(file => {
        const reader = new FileReader();
        reader.onload = function (e) {
          attachedDocs.push(e.target.result);
          const img = document.createElement("img");
          img.src = e.target.result;
          img.className = "w-16 h-16 object-cover border cursor-pointer rounded";
          img.onclick = () => showHDImage(e.target.result);
          docPreview.appendChild(img);
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
      const img = document.createElement("img");
      img.src = imageData;
      img.className = "w-16 h-16 object-cover border cursor-pointer rounded";
      img.onclick = () => showHDImage(imageData);
      docPreview.appendChild(img);
    }

    // Form Submission
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const date = document.getElementById("dateIssued").value;
      const subject = document.getElementById("subject").value;
      const caseFile = document.getElementById("caseFile").value;
      const archiveNo = document.getElementById("archiveNo").value;

      if (!date || !subject || !caseFile || !archiveNo) {
        alert("Please fill in all required fields");
        return;
      }

      if (editTargetRow) {
        // Update existing row
        const cells = editTargetRow.querySelectorAll("td");
        cells[0].textContent = date;
        cells[1].textContent = subject;
        cells[2].textContent = caseFile;
        cells[3].textContent = archiveNo;
        editTargetRow.dataset.docs = JSON.stringify(attachedDocs);
      } else {
        // Create new row
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td class="border px-4 py-2">${date}</td>
          <td class="border px-4 py-2">${subject}</td>
          <td class="border px-4 py-2">${caseFile}</td>
          <td class="border px-4 py-2">${archiveNo}</td>
          <td class="border px-4 py-2 space-x-1">
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded-md transition" onclick="viewRow(this)">
              <i class="fas fa-eye mr-1"></i> View
            </button>
            <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded-md transition" onclick="editRow(this)">
              <i class="fas fa-edit mr-1"></i> Edit
            </button>
            <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded-md transition" onclick="deleteRow(this)">
              <i class="fas fa-trash mr-1"></i> Delete
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
          archiveNo: "ARC-2023-001",
          docs: []
        },
        {
          date: "2023-07-20",
          subject: "Road expansion survey results",
          caseFile: "CF-2023-002",
          archiveNo: "ARC-2023-002",
          docs: []
        }
      ];

      sampleData.forEach(data => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td class="border px-4 py-2">${data.date}</td>
          <td class="border px-4 py-2">${data.subject}</td>
          <td class="border px-4 py-2">${data.caseFile}</td>
          <td class="border px-4 py-2">${data.archiveNo}</td>
          <td class="border px-4 py-2 space-x-1">
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded-md transition" onclick="viewRow(this)">
              <i class="fas fa-eye mr-1"></i> View
            </button>
            <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded-md transition" onclick="editRow(this)">
              <i class="fas fa-edit mr-1"></i> Edit
            </button>
            <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded-md transition" onclick="deleteRow(this)">
              <i class="fas fa-trash mr-1"></i> Delete
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
