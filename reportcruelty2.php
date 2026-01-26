<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require('config/databaseconnection.php'); 
include_once './notification.php';
// Fetch cruelty reports
$crueltyReportQuery = "SELECT crueltyID, reportDate, description, evidence, animalStreetAddress, 
           animalCity, reporterFirstName, reporterLastName, reporterEmail, 
           reporterPhone, status, animalType, incidentType
    FROM crueltyreport 
    WHERE isDeleted = 0
    ORDER BY reportDate DESC
";

$result = $conn->query($crueltyReportQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cruelty Reports</title>
<link rel="stylesheet" href="style2.css">
<style>
    h1 {
      color: #003366;
      text-align: center;
      border-bottom: 2px solid #FF8C00;
      padding-bottom: 0.5rem;
    }
    blockquote {
      font-style: italic;
      text-align: center;
      color: #003366;
      font-size: 1.1rem;
      margin: 1.5rem 0;
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.6);
    }
    .modal-content {
      background: #fff;
      margin: 5% auto;
      padding: 2rem;
      border-radius: 10px;
      max-width: 700px;
      position: relative;
    }
    .close {
      position: absolute;
      right: 1rem;
      top: 1rem;
      font-size: 1.5rem;
      cursor: pointer;
    }
    .status-form {
      margin-top: 1rem;
    }
    textarea {
      width: 100%;
      padding: 6px;
      resize: none;
    }
    .badge {
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 0.85rem;
      font-weight: bold;
      color: white;
      display: inline-block;
    }
    .badge.open { background-color: green; }
    .badge.pending { background-color: orange; }
    .badge.closed { background-color: gray; }

    @media (max-width: 600px) {
      body { padding: 1rem; }
    }
</style>
</head>
<body>

<div class="dashboard-container">
   <?php include 'sidebar2.php'; ?>
   <div id="main" class="main-content">
      <h1>Cruelty Reports Page</h1>
      <blockquote>"Record every rescue — every life matters."</blockquote>
      <div class="card">

      
      <a href="cruelty.php"><button>New Report</button></a>

      <table id="reportTable">
          <tr>
              <th>Report By</th>
              <th>Report Date</th>
              <th>Street Address</th>
              <th>Incident Type</th>
              <th>Status</th>
              <th>Action</th>
          </tr>
          <?php
          if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  $first = trim($row['reporterFirstName'] ?? '');
                  $last  = trim($row['reporterLastName'] ?? '');
                  $reportBy = $first || $last ? htmlspecialchars("$first $last") : "Anonymous";

                  $status = $row['status'] ?? 'Open';
                  $badgeClass = strtolower($status);

                  echo "<tr data-report='" . json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT) . "'>";
                  echo "<td>$reportBy</td>";
                  echo "<td>" . date("d-M-Y", strtotime($row['reportDate'])) . "</td>";
                  echo "<td>" . htmlspecialchars($row['animalStreetAddress'] ?? '') . "</td>";
                  echo "<td>" . htmlspecialchars($row['incidentType'] ?? '') . "</td>";
                  echo "<td><span class='badge $badgeClass'>" . htmlspecialchars($status) . "</span></td>";

                  echo "<td>
                          <form method='POST' action='./controllers/CrueltyReportController.php' 
                                onsubmit='return confirm(\"Are you sure you want to delete this report?\");' 
                                style='display:inline;'>
                              <input type='hidden' name='action' value='softDelete'>
                              <input type='hidden' name='crueltyID' value='" . htmlspecialchars($row['crueltyID']) . "'>
                              <button type='submit' class='delete-btn'>Delete</button>
                          </form>
                        </td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='6'>No records found</td></tr>";
          }
          $conn->close();
          ?>
      </table>
   </div>
</div>
</div>
<!-- Modal -->
<div id="reportModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>

    <div id="modalBody"></div>
  </div>
</div>

<?php include 'footer2.php'; ?>
<script src="sidebar2.js"></script>
<script>
const rows = document.querySelectorAll("#reportTable tr[data-report]");
const modal = document.getElementById("reportModal");
const modalBody = document.getElementById("modalBody");
const closeBtn = document.querySelector(".close");

rows.forEach(row => {
  row.addEventListener("click", (e) => {
    if (e.target.closest("form")) return;

    const data = JSON.parse(row.getAttribute("data-report"));
    modalBody.innerHTML = `
      <h3>Reporter Details</h3>
      <p><strong>Name:</strong> ${data.reporterFirstName ?? ''} ${data.reporterLastName ?? ''}</p>
      <p><strong>Email:</strong> ${data.reporterEmail ?? 'N/A'}</p>
      <p><strong>Phone:</strong> ${data.reporterPhone ?? 'N/A'}</p>

      <h3>Report Information</h3>
      <p><strong>Date:</strong> ${data.reportDate}</p>
      <p><strong>Street:</strong> ${data.animalStreetAddress ?? ''}</p>
      <p><strong>City:</strong> ${data.animalCity ?? ''}</p>
      <p><strong>Type:</strong> ${data.incidentType ?? ''}</p>
      <p><strong>Animal:</strong> ${data.animalType ?? ''}</p>
      <p><strong>Description:</strong><br>
        <textarea rows="4" readonly>${data.description ?? ''}</textarea>
      </p>
      <p><strong>Evidence:</strong> ${data.evidence ? `<a href="./images/reports/${data.evidence}">View</a>` : 'N/A'}</p>

      <form method='POST' action='update_status.php' class='status-form'>
        <input type='hidden' name='crueltyID' value='${data.crueltyID}'>
        <select name='status' class='status-dropdown' onchange='this.form.submit()'>
            <option value='Open' ${data.status === 'Open' ? 'selected' : ''}>Open</option>
            <option value='Pending' ${data.status === 'Pending' ? 'selected' : ''}>Pending</option>
            <option value='Closed' ${data.status === 'Closed' ? 'selected' : ''}>Closed</option>
        </select>
      </form>
    `;
    modal.style.display = "block";
  });
});

closeBtn.onclick = () => modal.style.display = "none";
window.onclick = (e) => { if (e.target == modal) modal.style.display = "none"; };
</script>
</body>
</html>
