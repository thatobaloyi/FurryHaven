<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/config/databaseconnection.php';
if (!isset($conn) || $conn->connect_error) { die("DB error"); }

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applicationsID'], $_POST['action'])) {
    $id = $_POST['applicationsID'];
    $status = ($_POST['action'] === 'approve') ? 'Accepted' : 'Rejected';

    $stmt = $conn->prepare("UPDATE applications SET applicationStatus=? WHERE applicationsID=?");
    $stmt->bind_param("ss", $status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: applications.php");
    exit;
}

// Load all volunteer apps (case-insensitive)
$res = $conn->query("
    SELECT * FROM applications
    WHERE LOWER(applicationType) LIKE 'volunt%'
    ORDER BY applicationsID DESC
");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Volunteer Applications</title>
</head>
<body>
<h2>Volunteer Applications</h2>

<table border="1" cellpadding="6">
<tr>
  <th>ID</th><th>Name</th><th>Email</th><th>Phone</th>
  <th>Skills</th><th>Status</th><th>Type</th><th>Actions</th>
</tr>
<?php if ($res->num_rows === 0): ?>
  <tr><td colspan="8">No applications yet.</td></tr>
<?php else: while ($row = $res->fetch_assoc()): ?>
  <tr>
    <td><?= htmlspecialchars($row['applicationsID']) ?></td>
    <td><?= htmlspecialchars($row['applicantFirstName']." ".$row['applicantLastName']) ?></td>
    <td><?= htmlspecialchars($row['applicantEmail']) ?></td>
    <td><?= htmlspecialchars($row['applicantPhone']) ?></td>
    <td><?= nl2br(htmlspecialchars($row['applicantSkills'])) ?></td>
    <td><?= htmlspecialchars($row['applicationStatus']) ?></td>
    <td><?= htmlspecialchars($row['applicationType']) ?></td>
    <td>
      <form method="post" style="display:inline">
        <input type="hidden" name="applicationsID" value="<?= $row['applicationsID'] ?>">
        <button type="submit" name="action" value="approve">Approve</button>
      </form>
      <form method="post" style="display:inline">
        <input type="hidden" name="applicationsID" value="<?= $row['applicationsID'] ?>">
        <button type="submit" name="action" value="reject">Reject</button>
      </form>
    </td>
  </tr>
<?php endwhile; endif; ?>
</table>

<p><a href="volunteer.php">➡ View Approved Volunteers</a></p>
</body>
</html>


