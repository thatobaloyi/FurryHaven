<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$serverName = "is3-dev.ict.ru.ac.za";
$user = "EzTeck";
$password = "Ezt3ck!25";
$database = "ezteck";  

$conn = new mysqli($serverName, $user, $password, $database);

include_once __DIR__ . '/./config/databaseconnection.php';

global $conn;

// CALENDAR SETUP
$month = date("m");
$year = date("Y");

if (isset($_GET['month']) && isset($_GET['year'])) {
    $month = $_GET['month'];
    $year = $_GET['year'];
}

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDay = date("w", strtotime("$year-$month-01"));

// fetch events
$result = $conn->query("SELECT * FROM events WHERE MONTH(event_date) = $month AND YEAR(event_date) = $year");
$events = [];
while ($row = $result->fetch_assoc()) {
    $events[$row['event_date']][] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Interactive Calendar</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 10px; vertical-align: top; }
        td { cursor: pointer; }
        td:hover { background: #f0f0f0; }
        .event { background: #e0f7fa; margin: 2px 0; padding: 2px; border-radius: 3px; }
        #popupForm {
            display:none; position:fixed; top:20%; left:30%; background:white;
            border:1px solid black; padding:20px; z-index:1000;
        }
        #overlay {
            display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.5); z-index:999;
        }
    </style>
</head>
<body>
    <h2>Calendar - <?php echo date("F Y", strtotime("$year-$month-01")); ?></h2>
    <a href="?month=<?php echo ($month==1 ? 12 : $month-1); ?>&year=<?php echo ($month==1 ? $year-1 : $year); ?>">Prev</a> |
    <a href="?month=<?php echo ($month==12 ? 1 : $month+1); ?>&year=<?php echo ($month==12 ? $year+1 : $year); ?>">Next</a>

    <table>
        <tr>
            <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th>
            <th>Thu</th><th>Fri</th><th>Sat</th>
        </tr>
        <tr>
        <?php
        for ($i=0; $i<$firstDay; $i++) {
            echo "<td></td>";
        }

        for ($day=1; $day<=$daysInMonth; $day++) {
            $dateStr = "$year-$month-".str_pad($day,2,"0",STR_PAD_LEFT);

            echo "<td onclick=\"openPopup('$dateStr')\"><b>$day</b><br>";

            if (isset($events[$dateStr])) {
                foreach ($events[$dateStr] as $event) {
                    echo "<div class='event' title='".htmlspecialchars($event['details'])."'>📌 ".$event['title']."</div>";
                }
            }
            echo "</td>";

            if (($day + $firstDay) % 7 == 0) echo "</tr><tr>";
        }
        ?>
        </tr>
    </table>

    <!-- Overlay -->
    <div id="overlay" onclick="closePopup()"></div>

    <!-- Popup Form -->
    <div id="popupForm">
        <h3>Add Event</h3>
        <form method="POST" action="save_event.php">
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="event_date" id="eventDate">
            <label>Title:</label><br>
            <input type="text" name="title" required><br><br>

            <label>Details:</label><br>
            <textarea name="details"></textarea><br><br>

            <button type="submit">Save Event</button>
            <button type="button" onclick="closePopup()">Cancel</button>
        </form>
    </div>

    <script>
        function openPopup(date) {
            document.getElementById('eventDate').value = date;
            document.getElementById('popupForm').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }
        function closePopup() {
            document.getElementById('popupForm').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
    </script>
</body>
</html>