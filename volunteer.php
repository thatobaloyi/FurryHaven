<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . "/./models/VolunteerActivity.php";

$volunteerActivity = new VolunteerActivity();

$result = $volunteerActivity->findAll();

?>


 <div class="main-content" id="mainContent">
      <h2>Volunteer</h2>
      <button onclick="location.href='/campaign/create'">
        + New
      </button>

      <form method="GET" style="margin:15px 0;">
        <input type="text" name="search" placeholder="Search Volunteer name"
          value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        <button type="submit">
          Search
        </button>
      </form>

      <table class="volunteer-table">
         <tr>
    <th>Activity ID</th>
    <th>Volunteer</th>
    <th>Animal ID</th>
    <th>Activity Type</th>`
    <th>Date</th>
    <th>Duration (hrs)</th>
    <th>Assigned By</th>
  </tr>
            
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr onclick="showDetails('<?php echo $row['ActivityID']; ?>')">
        <td><?php echo $row['ActivityID']; ?></td>
        <td><?php echo $row['VolunteerID']; ?></td>
        <td><?php echo $row['AnimalID']; ?></td>
        <td><?php echo $row['ActivityType']; ?></td>
        <td><?php echo date("d-M-Y", strtotime($row['Date'])); ?></td>
        <td><?php echo $row['Duration']; ?></td>
        <td><?php echo $row['AssignedBy']; ?></td>
      </tr>
    <?php endwhile; ?>
</table>
      
  </div>

