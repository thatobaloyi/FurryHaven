<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/config/databaseconnection.php';
if (!isset($conn) || $conn->connect_error) { die("DB error"); }

$id = $_GET['id'] ?? '';
if (!$id) { die("Missing volunteer ID"); }

// Save changes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['applicationStatus'] ?? 'Accepted';

    $stmt = $conn->prepare("UPDATE volunteerapplication SET status=? WHERE volAppID=?");
    $stmt->bind_param("ss", $status, $id);
    $stmt->execute();
    $stmt->close();

    echo "<script>window.opener.location.reload(); window.close();</script>";
    exit;
}

// Load record
$stmt = $conn->prepare("SELECT * FROM volunteerapplication WHERE volAppID=?");
$stmt->bind_param("s", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$data) { die("Volunteer not found"); }
?>
<!DOCTYPE html>
<html>
<head><title>Edit Volunteer</title></head>
<body>
<h2>Edit Volunteer</h2>
<form method="post">
  <input type="hidden" name="applicationsID" value="<?= $data['volAppID'] ?>">

  <p><b>Name:</b> <?= htmlspecialchars($data['username']) ?></p>

  <label>Skills</label>
<p style="white-space: pre-wrap; border:1px solid #ccc; padding:5px; width:50%;">
    <?= nl2br(htmlspecialchars($data['applicantSkills'])) ?>
</p><br>

<label>Why Volunteering</label>
<p style="white-space: pre-wrap; border:1px solid #ccc; padding:5px; width:50%;">
    <?= nl2br(htmlspecialchars($data['whyVolunteering'])) ?>
</p><br>

<label>Criminal Conviction Affidavit</label>
<p><a href="/images/volunteer_applications/<?= htmlspecialchars($data['criminalConvictionAffidavit']) ?>" target="_blank">View Document</a></p><br>

<label>Certified ID</label>
<p><a href="/images/volunteer_applications/<?= htmlspecialchars($data['certifiedID']) ?>" target="_blank">View Document</a></p><br>

<label>Indemnity Form</label>
<p><a href="/images/volunteer_applications/<?= htmlspecialchars($data['indemnityForm']) ?>" target="_blank">View Document</a></p><br>

<label>Authority to Search Form</label>
<p><a href="/images/volunteer_applications/<?= htmlspecialchars($data['authorityTosearchForm']) ?>" target="_blank">View Document</a></p><br>

  <label>Status</label><br>
  <select name="applicationStatus">
    <?php foreach(['Accepted','Open','Pending','Rejected'] as $s): ?>
      <option value="<?= $s ?>" <?= ($data['status']===$s?'selected':'') ?>><?= $s ?></option>
    <?php endforeach; ?>
  </select><br><br>

  <button type="submit">Save Changes</button>
</form>
</body>
</html>


