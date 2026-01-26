<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include_once './notification.php';
include_once './core/functions.php';
include_once './models/Animal.php';

$animal = new Animal();

$a = $animal->findOne($_POST['Animal_ID']);

if (!isLoggedIn()) {
  $_SESSION['notification'] = [
    'message' => "You have to login first before you can adopt.",
    'type' => 'error'
  ];

  redirectTo('./login.php');
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Adopt or Foster</title>
  <link rel="stylesheet" href="style2.css">
  <style>
    /* body {
      font-family: Arial, sans-serif;
      background-color: #FFF8F0;
      margin: 0 AUTO;
      padding: 0;
    } */

    input,
    select {
      width: 300px;
      padding: 5px;
      margin-top: 5px;
    }

    .hidden {
      display: none;
    }

    .container {
      max-width: 900px;
      margin: 50px auto;
      background-color: #fffdf8;
      border: 1px solid #e0d6c2;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      padding: 0 30px 30px 30px;
    }

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
      margin: 0 0 2rem 0;
    }

    .donation-section {
      text-align: center;
      margin: 25px 0 40px 0;
      font-size: 1rem;
      color: #333;
    }

    .donation-section p {
      margin-bottom: 15px;
    }

    button {
      background-color: #FF8C00;
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s;
      display: block;
      margin: 2rem auto 0;
      font-weight: bold;
      width: 200px;
      text-align: center;
    }

    .donate-button:hover {
      background-color: #e06e00;
      box-shadow: 0 6px 14px rgba(224, 110, 0, 0.6);
    }

    .tab-buttons {
      display: flex;
      border-bottom: 1px solid #e0d6c2;
    }

    .tab-buttons button {
      flex: 1;
      padding: 16px;
      border: none;
      font-size: 16px;
      cursor: pointer;
      background-color: #f0f0f0;
      transition: all 0.3s ease;
      font-weight: 600;
      color: #333;
    }

    .tab-buttons button.active {
      background-color: #0a2463;
      color: white;
    }

    .tab-buttons button:not(.active):hover {
      background-color: #fb8500;
      color: white;
    }

    .form-section {
      padding: 30px 0 0 0;
      background-color: #fffdf8;
    }

    form {
      display: none;
    }

    form.active {
      display: block;
    }

    h3 {
      margin-bottom: 20px;
      color: #0a2463;
      padding-bottom: 10px;
      position: relative;
      padding-left: 16px;
    }

    h3::before {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      width: 6px;
      height: 100%;
      background-color: #fb8500;
      border-radius: 4px 0 0 4px;
    }

    label {
      display: block;
      margin-top: 1rem;
      font-weight: bold;
      color: #003366;
    }

    input,
    textarea,
    select {
      width: 100%;
      padding: 12px;
      margin-top: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
      background-color: #fff;
      transition: all 0.2s ease;
    }

    input:focus,
    textarea:focus,
    select:focus {
      border-color: #fb8500;
      outline: none;
      box-shadow: 0 0 0 3px rgba(251, 133, 0, 0.1);
    }

    textarea {
      min-height: 100px;
    }

    .required {
      color: #d32f2f;
    }

    button {
      background-color: #FF8C00;
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s;
      display: block;
      margin: 2rem auto 0;
      font-weight: bold;
      width: 200px;
      text-align: center;
    }

    button[type='submit']:hover {
      background-color: #e06e00;
      transform: translateY(-2px);
      box-shadow: 0 6px 14px rgba(224, 110, 0, 0.6);
    }

    select {
      appearance: none;
      background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%230a2463'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 12px center;
      background-size: 16px;
    }

    .terms-row {
      display: flex;
      justify-content: flex-start;
      /* align left */
      align-items: center;
      margin-top: 1.5rem;
    }

    .terms-row label {
      display: flex;
      align-items: left;
      gap: 0.5rem;
      font-weight: bold;
      color: #003366;
      margin: 0;
    }

    .terms-row input[type="checkbox"] {
      margin: 0;
      width: auto;
    }

    .error-message {
      color: #d32f2f;
      font-size: 0.85rem;
      margin-top: 0.3rem;
      display: none;
    }

    @media (max-width: 600px) {
      .container {
        margin: 20px auto;
      }

      .tab-buttons {
        flex-direction: column;
      }
    }

    .image-wrapper {
      position: relative;
      display: flex;
      /* use flex for cleaner vertical alignment */
      flex-direction: column;
      align-items: center;
    }

    img {
      margin: 5em;
      /* fixed width for uniform look */
      
      /* fixed height for uniform look */
      object-fit: cover;
      /* crops nicely */
      border-radius: 12px;
      /* smoother corners */
      box-shadow: 0 4px 12px rgba(223, 157, 15, 0.94);
      /* modern soft shadow */
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
      /* feels clickable */
      margin-bottom: 0;
      display: block;
    }
  </style>
</head>

<body>


  <h1>Adoption/Fostering Form</h1>
  <blockquote>"Every paw deserves a loving home."</blockquote>
  <div class="container">
    <div class="image-wrapper">
      <img width="50%" src='./images/animals/<?= htmlspecialchars($a['filePath']) ?>' alt="">
    </div>
    <!-- <div class="tab-buttons">
          <button id="adoptBtn" class="active" onclick="showForm('adopt')">Adopt</button>
          <button id="fosterBtn" onclick="showForm('foster')">Foster</button>
        </div> -->

    <div class="form-section">
      <form id="adoptForm" class="active" action="./controllers/ApplicationsController.php" method="POST">
        <input type="hidden" name="action" value="submit">
        <h3>Personal Information</h3>

        <label>Do you have a South African ID? <span class="required">*</span></label>
        <select id="adoptHasSAID" onchange="toggleIDFields('adoptForm')">
          <option value=""></option>
          <option value="yes">Yes</option>
          <option value="no">No</option>
        </select>

        <div id="adoptSaIDField" class="hidden">
          <label for="IDnumber">South African ID Number <span class="required">*</span></label>
          <input type="text" id="adoptSaId" name="IDnumber" maxlength="13" placeholder="Enter 13-digit SA ID" required oninput="calculateAgeFromId('adoptSaId', 'adoptAge')">
        </div>

        <div id="adoptPassportNumber" class="hidden">
          <label for="passportNumber">Passport Number</label>
          <input type="text" id="adoptPassport" name="passportNumber" placeholder="Enter Passport Number">
        </div>
        <div>
          <label for="adoptAge">Age <span class="required">*</span></label>
          <input type="number" id="adoptAge" name="age" min="14" required>
          <div class="error-message" id="adoptAgeError">You must be at least 14 years old to apply</div>
          <p>You'll need to be at least 14 to apply (16 or 18 for some roles).</p>
        </div>
        <h3>Address Information</h3>
        <label for="adoptAddressLine1">Street Name <span class="required">*</span></label>
        <input type="text" id="adoptAddressLine1" name="addressLine1" required>

        <label for="adoptCity">City <span class="required">*</span></label>
        <input type="text" id="adoptCity" name="city" required>

        <label for="adoptProvince">Province <span class="required">*</span></label>
        <select id="adoptProvince" name="province" required>
          <option value=""></option>
          <option>Eastern Cape</option>
          <option>Free State</option>
          <option>Gauteng</option>
          <option>KwaZulu-Natal</option>
          <option>Limpopo</option>
          <option>Mpumalanga</option>
          <option>Northern Cape</option>
          <option>North West</option>
          <option>Western Cape</option>
        </select>

        <label for="adoptPostalCode">Postal Code <span class="required">*</span></label>
        <input type="text" id="adoptPostalCode" name="postalCode" maxlength="4" pattern="\d{4}" required>

        <label for="adoptCountry">Country <span class="required">*</span></label>
        <input type="text" id="adoptCountry" name="country" required>

        <h3>Housing Information</h3>
        <label for="adoptHousingType">Type of Home: <span class="required">*</span></label>
        <select id="adoptHousingType" name="housingType" required>
          <option value="">Select Housing Type</option>
          <option value="house">House</option>
          <option value="flat">Flat/Apartment</option>
          <option value="farm">Farm</option>
          <option value="other">Other</option>
        </select>

        <label for="adoptHomeOwnershipStatus">Home Ownership Status:<span class="required">*</span></label>
        <select id="adoptHomeOwnershipStatus" name="homeOwnershipStatus" required>
          <option value=""></option>
          <option value="own">Own</option>
          <option value="rent">Rent</option>
          <option value="other">Other</option>
        </select>

        <label for="adoptHasFencedYard">Do You Have a Secure Yard? <span class="required">*</span></label>
        <select id="adoptHasFencedYard" name="hasFencedYard" required>
          <option value=""></option>
          <option value="yes">Yes</option>
          <option value="no">No</option>
        </select>

        <h3>Pet Information</h3>
        <label>Do you have other pets? <span class="required">*</span></label>
        <select id="adoptHasOtherPets" name="hasOtherPets" onchange="togglePetCount('adoptForm')">
          <option value=""></option>
          <option value="1">Yes</option>
          <option value="0">No</option>
        </select>

        <div id="adoptNumberOfPets" class="hidden">
          <label for="adoptNumberOfPets">How many other pets do you have?</label>
          <input type="number" id="adoptNumberOfPets" name="numberOfPets" min="1" max="50" />
        </div>

        <label>Is anyone in your household allergic to animals? <span class="required">*</span></label>
        <select id="adoptAllergic" name="allergicHousehold" required>
          <option value=""></option>
          <option value="1">Yes</option>
          <option value="0">No</option>
        </select>

        <h3>Adoption Questions</h3>
        <label for="whyAdopt">Why Do You Want to Adopt? <span class="required">*</span></label>
        <textarea id="whyAdopt" name="whyAdopt" required></textarea>


        <div class="terms-row">
          <label>
            <input type="checkbox" id="agreeTerms" name="agreedToTerms" onchange="toggleTermsBox('adoptForm')" required />
            I agree to the <span class="highlight">Terms and Conditions</span>
          </label>
        </div>
        <div id="termsBox" class="hidden">
          <h4>Terms and Conditions</h4>
          <p>
            By submitting this application, you confirm that all information provided is accurate and complete.
            You agree to provide a safe and loving home. The organization may conduct checks and may deny the
            application if necessary.
          </p>
        </div>

        <!-- <?php var_dump($_POST) ?> -->

        <input type="hidden" name="applicationType" value="Adoption">
        <input type="hidden" name="Animal_ID" value="<?php echo $_POST["Animal_ID"] ?>">

        <button type="submit">Submit Adoption Form</button>
        <button onclick="window.location.href='adoptable.php'">Back</button>
      </form>


      <!-- 
          <form id="fosterForm" action="./controllers/ApplicationsController.php" method="POST">
            <input type="hidden" name="action" value="submit">
            <h3>Fosterer Information</h3>

            <label>Do you have a South African ID? <span class="required">*</span></label>
            <select id="fosterHasSAID" onchange="toggleIDFields('fosterForm')">
              <option value=""></option>
              <option value="yes">Yes</option>
              <option value="no">No</option>
            </select>

            <div id="fosterSaIDField" class="hidden">
              <label for="fosterSaId">South African ID Number <span class="required">*</span></label>
              <input type="text" id="fosterSaId" name="IDnumber" maxlength="13" placeholder="Enter 13-digit SA ID" required oninput="calculateAgeFromId('fosterSaId', 'fosterAge')">
              required>
            </div>

            <div id="fosterPassportNumber" class="hidden">
              <label for="fosterPassport">Passport Number<span class="required">*</span></label>
              <input type="text" id="fosterPassport" name="passportNumber" placeholder="Enter Passport Number">
            </div>
            <div>
              <label for="fosterAge">Age <span class="required">*</span></label>
              <input type="number" id="fosterAge" name="age" min="14" required>
              <div class="error-message" id="fosterAgeError">You must be at least 14 years old to apply</div>
              <p>You'll need to be at least 14 to apply (16 or 18 for some roles).</p>
            </div>
            <h3>Address Information</h3>

            <label for="fosterCity">City <span class="required">*</span></label>
            <input type="text" id="fosterCity" name="city" required>

            <label for="fosterProvince">Province <span class="required">*</span></label>
            <select id="fosterProvince" name="province" required>
              <option value=""></option>
              <option>Eastern Cape</option>
              <option>Free State</option>
              <option>Gauteng</option>
              <option>KwaZulu-Natal</option>
              <option>Limpopo</option>
              <option>Mpumalanga</option>
              <option>Northern Cape</option>
              <option>North West</option>
              <option>Western Cape</option>
            </select>

            <label for="fosterPostalCode">Postal Code <span class="required">*</span></label>
            <input type="text" id="fosterPostalCode" name="postalCode" maxlength="4" pattern="\d{4}" required>

            <label for="fosterCountry">Country <span class="required">*</span></label>
            <input type="text" id="fosterCountry" name="country" required>

            <h3>Housing Information</h3>
            <label for="fosterHousingType">Type of Home: <span class="required">*</span></label>
            <select id="fosterHousingType" name="housingType" required>
              <option value="">Select Housing Type</option>
              <option value="house">House</option>
              <option value="flat">Flat/Apartment</option>
              <option value="farm">Farm</option>
              <option value="other">Other</option>
            </select>

            <label for="fosterHomeOwnershipStatus">Home Ownership Status:<span class="required">*</span></label>
            <select id="fosterHomeOwnershipStatus" name="homeOwnershipStatus" required>
              <option value=""></option>
              <option value="own">Own</option>
              <option value="rent">Rent</option>
              <option value="other">Other</option>
            </select>

            <label for="fosterHasFencedYard">Do You Have a Secure Yard? <span class="required">*</span></label>
            <select id="fosterHasFencedYard" name="hasFencedYard" required>
              <option value=""></option>
              <option value="yes">Yes</option>
              <option value="no">No</option>
            </select>

            <h3>Pet Information</h3>
            <label>Do you have other pets? <span class="required">*</span></label>
            <select id="fosterHasOtherPets" name="hasOtherPets" onchange="togglePetCount('fosterForm')">
              <option value=""></option>
              <option value="1">Yes</option>
              <option value="0">No</option>
            </select>

            <div id="fosterNumberOfPets" class="hidden">
              <label for="fosterNumberOfPets">How many other pets do you have?<span class="required">*</span></label>
              <input type="number" id="fosterNumberOfPets" name="numberOfPets" min="1" max="50" />
            </div>

            <label>Is anyone in your household allergic to animals? <span class="required">*</span></label>
            <select id="fosterAllergic" name="allergicHousehold" required>
              <option value=""></option>
              <option value="1">Yes</option>
              <option value="0">No</option>
            </select>

            <h3>Foster Questions</h3>
            <label for="whyFoster">Why Do You Want to Adopt? <span class="required">*</span></label>
            <textarea id="whyFoster" name="whyFoster" required></textarea>

            <h3>Foster Details</h3>
            <label for="fosterDuration">How long will you foster</label>
            <select id="fosterDuration" name="fosterDuration" required>
              <option value="">--Select--</option>
              <option value="1-2 weeks">1-2 weeks</option>
              <option value="3-4 weeks">1-2 months </option>
              <option value="5-6 weeks">3+ months</option>
            </select>

            <label for="homeInspected">Are You Open to home inspections? <span class="required">*</span></label>
            <select id="homeInspected" name="permissionFromLandlord" required>
              <option value=""></option>
              <option value="yes">Yes</option>
              <option value="no">No</option>
            </select>
            <div id="fosterTermsBox" class="hidden">
              <h4>Terms and Conditions</h4>
              <p>
                By submitting this application, you confirm that all information provided is accurate and complete.
                You agree to provide a safe and loving home. The organization may conduct checks and may deny the
                application if necessary.
              </p>
            </div>

            <input type="hidden" name="applicationType" value="Foster">
            <input type="hidden" name="Animal_ID" value="<?php echo $_POST["Animal_ID"] ?>">

            <button type="submit">Submit Foster Form</button>
          </form> -->
    </div>
  </div>
  </div>

  </div>


  <script src="./sidebar2.js"></script>
  <script>
    function togglePetCount(formId) {
      const form = document.getElementById(formId);
      const select = form.querySelector('select[name="hasOtherPets"]');
      const countDiv = form.querySelector('div[id$="NumberOfPets"]');
      if (select.value === "1") {
        countDiv.classList.remove("hidden");
      } else {
        countDiv.classList.add("hidden");
      }
    }

    function toggleIDFields(formId) {
      const form = document.getElementById(formId);
      const select = form.querySelector('select[id$="HasSAID"]');
      const saIDDiv = form.querySelector('div[id$="SaIDField"]');
      const passportDiv = form.querySelector('div[id$="PassportNumber"]');

      if (select.value === "yes") {
        saIDDiv.classList.remove("hidden");
        passportDiv.classList.add("hidden");
      } else if (select.value === "no") {
        passportDiv.classList.remove("hidden");
        saIDDiv.classList.add("hidden");
      } else {
        saIDDiv.classList.add("hidden");
        passportDiv.classList.add("hidden");
      }
    }

    // New function to handle terms box for both forms
    function toggleTermsBox(formId) {
      const form = document.getElementById(formId);
      const checkbox = form.querySelector('input[name="agreedToTerms"]');
      const termsBox = form.querySelector('div[id$="termsBox"]');

      if (checkbox.checked) {
        termsBox.classList.remove("hidden");
      } else {
        termsBox.classList.add("hidden");
      }
    }

    document.addEventListener("DOMContentLoaded", () => {
      const adoptAgeField = document.getElementById("adoptAge");
      const fosterAgeField = document.getElementById("fosterAge");

      adoptAgeField.addEventListener("blur", function() {
        if (this.value && this.value < 14) {
          document.getElementById("adoptAgeError").style.display = "block";
          this.value = "";
        } else {
          document.getElementById("adoptAgeError").style.display = "none";
        }
      });

      fosterAgeField.addEventListener("blur", function() {
        if (this.value && this.value < 14) {
          document.getElementById("fosterAgeError").style.display = "block";
          this.value = "";
        } else {
          document.getElementById("fosterAgeError").style.display = "none";
        }
      });
    });

    function showForm(type) {
      const adoptForm = document.getElementById('adoptForm');
      const fosterForm = document.getElementById('fosterForm');
      const adoptBtn = document.getElementById('adoptBtn');
      const fosterBtn = document.getElementById('fosterBtn');

      if (type === 'adopt') {
        adoptForm.classList.add('active');
        fosterForm.classList.remove('active');
        adoptBtn.classList.add('active');
        fosterBtn.classList.remove('active');
      } else {
        fosterForm.classList.add('active');
        adoptForm.classList.remove('active');
        fosterBtn.classList.add('active');
        adoptBtn.classList.remove('active');
      }
    }
    // Function to calculate age from South African ID number
    function calculateAgeFromId(idInputId, ageInputId) {
      const idNumber = document.getElementById(idInputId).value;
      const ageField = document.getElementById(ageInputId);

      if (idNumber.length === 13 && /^\d{13}$/.test(idNumber)) {
        const year = parseInt(idNumber.substring(0, 2), 10);
        const month = parseInt(idNumber.substring(2, 4), 10);
        const day = parseInt(idNumber.substring(4, 6), 10);

        const currentYear = new Date().getFullYear();
        const fullYear = (year < (currentYear % 100)) ? 2000 + year : 1900 + year;

        const birthDate = new Date(fullYear, month - 1, day);
        const today = new Date();

        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDifference = today.getMonth() - birthDate.getMonth();
        const dayDifference = today.getDate() - birthDate.getDate();

        if (monthDifference < 0 || (monthDifference === 0 && dayDifference < 0)) {
          age--;
        }
        ageField.value = age;
      } else {
        ageField.value = "";
      }
    }
  </script>

</body>

</html>