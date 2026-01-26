<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/config/databaseconnection.php';
if (!isset($conn) || $conn->connect_error) {
  die("DB error");
}

$animalID = $_GET['animal_id'] ?? '';
if (!$animalID) {
  die("Missing animal ID");
}

$res = $conn->query(
  "SELECT * FROM animalapplication 
   WHERE animalID = '" . $conn->real_escape_string($animalID) . "' 
   ORDER BY applicationDate DESC"
);
?>
<!DOCTYPE html>
<html>

<head>
  <title>Animal Applications</title>
  <link rel="stylesheet" href="style2.css">
</head>

<body>

  <div class="dashboard-container">

    <?php include 'sidebar2.php'; ?>

    <div class="main-content" id="mainContent">
      <p><a href="/animals">Back to animal database</a></p>
      <h2>Animal Applications</h2>

      <table>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Application Date</th>
          <th>Type</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
        <?php if ($res->num_rows === 0): ?>
          <tr>
            <td colspan="6">No applications for this animal yet.</td>
          </tr>
        <?php else: while ($row = $res->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['animalappID']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['applicationDate']) ?></td>
            <td><?= htmlspecialchars($row['animalAppType']) ?></td>
            <td><?= htmlspecialchars($row['applicationStatus']) ?></td>
            <td>
              <button onclick="openApplicationPopup('<?= $row['animalappID'] ?>')">
                View / Edit
              </button>
            </td>
          </tr>
        <?php endwhile; endif; ?>
      </table>
    </div>
  </div>

  <!-- Popup + overlay -->
  <div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:1000;"></div>
  <div id="animalPopup" style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%); background:#fff; padding:2rem; z-index:1001; border-radius:8px; min-width:300px;">
    <div id="popupContent"></div>
    <button onclick="closeApplicationPopup()">Close</button>
  </div>

  <!-- Scripts -->
  <script src="sidebar2.js"></script>
  <script>
    function openApplicationPopup(id) {
      fetch('animal_application_edit.php?id=' + id)
        .then(res => res.text())
        .then(data => {
          document.getElementById('popupContent').innerHTML = data;
          document.getElementById('animalPopup').style.display = 'block';
          document.getElementById('overlay').style.display = 'block';

          // Attach AJAX submit handler
          const form = document.querySelector('#popupContent form');
          if (form) {
            form.onsubmit = function(e) {
              e.preventDefault();
              const formData = new FormData(form);
              fetch('animal_application_edit.php?id=' + id, {
                method: 'POST',
                body: formData
              })
              .then(res => res.text())
              .then(() => {
                closeApplicationPopup();
                location.reload(); // reload parent table
              });
            };
          }
        });
    }

    function closeApplicationPopup() {
      document.getElementById('animalPopup').style.display = 'none';
      document.getElementById('overlay').style.display = 'none';
    }
  </script>

</body>
</html>
<?php
echo "OK";
exit;
?>
