<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include_once './notification.php';
require('config/databaseconnection.php');
include_once 'controllers/BoardingController.php';

// Handle POST actions (check-in/check-out/remove) BEFORE any HTML is output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $controller = new BoardingController();

  if (isset($_POST['action'])) { // Check-in or Check-out
    $id = $_POST['boardBookID'];
    $anid = $_POST['boardingAnimalID'];
    $cage = $_POST['cageNumber'];
    if ($_POST['action'] === 'checkin') {
      $controller->checkIn($id, $anid, $cage);
    } elseif ($_POST['action'] === 'checkout') {
      $controller->checkOut($id, $anid, $cage);
    }
  } elseif (isset($_POST['removeAnimal'])) { // Remove animal
    $animalID = $_POST['boardAnimalID'];
    $sql = "UPDATE boarding_animals SET isDeleted = 1 WHERE boardAnimalID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $animalID);
    if ($stmt->execute()) {
      $_SESSION['notification'] = ['message' => 'Animal removed successfully.', 'type' => 'success'];
    } else {
      $_SESSION['notification'] = ['message' => 'Error removing animal.', 'type' => 'error'];
    }
  }

  // Redirect to the same page to prevent form resubmission on refresh
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

// --- Page rendering logic ---
$controller = new BoardingController();
$upcomingBookings = $controller->displayUpcomingBookings();
$activeBookings = $controller->displayActiveBookings();
$completedBookings = $controller->displayCompletedBookings(); // new

// --- Fetch Approved Boarding Animals ---
$boardingSql = "SELECT ba.*, u.username AS ownerName
                FROM boarding_animals ba
                LEFT JOIN users u ON ba.ownerID = u.username
                WHERE ba.isDeleted = 0
                ORDER BY ba.boardAnimalID DESC";
$boardingResult = $conn->query($boardingSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Approved Boarding</title>
  <link rel="stylesheet" href="style2.css">
  <style>
    h1 {
      color: #003366;
      text-align: center;
      border-bottom: 2px solid #FF8C00;
      padding-bottom: 0.5rem;
    }

    .table-container {
      width: calc(100% - 40px);
      /* full available width with side padding */
      max-width: 1200px;
      /* allow wider tables on large screens */
      margin: 32px auto 40px auto;
      padding: 0 20px;
      /* consistent side spacing */
      box-sizing: border-box;
    }

    .main-content {
      padding: 0 24px;
      /* give the main area some breathing room */
      box-sizing: border-box;
    }

    /* Make tables span the container fully and remove the fixed 950px cap */
    .main-content table,
    .table-container table {
      width: 100% !important;
      max-width: 100%;
      table-layout: fixed;
      border-collapse: separate;
      border-spacing: 0;
      border-radius: 16px;
      overflow: hidden;
      margin: 24px 0 32px 0;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    /* Keep existing visual styles for thead/tbody corners */
    thead tr:first-child th:first-child {
      border-top-left-radius: 16px;
    }

    thead tr:first-child th:last-child {
      border-top-right-radius: 16px;
    }

    tbody tr:last-child td:first-child {
      border-bottom-left-radius: 16px;
    }

    tbody tr:last-child td:last-child {
      border-bottom-right-radius: 16px;
    }

    /* Ensure cells wrap and remain readable */
    th,
    td {
      padding: 12px 14px;
      border: 1px solid #ddd;
      text-align: left;
      overflow-wrap: anywhere;
      word-break: break-word;
    }

    tr:hover {
      background: #f9f9f9;
      cursor: pointer;
    }

    button {
      margin-right: 6px;
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

    /* Modal (shared style - matches animal details popup) */
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
      padding: 20px;
      box-sizing: border-box;
      font-family: 'Lexend', Arial, sans-serif;
    }

    .modal-content {
      background: #FFF8F0;
      /* same soft card background */
      padding: 22px;
      border-radius: 14px;
      max-width: 680px;
      width: 100%;
      max-height: 85vh;
      overflow-y: auto;
      position: relative;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
      color: #18436e;
      /* primary text color used across modals */
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    .modal-content h3 {
      margin-top: 0;
      margin-bottom: 8px;
      font-size: 1.5rem;
      font-weight: 800;
      color: #18436e;
      letter-spacing: 0.2px;
    }

    .close {
      position: absolute;
      top: 12px;
      right: 16px;
      font-size: 20px;
      cursor: pointer;
      color: #333;
      background: transparent;
      border: none;
    }

    .animal-photo {
      max-width: 180px;
      max-height: 180px;
      border-radius: 12px;
      margin-bottom: 12px;
      object-fit: cover;
      border: 1px solid rgba(0, 0, 0, 0.06);
    }

    .booking-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 15px;
      margin-top: 15px;
    }

    .booking-card {
      background: #fff;
      border: 1px solid #eee;
      border-radius: 12px;
      padding: 16px;
      cursor: pointer;
      transition: transform 0.18s ease, box-shadow 0.18s ease;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
    }

    .booking-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 14px 36px rgba(0, 0, 0, 0.08);
    }

    .card-details h3 {
      margin: 0 0 6px 0;
      color: #18436e;
      font-weight: 700;
    }

    .card-details p {
      margin: 4px 0;
      color: #4b5563;
    }

    /* Booking modal specific overrides (keeps previous button styles) */
    #bookingModal .modal-content {
      max-width: 420px;
      padding: 20px;
    }

    .btn-checkin,
    .btn-checkout {
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
      font-weight: 700;
      letter-spacing: 0.3px;
    }

    @media (max-width: 700px) {
      .modal-content {
        padding: 16px;
        border-radius: 12px;
      }

      .animal-photo {
        max-width: 140px;
        max-height: 140px;
      }
    }
  </style>
</head>

<body>
  <?php if (isset($_SESSION['notification'])): ?>
    <div class="alert <?= $_SESSION['notification']['type'] === 'success' ? 'alert-success' : 'alert-danger' ?>" style="margin: 20px; padding: 10px; border-radius: 5px;">
      <?= htmlspecialchars($_SESSION['notification']['message']) ?>
    </div>
    <?php unset($_SESSION['notification']); ?>
  <?php endif; ?>
  <div class="dashboard-container">
    <?php include_once './sidebar2.php' ?>
    <div class="main-content" id="mainContent">
      <h1>Approved Boarding</h1>
      <h2>Boarding Animals Details</h2>

      <div class="booking-grid">
        <?php if ($boardingResult && $boardingResult->num_rows > 0): ?>
          <?php while ($row = $boardingResult->fetch_assoc()): ?>
            <div class="booking-card" onclick="openModal('board-<?= $row['boardAnimalID']; ?>')">
              <?php if (!empty($row['board_animal_photo'])): ?>
                <img src="<?= htmlspecialchars($row['board_animal_photo']); ?>" alt="Animal Photo" class="animal-photo">
              <?php endif; ?>
              <div class="card-details">
                <h3><?= htmlspecialchars($row['name']); ?></h3>
                <p><strong>Owner:</strong> <?= htmlspecialchars($row['ownerName']); ?></p>
                <p><strong>Breed:</strong> <?= htmlspecialchars($row['breed']); ?></p>
                <p><strong>Age Group:</strong> <?= htmlspecialchars($row['ageGroup']); ?></p>
                <p><strong>Type:</strong> <?= htmlspecialchars($row['animalType']); ?></p>
                <button class="btn btn-danger btn-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#removeModal"
                  data-id="<?= $row['boardAnimalID']; ?>"
                  onclick="event.stopPropagation();">
                  Remove
                </button>
              </div>
            </div>

            <!-- Modal for each boarding animal -->
            <div id="board-<?= $row['boardAnimalID']; ?>" class="modal">
              <div class="modal-content">
                <span class="close" onclick="closeModal('board-<?= $row['boardAnimalID']; ?>')">&times;</span>
                <h3>Boarding Details: <?= htmlspecialchars($row['name']); ?></h3>
                <?php if (!empty($row['board_animal_photo'])): ?>
                  <img src="<?= htmlspecialchars($row['board_animal_photo']); ?>" alt="Animal Photo" class="animal-photo">
                <?php endif; ?>
                <p><strong>Owner:</strong> <?= htmlspecialchars($row['ownerName']); ?></p>
                <p><strong>Breed:</strong> <?= htmlspecialchars($row['breed']); ?></p>
                <p><strong>Age Group:</strong> <?= htmlspecialchars($row['ageGroup']); ?></p>
                <p><strong>Animal Type:</strong> <?= htmlspecialchars($row['animalType']); ?></p>
                <hr>
                <p><strong>Emergency Contact:</strong> <?= htmlspecialchars($row['emergency_first_name'] . " " . $row['emergency_last_name']); ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($row['emergency_phone']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($row['emergency_email']); ?></p>
                <hr>
                <p><strong>Primary Vet:</strong> <?= htmlspecialchars($row['primary_vet_name']); ?></p>
                <p><strong>Vet Phone:</strong> <?= htmlspecialchars($row['primary_vet_phone']); ?></p>
                <hr>
                <p><strong>Medical Conditions:</strong> <?= nl2br(htmlspecialchars($row['medical_conditions'] ?? 'None')); ?></p>
                <p><strong>Behavioural Notes:</strong> <?= nl2br(htmlspecialchars($row['behavioural_notes'] ?? 'None')); ?></p>
                <p><strong>Allergies:</strong> <?= nl2br(htmlspecialchars($row['allergies'] ?? 'None')); ?></p>
                <p><strong>Dietary Requirements:</strong> <?= nl2br(htmlspecialchars($row['dietary_requirements'] ?? 'None')); ?></p>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No approved boarding animals found.</p>
        <?php endif; ?>
      </div>

      <div class="table-container">
        <h2>Active Bookings</h2>
        <table>
          <thead>
            <tr>
              <th>Animal Name</th>
              <th>Owner Name</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Status</th>
              <th>Cage</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($activeBookings && $activeBookings->num_rows > 0): ?>
              <?php while ($row = $activeBookings->fetch_assoc()): ?>
                <tr class="booking-row" data-type="active" data-booking='<?= htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>'>
                  <td><?= htmlspecialchars($row['animal_name']); ?></td>
                  <td><?= htmlspecialchars($row['owner_first'] . ' ' . $row['owner_last']); ?></td>
                  <td><?= htmlspecialchars($row['booking_start_date']); ?></td>
                  <td><?= htmlspecialchars($row['booking_end_date']); ?></td>
                  <td><?= htmlspecialchars($row['status']); ?></td>
                  <td><?= htmlspecialchars($row['cageNumber']); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6">No active bookings found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Completed Bookings -->
      <div class="table-container">
        <h2>Completed Bookings</h2>
        <table>
          <thead>
            <tr>
              <th>Animal Name</th>
              <th>Owner Name</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Status</th>
              <th>Cage</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($completedBookings && $completedBookings->num_rows > 0): ?>
              <?php while ($row = $completedBookings->fetch_assoc()): ?>
                <tr class="booking-row" data-type="completed" data-booking='<?= htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>'>
                  <td><?= htmlspecialchars($row['animal_name']); ?></td>
                  <td><?= htmlspecialchars($row['owner_first'] . ' ' . $row['owner_last']); ?></td>
                  <td><?= htmlspecialchars($row['booking_start_date']); ?></td>
                  <td><?= htmlspecialchars($row['booking_end_date']); ?></td>
                  <td><?= htmlspecialchars($row['status']); ?></td>
                  <td><?= htmlspecialchars($row['cageNumber']); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6">No completed bookings found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="table-container">
        <h2>Upcoming Bookings</h2>
        <table>
           <thead>
             <tr>
               <th>Animal Name</th>
               <th>Owner Name</th>
               <th>Start Date</th>
               <th>End Date</th>
               <th>Status</th>
               <th>Cage</th>
             </tr>
           </thead>
           <tbody>
             <?php if ($upcomingBookings && $upcomingBookings->num_rows > 0): ?>
               <?php while ($row = $upcomingBookings->fetch_assoc()): ?>
                 <tr class="booking-row" data-type="upcoming" data-booking='<?= htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>'>
                   <td><?= htmlspecialchars($row['animal_name']); ?></td>
                   <td><?= htmlspecialchars($row['owner_first'] . ' ' . $row['owner_last']); ?></td>
                   <td><?= htmlspecialchars($row['booking_start_date']); ?></td>
                   <td><?= htmlspecialchars($row['booking_end_date']); ?></td>
                   <td><?= htmlspecialchars($row['status']); ?></td>
                   <td><?= htmlspecialchars($row['cageNumber']); ?></td>
                 </tr>
               <?php endwhile; ?>
             <?php else: ?>
               <tr>
                 <td colspan="6">No upcoming bookings found.</td>
               </tr>
             <?php endif; ?>
           </tbody>
         </table>
       </div>
    </div>
  </div>
  <!-- Remove Confirmation Modal -->
  <div class="modal fade" id="removeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="POST" action="">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Removal</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to remove this animal from approved boarding?
          </div>
          <input type="hidden" name="boardAnimalID" id="removeAnimalID">
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="removeAnimal" class="btn btn-danger">Yes, Remove</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Booking Modal (restyled to match animal details modal) -->
  <div id="bookingModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width:420px;">
      <span id="closeBookingModal" class="close">&times;</span>
      <div id="bookingDetails" style="color:#18436e; font-weight:600; margin-bottom:12px;"></div>
      <div style="display:flex; gap:12px; justify-content:center; margin-top:8px;">
        <button id="checkInBtn" class="btn-checkin" style="display:none; padding:10px 18px; border-radius:8px; border:none; background:#98b06f; color:#fff;">Check In</button>
        <button id="checkOutBtn" class="btn-checkout" style="display:none; padding:10px 18px; border-radius:8px; border:none; background:#df7100; color:#fff;">Check Out</button>
      </div>
      <div id="bookingModalMsg" style="color:red; margin-top:12px; text-align:center;"></div>
    </div>
  </div>

  <form id="bookingActionForm" method="POST" action="approvedboarding2.php" style="display:none;">
    <input type="hidden" name="action" id="bookingAction">
    <input type="hidden" name="boardBookID" id="bookingID">
    <input type="hidden" name="boardingAnimalID" id="bookingAnimalID">
    <input type="hidden" name="cageNumber" id="bookingCage">
  </form>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./sidebar2.js"></script>
  <script>
    function openModal(id) {
      document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    // Responsive close for custom modals (not Bootstrap modals)
    document.addEventListener('mousedown', function(event) {
      document.querySelectorAll('.modal').forEach(function(modal) {
        if (
          modal.style.display === 'flex' &&
          !modal.classList.contains('fade') && // skip Bootstrap modals
          !modal.querySelector('.modal-content').contains(event.target)
        ) {
          modal.style.display = 'none';
        }
      });
    });

    // Prevent closing when clicking inside modal-content
    document.querySelectorAll('.modal-content').forEach(function(content) {
      content.addEventListener('mousedown', function(event) {
        event.stopPropagation();
      });
    });

    // Pass the selected animal ID to modal hidden input (Bootstrap modal)
    var removeModal = document.getElementById('removeModal');
    if (removeModal) {
      removeModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var animalID = button.getAttribute('data-id');
        document.getElementById('removeAnimalID').value = animalID;
      });
    }

    // Attach click handler to booking rows and show appropriate action
    document.querySelectorAll('.booking-row').forEach(row => {
      row.addEventListener('click', function() {
        const booking = JSON.parse(this.dataset.booking || '{}');
        const type = this.dataset.type || 'active';

        // Populate details
        let details = `
          <b>Booking ID:</b> ${booking.boardBookID || ''}<br>
          <b>Animal ID:</b> ${booking.boardingAnimalID || ''}<br>
          <b>Start Date:</b> ${booking.booking_start_date || ''}<br>
          <b>End Date:</b> ${booking.booking_end_date || ''}<br>
          <b>Status:</b> ${booking.status || ''}<br>
          <b>Cage:</b> ${booking.cageNumber || ''}<br>
        `;
        document.getElementById('bookingDetails').innerHTML = details;

        // Show modal
        document.getElementById('bookingModal').style.display = 'flex';
        document.getElementById('bookingModalMsg').innerText = '';

        // Set datasets on buttons
        const checkInBtn = document.getElementById('checkInBtn');
        const checkOutBtn = document.getElementById('checkOutBtn');

        checkInBtn.dataset.start = booking.booking_start_date || '';
        checkInBtn.dataset.id = booking.boardBookID || '';
        checkInBtn.dataset.cage = booking.cageNumber || '';
        checkInBtn.dataset.animal = booking.boardingAnimalID;

        checkOutBtn.dataset.end = booking.booking_end_date || '';
        checkOutBtn.dataset.id = booking.boardBookID || '';
        checkOutBtn.dataset.cage = booking.cageNumber || '';
        checkOutBtn.dataset.animal = booking.boardingAnimalID;

        // Display correct button based on row type
        if (type === 'upcoming') {
          checkInBtn.style.display = 'inline-block';
          checkOutBtn.style.display = 'none';
        } else if (type === 'active') {
          checkInBtn.style.display = 'none';
          checkOutBtn.style.display = 'inline-block';
        } else {
          // completed or other: hide both
          checkInBtn.style.display = 'none';
          checkOutBtn.style.display = 'none';
        }
      });
    });

    // Close modal
    document.getElementById('closeBookingModal').onclick = function() {
      document.getElementById('bookingModal').style.display = 'none';
    };
    window.onclick = function(event) {
      const modal = document.getElementById('bookingModal');
      if (event.target == modal) modal.style.display = 'none';
    };

    // Check In handler
    document.getElementById('checkInBtn').onclick = function() {
      const today = new Date().toISOString().slice(0, 10);
      const startDate = (this.dataset.start || '').slice(0, 10);
      if (startDate && today < startDate) {
        document.getElementById('bookingModalMsg').innerText = "Check-in is only allowed on or after the booking's start date.";
        return;
      }
      document.getElementById('bookingAction').value = 'checkin';
      document.getElementById('bookingID').value = this.dataset.id;
      document.getElementById('bookingAnimalID').value = this.dataset.animal;
      document.getElementById('bookingCage').value = this.dataset.cage;
      document.getElementById('bookingActionForm').submit();
    };

    // Check Out handler
    document.getElementById('checkOutBtn').onclick = function() {
      const today = new Date().toISOString().slice(0, 10);
      const endDate = (this.dataset.end || '').slice(0, 10);
      if (endDate && today < endDate) {
        document.getElementById('bookingModalMsg').innerText = "Check-out is only allowed on or after the booking's end date.";
        return;
      }
      document.getElementById('bookingAction').value = 'checkout';
      document.getElementById('bookingID').value = this.dataset.id;
      document.getElementById('bookingAnimalID').value = this.dataset.animal;
      document.getElementById('bookingCage').value = this.dataset.cage;
      document.getElementById('bookingActionForm').submit();
    };
  </script>
  <script src="sidebar2.js"></script>
</body>

</html>