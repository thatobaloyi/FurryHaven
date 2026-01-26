<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Animal Registration Form</title>
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
      margin: 1.5rem 0;
    }

    form {
      background-color: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
  </style>
</head>
<body>
  <h1>Animal Registration Form</h1>
  <blockquote>"Record every rescue — every life matters."</blockquote>

  <form action="process-registration" method="POST">

    <h3>Basic Animal Information</h3>

    <label for="animalName">Animal Name</label>
    <input type="text" id="animalName" name="animalName" required>

    <label for="animalType">Animal Type</label>
    <select id="animalType" name="animalType" required>
      <option value="" disabled selected>-- Select Type --</option>
      <option>Dog</option>
      <option>Cat</option>
      <option>Bird</option>
      <option>Livestock</option>
      <option>Other</option>
    </select>

    <label for="age">Animal Age</label>
    <input type="text" id="age" name="age" required>

    <label for="gender">Gender</label>
    <select id="gender" name="gender" required>
      <option value="" disabled selected>-- Select Gender --</option>
      <option>Male</option>
      <option>Female</option>
      <option>Unknown</option>
    </select>

    <label for="breed">Breed</label>
    <input type="text" id="breed" name="breed" required>

    <h3>Rescue & Intake Information</h3>

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

    <label for="intakeDate">Intake Date</label>
    <input type="date" id="intakeDate" name="intakeDate" required>

    <h3>Health & Vaccination Status</h3>

    <label for="healthStatus">Health Status</label>
    <textarea id="healthStatus" name="healthStatus" placeholder="e.g. Injured, healthy, under treatment..." required></textarea>

    <label for="vaccinationStatus">Vaccination Status</label>
    <select id="vaccinationStatus" name="vaccinationStatus" required>
      <option value="" disabled selected>-- Select Status --</option>
      <option>Vaccinated</option>
      <option>Not Vaccinated</option>
      <option>Partially Vaccinated</option>
    </select>

    <h3>Shelter Details</h3>

    <label for="cageId">Cage ID / Enclosure Number</label>
    <input type="text" id="cageId" name="cageId" required>

    <h3>Registered By</h3>

    <label for="registeredBy">Staff Name or ID</label>
    <input type="text" id="registeredBy" name="registeredBy" required>

    <button type="submit">Register Animal</button>
  </form> 
</body>
</html>
