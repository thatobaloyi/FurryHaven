<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Medical Record</title>
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
        background-color: #f7f8f2;
        margin: 0;
        padding: 2rem;
    }

    .form-wrapper {
        max-width: 600px;
        margin: 0 auto;
        background: #fff;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    label {
        display: block;
        margin-top: 1rem;
        font-weight: bold;
        color: #003366;
    }

    input, select, textarea {
        width: 100%;
        padding: 10px;
        margin-top: 6px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    textarea {
        resize: vertical;
        min-height: 80px;
    }

    button {
        width: 100%;
        padding: 12px;
        margin-top: 20px;
        background-color: #FF8C00;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        color: white;
        cursor: pointer;
    }

    button:hover {
        background-color: #e67800;
    }

    .back-link {
        display: block;
        text-align: center;
        margin-top: 15px;
        text-decoration: none;
        color: #003366;
        font-weight: bold;
    }
  </style>
</head>
<body>

  <div class="form-wrapper">
    <h1>Add New Medical Record</h1>
    <blockquote>Please fill in the details of the medical procedure below.</blockquote>

    <form action="./controllers/MedicalProcedureController.php" method="POST">
      <input type="hidden" name="action" value="add">
      <input type="hidden" name="Animal_ID" id="animalID" value="<?php echo htmlspecialchars($_GET['Animal_ID'] ?? ''); ?>">

      <label for="procedureType">Procedure Type</label>
      <select name="procedureType" id="procedureType" required>
        <option value="">-- Select --</option>
        <option value="Vaccination">Vaccination</option>
        <option value="Surgery">Surgery</option>
        <option value="Dental">Dental</option>
        <option value="Sterilisation">Sterilisation</option>
        <option value="Check-up">Check-up</option>
      </select>

      <label for="procedureOutcome">Procedure Outcome</label>
      <select name="procedureOutcome" id="procedureOutcome" required>
        <option value="">-- Select --</option>
        <option value="Successful">Successful</option>
        <option value="Ongoing">Ongoing</option>
        <option value="Failed">Failed</option>
        <option value="Follow-up Required">Follow-up Required</option>
      </select>

      <label for="procedureDate">Procedure Date</label>
      <input type="datetime-local" name="procedureDate" id="procedureDate" required>

      <label for="details">Details</label>
      <textarea name="details" id="details"></textarea>

      <button type="submit">Add Record</button>
    </form>

    <a href="vet_history.php" class="back-link">← Back to Medical History</a>
  </div>

</body>
</html>
