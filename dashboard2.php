<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include_once './notification.php';

// --- Database Connection ---
require('config/databaseconnection.php');


include_once __DIR__ . '/models/Notification.php';
include_once __DIR__ . '/core/functions.php';
include_once __DIR__ . '/models/Animal.php';

$userPreferredName = isset($_SESSION['preferredName']) && !empty($_SESSION['preferredName'])
  ? $_SESSION['preferredName']
  : $_SESSION['first_name'];

$animal = new Animal();

$currentUserID = $_SESSION['username']; // Assuming userID is stored in session upon login
$notification = new Notification();
$unreadNotifications = $notification->getUnread($currentUserID);
$unreadCount = $unreadNotifications->num_rows;



// --- Analytics ---
$totalAnimals = $animal->getTotalAnimals();
$adopted = $animal->getAdoptedAnimals();
$available = $animal->getAvailableAnimals();
$monthlyIntakes = $conn->query("SELECT COUNT(*) as count FROM animal WHERE MONTH(Animal_RescueDate) = MONTH(CURDATE()) AND YEAR(Animal_RescueDate) = YEAR(CURDATE())")->fetch_assoc()['count'];


// --- Monthly Adoptions ---
$currentMonth = date('m');
$currentYear = date('Y');
$monthlyAdoptions = $conn->query("SELECT COUNT(*) as count FROM animal WHERE outtakeType='Adoption' AND MONTH(outtakeDate)=$currentMonth AND YEAR(outtakeDate)=$currentYear")->fetch_assoc()['count'];

$healthy = $animal->getHealthyAnimals();
$inTreatment = $animal->getInTreatmentAnimals();

// --- Boarded animals count and animals in care ---
$boardedCount = 0;
try {
  $resBoard = $conn->query("SELECT IFNULL(COUNT(*),0) as count FROM boarding_animals WHERE isDeleted = 0");
  $boardedCount = $resBoard ? intval($resBoard->fetch_assoc()['count']) : 0;
} catch (Exception $e) {
  $boardedCount = 0;
}
$animalsInCare = $available + $boardedCount;

// --- Donations (weekly comparison) ---
// We'll compute two 7-day windows: last 7 days (past week) and the 7 days before that (previous week)
$lastWeekTotal = 0;
$prevWeekTotal = 0;
try {
  $res1 = $conn->query("SELECT IFNULL(SUM(DonationAmount),0) as total FROM donations WHERE DonationDate >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND isDeleted = 0");
  $lastWeekTotal = $res1 ? floatval($res1->fetch_assoc()['total']) : 0;
  $res2 = $conn->query("SELECT IFNULL(SUM(DonationAmount),0) as total FROM donations WHERE DonationDate >= DATE_SUB(NOW(), INTERVAL 14 DAY) AND DonationDate < DATE_SUB(NOW(), INTERVAL 7 DAY) AND isDeleted = 0");
  $prevWeekTotal = $res2 ? floatval($res2->fetch_assoc()['total']) : 0;
} catch (Exception $e) {
  // If donations table doesn't exist or query fails, keep totals at 0 to avoid breaking the page
  $lastWeekTotal = 0;
  $prevWeekTotal = 0;
}

// --- Calendar Setup ---
$month = $_GET['month'] ?? $currentMonth;
$year = $_GET['year'] ?? $currentYear;
$month = (int)$month;
$year = (int)$year;

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDay = date("w", strtotime("$year-$month-01"));

$resultEvents = $conn->query("SELECT * FROM events WHERE MONTH(event_date) = $month AND YEAR(event_date) = $year");
$events = [];
while ($row = $resultEvents->fetch_assoc()) $events[$row['event_date']][] = $row;





// animals table
$filter = '';
$search = '';



if ($_SERVER['REQUEST_METHOD'] == "GET") {
  $filter = isset($_GET["filter"]) ? sanitizeInput($_GET["filter"]) : "";
  $search = isset($_GET["search"]) ? sanitizeInput($_GET["search"]) : "";
  if (!empty($filter) && !empty($search)) {
    $result = $this->animal->searchAndFilter($search, $filter);
  } else if (!empty($filter) && empty($search)) {
    $result = $animal->filter($filter);
    // redirectTo("/");
  } else if (empty($filter) && !empty($search)) {
    $result = $animal->search($search);
    // redirectTo("/");
  } else {
    $result = $animal->findAll(5);
  }
}


// --- Registered Animals ---
// $search = $_GET['search'] ?? '';
// $filter = $_GET['filter'] ?? '';
// $sqlAnimals = "SELECT * FROM animal WHERE 1=1";
// if ($search) $sqlAnimals .= " AND Animal_Name LIKE '%$search%'";
// if ($filter) $sqlAnimals .= " AND (Animal_AgeGroup='$filter' OR Animal_Type='$filter')";
// $resultAnimals = $conn->query($sqlAnimals);
?>
<!DOCTYPE html>
<html>

<head>
  <title>Dashboard</title>
  <link rel="stylesheet" href="style2.css">
  <!-- Google Fonts: Lexend -->
  <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- FullCalendar CSS -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />

  <!-- FullCalendar JS -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<style>

.calendar-section, #admin-calendar {
   
    max-width: 100%;   /* prevent any max-width restriction */
    margin: 0;         /* remove centering margins */
}


#admin-calendar {
    width: 100%;     
    max-width: 100%;   
    margin: 0 auto;    
}


#admin-calendar .fc-toolbar-title {
    text-align: center;
    width: 100%;
    display: block;
    font-size: 1.5rem; 
    font-weight: bold;
    color: #003366; 
}


#admin-calendar .fc-toolbar {
    justify-content: center; 
}


.fc .fc-col-header-cell-cushion {
    color: #ffffff; 
    font-weight: bold; 
    text-align: center; 
}


.fc .fc-col-header {
    background-color: #2c3e50; 
}


#admin-calendar {
    max-width: 1200px;   
    margin: 0 auto;
    font-size: 0.8rem; 
}


.fc .fc-toolbar-title {
    font-size: 1rem;   
}


.fc .fc-col-header-cell-cushion {
    font-size: 0.8rem; 
}


.fc .fc-daygrid-day-frame {
    padding: 5px;       /* smaller padding inside day cells */
}

/* Optional: reduce event text size */
.fc .fc-event-title {
    font-size: 0.7rem;
}



/* Modal Container */
#eventModal {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #FFF8F0 !important;
  border-radius: 12px;
  padding: 30px 25px;
  width: 400px;
  max-width: 90%;
  box-shadow: 0 8px 25px rgba(0,0,0,0.2);
  z-index: 1000;
  display: none; /* show with JS */
  font-family: 'Segoe UI', sans-serif;
}

/* Modal Title */
#modalTitle {
  margin: 0 0 20px;
  font-size: 1.6rem;
  color:#18436e;
 border-bottom: 3px solid #df7100;
  text-align: center;
  font-weight: 600;
}

/* Form Styles */
#eventForm label {
  display: block;
  margin-bottom: 15px;
  font-weight: 500;
  color: #18436e;
}

#eventForm input[type="text"],
#eventForm input[type="datetime-local"],
#eventForm textarea {
  width: 100%;
  padding: 10px 12px;
  margin-top: 5px;
  border: 1.5px solid #ccc;
  border-radius: 8px;
  font-size: 1rem;
  transition: border 0.3s;
}

#eventForm input:focus,
#eventForm textarea:focus {
  border-color: #007bff;
  outline: none;
  box-shadow: 0 0 5px rgba(0,123,255,0.3);
}

#eventForm textarea {
  min-height: 80px;
  resize: vertical;
}

/* Buttons */
#eventForm button {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  margin-right: 10px;
  transition: background 0.3s, transform 0.2s;
}

#saveEventBtn {
  background-color: #98b06f;
  color: #fff;
}

#saveEventBtn:hover {
  background-color: #98b06f;
  transform: translateY(-2px);
}

#deleteEventBtn {
  background-color: #800000!important ;
  color: #fff;
}

#deleteEventBtn:hover {
  background-color:#800000 !important ;
  transform: translateY(-2px);
}

#eventForm button[type="button"] {
  background-color: #df7100;
  color: #fff;
}

#eventForm button[type="button"]:hover {
  background-color: #df7100;
  transform: translateY(-2px);
}

/* Center buttons in the modal */
.modal-buttons {
  display: flex;
  justify-content: center;   /* centers horizontally */
  gap: 15px;                 /* space between buttons */
  margin-top: 20px;          /* space from form fields */
}


/* Responsive */
@media (max-width: 500px) {
  #eventModal {
    width: 90%;
    padding: 20px;
  }
}
/* Donations chart title counter */
.chart-with-counter {
  display: flex;
  flex-direction: column; /* title above chart */
  align-items: stretch;
  gap: 8px;
  margin-bottom: 12px;
  width: 300px; /* match other charts' width */
  height: 300px; /* match other charts' height */
  box-sizing: border-box;
  padding: 8px;
  /*background: linear-gradient(135deg, #ffffff, #fbfbfb);*/
  border-radius: 8px;
}
.donations-title {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: center;
  gap: 6px;
  width: 100%;
}
.donations-total, .animalCare {
  font-size: 1.25rem;
  font-weight: 700;
  color: #18436e;
}
.donations-delta {
  font-size: 0.9rem;
  color: #666;
}
.delta-up { color: #0a9b3e; font-weight:600; }
.delta-down { color: #c62828; font-weight:600; }
.delta-neutral { color: #666; }

/* Apply admin calendar styling to vet (#calendar) and volunteer (#volunteer-calendar) calendars */
#calendar, #volunteer-calendar {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  font-size: 0.8rem;
}

/* Toolbar / title */
#calendar .fc-toolbar, #volunteer-calendar .fc-toolbar {
  justify-content: center;
}
#calendar .fc-toolbar-title,
#volunteer-calendar .fc-toolbar-title {
  text-align: center;
  width: 100%;
  display: block;
  font-size: 1.5rem;
  font-weight: bold;
  color: #003366;
}

/* Column headers */
#calendar .fc-col-header, #volunteer-calendar .fc-col-header {
  background-color: #2c3e50;
}
#calendar .fc-col-header-cell-cushion,
#volunteer-calendar .fc-col-header-cell-cushion {
  color: #ffffff;
  font-weight: bold;
  text-align: center;
  font-size: 0.8rem;
}

/* Day cell padding and event text size */
#calendar .fc-daygrid-day-frame,
#volunteer-calendar .fc-daygrid-day-frame {
  padding: 5px;
}
#calendar .fc-event-title,
#volunteer-calendar .fc-event-title {
  font-size: 0.7rem;
}

/* Make sure the calendar container behaves like admin calendar on small screens */
@media (max-width: 700px) {
  #calendar, #volunteer-calendar { max-width: 100%; padding: 0 8px; }
  #calendar .fc-toolbar-title, #volunteer-calendar .fc-toolbar-title { font-size: 1.1rem; }
}

/* --- Add: make vet / volunteer modals match admin event modal --- */
/* Reuse admin modal visuals for the vet/volunteer/dayOverview popups and overlays */
#volunteerEventModal,
#dayOverview {
  background-color: #FFF8F0;
  border-radius: 12px;
  padding: 30px 25px;
  width: 420px;
  max-width: 92%;
  box-shadow: 0 8px 25px rgba(0,0,0,0.2);
  z-index: 1001;
  /* do NOT force display:none here — let JS control visibility */
  position: fixed;
  left: 50%;
  transform: translateX(-50%);
  font-family: 'Lexend', Arial, sans-serif;
  color: #18436e;
}

#volunteerEventModal h3,
#dayOverview h3 {
  margin: 0 0 18px;
  font-size: 1.3rem;
  color: #18436e;
  text-align: center;
  border-bottom: 3px solid #df7100;
  padding-bottom: 8px;
  font-weight: 600;
}

/* Buttons inside volunteer modal */
#volunteerEventModal button,
#dayOverview button {
  padding: 10px 16px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-weight: 600;
  margin-top: 12px;
}

#volunteerEventModal .close-btn,
#dayOverview .close-btn {
  position: absolute;
  right: 12px;
  top: 10px;
  background: transparent;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: #333;
}

/* Overlays: match admin overlay look & z-index */
#volEventOverlay,
#overlay,
#eventOverlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0,0,0,0.3);
  z-index: 1000;
}

/* Make volunteer modal body text consistent */
#volunteerEventModal p,
#dayOverview p {
  color: #4b5563;
  line-height: 1.45;
  margin: 8px 0;
}

/* Ensure small screens look good */
@media (max-width:600px) {
  #volunteerEventModal, #dayOverview { width: 92% !important; left: 50% !important; transform: translateX(-50%) !important; padding: 20px !important; }
}
</style>

</head>

<body>

  <script>
    function toggleNotifications() {
      const dropdown = document.getElementById('notificationsDropdown');
      dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }
  </script>
  <div class="dashboard-container">


    <?php include 'sidebar2.php'; ?>

    <!-- Banner -->
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
      <div class="dashboard-banner">
        <!-- <img src="bannerdog.jpg" alt="FurryHaven Banner"> -->
        <h1 class="banner-text">
          Welcome back, <?php echo htmlspecialchars($userPreferredName); ?>!<br>
          Ready to manage FurryHaven?
        </h1>
      </div>



      <div id="notificationBell" class="notification">
        <!-- Bell Icon -->
        <i class="fa-solid fa-bell" onclick="toggleNotifications()"></i>

        <!-- Unread Count Badge -->
        <?php if ($unreadCount > 0): ?>
          <span class="badge"><?php echo $unreadCount; ?></span>
        <?php endif; ?>

        <!-- Dropdown with Notifications -->
        <div id="notificationsDropdown">
          <?php if ($unreadCount > 0): ?>
            <?php while ($row = $unreadNotifications->fetch_assoc()): ?>
              <div class="notification-item">
                <?php if($_SESSION['user_role'] === 'Admin') :?>
                <a href="./reportCruelty2.php"><?php echo htmlspecialchars($row['message']); ?></a>
                <?php endif; ?>
                <?php if($_SESSION['user_role'] === 'Vet') :?>
                <a href="./animaldatabase2.php"><?php echo htmlspecialchars($row['message']); ?></a>
                <?php endif; ?>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="notification-item empty">
              No new notifications
            </div>
          <?php endif; ?>
        </div>
      </div>


      <section class="analytics-section">
        <h1> This Months Paw-gress</h1>
        <div class="analytics-charts">
          

            <div class="chart-box">
              <canvas id="animalsChart" style="width: 300px; height: 300px; display:block;"></canvas>
            </div>
            
            <!-- Donations chart with title counter -->
            <div class="chart-with-counter">
              <div class="donations-title">
                <div class="donations-total"> Weekly Donations Total: R<?php echo number_format($lastWeekTotal, 2); ?></div>
                <div id="donationsDelta" class="donations-delta small">
                  <?php
                    // Determine arrow and label comparing last week to previous week
                    if ($prevWeekTotal == 0 && $lastWeekTotal == 0) {
                      echo 'No donations in the last two weeks';
                    } else if ($prevWeekTotal == 0) {
                      echo '<span class="delta-up">&uarr; New</span> vs previous week';
                    } else {
                      $diff = $lastWeekTotal - $prevWeekTotal;
                      $pct = ($prevWeekTotal > 0) ? round(($diff / $prevWeekTotal) * 100, 1) : null;
                      if ($diff > 0) {
                        echo "<span class=\"delta-up\">&uarr; {$pct}%</span> vs previous week";
                      } else if ($diff < 0) {
                        echo "<span class=\"delta-down\">&darr; {" . abs($pct) . "%}</span> vs previous week";
                      } else {
                        echo "<span class=\"delta-neutral\">No change</span> vs previous week";
                      }
                    }
                    ?>
                </div>
              </div>
              <div class="donations-chart-wrap" style="flex:1; width:100%; height:100%;">
                <canvas id="donationsChart" style="width:100%; height:100%; display:block;"></canvas>
              </div>
            </div>
            
            <div class="chart-box">
              <div class="animalCare" style="text-align:center; margin-top:6px; font-weight:600;">Animals in Care: <?php echo $animalsInCare; ?></div>
              <canvas id="inCareChart" style="width: 300px; height: 300px; display:block;"></canvas>
            </div>
          </div>
        <div class="view-all-container">
          <button onclick="location.href='analytics.php'" class="view-all-btn">
            View All Statistics
          </button><br><br><br>
        </div><br><br>


      </section>
      <?php if ($_SESSION['user_role'] === 'Vet'): ?>
        <section class="calendar-section">
          <div class="calendar" id="calendar">
          </div>


          <script>
            document.addEventListener('DOMContentLoaded', function() {
              var calendarEl = document.getElementById('calendar');
              if (!calendarEl) return; // Prevent error if element doesn't exist
              var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: './controllers/MedicalProcedureController.php?action=getProceduresForCalendar',
                eventClick: function(info) {
                  const date = info.event.startStr;
                  fetch('./controllers/MedicalProcedureController.php?action=getProceduresForDay&date=' + date)
                    .then(res => res.json())
                    .then(data => {
                      let html = `<h3>Procedures for ${date}</h3><ul>`;
                      data.forEach(proc => {
                        html += `<li>${proc.time} - ${proc.procedureType} (${proc.animalName}) [${proc.status}]</li>`;
                      });
                      html += '</ul>';
                      document.getElementById('dayOverview').innerHTML = html;
                      document.getElementById('dayOverview').style.display = 'block';
                      document.getElementById('overlay').style.display = 'block';
                    });
                }
              });
              calendar.render();
            });
          </script>

        <?php endif; ?>
        </section>

        </section>
        <?php if ($_SESSION['user_role'] === 'Admin'): ?>
          <section class="calendar-section">
            <div class="calendar" id="admin-calendar"></div>
            <!-- Event Modal -->
            <div id="eventModal" style="display:none; position:fixed; top:20%; left:50%; transform:translateX(-50%); background:#fff; padding:20px; border-radius:8px; z-index:1001; min-width:300px;">
  <h3 id="modalTitle">Add/Edit Event</h3>
  <form id="eventForm">
    <input type="hidden" name="eventId" id="eventId">
    <label>Title: <input type="text" name="title" id="eventTitle" required></label><br>
    <label>Date & Time:
      <input type="datetime-local" name="datetime" id="eventDateTime" required>
    </label><br>
    <label>Details: <textarea name="details" id="eventDetails"></textarea></label><br>
    <!-- Button container -->
<div class="modal-buttons">
  <button type="submit" id="saveEventBtn">Save</button>
  <button type="button" onclick="closeEventModal()">Cancel</button>
  <button type="button" id="deleteEventBtn" style="display:none;" onclick="deleteEvent()">Delete</button>
</div>

  </form>
</div>
            <div id="eventOverlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:1000;"></div>
          </section>
        <?php endif; ?>
          </section>

          <br><br><br>


          <?php if ($_SESSION['user_role'] === 'Volunteer'): ?>
            <section class="calendar-section">
              <div class="calendar" id="volunteer-calendar">
              </div>
              <script>
                document.addEventListener('DOMContentLoaded', function() {
                  var calendarEl = document.getElementById('volunteer-calendar');
                  if (!calendarEl) return;
                  var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    events: './controllers/CalendarController.php?action=fetch',
                    editable: false,
                    selectable: false,
                    eventTimeFormat: { // 24-hour format
                      hour: '2-digit',
                      minute: '2-digit',
                      hour12: false
                    },
                    eventDisplay: 'block', // Show time and title in month view
                    eventClick: function(info) {
                      document.getElementById('volEventTitle').textContent = info.event.title;
                      document.getElementById('volEventDetails').textContent = info.event.extendedProps.details || '';
                      document.getElementById('volEventDate').textContent = info.event.start.toLocaleString();
                      document.getElementById('volunteerEventModal').style.display = 'block';
                      document.getElementById('volEventOverlay').style.display = 'block';
                    }
                  });
                  calendar.render();
                });
              </script>
            </section>
          <?php endif; ?>

          <section class="animals-section">
            <h1>Registered Animals</h1>


            <?php if ($_SESSION['user_role'] === 'Admin') : ?>
              <button class="action-btn" onclick="location.href='./registration2.php'">+ New</button>
            <?php endif; ?>

            <form method="GET" class="form-inline">
              <input type="text" class="search-box" name="search" placeholder="Search by name..." value="<?php echo $search; ?>">
              <select class="filter-select" name="filter">
                <option value="">-- Filter By --</option>
                <option value="Adult" <?php echo $filter == 'Adult' ? 'selected' : ''; ?>>Adult</option>
                <option value="Juvenile" <?php echo $filter == 'Juvenile' ? 'selected' : ''; ?>>Juvenile</option>
                <option value="Senior" <?php echo $filter == 'Senior' ? 'selected' : ''; ?>>Senior</option>
                <option value="Dog" <?php echo $filter == 'Dog' ? 'selected' : ''; ?>>Dog</option>
                <option value="Cat" <?php echo $filter == 'Cat' ? 'selected' : ''; ?>>Cat</option>
              </select>
              <button type="submit" class="search-filter-btn">Search / Filter</button>
            </form>


            <table>
              <tr>
                <th>Thumbnail</th>
                <th>Name</th>
                <th>Adoption Status</th>
                <?php if ($_SESSION['user_role'] === "Admin"): ?>
                  <th>Applications</th>
                <?php endif; ?>
                <th>Species</th>
                <th>Gender</th>
                <th>Intake Date</th>
              </tr>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr onclick="showDetails('<?php echo $row['Animal_ID']; ?>')">
                  <td><img src="images/animals/<?php echo $row['filePath']; ?>" width="50"></td>
                  <td><?php echo $row['Animal_Name']; ?></td>
                  <td class="status <?php echo $row['outtakeType'] ? 'adopted' : 'available'; ?>">
                    <span class="status-badge"><?php echo $row['outtakeType'] ? 'Adopted' : 'Available'; ?></span>
                  </td>

                  <?php if ($_SESSION['user_role'] === "Admin"): ?>
                    <td><a href="animal_applications.php?animal_id=<?php echo $row['Animal_ID']; ?>">View Applications</a></td>
                  <?php endif; ?>
                  <td><?php echo $row['Animal_Type']; ?></td>
                  <td><?php echo $row['Animal_Gender']; ?></td>
                  <td><?php echo date("d-M-Y", strtotime($row['Animal_RescueDate'])); ?></td>
                </tr>
              <?php endwhile; ?>
            </table>
            <div class="view-all-container">
              <button class="view-all-btn" onclick="location.href='./animaldatabase2.php'">View All Animals</button>
            </div>
          </section>
          <div id="animalPopup" style="display: none;">
            <div id="popupContent">

            </div>
            <button onclick="closeAnimalPopup()">Close</button>
          </div>
          <div id="dayOverview" style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%); background:#fff; padding:20px; border-radius:8px; z-index:1000;"></div>
          <div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:999;"></div>

          <!-- Volunteer Event Details Modal -->
          <div id="volunteerEventModal" style="display:none; position:fixed; top:20%; left:50%; transform:translateX(-50%); background:#fff; padding:20px; border-radius:8px; z-index:1001; min-width:300px;">
            <h3 id="volEventTitle"></h3>
            <p id="volEventDetails"></p>
            <p><strong>Date:</strong> <span id="volEventDate"></span></p>
            <button onclick="closeVolunteerEventModal()">Close</button>
          </div>
          <div id="volEventOverlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:1000"></div>

    </div>

  </div>


 

  <script src="sidebar2.js"></script>
  <script>
    (function(){
      // Local-scoped sidebar toggle to avoid global collisions with other scripts
      function toggleSidebar(){
        var s = document.getElementById('sidebar');
        var main = document.getElementById('main');
        if(!s || !main) return;
        if (s.style.left === '-220px') {
          s.style.left = '0';
          main.style.marginLeft = '220px';
        } else {
          s.style.left = '-220px';
          main.style.marginLeft = '0';
        }
      }
      if (typeof window.toggleSidebar !== 'function') window.toggleSidebar = toggleSidebar;
    })();

    // Calendar popup
    function openPopup(cell, date) {
      const rect = cell.getBoundingClientRect();
      const popup = document.getElementById('popupForm');
      popup.style.top = (window.scrollY + rect.top + 25) + 'px';
      popup.style.left = (rect.left) + 'px';
      document.getElementById('eventDate').value = date;
      popup.style.display = 'block';
      document.getElementById('overlay').style.display = 'block';
    }

    function closePopup() {
      document.getElementById('popupForm').style.display = 'none';
      document.getElementById('overlay').style.display = 'none';
    }

    // window.addEventListener("click",()=>{
    //   closeAnimalPopup();
    //   // closePopup();
    // })

    // Animal popup
    function showDetails(animalID) {
      fetch('showAnimalDetails.php?details=' + animalID)
        .then(res => res.text())
        .then(data => {
          document.getElementById('popupContent').innerHTML = data;
          document.getElementById('animalPopup').style.display = 'block';
          document.getElementById('overlay').style.display = 'block';
        });
    }

    function closeAnimalPopup() {
      document.getElementById('animalPopup').style.display = 'none';
      document.getElementById('overlay').style.display = 'none';
    }

    document.getElementById('overlay').onclick = function() {
      document.getElementById('dayOverview').style.display = 'none';
      document.getElementById('overlay').style.display = 'none';
    };

    // Modal close function for Volunteer Calendar
    function closeVolunteerEventModal() {
      document.getElementById('volunteerEventModal').style.display = 'none';
      document.getElementById('volEventOverlay').style.display = 'none';
    }

    // Allow clicking the overlay to close the modal
    document.getElementById('volEventOverlay').onclick = closeVolunteerEventModal;
  </script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


  <script>
    // Animals Chart (Available vs Adopted vs Total)
    (function(){
      const animalsEl = document.getElementById('animalsChart');
      if (!animalsEl) return;
      const animalsCtx = animalsEl.getContext('2d');
      new Chart(animalsCtx, {
        type: 'doughnut',
        data: {
          labels: ['Available', 'Adopted'],
          datasets: [{
            data: [<?php echo $available; ?>, <?php echo $adopted; ?>],
            backgroundColor: ['#F4D35E', '#00796B'],
            borderWidth: 1
          }]
        },
        options: {
          plugins: {
            title: {
              display: true,
              text: 'Animal Status'
            },
            legend: {
              position: 'bottom'
            }
          }
        }
      });
    })();

    // Health Chart (Healthy vs In Treatment)
    (function(){
      const healthEl = document.getElementById('healthChart');
      if (!healthEl) return;
      const healthCtx = healthEl.getContext('2d');
      new Chart(healthCtx, {
        type: 'doughnut',
        data: {
          labels: ['Healthy', 'In Treatment'],
          datasets: [{
            data: [<?php echo $healthy; ?>, <?php echo $inTreatment; ?>],
            backgroundColor: ['#2A9D8F', '#F4E1D2'],
            borderWidth: 1
          }]
        },
        options: {
          plugins: {
            title: {
              display: true,
              text: 'Health Status'
            },
            legend: {
              position: 'bottom'
            }
          }
        }
      });
    })();


    // Total Animals Distribution
    (function(){
      const totalEl = document.getElementById('totalChart');
      if (!totalEl) return;
      const totalCtx = totalEl.getContext('2d');
      new Chart(totalCtx, {
        type: 'pie',
        data: {
          labels: ['Total Animals', 'Adopted + Available'],
          datasets: [{
            data: [<?php echo $totalAnimals; ?>, <?php echo $adopted + $available; ?>],
            backgroundColor: ['#6A994E', '#6B7C93']
          }]
        },
        options: {
          plugins: {
            title: {
              display: true,
              text: 'Total Distribution'
            },
            legend: {
              position: 'bottom'
            }
          }
        }
      });
    })();

  </script>

  <script>
    (function(){
      const inCareEl = document.getElementById('inCareChart');
      if (!inCareEl) return;
      const inCareCtx = inCareEl.getContext('2d');
      new Chart(inCareCtx, {
        type: 'doughnut',
        data: {
          labels: ['Normal (Available)', 'Boarded'],
          datasets: [{
            data: [<?php echo $available; ?>, <?php echo $boardedCount; ?>],
            backgroundColor: ['#4e9f3d', '#f4d35e']
          }]
        },
        options: {
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });
    })();
  </script>

    <script>
      // Donations chart data prepared server-side (stacked by campaign)
      <?php
        // Prepare labels (last 7 days: oldest -> newest)
        $labels = [];
        $dateIndex = []; // map date string to index
        for ($i = 6; $i >= 0; $i--) {
          $day = date('Y-m-d', strtotime("-{$i} days"));
          $labels[] = date('D', strtotime($day));
          $dateIndex[$day] = count($labels) - 1;
        }

        // Aggregate donations per campaign per day for the last 7 days
        $campaignData = []; // cid => ['name'=>..., 'totals'=>[0..6]]
        try {
          $sql = "SELECT COALESCE(c.CampaignID,'GENERAL') AS cid, COALESCE(c.CampaignName,'General') AS cname, DATE(d.DonationDate) as d, SUM(d.DonationAmount) AS total
                  FROM donations d
                  LEFT JOIN campaign c ON d.CampaignID = c.CampaignID
                  WHERE DATE(d.DonationDate) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                    AND d.isDeleted = 0
                  GROUP BY cid, d
                  ORDER BY cid";
          $res = $conn->query($sql);
          if ($res) {
            while ($row = $res->fetch_assoc()) {
              $cid = $row['cid'];
              $cname = $row['cname'];
              $d = $row['d'];
              $total = floatval($row['total']);
              if (!isset($campaignData[$cid])) {
                $campaignData[$cid] = ['name' => $cname, 'totals' => array_fill(0, 7, 0)];
              }
              if (isset($dateIndex[$d])) {
                $campaignData[$cid]['totals'][$dateIndex[$d]] = $total;
              }
            }
          }
        } catch (Exception $e) {
          // leave campaignData empty on error
        }

        // Ensure there's always a 'General' dataset even if empty (for consistency)
        if (!isset($campaignData['GENERAL'])) {
          $campaignData['GENERAL'] = ['name' => 'General', 'totals' => array_fill(0,7,0)];
        }

        // Prepare JS-friendly datasets array
        $jsDatasets = [];
        // Color palette to cycle through
        $colors = ['#4e9f3d','#2a9d8f','#f4d35e','#00796B','#6A994E','#6B7C93','#df7100','#c62828','#8e44ad','#3498db'];
        $i = 0;
        foreach ($campaignData as $cid => $info) {
          $color = $colors[$i % count($colors)];
          $jsDatasets[] = [
            'label' => $info['name'],
            'data' => $info['totals'],
            'backgroundColor' => $color
          ];
          $i++;
        }
      ?>

      const donationsLabels = <?php echo json_encode($labels); ?>;
      const donationsDatasets = <?php echo json_encode($jsDatasets); ?>;

      const donationsCtx = document.getElementById('donationsChart').getContext('2d');
      const donationsChart = new Chart(donationsCtx, {
        type: 'bar',
        data: {
          labels: donationsLabels,
          datasets: donationsDatasets
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            title: { display: false },
            tooltip: {
              enabled: true,
              // Show campaign name as the tooltip title and amount as the label
              callbacks: {
                title: function(context) {
                  // context is an array of tooltip items; for stacked bars we get one item when intersecting
                  if (!context || !context.length) return '';
                  return context[0].dataset.label || '';
                },
                label: function(context) {
                  const value = context.parsed && context.parsed.y !== undefined ? context.parsed.y : context.formattedValue;
                  return 'R' + Number(value).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
              }
            }
          },
          interaction: { mode: 'index', intersect: false },
          scales: {
            x: { stacked: true },
            y: { stacked: true, beginAtZero: true }
          },
          layout: { padding: { left:6, right:6, top:4, bottom:4 } }
        }
      });

      // Style the delta element based on content (simple runtime check)
      (function styleDonationsDelta(){
        const el = document.getElementById('donationsDelta');
        if (!el) return;
        const up = el.querySelector('.delta-up');
        const down = el.querySelector('.delta-down');
        if (up) el.style.color = window.getComputedStyle(up).color;
        if (down) el.style.color = window.getComputedStyle(down).color;
      })();
    </script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
  // Admin calendar
  var adminCalendarEl = document.getElementById('admin-calendar');
  var adminCalendar = null;
  if (adminCalendarEl) {
    adminCalendar = new FullCalendar.Calendar(adminCalendarEl, {
      initialView: 'dayGridMonth',
      selectable: true,
      editable: true,
      events: './controllers/CalendarController.php?action=fetch',
      eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
      eventDisplay: 'block',
      select: function(info) {
        openEventModal({ date: info.startStr });
      },
      eventClick: function(info) {
        openEventModal({
          id: info.event.id,
          title: info.event.title,
          date: info.event.startStr,
          details: info.event.extendedProps.details
        });
      },
      eventDrop: function(info) {
        updateEventDate(info.event.id, info.event.startStr);
      }
    });
    adminCalendar.render();
  }

  // Modal logic
  window.openEventModal = function(event = {}) {
    document.getElementById('eventId').value = event.id || '';
    document.getElementById('eventTitle').value = event.title || '';
    document.getElementById('eventDateTime').value = event.date || '';
    document.getElementById('eventDetails').value = event.details || '';
    document.getElementById('modalTitle').textContent = event.id ? 'Edit Event' : 'Add Event';
    document.getElementById('deleteEventBtn').style.display = event.id ? '' : 'none';
    document.getElementById('eventModal').style.display = 'block';
    document.getElementById('eventOverlay').style.display = 'block';
  };
  window.closeEventModal = function() {
    document.getElementById('eventModal').style.display = 'none';
    document.getElementById('eventOverlay').style.display = 'none';
  };
  var eventOverlayEl = document.getElementById('eventOverlay');
  if (eventOverlayEl) eventOverlayEl.onclick = closeEventModal;

  // Save event (add or edit)
  var eventForm = document.getElementById('eventForm');
  if (eventForm) {
    eventForm.onsubmit = function(e) {
      e.preventDefault();
      var formData = new FormData(this);
      var action = formData.get('eventId') ? 'update' : 'create';
      // convert datetime-local to MySQL DATETIME if present
      var dt = formData.get('datetime');
      if (dt) {
        var mysqlDateTime = dt.replace('T', ' ') + ':00';
        formData.set('event_date', mysqlDateTime);
      }
      // map fields expected by backend
      formData.set('action', action);
      formData.set('event_id', formData.get('eventId') || '');
      formData.set('event_title', formData.get('title') || formData.get('eventTitle') || '');
      formData.set('event_details', formData.get('details') || formData.get('eventDetails') || '');

      fetch('./controllers/CalendarController.php', {
        method: 'POST',
        body: formData
      })
      .then(function(res){ return res.json(); })
      .then(function(response) {
        if (response.success) {
          closeEventModal();
          if (adminCalendar) adminCalendar.refetchEvents();
        } else {
          alert(response.message || 'Error saving event.');
        }
      })
      .catch(function(err){
        console.error('Save event error', err);
        alert('Error saving event. See console.');
      });
    };
  }

  // Delete event
  window.deleteEvent = function() {
    var eventId = document.getElementById('eventId').value;
    if (!eventId) return;
    if (!confirm('Delete this event?')) return;
    var fd = new FormData();
    fd.append('action', 'delete');
    fd.append('event_id', eventId);
    fetch('./controllers/CalendarController.php', {
      method: 'POST',
      body: fd
    })
    .then(function(res){ return res.json(); })
    .then(function(response) {
      if (response.success) {
        closeEventModal();
        if (adminCalendar) adminCalendar.refetchEvents();
      } else {
        alert(response.message || 'Error deleting event.');
      }
    })
    .catch(function(err){
      console.error('Delete event error', err);
      alert('Error deleting event. See console.');
    });
  };

  // Drag & drop update
  function updateEventDate(eventId, newDate) {
    var fd = new FormData();
    fd.append('action', 'update');
    fd.append('event_id', eventId);
    fd.append('event_date', (newDate || '').replace('T', ' ').slice(0, 19));
    fetch('./controllers/CalendarController.php', {
      method: 'POST',
      body: fd
    })
    .then(function(res){ return res.json(); })
    .then(function(response) {
      if (!response.success) {
        alert(response.message || 'Error updating event date.');
        if (adminCalendar) adminCalendar.refetchEvents();
      }
    })
    .catch(function(err){
      console.error('Update event date error', err);
      if (adminCalendar) adminCalendar.refetchEvents();
    });
  }
});
</script>
</body>

</html>