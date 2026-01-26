<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include_once './notification.php';
require('config/databaseconnection.php');

// --- Fetch Animal Applications ---
$animalSql = "SELECT aa.*, u.username, a.Animal_Name
              FROM animalapplication aa
              LEFT JOIN users u ON aa.username = u.username
              LEFT JOIN animal a ON aa.animalID = a.Animal_ID
              WHERE aa.isDeleted = 0 and aa.applicationStatus = 'Pending'
              ORDER BY aa.applicationDate DESC";
$animalResult = $conn->query($animalSql);

// --- Fetch Volunteer Applications ---
$volunteerSql = "SELECT va.*, u.username
                 FROM volunteerapplication va
                 LEFT JOIN users u ON va.username = u.username
                 WHERE va.isDeleted = 0 and va.status = 'Pending'
                 ORDER BY va.applicationDate DESC";
$volResult = $conn->query($volunteerSql);

// --- Fetch Boarding Applications ---
// $boardingSql = "SELECT b.*, u.username AS ownerName, a.Animal_Name
//                 FROM boarding b
//                 LEFT JOIN users u ON b.ownerID = u.username
//                 LEFT JOIN animal a ON b.AnimalID = a.Animal_ID
//                 WHERE b.isDeleted = 0
//                 ORDER BY b.StartDate DESC";
// $boardingResult = $conn->query($boardingSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Applications Management</title>
  <link rel="stylesheet" href="style2.css">
  <style>
    h1 {
      color: #003366;
      text-align: center;
      border-bottom: 2px solid #FF8C00;
      padding-bottom: 0.5rem;
    }

    /* Tabs */
    /* Modern Tabs */
    .tabs {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }

    .tab {
      padding: 10px 25px;
      border-radius: 25px;
      background: #e0e0e0;
      color: #333;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
      border: 1px solid transparent;
    }

    .tab:hover {
      background: #d0d0d0;
    }

    .tab.active {
      background: #fff;
      border: 1px solid #1f3c74;
      color: #1f3c74;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Tables */

    button {
      margin-right: 8px;
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
    }

    .btn-approve {
      background: #28a745;
      color: white;
    }

    .btn-disapprove {
      background: #dc3545;
      color: white;
    }

    .btn-view {
      background: #007bff;
      color: white;
    }

    /* Modal */
    /* Modal */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      z-index: 1000;
      /* make sure it’s above everything */
      padding: 20px;
      box-sizing: border-box;
    }

    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      max-width: 600px;
      width: 100%;
      max-height: 80vh;
      /* limit height */
      overflow-y: auto;
      /* add scroll if content is long */
      position: relative;
    }

    .close {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 18px;
      cursor: pointer;
    }
  </style>
</head>

<body>
  <div class="dashboard-container">
    <?php include 'sidebar2.php'; ?>
    <div class="main-content" id="mainContent">
      <h1>Applications Management</h1>

      <!-- Tabs -->
      <div class="tabs">
        <div class="tab active" onclick="showTab('animal')">Adoption / Fostering</div>
        <div class="tab" onclick="showTab('volunteer')">Volunteer</div>
      </div>

      <!-- ----------------- Animal Applications ----------------- -->
      <div id="animal" class="tab-content">
        <table>
          <thead>
            <tr>
              <th>Applicant</th>
              <th>Animal</th>
              <th>Application Date</th>
              <th>Status</th>
              <th>Application Type</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($animalResult && $animalResult->num_rows > 0): ?>
              <?php while ($row = $animalResult->fetch_assoc()): ?>
                <tr onclick="openModal('animal-<?= $row['animalappID']; ?>')">
                  <td><?= htmlspecialchars($row['username']); ?></td>
                  <td><?= htmlspecialchars($row['Animal_Name'] ?? $row['animalID']); ?></td>
                  <td><?= $row['applicationDate']; ?></td>
                  <td><?= $row['applicationStatus']; ?></td>
                  <td><?= $row['animalAppType']; ?></td>
                  <td>

                    <!-- <?php var_dump($row) ?> -->
                    <form action="./controllers/ApplicationsController.php" method="POST">
                      <input type="hidden" name="animalID" value="<?php echo $row["animalID"] ?>">
                      <input type="hidden" name="type" value="animal">
                      <input type="hidden" name="animalAppID" value="<?php echo $row["animalappID"] ?>">
                      
                      <?php if ($row['animalAppType'] === "Foster"): ?>
                        <input type="hidden" name="duration" value="<?php echo $row["fosterDuration"] ?>">
                        <?php endif; ?>
                        
                        <!-- <?php echo $row['fosterDuration'] ?> -->
                        
                        <input type="hidden" name="animalAppType" value="<?php echo $row["animalAppType"] ?>">
                        
                        <input type="hidden" name="ApplicantID" value="<?php echo $row["username"] ?>">
                        <input type="hidden" name="action" value="approve">
                        <button class="btn-approve" type="submit">
                          Approve
                        </button>
                      </form>
                      
                      <form action="controllers/ApplicationsController.php" method="POST">
                        <input type="hidden" name="type" value="animal">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="animalID" value="<?php echo $row["animalappID"] ?>">

                      <button class="btn-disapprove" type="submit">
                        Disapprove
                      </button>
                    </form>
                  </td>

                </tr>

                <div id="animal-<?= $row['animalappID']; ?>" class="modal">
                  <div class="modal-content">
                    <span class="close" onclick="closeModal('animal-<?= $row['animalappID']; ?>')">&times;</span>
                    <h3>Application Details: <?= htmlspecialchars($row['username']); ?></h3>
                    <p><strong>Animal:</strong> <?= htmlspecialchars($row['Animal_Name'] ?? $row['animalID']); ?></p>
                    <?php if ($row['animalAppType'] === 'Foster'): ?>
                      <p><strong>Foster Duration:</strong> <?= $row['fosterDuration']; ?></p>
                    <?php endif; ?>
                    <p><strong>Age:</strong> <?= $row['age']; ?></p>
                    <p><strong>City:</strong> <?= $row['city']; ?></p>
                    <p><strong>Housing Type:</strong> <?= $row['housingType']; ?>, <?= $row['homeOwnershipStatus']; ?></p>
                    <p><strong>Other Pets:</strong> <?= $row['hasOtherPets'] ? 'Yes' : 'No'; ?>, Number: <?= $row['numberOfPets']; ?></p>
                    <p><strong>Why Foster/Adopt:</strong> <?= nl2br(htmlspecialchars($row['whyFosterOrAdopt'])); ?></p>
                    <p><strong>Application Status:</strong> <?= $row['applicationStatus']; ?></p>
                  </div>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5">No applications found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
        <br>
        <button onclick="window.location.href='approvedadopters2.php'">View Approved Adopters/Fosterers</button>
      </div>

      <!-- ----------------- Volunteer Applications ----------------- -->
      <div id="volunteer" class="tab-content" style="display:none;">
        <table>
          <thead>
            <tr>
              <th>Applicant</th>
              <th>Age</th>
              <th>Application Date</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($volResult && $volResult->num_rows > 0): ?>
              <?php while ($row = $volResult->fetch_assoc()): ?>
                <tr onclick="openModal('vol-<?= $row['volAppID']; ?>')">
                  <td><?= htmlspecialchars($row['username']); ?></td>
                  <td><?= $row['age']; ?></td>
                  <td><?= $row['applicationDate']; ?></td>
                  <td><?= $row['status']; ?></td>
                  <td>
                    <!-- <button class="btn-view" onclick="openModal('vol-<?= $row['volAppID']; ?>')">View</button> -->
                  <div style="display: flex;gap:.5em">
                    <form action="controllers/ApplicationsController.php" method="POST">
                      <input type="hidden" name="username" value="<?php echo $row['username'] ?>">
                      <input type="hidden" name="action" value="approve">
                      <input type="hidden" name="type" value="volunteer">
                      <input type="hidden" name="volAppID" value="<?php echo $row['volAppID'] ?>">
                      <button class="btn-approve" type="submit">Approve</button>
                    </form>
                    
                    <form action="controllers/ApplicationsController.php" method="POST">
                      <input type="hidden" name="type" value="volunteer">
                      <input type="hidden" name="volAppID" value=<?php echo $row['volAppID'] ?>>
                      <input type="hidden" name="action" value="reject">
                      <button class="btn-reject" type="submit">Reject</button>
                    </form>
                  </div>

                  </td>
                </tr>
                <div id="vol-<?= $row['volAppID']; ?>" class="modal">
                  <div class="modal-content">
                    <span class="close" onclick="closeModal('vol-<?= $row['volAppID']; ?>')">&times;</span>
                    <h3>Application Details: <?= htmlspecialchars($row['username']); ?></h3>
                    <p><strong>Age:</strong> <?= $row['age']; ?></p>
                    <p><strong>Skills:</strong> <?= nl2br(htmlspecialchars($row['applicantSkills'])); ?></p>
                    <?php if(strtolower($row['criminalConvictions']) === "yes"):?>
                      <p><strong>Criminal Convictions:</strong> <?= htmlspecialchars($row['criminalConvictions']); ?></p>
                    <?php endif; ?>
                    <!-- <p><strong>References:</strong> <?= htmlspecialchars($row['contactableReference1']); ?>, <?= htmlspecialchars($row['contactableReference2']); ?></p> -->
                    <p><strong>Certified ID:</strong> <?= isset($row['certifiedID']) ? "<a href='./images/volunteer_applications/$row[certifiedID]'>View</a>" : 'N/A' ; ?></p>
                    <p><strong>Indemnity Form:</strong> <?= isset($row['indemnityForm']) ? "<a href='./images/volunteer_applications/$row[indemnityForm]'>View</a>" : 'N/A' ; ?></p>
                    <p><strong>Authority To Search Form:</strong> <?= isset($row['authorityTosearchForm']) ? "<a href='./images/volunteer_applications/$row[authorityTosearchForm]'>View</a>" : 'N/A' ; ?></p>
                  </div>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5">No volunteer applications found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
        <br>
        <button onclick="window.location.href='approvedvolunteers2.php'">View Approved Volunteers</button>
      </div>

  
  <script>
    // Tabs
    function showTab(tabId) {
      document.querySelectorAll('.tab-content').forEach(tc => tc.style.display = 'none');
      document.getElementById(tabId).style.display = 'block';
      document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
      event.currentTarget.classList.add('active');
    }

    // Modal functions
    function openModal(id) {
      document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    function approveApplication(type, id) {
      if (confirm('Are you sure you want to approve this application?')) {
        window.location.href = `process_application.php?type=${type}&id=${id}&action=approve`;
      }
    }

    function disapproveApplication(type, id) {
      if (confirm('Are you sure you want to disapprove this application?')) {
        window.location.href = `process_application.php?type=${type}&id=${id}&action=disapprove`;
      }
    }
  </script>
  <script src="sidebar2.js"></script>
</body>

</html>