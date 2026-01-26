<!-- databaseConnection Section -->

<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include_once './notification.php';
require('config/databaseconnection.php');

include_once './models/Campaign.php';

$campaign = new Campaign();


$result = $campaign->findAll();

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>FurryHaven Admin Dashboard</title>
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
  </style>

</head>

<body>

  <div id="Campaigns" class="dashboard-container">
    <?php include 'sidebar2.php'; ?>


    <div class="main-content" id="mainContent">
      <h1>Campaigns</h1>
      <blockquote>Manage, track, and update fundraising campaigns here.</blockquote>
      <div class="card" style="margin-top: 2rem;">
        <button onclick="location.href='./create_campaign.php'" class="btn-primary">
          + New
        </button>

        <form method="GET" style="margin:15px 0;">
          <input type="text" name="search" placeholder="Search Campaign name" class="search-box"
            value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
          <button type="submit" class="btn-primary">
            Search
          </button>
        </form>

        <table class="campaign-table">
          <tr>
            <th>Campaign Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Target Amount</th>
            <th>Inititated By</th>
            <th>Action</th>
          </tr>


          <?php while ($row = $result->fetch_assoc()): ?>
            <tr onclick="showDetails('<?php echo $row['CampaignID']; ?>')">
              <td><?php echo $row['CampaignName']; ?></td>

              <td><?php echo date("d-M-Y", strtotime($row['Campaign_StartDate'])); ?></td>
              <td><?php echo date("d-M-Y", strtotime($row['campaign_EndDate'])); ?></td>
              <td><?php echo $row['TargetAmount']; ?></td>
              <td><?php echo $row['InitiatedBy']; ?></td>
              <td>
                <form method='POST' action="./controllers/CampaignController.php" class="delete-form">
                  <input type='hidden' name='CampaignID' value='<?php echo $row['CampaignID'] ?>'>
                  <input type='hidden' name='action' value='softDelete'>

                  <button type='submit' class="delete-btn" name="soft_delete">Delete</button>
                </form>
              </td>

            </tr>
          <?php endwhile; ?>
        </table>
      </div>
    </div>
  </div>
  <?php include 'footer2.php'; ?>
  <script src="sidebar2.js"></script>
</body>

</html>