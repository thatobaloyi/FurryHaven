<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SPCA Pet Boarding Form</title>
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
    input[type="email"],
    input[type="tel"],
    input[type="date"],
    input[type="time"],
    input[type="number"],
    input[type="file"],
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
    /* Base button */
    button {
      border: none;
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      border-radius: 4px;
      cursor: pointer;
      transition: transform 0.12s ease, background 0.12s ease;
      font-weight: bold;
      display: inline-block;
      color: #fff;
    }

    /* Primary submit - site green */
    .btn-submit {
      background: #98b06f;
      min-width: 200px;
    }
    .btn-submit:hover { background: #86a45f; transform: translateY(-2px); }

    /* Go back - site orange */
    .btn-back {
      background: #FF8C00;
      min-width: 200px;
    }
    .btn-back:hover { background: #E67E00; transform: translateY(-2px); }

    /* Reset / subtle */
    button[type="reset"],
    .subtle {
      background-color: #ddd;
      color: #333;
      min-width: 120px;
    }
    button[type="reset"]:hover,
    .subtle:hover { background-color: #ccc; transform: translateY(-1px); }

    /* keep form-actions layout */
    .form-actions {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-top: 1em;
      align-items: center;
      flex-wrap: wrap;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(12, 1fr);
      gap: 1rem;
    }
    .col-12 { grid-column: span 12; }
    .col-6 { grid-column: span 6; }
    .col-4 { grid-column: span 4; }
    .col-3 { grid-column: span 3; }
    .col-2 { grid-column: span 2; }
    .hint {
      color: #666;
      font-size: 0.85rem;
      margin-top: 0.3rem;
    }
    .req::after {
      content: " *";
      color: #dc2626;
    }
    .inline {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    @media (max-width: 800px) {
      .col-6, .col-4, .col-3, .col-2 {
        grid-column: span 12;
      }
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
  <div class="dashboard-container">
   
    <div class="main-content" id="mainContent">
      <header>
        <h1>SPCA Pet Boarding Form</h1>
        <blockquote>"We'll care for your pet like our own while you're away."</blockquote>
      </header>
      <?php include_once __DIR__ . '/notification.php'; ?>
      <main>
        <form action="./controllers/BoardingPaymentController.php" method="POST" novalidate>
          <input type="hidden" name="action" value="process">
          <input type="hidden" name="ownerID" value="<?php echo $_SESSION['user_id']; ?>">
          <input type="hidden" name="animalID" value="<?php echo htmlspecialchars($_POST['animal_id'] ?? ''); ?>">
          <input type="hidden" id="dailyRate" name="dailyRate" value="<?php echo htmlspecialchars($_POST['daily_rate'] ?? 0); ?>">
          <input type="hidden" id="daysStayed" name="daysStayed" value="0">

          <h3 id="boarding-duration">Boarding Duration</h3>
          <section class="col-4">
            <label class="req" for="StartDate">Start Date</label>
            <input id="StartDate" name="StartDate" type="date" required>
          </section>
          <section class="col-4">
            <label class="req" for="EndDate">End Date</label>
            <input id="EndDate" name="EndDate" type="date" required>
          </section>
          <section class="col-4">
            <label>Total Amount:
              <input type="text" id="TotalAmount" name="TotalAmount" readonly>
            </label>
          </section>
          
          <h3 id="payment-details">Payment Details</h3>
          <section class="col-6">
            <label class="req" for="paymentMethod">Payment Method</label>
            <select id="paymentMethod" name="paymentMethod" required>
                <option value="card">Card</option>
                <option value="cash">Cash</option>
                <option value="online">Online</option>
            </select>
          </section>

          <section class="form-actions">
            <button type="reset" class="subtle" title="Clear all fields">Clear</button>
            <button type="submit" title="Submit boarding request" class="btn-submit">Submit Boarding Request</button>
            <button type="button" class="subtle btn-back" onclick="window.location.href='userAnimal.php'" title="Cancel and return to the users boarding">Go back</button>
          </section>
        </form>
      </main>
    </div>
  </div>

  <script>
    function calculateAmount() {
      const rateInput = document.getElementById("dailyRate");
      const startDateInput = document.getElementById("StartDate");
      const endDateInput = document.getElementById("EndDate");
      const totalAmountInput = document.getElementById("TotalAmount");
      const daysStayedInput = document.getElementById("daysStayed");

      const rate = parseFloat(rateInput.value) || 0;
      const startDate = new Date(startDateInput.value);
      const endDate = new Date(endDateInput.value);
      
      let days = 0;
      // Check if dates are valid and end date is after start date
      if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime()) && endDate > startDate) {
        const diffTime = Math.abs(endDate - startDate);
        days = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
      }

      daysStayedInput.value = days;

      const total = days * rate;
      // Format to 2 decimal places for currency
      totalAmountInput.value = `R${total.toFixed(2)}`;
    }

    // Run calculation when the page loads and when dates change
    document.addEventListener('DOMContentLoaded', function() {
        // Set minimum start date to tomorrow
        const startDateInput = document.getElementById("StartDate");
        const endDateInput = document.getElementById("EndDate");
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);
        const minStart = tomorrow.toISOString().split('T')[0];
        startDateInput.min = minStart;

        // When start date changes, set min end date to start date + 1
        startDateInput.addEventListener("change", function() {
            if (startDateInput.value) {
                const start = new Date(startDateInput.value);
                const minEnd = new Date(start);
                minEnd.setDate(start.getDate() + 1);
                endDateInput.min = minEnd.toISOString().split('T')[0];
                // If end date is before new min, clear it
                if (endDateInput.value && endDateInput.value < endDateInput.min) {
                    endDateInput.value = '';
                }
            } else {
                endDateInput.min = '';
            }
        });

        // Prevent form submission if end date is not after start date
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const start = new Date(startDateInput.value);
            const end = new Date(endDateInput.value);
            if (!startDateInput.value || !endDateInput.value || end <= start) {
                alert("End Date must be after Start Date, and Start Date must be at least tomorrow.");
                e.preventDefault();
            }
        });

        // Add these lines to recalculate amount when dates change
        startDateInput.addEventListener('change', calculateAmount);
        endDateInput.addEventListener('change', calculateAmount);

        // Set initial value
        calculateAmount();
    });
  </script>
</body>
</html>
