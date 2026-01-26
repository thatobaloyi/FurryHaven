<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/controllers/DonationsController.php';
require_once __DIR__ . '/controllers/PledgeController.php';
require_once __DIR__ . '/controllers/PledgeInstallmentController.php';

$donationsController = new DonationsController();
$pledgeController = new PledgeController();
$installmentController = new PledgeInstallmentController();

$donations = $donationsController->getAllDonations();
$pledges = $pledgeController->getAllPledges();
$installments = $installmentController->getAllInstallments();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Donations & Pledges</title>
  <link rel="stylesheet" href="style2.css">
  <style>
    h1 {
      color: #003366;
      text-align: center;
      border-bottom: 2px solid #FF8C00;
      padding-bottom: 0.5rem;
    }

    blockquote {
      font-style: italic;
      text-align: center;
      color: #003366;
      font-size: 1.1rem;
      margin: 1.5rem 0;
    }

    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      margin: 0;
      /* padding: 20px; */
    }

    h2 {
      margin-bottom: 15px;
      color: #333;
    }

    .toolbar {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
    }

    .toolbar input {
      padding: 8px;
      width: 250px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .expand-btn {
      cursor: pointer;
      color: #007bff;
      text-decoration: underline;
      font-size: 14px;
    }

    .details-row {
      display: none;
      background: #f9fafb;
    }

    .details-cell {
      padding: 15px;
    }

    .pledge-card {
      padding: 15px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    .actions {
      margin-top: 10px;
    }

    .actions button {
      margin-right: 8px;
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
    }

    .btn-paid {
      background: #28a745;
      color: white;
    }

    .btn-edit {
      background: #ffc107;
      color: black;
    }

    .btn-receipt {
      background: #17a2b8;
      color: white;
    }

    .btn-delete {
      background: #dc3545;
      color: white;
    }

    .btn-reminder {
      background: #6c757d;
      color: white;
    }
  </style>
</head>

<body>

  <div class="dashboard-container">
    <?php include 'sidebar2.php'; ?>
    <div class="main-content" id="mainContent">
      <h1>Donations & Pledges</h1>
      <blockquote>"Every contribution fuels our mission to save lives."</blockquote>

      <div class="toolbar">
        <input type="text" id="searchInput" placeholder="Search by donor or campaign...">
      </div>

      <h2>All Donations</h2>
      <table id="donationsTable" class="donation-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Donor</th>
            <th>Amount</th>
            <th>Type</th>
            <th>Campaign</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($donations as $donation): ?>
            <tr>
              <td><?= htmlspecialchars($donation['DonationDate']) ?></td>
              <td><?= htmlspecialchars($donation['DonorID']) ?></td>
              <td>R<?= number_format($donation['DonationAmount'], 2) ?></td>
              <td><?= htmlspecialchars($donation['DonationType']) ?></td>
              <td><?= htmlspecialchars($donation['CampaignID'] ?? 'N/A') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <h2>All Pledges (Overdue First)</h2>
      <table id="pledgesTable" class="donation-table">
        <thead>
          <tr>
            <th>Pledge ID</th>
            <th>Donor</th>
            <th>Amount</th>
            <th>Start Date</th>
            <th>Frequency</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pledges as $pledge): ?>
            <tr <?= ($pledge['Status'] === 'Pending' && $pledge['StartDate'] < date('Y-m-d')) ? 'style="background:#ffeaea;"' : '' ?>>
              <td><?= htmlspecialchars($pledge['PledgeID']) ?></td>
              <td><?= htmlspecialchars($pledge['DonorID']) ?></td>
              <td>R<?= number_format($pledge['PledgeAmount'], 2) ?></td>
              <td><?= htmlspecialchars($pledge['StartDate']) ?></td>
              <td><?= htmlspecialchars($pledge['Frequency']) ?></td>
              <td><?= htmlspecialchars($pledge['Status']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <h2>All Pledge Installments (Overdue First)</h2>
      <table id="installmentsTable" class="donation-table">
        <thead>
          <tr>
            <th>Installment ID</th>
            <th>Pledge ID</th>
            <th>Due Date</th>
            <th>Amount Due</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($installments as $inst): ?>
            <tr <?= ($inst['Status'] === 'Pending' && $inst['DueDate'] < date('Y-m-d')) ? 'style="background:#ffeaea;"' : '' ?>>
              <td><?= htmlspecialchars($inst['InstallmentID']) ?></td>
              <td><?= htmlspecialchars($inst['PledgeID']) ?></td>
              <td><?= htmlspecialchars($inst['DueDate']) ?></td>
              <td>R<?= number_format($inst['AmountDue'], 2) ?></td>
              <td><?= htmlspecialchars($inst['Status']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

  <script>
    function toggleDetails(id) {
      const row = document.getElementById('details-' + id);
      row.style.display = row.style.display === 'table-row' ? 'none' : 'table-row';
    }

    // Search filter
    document.getElementById('searchInput').addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();

      // Donations Table
      document.querySelectorAll('#donationsTable tbody tr').forEach(row => {
        const donor = row.cells[1].textContent.toLowerCase();
        const campaign = row.cells[4].textContent.toLowerCase();
        if (donor.includes(filter) || campaign.includes(filter)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });

      // Pledges Table
      document.querySelectorAll('#pledgesTable tbody tr').forEach(row => {
        const donor = row.cells[1].textContent.toLowerCase();
        const freq = row.cells[4].textContent.toLowerCase();
        const status = row.cells[5].textContent.toLowerCase();
        if (
          donor.includes(filter) ||
          freq.includes(filter) ||
          status.includes(filter)
        ) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });

      // Installments Table
      document.querySelectorAll('#installmentsTable tbody tr').forEach(row => {
        const pledgeId = row.cells[1].textContent.toLowerCase();
        const status = row.cells[4].textContent.toLowerCase();
        if (
          pledgeId.includes(filter) ||
          status.includes(filter)
        ) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  </script>

  <?php include 'footer2.php'; ?>
  <script src="sidebar2.js"></script>
</body>

</html>