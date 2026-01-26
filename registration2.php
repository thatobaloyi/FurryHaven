<?php if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include_once './models/Cage.php';

$cage = new Cage();

$availableCages = null;
if (isset($_GET['animalType']) && !empty($_GET['animalType'])) {
  $animalType = $_GET['animalType'];

  $availableCages = $cage->findByAnimalType($animalType); // This method from a previous response is perfect
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>FurryHaven Admin Dashboard</title>
  <link rel="stylesheet" href="style2.css">
  <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php include 'sidebar2.php'; ?>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">

      <h1>Animal Registration Form</h1>
      <blockquote>"Record every rescue — every life matters."</blockquote>

      <form action="./controllers/AnimalController.php" method="POST" enctype="multipart/form-data" class="registration-form">
        <input type="hidden" name="action" value="register">

        <fieldset>
          <legend>Animal Type</legend>
          <select id="animalType" name="animalType" onchange="redirectToNewType(this)" required>
            <option value="" disabled selected>-- Select Type --</option>
            <option <?php echo (isset($_GET['animalType']) && $_GET['animalType'] === "Dog") ? "selected" : "" ?>>Dog</option>
            <option <?php echo (isset($_GET['animalType']) && $_GET['animalType'] === "Cat") ? "selected" : "" ?>>Cat</option>
            <option <?php echo (isset($_GET['animalType']) && $_GET['animalType'] === "Bird") ? "selected" : "" ?>>Bird</option>
            <option <?php echo (isset($_GET['animalType']) && $_GET['animalType'] === "Livestock") ? "selected" : "" ?>>Livestock</option>
            <option <?php echo (isset($_GET['animalType']) && $_GET['animalType'] === "Other") ? "selected" : "" ?>>Other</option>
          </select>
        </fieldset>
        <!-- row 1 -->
        <div class="form-row">
          <fieldset class="form-column">
            <legend>Basic Animal Information</legend>
            <label for="animalName">Animal Name</label>
            <input type="text" id="animalName" name="animalName" required>

            <label for="age">Animal Age</label>
            <select name="age" id="age" required>
              <option value="" disabled selected>-- Select Age --</option>
              <option value="Juvenile">Juvenile</option>
              <option value="Senior">Senior</option>
              <option value="Adult">Adult</option>
            </select>

            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
              <option value="" disabled selected>-- Select Gender --</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Unknown">Unknown</option>
            </select>

            <label for="breed">Breed</label>
            <input type="text" id="breed" name="breed" required>
          </fieldset>

          <fieldset class="form-column">
            <legend>Rescue & Intake Information</legend>
            <label for="rescueDate">Rescue Date</label>
            <input type="date" id="rescueDate" name="rescueDate" required>

            <label for="rescueStreet">Street Address</label>
            <input type="text" id="rescueStreet" name="rescueStreet" required>

            <label for="rescueCity">City/Town</label>
            <input type="text" id="rescueCity" name="rescueCity" required>

            <label for="rescueProvince">Province</label>
            <input type="text" id="rescueProvince" name="rescueProvince" required>

            <label for="rescuePostal">Postal Code</label>
            <input type="text" id="rescuePostal" name="rescuePostal" required>
          </fieldset>
        </div>
        <!-- row 2 -->
        <div class="form-row">
          <fieldset class="form-column">
            <legend>Health & Vaccination</legend>
            

            <label for="intakeType">Intake type</label>
            <select id="intakeType" name="intakeType" required>
              <option value="" disabled selected>-- Select Status --</option>
              <option value="Stray">Stray</option>
              <option value="Surrender">Surrender</option>
              <option value="Born in care">Born in care</option>
            </select>

            <label for="isSpayed">Is it spayed?</label>
            <select id="isSpayed" name="isSpayed" required>
              <option value="" disabled selected>-- Select Status --</option>
              <option value="Yes">Yes</option>
              <option value="No">No</option>
            </select>

            <label for="healthStatus">Health Status</label>
            <select id="healthStatus" name="healthStatus" required>
              <option value="" disabled selected>-- Select Status --</option>
              <option value="Healthy">Healthy</option>
              <option value="Sick">Sick</option>
              <option value="Injured">Injured</option>
              <option value="Recovering">Recovering</option>
              <option value="Under observation">Under observation</option>
            </select>

            <label for="vaccinationStatus">Vaccination Status</label>
            <select id="vaccinationStatus" name="vaccinationStatus" required>
              <option value="" disabled selected>-- Select Status --</option>
              <option value="Vaccinated">Vaccinated</option>
              <option value="Not Vaccinated">Not Vaccinated</option>
            </select>
          </fieldset>

          <fieldset class="form-column">
            <legend>Housing & Photos</legend>
            <label for="animalImages">Upload Animal Photos <small>(max 2MB each)</small></label>
            <input type="file" name="animalImages[]" id="animalImages" multiple>

            <label for="cage">Available Cages</label>
            <div class="cage-options">
              <?php
              foreach ($_GET as $key => $value) {
                echo "<input type='hidden' name='$key' value='$value'>";
              }

              if (isset($availableCages) && $availableCages->num_rows > 0) {
                foreach ($availableCages as $cage) {
                  echo "<div class='cage-option'>
                                    <input type='radio' name='cageId' id='{$cage['CageID']}' value='{$cage['CageID']}'>
                                    <label for='{$cage['CageID']}'>{$cage['CageID']}</label>
                                  </div>";
                }
              } else {
                echo "<p>No available cages for this animal type.</p>";
              }
              ?>
            </div>
          </fieldset>
        </div>

        <button type="submit">Register Animal</button>
      </form>
    </div>
  </div>


  <?php include 'footer2.php'; ?>
  <script src="sidebar2.js"></script>

  <script>
    function redirectToNewType(selectElement) {
      const selectedType = selectElement.value;
      if (selectedType) {
        // Get the base URL (without any query string)
        const baseUrl = window.location.pathname;

        // Construct the new URL with only the selected animal type
        const newUrl = baseUrl + '?animalType=' + selectedType;

        // Redirect the user to the new URL
        window.location.href = newUrl;
      }
    }
  </script>

</body>

</html>