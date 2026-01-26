<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once './notification.php';

require('config/databaseconnection.php');

// --- Fetch Approved Volunteers ---
$volunteersSql = "SELECT u.username, u.FirstName, u.LastName, u.email, u.phone
                  FROM users u
                  JOIN volunteerapplication va ON u.username = va.username
                  WHERE va.status = 'Accepted' AND u.userRole = 'volunteer'
                  ORDER BY u.username ASC";
$volunteersResult = $conn->query($volunteersSql);

// --- Fetch Volunteer Activities ---
$activities = [];
if ($volunteersResult && $volunteersResult->num_rows > 0) {
  $volunteersResult->data_seek(0); // Reset pointer
  while ($volunteer = $volunteersResult->fetch_assoc()) {
    $activitySql = "SELECT * FROM volunteeractivity WHERE VolunteerID = ? ORDER BY Date DESC";
    $stmt = $conn->prepare($activitySql);
    $stmt->bind_param("s", $volunteer['username']);
    $stmt->execute();
    $activityResult = $stmt->get_result();
    $activities[$volunteer['username']] = $activityResult->fetch_all(MYSQLI_ASSOC);
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Approved Volunteers</title>
  <link rel="stylesheet" href="style2.css">
  <style>
    h1 {
      color: #003366;
      text-align: center;
      border-bottom: 2px solid #FF8C00;
      padding-bottom: 0.5rem;
    }

    button {
      margin-right: 8px;
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
    }

    .btn-view {
      background: #007bff;
      color: white;
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      max-width: 800px;
      width: 90%;
      position: relative;
      max-height: 80vh;
      overflow-y: auto;
    }

    .close {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 24px;
      font-weight: bold;
      cursor: pointer;
    }

    .modal-content h3 {
      border-bottom: 1px solid #ccc;
      padding-bottom: 10px;
      margin-bottom: 10px;
    }
  </style>
</head>

<body>
  <div class="dashboard-container">
    <?php include 'sidebar2.php'; ?>
    <div class="main-content" id="mainContent">
      <h1>Approved Volunteers</h1>

      <table>
        <thead>
          <tr>
            <th>Username</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($volunteersResult && $volunteersResult->num_rows > 0):
            $volunteersResult->data_seek(0);
            while ($row = $volunteersResult->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['username']); ?></td>
                <td><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['phone']); ?></td>
                <td>
                  <button class="btn-view" onclick="openModal('vol-activities-<?= $row['username']; ?>')">View Activities</button>
                </td>
              </tr>
            <?php endwhile;
          else: ?>
            <tr>
              <td colspan="5">No approved volunteers found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- Modals for Volunteer Activities -->
      <?php if ($volunteersResult && $volunteersResult->num_rows > 0):
        $volunteersResult->data_seek(0);
        while ($row = $volunteersResult->fetch_assoc()):
      ?>
          <div id="vol-activities-<?= $row['username']; ?>" class="modal">
            <div class="modal-content">
              <span class="close" onclick="closeModal('vol-activities-<?= $row['username']; ?>')">&times;</span>
              <h3>Activities for <?= htmlspecialchars($row['username']); ?></h3>
              <?php if (!empty($activities[$row['username']])) : ?>
                <table>
                  <thead>
                    <tr>
                      <th>Activity Type</th>
                      <th>Date</th>
                      <th>Duration (hours)</th>
                      <th>Animal ID</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($activities[$row['username']] as $activity): ?>
                      <tr>
                        <td><?= htmlspecialchars($activity['ActivityType']); ?></td>
                        <td><?= htmlspecialchars($activity['Date']); ?></td>
                        <td><?= htmlspecialchars($activity['Duration']); ?></td>
                        <td><?= htmlspecialchars($activity['AnimalID'] ?? 'N/A'); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <p>No activities found for this volunteer.</p>
              <?php endif; ?>
            </div>
          </div>
      <?php
        endwhile;
      endif;
      ?>
    </div>
  </div>

  <script>
    // Modal functions
    function openModal(id) {
      document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    // Close modal if clicking outside of it
    window.onclick = function(event) {
      const modals = document.getElementsByClassName('modal');
      for (let i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
          modals[i].style.display = "none";
        }
      }
    }
  </script>
  <script src="sidebar2.js"></script>
</body>

</html>