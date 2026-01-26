<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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

// --- Fetch Approved Boarding Animals ---
$boardingSql = "SELECT ba.*, u.username AS ownerName
                FROM boarding_animals ba
                LEFT JOIN users u ON ba.ownerID = u.username
                WHERE ba.isDeleted = 0
                ORDER BY ba.boardAnimalID DESC";
$boardingResult = $conn->query($boardingSql);
$boardingAnimals = [];
if ($boardingResult && $boardingResult->num_rows > 0) {
    $boardingAnimals = $boardingResult->fetch_all(MYSQLI_ASSOC);
}

// Helper: Only show bookings for current month
function isBookingInCurrentMonth($start, $end) {
    $currentMonth = date('Y-m');
    return (strpos($start, $currentMonth) === 0) || (strpos($end, $currentMonth) === 0);
}
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
    .booking-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 15px;
      margin-top: 15px;
    }
    .booking-card {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      cursor: pointer;
      transition: transform 0.2s;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .booking-card .animal-photo {
      width: 110px;
      height: 110px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 10px;
    }
    .card-details {
      margin-top: 10px;
      width: 100%;
      text-align: left;
    }
    .kanban-board {
      display: flex;
      gap: 24px;
      justify-content: center;
      margin: 40px 0;
    }
    .kanban-column {
      flex: 1;
      min-width: 260px;
      background: #f7f7f7;
      border-radius: 10px;
      padding: 10px;
      min-height: 350px;
      box-sizing: border-box;
    }
    .kanban-column h3 {
      text-align: center;
      margin-bottom: 10px;
    }
    .kanban-list {
      min-height: 300px;
      min-width: 220px;
    }
    .kanban-card {
      background: #fff;
      margin-bottom: 10px;
      border-radius: 8px;
      box-shadow: 0 1px 4px #0001;
      padding: 12px;
      cursor: grab;
      border: 1px solid #ccc;
      user-select: none;
    }
    .kanban-card.dragging {
      opacity: 0.5;
      border: 2px dashed #007bff;
    }
    #kanbanMsg {
      text-align: center;
      color: #fff;
      background: #d9534f;
      padding: 10px 0;
      border-radius: 6px;
      margin-bottom: 20px;
      font-weight: bold;
      min-height: 24px;
      transition: opacity 0.3s;
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
    <div class="main-content" id="mainContent">
      <h1>Approved Boarding</h1>
      <h2>Boarding Animals Details</h2>
      <div class="booking-grid">
        <?php if (!empty($boardingAnimals)): ?>
          <?php foreach ($boardingAnimals as $row): ?>
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
          <?php endforeach; ?>
        <?php else: ?>
          <p>No approved boarding animals found.</p>
        <?php endif; ?>
      </div>

      <!-- Modals for each boarding animal -->
      <?php if (!empty($boardingAnimals)): ?>
        <?php foreach ($boardingAnimals as $row): ?>
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
        <?php endforeach; ?>
      <?php endif; ?>

      <h2 style="margin-top:40px;">Bookings Kanban Board (Current Month)</h2>
      <div id="kanbanMsg"></div>
      <div class="kanban-board">
        <!-- Upcoming -->
        <div id="upcoming" class="kanban-column" ondragover="allowDrop(event)" ondrop="dropBooking(event, 'active')">
          <h3>Upcoming</h3>
          <div class="kanban-list">
            <?php
            if ($upcomingBookings && $upcomingBookings->num_rows > 0) {
              $upcomingBookings->data_seek(0);
              while ($row = $upcomingBookings->fetch_assoc()):
                if (!isBookingInCurrentMonth($row['booking_start_date'], $row['booking_end_date'])) continue;
            ?>
              <div class="kanban-card"
                id="booking-<?= $row['boardBookID']; ?>"
                draggable="true"
                ondragstart="dragBooking(event)"
                data-booking='<?= htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>'>
                <b><?= htmlspecialchars($row['animal_name']); ?></b><br>
                Owner: <?= htmlspecialchars($row['owner_first'] . ' ' . $row['owner_last']); ?><br>
                Start: <?= htmlspecialchars($row['booking_start_date']); ?><br>
                End: <?= htmlspecialchars($row['booking_end_date']); ?><br>
                Status: <?= htmlspecialchars($row['status']); ?>
              </div>
            <?php endwhile; } ?>
          </div>
        </div>
        <!-- Active -->
        <div id="active" class="kanban-column" ondragover="allowDrop(event)" ondrop="dropBooking(event, 'completed')">
          <h3>Active</h3>
          <div class="kanban-list">
            <?php
            if ($activeBookings && $activeBookings->num_rows > 0) {
              $activeBookings->data_seek(0);
              while ($row = $activeBookings->fetch_assoc()):
                if (!isBookingInCurrentMonth($row['booking_start_date'], $row['booking_end_date'])) continue;
            ?>
              <div class="kanban-card"
                id="booking-<?= $row['boardBookID']; ?>"
                draggable="true"
                ondragstart="dragBooking(event)"
                data-booking='<?= htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>'>
                <b><?= htmlspecialchars($row['animal_name']); ?></b><br>
                Owner: <?= htmlspecialchars($row['owner_first'] . ' ' . $row['owner_last']); ?><br>
                Start: <?= htmlspecialchars($row['booking_start_date']); ?><br>
                End: <?= htmlspecialchars($row['booking_end_date']); ?><br>
                Status: <?= htmlspecialchars($row['status']); ?>
              </div>
            <?php endwhile; } ?>
          </div>
        </div>
        <!-- Completed -->
        <div id="completed" class="kanban-column">
          <h3>Completed</h3>
          <div class="kanban-list">
            <!-- You can fetch and display completed bookings here if desired -->
          </div>
        </div>
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

  <form id="bookingActionForm" method="POST" action="testingkanban.php" style="display:none;">
    <input type="hidden" name="action" id="bookingAction">
    <input type="hidden" name="boardBookID" id="bookingID">
    <input type="hidden" name="boardingAnimalID" id="bookingAnimalID">
    <input type="hidden" name="cageNumber" id="bookingCage">
  </form>

  <script>
    // Modal logic for animal cards
    function openModal(id) {
      document.getElementById(id).style.display = 'flex';
    }
    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
    }
    document.addEventListener('mousedown', function(event) {
      document.querySelectorAll('.modal').forEach(function(modal) {
        if (
          modal.style.display === 'flex' &&
          !modal.classList.contains('fade') &&
          !modal.querySelector('.modal-content').contains(event.target)
        ) {
          modal.style.display = 'none';
        }
      });
    });
    document.querySelectorAll('.modal-content').forEach(function(content) {
      content.addEventListener('mousedown', function(event) {
        event.stopPropagation();
      });
    });
    var removeModal = document.getElementById('removeModal');
    if (removeModal) {
      removeModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var animalID = button.getAttribute('data-id');
        document.getElementById('removeAnimalID').value = animalID;
      });
    }

    // Kanban drag and drop logic
    let draggedBooking = null;
    let draggedBookingData = null;
    function dragBooking(event) {
      draggedBooking = event.target;
      draggedBooking.classList.add('dragging');
      draggedBookingData = JSON.parse(event.target.dataset.booking);
      event.dataTransfer.effectAllowed = "move";
    }
    function allowDrop(event) {
      event.preventDefault();
    }
    function dropBooking(event, targetColumn) {
      event.preventDefault();
      if (!draggedBooking || !draggedBookingData) return;
      const today = new Date().toISOString().slice(0,10);

      // Upcoming → Active (check-in)
      if (targetColumn === 'active') {
        if (draggedBooking.parentElement.parentElement.id !== 'upcoming') return;
        if (today !== draggedBookingData.booking_start_date) {
          showKanbanMsg("You can only check in on the booking's start date.");
          draggedBooking.classList.remove('dragging');
          return;
        }
        kanbanAction('checkin', draggedBookingData);
        document.getElementById('active').querySelector('.kanban-list').appendChild(draggedBooking);
      }
      // Active → Completed (check-out)
      else if (targetColumn === 'completed') {
        if (draggedBooking.parentElement.parentElement.id !== 'active') return;
        if (today !== draggedBookingData.booking_end_date) {
          showKanbanMsg("You can only check out on the booking's end date.");
          draggedBooking.classList.remove('dragging');
          return;
        }
        kanbanAction('checkout', draggedBookingData);
        document.getElementById('completed').querySelector('.kanban-list').appendChild(draggedBooking);
      }
      draggedBooking.classList.remove('dragging');
      draggedBooking = null;
      draggedBookingData = null;
    }
    function showKanbanMsg(msg) {
      let msgDiv = document.getElementById('kanbanMsg');
      msgDiv.innerText = msg;
      msgDiv.style.opacity = 1;
      setTimeout(() => { msgDiv.innerText = ''; msgDiv.style.opacity = 0; }, 3500);
    }
    function kanbanAction(action, booking) {
      // Submit a hidden form for checkin/checkout
      document.getElementById('bookingAction').value = action;
      document.getElementById('bookingID').value = booking.boardBookID;
      document.getElementById('bookingAnimalID').value = booking.boardingAnimalID;
      document.getElementById('bookingCage').value = booking.cageNumber;
      document.getElementById('bookingActionForm').submit();
    }
    // Remove dragging style on dragend
    document.addEventListener('dragend', function(e) {
      if (draggedBooking) draggedBooking.classList.remove('dragging');
      draggedBooking = null;
      draggedBookingData = null;
    });
  </script>
  <script src="sidebar2.js"></script>
</body>
</html>
