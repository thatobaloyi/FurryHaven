<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include_once './models/Donation.php';
include_once './core/functions.php';
include_once './controllers/DonationsController.php';
require_once './controllers/PledgeController.php';
include_once './models/Pledges.php';

require('config/databaseconnection.php');

$donation = new Donation();
$donationsController = new DonationsController();
$pledgesModel = new Pledges();

$donationsController->processRecurringDonations();

$result = $donation->findByUsername($_SESSION['username']);
$username = $_SESSION['username'];

$Totalquery = "SELECT SUM(DonationAmount) AS TotalDonations FROM donations WHERE DonorID = ?";
$stmt = $conn->prepare($Totalquery);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row1 = $result->fetch_assoc();

$totalDonations = $row1['TotalDonations'] ?? 0;

$result = $donation->findByUsername($_SESSION['username']);
$userPledges = $pledgesModel->findAll(); // We'll filter by user below

$campaigns = [];
$campaignResult = $conn->query("SELECT CampaignID, CampaignName FROM campaign");
if ($campaignResult && $campaignResult->num_rows > 0) {
  while ($row = $campaignResult->fetch_assoc()) {
    $campaigns[$row['CampaignID']] = $row['CampaignName'];
  }
}

$recurringDonations = [];
$recurringQuery = "SELECT * FROM donations WHERE DonorID = ? AND IsRecurring = 1 AND isDeleted = 0";
$stmt = $conn->prepare($recurringQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$recurringResult = $stmt->get_result();
if ($recurringResult && $recurringResult->num_rows > 0) {
  while ($row = $recurringResult->fetch_assoc()) {
    $recurringDonations[] = $row;
  }
}

$pledgeController = new PledgeController();
$pledgeController->sendOverduePledgeReminders();

// Overdue Pledges (Once-off, not paid, start date in the past)
$overduePledges = [];
$today = date('Y-m-d');
$overduePledgeQuery = "SELECT * FROM pledges WHERE DonorID = ? AND Frequency = 'Once' AND Status = 'Pending' AND StartDate < ? AND isDeleted = 0";
$stmt = $conn->prepare($overduePledgeQuery);
$stmt->bind_param("ss", $username, $today);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  $overduePledges[] = $row;
}

// Overdue Installments (Pending, due date in the past)
$overdueInstallments = [];
$overdueInstallmentQuery = "SELECT pi.*, p.PledgeAmount, p.CampaignID FROM pledge_installments pi
    JOIN pledges p ON pi.PledgeID = p.PledgeID
    WHERE p.DonorID = ? AND pi.Status = 'Pending' AND pi.DueDate < ? AND pi.isDeleted = 0";
$stmt = $conn->prepare($overdueInstallmentQuery);
$stmt->bind_param("ss", $username, $today);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  $overdueInstallments[] = $row;
}

$donationModel = new Donation();

// Get the leaderboard (all donors, summed)
$leaderboardResult = $donationModel->leaderboard(0); // 0 = all
$rank = 1;
$userRank = null;
$userDonationTotal = 0;

if ($leaderboardResult && $leaderboardResult->num_rows > 0) {
  while ($row = $leaderboardResult->fetch_assoc()) {
    if ($row['DonorID'] === $username) {
      $userRank = $rank;
      $userDonationTotal = $row['TotalDonated'];
    }
    $rank++;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Donation Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

    body {
      font-family: 'Arial', sans-serif;
      background-color: #FFF8E1;
      background-image: linear-gradient(135deg, #FFF8E1 0%, #F5E9D1 100%);
      color: #002D62;
      min-height: 100vh;
    }

    .card {
      background-color: #FFFBF4;
      border-radius: 1.5rem;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      /* ensure equal spacing between cards */
      margin-bottom: 1.5rem;
      padding: 1.75rem;
      /* keep consistent inner spacing */
      box-sizing: border-box;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .gradient-text {
      background: linear-gradient(45deg, #FF8C00, #002D62);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    /* Page header / title (fixed, larger and consistent) */
    .page-title {
      color: #003366;
      text-align: left;
      border-bottom: 2px solid #FF8C00;
      padding-bottom: 0.5rem;
      font-family: 'Lexend', sans-serif;
      font-size: 2.25rem;
      font-weight: 700;
      margin: 0 0 1.25rem 0;
      letter-spacing: 0.2px;
    }

    /* Overdue section headings - larger and prominent */
    .overdue-heading {
      color: #FF8C00 !important;
      font-weight: 800;
      font-size: 1.5rem;
      margin: 0 0 1rem 0;
      font-family: 'Lexend', sans-serif;
      letter-spacing: 0.2px;
    }

    /* Tables take full width and get consistent vertical spacing inside cards */
    .donation-table,
    #overduePledgesTable,
    #overdueInstallmentsTable,
    #installmentsTable,
    #pledges-history-list,
    #donations-history-list {
      width: 100% !important;
      table-layout: fixed;
      border-collapse: collapse;
      margin-top: 0.75rem;
      margin-bottom: 1rem;
    }

    .donation-table th,
    .donation-table td,
    #overduePledgesTable th,
    #overduePledgesTable td,
    #overdueInstallmentsTable th,
    #overdueInstallmentsTable td,
    #installmentsTable th,
    #installmentsTable td {
      padding: 0.85rem;
      border: 1px solid #e5e7eb;
      vertical-align: middle;
      word-wrap: break-word;
      white-space: normal;
      overflow-wrap: break-word;
    }

    /* Make table headers consistent and readable */
    .donation-table thead th,
    #overduePledgesTable thead th,
    #overdueInstallmentsTable thead th {
      background-color: #18436e;
      color: #ffffff;
      font-weight: 600;
      text-align: left;
      padding: 0.85rem;
    }

    /* make rows breathe more */
    tbody tr td {
      line-height: 1.35;
    }

    /* Modal overlay and content (consistent with booking modal) */
    .modal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      padding: 20px;
      box-sizing: border-box;
      font-family: 'Lexend', Arial, sans-serif;
    }

    .modal .modal-content {
      background: #FFF8F0 !important;
      border: 3px solid #FF8C00 !important;
      border-radius: 14px !important;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12) !important;
      max-width: 700px !important;
      width: 90% !important;
      margin: 0 auto !important;
      padding: 20px 22px !important;
      color: #18436e !important;
      max-height: 85vh !important;
      overflow-y: auto !important;
    }

    .modal.small .modal-content {
      max-width: 520px !important;
    }

    .modal .close,
    .modal .close-modal,
    .modal .close-btn {
      position: absolute;
      right: 14px;
      top: 10px;
      cursor: pointer;
      font-size: 20px;
      color: #333;
      background: transparent;
      border: none;
    }

    /* Submit / primary action buttons - use site green */
    button[type="submit"],
    input[type="submit"],
    .modal .modal-content button[type="submit"],
    form button:not([type]) {
      background: #98b06f;
      color: #ffffff;
      border: none;
      padding: 10px 14px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 700;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
      transition: transform 0.12s ease, background 0.12s ease;
    }

    button[type="submit"]:hover,
    input[type="submit"]:hover,
    form button:not([type]):hover {
      background: #86a45f;
      transform: translateY(-2px);
    }

    /* ensure disabled state looks correct */
    button[disabled],
    input[disabled] {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    /* Responsive tweaks */
    @media (max-width: 900px) {
      .modal .modal-content {
        width: 96% !important;
        padding: 16px !important;
        max-width: 520px !important;
      }

      .donation-table th,
      .donation-table td {
        padding: 0.6rem;
        font-size: 0.95rem;
      }

      .page-title {
        font-size: 1.75rem;
        text-align: center;
      }
    }

    /* Close button consistency */
    .modal .close,
    .modal .close-modal,
    .modal .close-btn {
      position: absolute;
      right: 14px;
      top: 10px;
      cursor: pointer;
      font-size: 20px;
      color: #333;
      background: transparent;
      border: none;
    }

    /* Make donation/pledge/installment tables fill their container */
    .donation-table,
    #overduePledgesTable,
    #overdueInstallmentsTable,
    #installmentsTable,
    #pledges-history-list,
    #donations-history-list {
      width: 100% !important;
      table-layout: fixed;
      border-collapse: collapse;
    }

    .donation-table th,
    .donation-table td,
    #overduePledgesTable th,
    #overduePledgesTable td,
    #overdueInstallmentsTable th,
    #overdueInstallmentsTable td,
    #installmentsTable th,
    #installmentsTable td {
      padding: 0.75rem;
      border: 1px solid #e5e7eb;
      vertical-align: middle;
      word-wrap: break-word;
      white-space: normal;
      overflow-wrap: break-word;
    }

    /* Ensure header stands out and fits */
    .donation-table thead th,
    #overduePledgesTable thead th,
    #overdueInstallmentsTable thead th {
      background-color: #18436e;
      color: #ffffff;
      font-weight: 600;
      text-align: left;
    }

    /* Responsive adjustments */
    @media (max-width: 900px) {
      .modal .modal-content {
        width: 96% !important;
        padding: 16px !important;
        max-width: 520px !important;
      }

      .donation-table th,
      .donation-table td {
        padding: 0.6rem;
        font-size: 0.95rem;
      }

      .page-title {
        font-size: 1.75rem;
        text-align: center;
      }
    }

    /* Overdue headings use site deep orange */
    .overdue-heading {
      color: #FF8C00 !important;
      font-weight: 700;
      margin: 0 0 0.75rem 0;
      font-family: 'Lexend', sans-serif;
    }

    /* Match Approved Boarding H1 styling */
    .page-title {
      color: #003366;
      text-align: center;
      border-bottom: 2px solid #FF8C00;
      padding-bottom: 0.5rem;
    }

    /* Page content wrapper: center and add side margins/padding */
    .content-wrapper {
      max-width: 1200px;
      margin: 2rem auto;
      /* space above and below, center horizontally */
      padding: 0 1.25rem;
      /* left/right padding so content doesn't touch edges */
      box-sizing: border-box;
    }

    /* Slightly reduce padding on very small screens */
    @media (max-width: 640px) {
      .content-wrapper {
        padding: 0 0.75rem;
        margin: 1rem auto;
      }
    }

    /* Make buttons match userAnimal sizes / not full-width */
    /* Scoped to this file via the embedded style block so it won't affect other pages */
    .content-wrapper button,
    .content-wrapper .btn,
    .content-wrapper .action-btn,
    .content-wrapper .savebtn,
    .content-wrapper input[type="submit"],
    .content-wrapper button[type="submit"] {
      display: inline-block !important;
      width: auto !important;
      min-width: 120px !important;
      padding: 10px 20px !important;
      border-radius: 12px !important;
      font-family: 'Lexend', Arial, sans-serif !important;
      font-weight: 700 !important;
      text-align: center !important;
      box-sizing: border-box !important;
      line-height: 1 !important;
    }

    /* Ensure modal action buttons also keep same sizing */
    .modal .modal-content .savebtn,
    .modal .modal-content button,
    .modal .modal-content input[type="submit"] {
      display: inline-block !important;
      width: auto !important;
      min-width: 120px !important;
    }

    /* Prevent any utility classes from forcing 100% width inside cards/modals */
    .card .btn,
    .card button,
    .modal .btn,
    .modal button {
      width: auto !important;
    }

    /* Small screens - keep buttons readable but compact */
    @media (max-width: 640px) {
      .content-wrapper button,
      .content-wrapper .btn,
      .content-wrapper .savebtn,
      .content-wrapper input[type="submit"] {
        min-width: 100px !important;
        padding: 8px 14px !important;
        border-radius: 10px !important;
      }
    }
  </style>
  <!-- Link holder to store data that can be "fetched" by JavaScript -->

</head>

<body>
<div class="content-wrapper">
   <div class="relative w-full flex flex-col items-center justify-center text-center mt-6">
  <!-- Back Button - top left corner -->
  <button id="backButton"
    class="absolute top-4 left-6 bg-[#FF8C00] text-white font-semibold px-4 py-2 rounded-lg shadow hover:bg-[#E67E00] transition flex items-center gap-2">
    <span class="back-icon">←</span> Back
  </button>

  <!-- Centered Heading -->
  <h1 class="page-title">My Donations</h1>

  <blockquote class="text-lg italic text-[#003366] mt-2">"Contributions that save lives"</blockquote>
</div>


      <main class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Donation Summary + Ranking Row -->
        <div class="lg:col-span-3 flex flex-col md:flex-row gap-8 mb-8">
          <!-- User Donation Summary Card -->
          <div class="card flex-1 p-8 text-center flex flex-col items-center justify-center">
            <i class="fas fa-heart text-6xl text-[#FF8C00] mb-4 animate-pulse"></i>
            <h2 class="text-3xl font-bold">Total Donations</h2>
            <p id="total-donations" class="text-6xl font-extrabold gradient-text mt-2">
              R<?php echo number_format($totalDonations, 2); ?>
            </p>
            <p class="text-sm text-slate-500 mt-4">Thank you for your generosity!</p>
             <!-- Add Donation Button directly below -->
              <button id="openDonationModal"
              class="btn bg-[#FF8C00] text-white px-6 py-2 rounded-lg shadow hover:bg-[#ffb366] transition mt-4">
              Add Donation
            </button>
          </div>
          <!-- User Ranking Card -->
          <div class="card flex-1 p-8 text-center flex flex-col items-center justify-center"
            style="background: linear-gradient(90deg, #fffbe6 60%, #ffe082 100%); min-width:260px;">
            <span style="font-size:2.5em;">🏆</span>
            <h2 class="text-2xl font-bold mt-2 mb-2" style="color:#18436e;">Leaderboard Rank</h2>
            <?php if ($userRank): ?>
              <div style="font-size:2.5em; color:#FF8C00; font-weight:700;"><?php echo $userRank; ?></div>
              <div class="text-slate-600 mt-2">With a total of <span style="color:#388e3c; font-weight:600;">R<?php echo number_format($userDonationTotal, 2); ?></span></div>
            <?php else: ?>
              <div style="color:#888; font-size:1.1em;">You are not currently ranked on the leaderboard.</div>
            <?php endif; ?>
          </div>
        </div>

        <div class="lg:col-span-3 card p-8">
          <h2 class="overdue-heading">Overdue Pledges</h2>
          <table class="donation-table" id="overduePledgesTable">
            <thead>
              <tr>
                <th>Pledge Amount</th>
                <th>Start Date</th>
                <th>Campaign</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($overduePledges) > 0): ?>
                <?php foreach ($overduePledges as $pledge): ?>
                  <tr class="overdue-pledge-row" data-pledge-id="<?= htmlspecialchars($pledge['PledgeID']) ?>">
                    <td>R<?= number_format($pledge['PledgeAmount'], 2) ?></td>
                    <td><?= htmlspecialchars($pledge['StartDate']) ?></td>
                    <td><?= !empty($pledge['CampaignID']) && isset($campaigns[$pledge['CampaignID']]) ? htmlspecialchars($campaigns[$pledge['CampaignID']]) : 'No Campaign' ?></td>
                    <td><?= htmlspecialchars($pledge['Status']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center text-slate-400 italic">No overdue pledges.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>


        <div class="lg:col-span-3 card p-8">
          <h2 class="overdue-heading">Overdue Installments</h2>
          <table class="donation-table" id="overdueInstallmentsTable">
            <thead>
              <tr>
                <th>Due Date</th>
                <th>Amount Due</th>
                <th>Campaign</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($overdueInstallments) > 0): ?>
                <?php foreach ($overdueInstallments as $inst): ?>
                  <tr class="overdue-installment-row" data-installment-id="<?= htmlspecialchars($inst['InstallmentID']) ?>">
                    <td><?= htmlspecialchars($inst['DueDate']) ?></td>
                    <td>R<?= number_format($inst['AmountDue'], 2) ?></td>
                    <td><?= !empty($inst['CampaignID']) && isset($campaigns[$inst['CampaignID']]) ? htmlspecialchars($campaigns[$inst['CampaignID']]) : 'No Campaign' ?></td>
                    <td><?= htmlspecialchars($inst['Status']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center text-slate-400 italic">No overdue installments.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>





        <!-- Modal -->
        <div id="donationModal" class="modal" style="display:none;">
          <div class="modal-content">
            <span id="closeDonationModal" class="close">&times;</span>
            <form method="POST" action="./controllers/DonationsController.php" id="donationForm" class="flex flex-col gap-3">
              <div class="mb-2">
                <label for="campaignSelect">Campaign:</label>
                <select id="campaignSelect" name="CampaignID">
                  <option value="">No Campaign</option>
                </select>
              </div>

              <div class="mb-2">
                <label for="donationType">Donation Type:</label>
                <select id="donationType" name="DonationType" required>
                  <option value="">Select type</option>
                  <option value="Monetary">Monetary</option>
                  <option value="Pet supplies">Pet supplies</option>
                  <option value="Medical supplies">Medical supplies</option>
                </select>
              </div>

              <div class="mb-2">
                <label for="donationAmount">Amount:</label>
                <input type="number" step="0.01" min="0" name="DonationAmount" id="donationAmount" required>
              </div>


              <div class="mb-2" id="payment-method-container">
                <label for="paymentMethod">Payment Method:</label>
                <select id="paymentMethod" name="PaymentMethod" required>
                  <option value="">Select method</option>
                  <option value="Credit Card">Credit Card</option>
                  <option value="PayPal">PayPal</option>
                  <option value="Bank Transfer">Bank Transfer</option>
                </select>
              </div>

              <div class="mb-2" id="recurring-container">
                <label for="IsRecurring">Recurring Donation?</label>
                <select name="IsRecurring" id="IsRecurring">
                  <option value="0">No</option>
                  <option value="1">Yes</option>
                </select>
              </div>

              <div class="mb-2" id="frequency-container">
                <label for="Frequency">Frequency</label>
                <select name="Frequency" id="Frequency">
                  <option value="">Select frequency</option>
                  <option value="Monthly">Monthly</option>
                  <option value="Quarterly">Quarterly</option>
                </select>
              </div>
              <input type="hidden" name="action" value="donate">
              <button type="submit">Submit</button>
            </form>
          </div>
        </div>

        <!-- Donation History Card -->
        <div class="lg:col-span-3 card p-8">
          <h2 class="text-2xl font-bold mb-4"> Your Donation History </h2>


          <table class="donation-table">
            <thead>
              <tr>
                <th>DonationAmount</th>
                <th>DonationDate</th>
                <th>PaymentMethod</th>
                <th>DonationType</th>
                <th>Campaign Name</th>
                <th>ReceiptIssued</th>
              </tr>
            </thead>
            <tbody id="donations-history-list">
              <?php
              $donationHistory = $donation->findByUsername($_SESSION['username']);
              if ($donationHistory && $donationHistory->num_rows > 0) {
                while ($row = $donationHistory->fetch_assoc()) {
                  echo "<tr>
                <td>R" . number_format($row['DonationAmount'], 2) . "</td>
                <td>{$row['DonationDate']}</td>
                <td>{$row['PaymentMethod']}</td>
                <td>{$row['DonationType']}</td>
                <td>" . ($row['CampaignID'] && isset($campaigns[$row['CampaignID']]) ? htmlspecialchars($campaigns[$row['CampaignID']]) : 'No Campaign') . "</td>
                <td>";
                  if ($row['ReceiptIssued']) {
                    echo "<a href='{$row['ReceiptIssued']}' target='_blank'>View</a>";
                  } else {
                    echo "N/A";
                  }
                }
              } else {
                echo "<tr><td colspan='8' class='text-center text-slate-400 italic'>No donations found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
        <Section>


      </main>

      <script>
        document.addEventListener('DOMContentLoaded', function() {
          // Back Button functionality
          const backButton = document.getElementById('backButton');
          if (backButton) {
            backButton.addEventListener('click', function() {
              // Check if there's a previous page in history
              if (document.referrer && document.referrer.includes(window.location.hostname)) {
                window.history.back();
              } else {
                // If no history or coming from external site, redirect to a default page
                window.location.href = './index.php'; // Change to your desired default page
              }
            });
          }

          // Donation Modal open/close logic
          document.getElementById('openDonationModal').onclick = function() {
            document.getElementById('donationModal').style.display = 'flex';
            fetchCampaigns('campaignSelect');
          };
          document.getElementById('closeDonationModal').onclick = function() {
            document.getElementById('donationModal').style.display = 'none';
            document.getElementById('donationForm').reset();
          };

          // Pledge Modal open/close logic
          document.getElementById('openPledgeModal').onclick = function() {
            document.getElementById('pledgeModal').style.display = 'flex';
            fetchCampaigns('pledgeCampaignSelect');
          };
          document.getElementById('closePledgeModal').onclick = function() {
            document.getElementById('pledgeModal').style.display = 'none';
            document.getElementById('pledgeForm').reset();
          };

          function fetchCampaigns(selectId) {
            fetch('./controllers/CampaignController.php?action=getAllCampaigns')
              .then(res => res.json())
              .then(data => {
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">No Campaign</option>';
                if (data.length === 0) {
                  select.innerHTML += '<option value="" disabled>No campaigns available</option>';
                } else {
                  data.forEach(campaign => {
                    const option = document.createElement('option');
                    option.value = campaign.CampaignID;
                    option.textContent = campaign.CampaignName;
                    select.appendChild(option);
                  });
                }
              })
              .catch(() => {
                document.getElementById(selectId).innerHTML = '<option value="">Failed to load campaigns</option>';
              });
          }

          document.getElementById('pledgeForm').onsubmit = function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            formData.append('action', 'create');
            fetch('./controllers/PledgeController.php', {
                method: 'POST',
                body: formData
              })
              .then(res => res.text())
              .then(text => {
                try {
                  const response = JSON.parse(text);
                  alert(response.message);
                  if (response.success) {
                    form.reset();
                    document.getElementById('pledgeModal').style.display = 'none';
                    location.reload();
                  }
                } catch (e) {
                  alert('An unexpected error occurred. The server sent an invalid response.');
                }
              })
              .catch(error => {
                alert('There was an error submitting your pledge: ' + error);
              });
          };

          // Handle View Installments button click
          document.querySelectorAll('.view-installments-btn').forEach(btn => {
            btn.onclick = function() {
              const pledgeId = this.getAttribute('data-pledge-id');
              fetch('./controllers/PledgeInstallmentController.php', {
                  method: 'POST',
                  body: new URLSearchParams({
                    pledgeId
                  })
                })
                .then(res => res.json())
                .then(data => {
                  const tbody = document.querySelector('#installmentsTable tbody');
                  tbody.innerHTML = '';
                  if (data.success && data.installments.length > 0) {
                    data.installments.forEach(inst => {
                      const row = document.createElement('tr');
                      row.innerHTML = `
                            <td>${inst.DueDate}</td>
                            <td>R${parseFloat(inst.AmountDue).toFixed(2)}</td>
                            <td>${inst.Status}</td>
                        `;
                      tbody.appendChild(row);
                    });
                  } else {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center text-slate-400 italic">No installments found.</td></tr>';
                  }
                  document.getElementById('installmentsModal').style.display = 'flex';
                })
                .catch(() => {
                  alert('Could not fetch installments.');
                });
            };
          });

          // Recurring donation edit button logic
          document.querySelectorAll('.edit-recurring-btn').forEach(button => {
            button.onclick = function() {
              const donationId = this.getAttribute('data-donation-id');
              const amount = this.getAttribute('data-amount');
              const frequency = this.getAttribute('data-frequency');
              const status = this.getAttribute('data-status');

              document.getElementById('editDonationID').value = donationId;
              document.getElementById('editDonationAmount').value = amount;
              document.getElementById('editFrequency').value = frequency;
              document.getElementById('editIsActive').value = status;
              document.getElementById('editRecurringModal').style.display = 'flex';
            };
          });
          document.getElementById('closeEditRecurringModal').onclick = function() {
            document.getElementById('editRecurringModal').style.display = 'none';
          };

          // Donation form dynamic fields logic
          const donationType = document.getElementById('donationType');
          const paymentMethodContainer = document.getElementById('payment-method-container');
          const recurringContainer = document.getElementById('recurring-container');
          const frequencyContainer = document.getElementById('frequency-container');
          const isRecurring = document.getElementById('IsRecurring');

          function updateDonationFormFields() {
            if (donationType.value === 'Monetary') {
              paymentMethodContainer.style.display = '';
              recurringContainer.style.display = '';
              if (isRecurring.value === '1') {
                frequencyContainer.style.display = '';
              } else {
                frequencyContainer.style.display = 'none';
              }
            } else {
              paymentMethodContainer.style.display = 'none';
              recurringContainer.style.display = 'none';
              frequencyContainer.style.display = 'none';
            }
          }

          donationType.addEventListener('change', updateDonationFormFields);
          isRecurring.addEventListener('change', updateDonationFormFields);
          updateDonationFormFields();

          // Modal close on outside click
          window.addEventListener('click', function(event) {
            const donationModal = document.getElementById('donationModal');
            if (event.target === donationModal) {
              donationModal.style.display = 'none';
              document.getElementById('donationForm').reset();
            }
            const pledgeModal = document.getElementById('pledgeModal');
            if (event.target === pledgeModal) {
              pledgeModal.style.display = 'none';
              document.getElementById('pledgeForm').reset();
            }
            const editRecurringModal = document.getElementById('editRecurringModal');
            if (event.target === editRecurringModal) {
              editRecurringModal.style.display = 'none';
            }
            const installmentsModal = document.getElementById('installmentsModal');
            if (event.target === installmentsModal) {
              installmentsModal.style.display = 'none';
            }
            const payModal = document.getElementById('payModal');
            if (event.target === payModal) {
              payModal.style.display = 'none';
            }
          });

          // Handle edit recurring form submit
          document.getElementById('editRecurringForm').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'updateRecurring');
            fetch('./controllers/DonationsController.php', {
                method: 'POST',
                body: formData
              })
              .then(res => res.json())
              .then(response => {
                alert(response.message);
                if (response.success) {
                  document.getElementById('editRecurringModal').style.display = 'none';
                  location.reload();
                }
              })
              .catch(() => {
                alert('There was an error updating the recurring donation.');
              });
          };

          // Overdue Pledge row click
          document.querySelectorAll('.overdue-pledge-row').forEach(row => {
            row.onclick = function() {
              document.getElementById('payType').value = 'pledge';
              document.getElementById('payId').value = this.getAttribute('data-pledge-id');
              document.getElementById('payModal').style.display = 'flex';
            };
          });
          // Overdue Installment row click
          document.querySelectorAll('.overdue-installment-row').forEach(row => {
            row.onclick = function() {
              document.getElementById('payType').value = 'installment';
              document.getElementById('payId').value = this.getAttribute('data-installment-id');
              document.getElementById('payModal').style.display = 'flex';
            };
          });
          // Close pay modal
          document.getElementById('closePayModal').onclick = function() {
            document.getElementById('payModal').style.display = 'none';
          };
          // Handle payment form submit
          document.getElementById('payForm').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'markPaid');
            fetch('./controllers/PayOverDueController.php', {
                method: 'POST',
                body: formData
              })
              .then(res => res.json())
              .then(response => {
                alert(response.message);
                if (response.success) {
                  document.getElementById('payModal').style.display = 'none';
                  location.reload();
                }
              })
              .catch(() => {
                alert('There was an error processing your payment.');
              });
          };
        });
      </script>

      <!-- Pledge Modal -->
      <div id="pledgeModal" class="modal" style="display:none;">
        <div class="modal-content">
          <span id="closePledgeModal" class="close">&times;</span>
          <form id="pledgeForm" class="flex flex-col gap-3">
            <div class="mb-2">
              <label for="pledgeCampaignSelect">Campaign:</label>
              <select id="pledgeCampaignSelect" name="CampaignID">
                <option value="">No Campaign</option>
              </select>
            </div>
            <div class="mb-2">
              <label for="pledgeAmount">Pledge Amount:</label>
              <input type="number" step="0.01" min="1" name="PledgeAmount" id="pledgeAmount" required>
            </div>
            <div class="mb-2">
              <label for="pledgeFrequency">Frequency:</label>
              <select id="pledgeFrequency" name="frequency" required>
                <option value="Once">Once-off</option>
                <option value="Monthly">Monthly</option>
                <option value="Quarterly">Quarterly</option>
              </select>
            </div>
            <div class="mb-2">
              <label for="pledgeStartDate">Start Date:</label>
              <input type="date" name="StartDate" id="pledgeStartDate" required>
            </div>
            <button type="submit">Submit Pledge</button>
          </form>
        </div>
      </div>
      <!-- Pledge History Card -->
      <div class="lg:col-span-3 card p-8">
        <h2 class="text-2xl font-bold mb-4"> Your Pledges </h2>
        <!-- Add Pledge Button (matches Add Donation button style) -->
        <button id="openPledgeModal"
          class="btn bg-[#FF8C00] text-white px-6 py-2 rounded-lg shadow hover:bg-[#ffb366] transition mt-4">
          Add Pledge
        </button><br><br>
        <table class="donation-table">
          <thead>
            <tr>
              <th>Pledge Amount</th>
              <th>Frequency</th>
              <th>Installments</th>
              <th>Expected Payment Date</th>
              <th>Status</th>
              <th>Campaign</th>
              <th>Installments</th>
            </tr>
          </thead>
          <tbody id="pledges-history-list">
            <?php
            $hasPledges = false;
            if ($userPledges && $userPledges->num_rows > 0) {
              while ($pledge = $userPledges->fetch_assoc()) {
                if ($pledge['DonorID'] !== $_SESSION['username']) continue;
                $hasPledges = true;
                echo "<tr>
            <td>R" . number_format($pledge['PledgeAmount'], 2) . "</td>
            <td>{$pledge['Frequency']}</td>
            <td>{$pledge['InstallmentCount']}</td>
            <td>{$pledge['StartDate']}</td>
            <td>" . ($pledge['IsActive'] ? 'Active' : 'Inactive') . "</td>
            <td>" . (
                  !empty($pledge['CampaignID']) && isset($campaigns[$pledge['CampaignID']])
                  ? htmlspecialchars($campaigns[$pledge['CampaignID']])
                  : 'No Campaign linked'
                ) . "</td>
            <td>
    <button class='view-installments-btn' data-pledge-id='{$pledge['PledgeID']}'>
        View Installments
    </button>
</td>
        </tr>";
              }
            }
            if (!$hasPledges) {
              echo "<tr><td colspan='6' class='text-center text-slate-400 italic'>No pledges found.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
      <!-- Active Recurring Donations Table -->
      <div class="lg:col-span-3 card p-8">
        <h2 class="text-2xl font-bold mb-4">Recurring Donations</h2>
        <table class="donation-table">
          <thead>
            <tr>
              <th>Amount</th>
              <th>Frequency</th>
              <th>Start Date</th>
              <th>Payment Method</th>
              <th>Campaign</th>
              <th>Status</th>
              <th>Actions</th> <!-- New Actions column -->
            </tr>
          </thead>
          <tbody>
            <?php
            if (count($recurringDonations) > 0) {
              foreach ($recurringDonations as $donation) {
                echo "<tr>
                  <td>R" . number_format($donation['DonationAmount'], 2) . "</td>
                  <td>" . htmlspecialchars($donation['Frequency'] ?? 'N/A') . "</td>
                  <td>" . htmlspecialchars($donation['DonationDate']) . "</td>
                  <td>" . htmlspecialchars($donation['PaymentMethod']) . "</td>
                  <td>" . (
                  !empty($donation['CampaignID']) && isset($campaigns[$donation['CampaignID']])
                  ? htmlspecialchars($campaigns[$donation['CampaignID']])
                  : 'No Campaign'
                ) . "</td>
                  <td>" . ((isset($donation['isActive']) && $donation['isActive']) ? 'Active' : 'Inactive') . "</td>
                  <td>
                    <button class='edit-recurring-btn' 
                        data-donation-id='" . $donation['DonationID'] . "'
                        data-amount='" . $donation['DonationAmount'] . "'
                        data-frequency='" . $donation['Frequency'] . "'
                        data-status='" . $donation['isActive'] . "'>
                        Edit
                    </button>
                  </td>
              </tr>";
              }
            } else {
              echo "<tr><td colspan='6' class='text-center text-slate-400 italic'>No active recurring donations found.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
      <!-- Installments Modal -->
      <div id="installmentsModal" class="modal" style="display:none;">
        <div class="modal-content">
          <span id="closeInstallmentsModal" class="close">&times;</span>
          <h3>Pledge Installments</h3>
          <table id="installmentsTable" class="donation-table">
            <thead>
              <tr>
                <th>Due Date</th>
                <th>Amount Due</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
      <!-- Edit Recurring Donation Modal -->
      <div id="editRecurringModal" class="modal" style="display:none;">
        <div class="modal-content">
          <span id="closeEditRecurringModal" class="close">&times;</span>
          <h3>Edit/Deactivate Recurring Donation</h3>
          <form id="editRecurringForm">
            <input type="hidden" name="DonationID" id="editDonationID">
            <div class="mb-2">
              <label for="editDonationAmount">Amount:</label>
              <input type="number" step="0.01" min="1" name="DonationAmount" id="editDonationAmount" required readonly style="background:#f3f4f6; cursor:not-allowed;">
            </div>
            <div class="mb-2">
              <label for="editFrequency">Frequency:</label>
              <select name="Frequency" id="editFrequency" required>
                <option value="Monthly">Monthly</option>
                <option value="Quarterly">Quarterly</option>
              </select>
            </div>
            <div class="mb-2">
              <label for="editIsActive">Status:</label>
              <select name="isActive" id="editIsActive">
                <option value="1">Active</option>
                <option value="0">Deactivate</option>
              </select>
            </div>
            <button type="submit">Save Changes</button>
          </form>
        </div>
      </div>
      <!-- Payment Modal -->
      <div id="payModal" class="modal" style="display:none;">
        <div class="modal-content">
          <span id="closePayModal" class="close">&times;</span>
          <h3>Pay Overdue</h3>
          <form id="payForm">
            <input type="hidden" name="type" id="payType">
            <input type="hidden" name="id" id="payId">
            <button type="submit">Mark as Paid</button>
          </form>
        </div>
      </div>
</div> <!-- end content-wrapper -->


</body>

</html>