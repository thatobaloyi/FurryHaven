<?php  if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once './notification.php'

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Community Volunteer Opportunities</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #FFF8F0;
      color: #333;
      margin: 0 auto;
      padding: 2rem;
      line-height: 1.6;
      max-width: 800px;
      position: relative;
    }
.opportunities {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
  margin: 2rem 0;
}

.opportunities article {
  background: linear-gradient(145deg, #ffffff, #f9f9f9);
  border-radius: 16px;
  padding: 1.8rem;
  box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
  border-top: 5px solid;
  border-image: linear-gradient(to right, var(--primary-color), var(--secondary-color)) 1;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  position: relative;
  overflow: hidden;
}

.opportunities article::before {
  content: "";
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at top right, rgba(0,0,0,0.05), transparent 60%);
  z-index: 0;
}

.opportunities article:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);
}

.opportunities h3 {
  margin-top: 0;
  font-size: 1.3rem;
  color: var(--primary-color);
  position: relative;
  z-index: 1;
}

.opportunities p {
  margin: 0.5rem 0;
  color: var(--text-color);
  position: relative;
  z-index: 1;
}

.opportunities article:hover h3 {
  color: var(--secondary-color);
  transition: color 0.3s ease;
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
      margin: 1.5rem 0;
    }

    :root {
      --primary-color: #003366;
      --secondary-color: #FF8C00;
      --background-color: #f8f4e9;
      --text-color: #333333;
      --light-text: #f8f8f8;
    }

    section {
      margin-bottom: 2rem;
      color: #003366;
    }

    .opportunities {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 2rem;
      margin-bottom: 3rem;
    }

    form {
      background-color: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      position: relative;
    }

    .file-upload {
      background-color: #f8f9fa;
      padding: 15px;
      border-radius: 5px;
      border: 1px dashed #ccc;
    }

    .avil {
      margin-right: 15px;
    }

    h3 {
      color: #003366;
      margin-top: 2rem;
      border-left: 4px solid #FF8C00;
      padding-left: 0.5rem;
    }

    label {
      display: block;
      margin-top: 1rem;
      font-weight: bold;
      color: #003366;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="date"],
    select,
    textarea {
      width: 100%;
      padding: 0.5rem;
      margin-top: 0.3rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-sizing: border-box;
    }

    textarea {
      min-height: 80px;
      resize: vertical;
    }

    input:focus,
    select:focus,
    textarea:focus {
      border-color: #FF8C00;
      outline: none;
      box-shadow: 0 0 0 2px rgba(255, 140, 0, 0.2);
    }

    input[type="checkbox"],
    input[type="radio"] {
      margin-right: 6px;
    }

    .radio-group {
      margin-top: 0.5rem;
    }

    .radio-group label {
      font-weight: normal;
      margin-right: 1.5rem;
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

    button[type="submit"]:hover {
      background-color: #E67E00;
    }

    @media (max-width: 600px) {
      body {
        padding: 1rem;
      }

      form {
        padding: 1.5rem;
      }
    }

       .error-message {
      color: #d32f2f;
      font-size: 0.85rem;
      margin-top: 0.3rem;
      display: none;
    }
.required{
      color:#d32f2f;
    }
 #age:invalid {
  border-color: #d9534f;
}
  </style>
</head>

<body>
`
  <!-- <?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?> -->

  <h1>Volunteering Form</h1>
  <blockquote>"Help animals in need — every hand makes a difference."</blockquote>

  <form id="volunteerForm" action="./controllers/ApplicationsController.php" method="POST" enctype="multipart/form-data">

<h3>Personal Information</h3>
    
    <label for="address">Street Address:<span class="required">*</span></label>
    <input type="text" id="address" name="address" required>

<!-- 
    <label for="date">Date: <span class="required">*</span></label>
    <input type="date" id="date" name="applicationDate" required> -->


    <label for="age">Age <span class="required">*</span></label>
    <input type="number" id="age" name="age" min="14" required>
    <div class="error-message" id="age-error">You must be at least 14 years old to apply</div>
    <p>You'll need to be at least 14 to apply (16 or 18 for some roles).</p>
    
    <h3>Skills & Motivation</h3>

    <label for="skills">What skills can you bring to our organisation? <span class="required">*</span></label>
    <textarea id="skills" name="applicantSkills" placeholder="Briefly tell us your experience with pets..." required></textarea>
    

    <label for="motivation">Why do you want to volunteer with us? <span class="required">*</span></label>
    <textarea id="motivation" name="whyVolunteering" required></textarea>
    
    <section>
      <h4>Do you have any criminal convictions? <span class="required">*</span></h4>
      <p>
        <label>
          <input type="radio" name="conviction" value="yes" required> Yes
        </label>
        <label>
          <input type="radio" name="conviction" value="no"> No
        </label>
      </p>
      <div >Please select an option</div>
    </section>
    
    <div class="form-group">
      <label for="criminalConviction">If yes, Upload Signed Criminal conviction form</label>
      <input type="file" id="criminalConviction" name="criminalConviction" accept=".pdf,.jpg,.jpeg,.png" class="file-upload">
    </div>
    
    <div class="form-group">
      <label for="affidavit">If No, Upload Signed Affidavit form <span class="required">*</span></label>
      <input type="file" id="affidavit" name="criminalConvictionAffidavit" accept=".pdf,.jpg,.jpeg,.png" class="file-upload">
      <div class="file-hint">Find document at the information about volunteering page</div>
    </div>

    <h3>Required Information</h3>
    <section>
      <div class="form-group">
        <label for="indemnity" class="required">Upload Your Certified ID <span class="required">*</span></label>
        <input type="file" id="indemnity" name="certifiedID" accept=".pdf,.jpg,.jpeg,.png" required class="file-upload">
        <!-- <div >Please upload the indemnity form</div> -->
      </div>
      <div class="form-group">
        <label for="indemnity" class="required">Upload Signed Indemnity Form <span class="required">*</span></label>
        <input type="file" id="indemnity" name="indemnityForm" accept=".pdf,.jpg,.jpeg,.png" required class="file-upload">
        <!-- <div >Please upload the indemnity form</div> -->
      </div>

      <div class="form-group">
        <label for="authority" class="required">Upload Authority to Search Form <span class="required">*</span></label>
        <input type="file" id="authority" name="authorityTosearchForm" accept=".pdf,.jpg,.jpeg,.png" required class="file-upload">
        <div class="error-message" id="authority-error">Please upload the authority to search form</div>
      </div>
    </section>
    <br>

    <input type="hidden" name="applicationType" value="volunteering">

    <input type="hidden" name="action" value="submit">

    <button type="submit">Submit Application</button>
    <button onclick="window.location.href='volunteerpage.php'">Back</button>
  </form>



<script>
  const form = document.getElementById('volunteerForm');
  const ageField = document.getElementById('age');

  // Validate age only
  function validateForm() {
    let isValid = true;

    if (ageField.value && ageField.value < 14) {
      showError('age-error');
      isValid = false;
    } else {
      hideError('age-error');
    }

    return isValid;
  }

  // Show error message
  function showError(errorId) {
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
      errorElement.style.display = 'block';
    }
  }

  // Hide error message
  function hideError(errorId) {
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
      errorElement.style.display = 'none';
    }
  }

  // Run validation before submit
  form.addEventListener('submit', function (e) {
    if (!validateForm()) {
      e.preventDefault(); // stop form submission
    }
  });

  // Validate on blur (when leaving the field)
  ageField.addEventListener('blur', function () {
    if (this.value && this.value < 14) {
      showError('age-error');
    } else {
      hideError('age-error');
    }
  });
</script>

</body>

</html>