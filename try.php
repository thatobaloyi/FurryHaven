<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require('config/databaseconnection.php');

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Fetch cruelty reports
$stmt = $conn->prepare("
    SELECT crueltyID, reportDate, description, evidence, animalStreetAddress, 
           animalCity, reporterFirstName, reporterLastName, reporterEmail, 
           reporterPhone, assignedTo, status, animalType, incidentType
    FROM crueltyreport
    WHERE isDeleted = 0
    ORDER BY reportDate DESC
");
$stmt->execute();
$result = $stmt->get_result();
$reports = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cruelty Reports</title>
<link rel="stylesheet" href="style2.css">
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar2.php'; ?>
    <div id="main" class="main-content">
        <h1>Cruelty Reports</h1>
        <a href="cruelty.php"><button>New Report</button></a>

        <table id="reportTable">
            <thead>
                <tr>
                    <th>Report By</th>
                    <th>Report Date</th>
                    <th>Street Address</th>
                    <th>Incident Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($reports)) : ?>
                <?php foreach ($reports as $row) : 
                    $first = trim($row['reporterFirstName'] ?? '');
                    $last = trim($row['reporterLastName'] ?? '');
                    if ($first === '' && $last === '') {
                        $reportBy = "Anonymous";
                    } elseif ($first !== '' && $last !== '') {
                        $reportBy = htmlspecialchars($last . " " . $first);
                    } else {
                        $reportBy = htmlspecialchars($first . $last);
                    }
                ?>
                <tr data-id="<?= htmlspecialchars($row['crueltyID']) ?>">
                    <td><?= $reportBy ?></td>
                    <td><?= date("d-M-Y", strtotime($row['reportDate'])) ?></td>
                    <td><?= htmlspecialchars($row['animalStreetAddress'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['incidentType'] ?? '') ?></td>
                    <td>
                        <form method="POST" action="update_status.php" class="status-form">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <input type="hidden" name="crueltyID" value="<?= htmlspecialchars($row['crueltyID']) ?>">
                            <select name="status" onchange="this.form.submit()">
                                <option value="Open" <?= $row['status']==='Open' ? 'selected' : '' ?>>Open</option>
                                <option value="Pending" <?= $row['status']==='Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Closed" <?= $row['status']==='Closed' ? 'selected' : '' ?>>Closed</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="softdelete.php" onsubmit="return confirm('Are you sure you want to delete this report?');">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <input type="hidden" name="crueltyID" value="<?= htmlspecialchars($row['crueltyID']) ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No records found</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="reportModal" style="display:none;">
    <div>
        <button id="closeModal">Close</button>
        <div id="modalBody"></div>
    </div>
</div>

<script src="sidebar2.js"></script>
<script>
// AJAX function to fetch report details
const rows = document.querySelectorAll("#reportTable tbody tr[data-id]");
const modal = document.getElementById("reportModal");
const modalBody = document.getElementById("modalBody");
const closeBtn = document.getElementById("closeModal");

rows.forEach(row => {
    row.addEventListener("click", (e) => {
        if (e.target.closest("form")) return; // ignore clicks on forms

        const id = row.getAttribute("data-id");
        fetch('get_report_details.php?id=' + encodeURIComponent(id))
        .then(response => response.json())
        .then(data => {
            modalBody.innerHTML = `
                <p><strong>Date:</strong> ${data.reportDate}</p>
                <p><strong>Street:</strong> ${data.animalStreetAddress ?? ''}</p>
                <p><strong>City:</strong> ${data.animalCity ?? ''}</p>
                <p><strong>Type:</strong> ${data.incidentType ?? ''}</p>
                <p><strong>Animal:</strong> ${data.animalType ?? ''}</p>
                <p><strong>Description:</strong><br>
                    <textarea rows="4" readonly>${data.description}</textarea>
                </p>
                <p><strong>Reporter:</strong> ${data.reporterFirstName ?? ''} ${data.reporterLastName ?? ''}</p>
                <p><strong>Email:</strong> ${data.reporterEmail}</p>
                <p><strong>Phone:</strong> ${data.reporterPhone ?? ''}</p>
                <p><strong>Assigned To:</strong> ${data.assignedTo ?? 'Unassigned'}</p>
                <p><strong>Evidence:</strong> ${data.evidence ? `<a href="uploads/${data.evidence}" target="_blank">View</a>` : 'N/A'}</p>
            `;
            modal.style.display = "block";
        })
        .catch(err => console.error(err));
    });
});

closeBtn.onclick = () => modal.style.display = "none";
window.onclick = e => { if(e.target==modal) modal.style.display="none"; };
</script>
</body>
</html>

