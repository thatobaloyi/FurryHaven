<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once './notification.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Animal Cruelty Report Form</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #FFF8F0; 
      color: #333;
      margin: 0 auto;
      padding: 2rem;
      line-height: 1.6;
      max-width: 800px;
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

    form {
      background-color: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

    .error-message {
      color: #d32f2f;
      font-size: 0.85rem;
      margin-top: 0.3rem;
      display: none;
    }

    @media (max-width: 600px) {
      body {
        padding: 1rem;
      }

      form {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <h1>Animal Cruelty Report Form</h1>
  <blockquote>"Report abuse — protect the voiceless."</blockquote>

  <form action="./controllers/CrueltyReportController.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="create">
    <label>
      <input type="checkbox" id="anonymousCheckbox" name="anonymous" />
      Submit Anonymously
    </label>

    <div id="yourInfoContainer">
      <h3>Your Information</h3>

      <label for="firstname">First Name:</label>
      <input type="text" id="firstname" name="firstname"  />

      <label for="lastname">Last Name:</label>
      <input type="text" id="lastname" name="lastname"  />

      <label for="email">Your Email:</label>
      <input type="email" id="email" name="email"  />

      <label for="phone">Phone Number:</label>
      <input type="tel" id="phone" name="phone" placeholder="027 123 456 7890"  maxlength="10" pattern="[0-9]{10}" />
      <div id="phoneError" class="error-message">Please enter a valid 10-digit phone number</div> 

      <p style="margin-top:1rem;">Can your contact information be shared with local authorities?</p>
      <div class="radio-group">
        <label><input type="radio" name="shared" value="yes" required /> Yes</label>
        <label><input type="radio" name="shared" value="no" /> No</label>
      </div>
    </div>

    <h3>Incident Information</h3>

    <label for="street_address">Street Address:</label>
    <input type="text" id="street_address" name="street_address"  />

    <label for="city">City/Town:</label>
    <input type="text" id="city" name="city"  />

    <label for="province">Province:</label>
    <input type="text" id="province" name="province"  />

    <label for="postalCode">Postal Code:</label>
    <input type="text" id="postalCode" name="postalCode"  />

    <label for="date">Date of Incident:</label>
    <input type="date" id="date" name="date" required />

     <label for="animal_type">Type of Animal:</label>
    <select id="animal_type" name="animal_type" required>
      <option value=""></option>
      <option value="dog">Dog</option>
      <option value="cat">Cat</option>
      <option value="bird">Bird</option>
      <option value="livestock">Livestock</option>
    </select>
    
    <label for="incident_type">Type of Incident:</label>
    <select id="incident_type" name="incident_type" required>
      <option value=""></option>
      <option value="Physical Abuse">Physical Abuse</option>
      <option value="Neglect">Neglect</option>
      <option value="Emotional Abuse">Emotional Abuse</option>
      <option value="Animal Fighting">Animal Fighting</option>
      <option value="Animal Sexual Abuse">Animal Sexual Abuse</option>
    </select>

   

    <label for="description">Description of Incident:</label>
    <textarea id="description" name="description" rows="5" required placeholder="Describe what you witnessed..."></textarea>

    <label for="evidence">Upload Photo (optional):</label><br>
    <input type="file" id="evidence" name="evidence" />

    <label>
      <input type="checkbox" id="consent" name="consent" required />
      I confirm that the information provided is accurate.
    </label>

    <button type="submit">Submit Report</button>
    <button onclick="window.location.href='cruelty.html'">Back </button>
  </form>

  <script>
    const anonymousCheckbox = document.getElementById('anonymousCheckbox');
    const yourInfoContainer = document.getElementById('yourInfoContainer');

    anonymousCheckbox.addEventListener('change', () => {
      if (anonymousCheckbox.checked) {
        yourInfoContainer.style.display = 'none';
        yourInfoContainer.querySelectorAll('input').forEach(input => input.required = false);
      } else {
        yourInfoContainer.style.display = 'block';
        yourInfoContainer.querySelectorAll('input').forEach(input => {
          if (
            input.id === 'firstname' ||
            input.id === 'lastname' ||
            input.id === 'email'
          ) {
            input.required = true;
          }
        });
      }
    });
  </script>
</body>
</html>
