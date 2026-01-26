<?php if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include_once './notification.php';
require_once './models/Campaign.php';
$campaign = new Campaign();
$campaigns = $campaign->findAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Donation Page</title>
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
      margin-bottom: 1.5rem;
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
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    h3 {
      color: #003366;
      margin-top: 2rem;
      border-left: 4px solid #FF8C00;
      padding-left:
        0.5rem;
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
    input[type="number"],
    select {
      width: 100%;
      padding: 0.5rem;
      margin-top: 0.3rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-sizing: border-box;
      font-size: 1rem;
    }

    input:focus,
    select:focus {
      border-color: #FF8C00;
      outline:
        none;
      box-shadow: 0 0 0 2px rgba(255, 140, 0, 0.2);
    }

    input[type="checkbox"],
    input[type="radio"] {
      margin-right: 6px;
      cursor: pointer;
    }

    .amount-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      margin-top: 0.5rem;
    }

    .amount-buttons button {
      background: white;
      border: 2px solid #003366;
      color: #003366;
      padding: 0.5rem 1rem;
      font-weight: bold;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease, color 0.3s ease;
      flex: 1 1 100px;
      font-size: 1rem;
    }

    .amount-buttons button.active,
    .amount-buttons button:hover {
      background-color: #FF8C00;
      color: white;
      border-color: #FF8C00;
    }

    .pledge-details {
      margin-top: 1rem;
      padding-left: 1rem;
      border-left: 4px solid #FF8C00;
      display: none;
    }

    .checkbox-inline label {
      font-weight: normal;
      cursor: pointer;
      margin-top: 1rem;
      display: inline-block;
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

      .amount-buttons button {
        flex: 1 1 45%;
      }
    }
  </style>

</head>

<body>
  <h1>Donation Form</h1>
  <blockquote>"Your generosity changes lives."</blockquote>
  <form action="./controllers/DonationsController.php" method="POST">
    <input type="hidden" name="action" value="donate">
    <h3>Your Information</h3>
    <input type="text" name="firstName" placeholder="First Name" />
    <input type="text" name="lastName" placeholder="Last Name" />
    <input type="text" name="preferredName" placeholder="Preferred Name" />
    <input type="email" name="email" placeholder="Email" />
    <input type="tel" name="phone" placeholder="Phone Number (Optional)" />
    <h3>Choose Your Impact</h3>
    <div class="amount-buttons" role="group" aria-label="Donation Amounts">
      <button type="button" data-amount="25">R25</button>
      <button type="button" data-amount="50">R50</button>
      <button type="button" data-amount="100">R100</button>
      <button type="button" data-amount="250">R250</button>
      <button type="button" data-amount="500">R500</button>
      <button type="button" data-amount="other">Other</button>
    </div>
    <input type="hidden" id="selectedAmount" name="selectedAmount" />
    <label for="customAmount">Enter custom amount (e.g. R150)</label>
    <input type="number" id="customAmount" name="customAmount" placeholder="Enter custom amount" disabled />
    <label for="campaign">Campaign</label>
    <select id="campaign" name="campaignId">
      <option value="">Select Campaign</option>
      <?php foreach ($campaigns as $campaign) {
        echo "<option value='$campaign[CampaignID]'>$campaign[CampaignName]</option>";
      } ?>
    </select>
    <label for="Donation Type">Donation Type</label>
    <select id="Donation Type" name="donationType">
      <option value="">--Select--</option>
      <option value="Monetary">Monetary</option>
      <option value="Pet Supplies">Pet supplies</option>
      <option value="Medical Supplies">Medical supplies</option><br><br>
      <div class="checkbox-inline">
        <input type="checkbox" id="isPledge" name="isPledge" /> This is a pledge
      </div>
      <div class="pledge-details" id="pledgeDetails">
        <label for="expectedDate">Expected Payment Date</label>
        <input type="date" id="expectedDate" name="expectedDate" />
        <label for="paymentPlan">Payment Plan</label>
        <select id="paymentPlan" name="paymentPlan">
          <option value="">Select Plan</option>
          <option value="weekly">Weekly</option>
          <option value="monthly">Monthly</option>
          <option value="quarterly">Quarterly</option>
        </select>
      </div>
      <h3>Payment Information</h3>
      <label><input type="radio" name="payOption" value="Card" checked /> Debit or Credit Card</label>
      <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.5rem;">
        <input type="text" name="cardFirstName" placeholder="First Name" style="flex: 1" />
        <input type="text" name="cardLastName" placeholder="Last Name" style="flex: 1" />
      </div>
      <input type="text" name="cardNumber" placeholder="xxxx xxxx xxxx xxxx" />
      <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.5rem;">
        <input type="text" name="expiry" placeholder="Expiry Date (MM/YY)" style="flex: 1" />
        <input type="text" name="cvv" placeholder="CVV" style="flex: 1" />
      </div>
      <div class="checkbox-inline" style="margin-top: 1rem;"> <label>
          <input type="radio" name="payOption" value="Paypal" /> PayPal</label>
        <label><input type="radio" name="payOption" value="EFT" /> EFT</label>
        <label><input type="radio" name="payOption" value="Google Pay" /> Google Pay</label>
      </div>
      <div class="checkbox-inline" style="margin-top: 1rem;"> <label>
          <input type="checkbox" name="recurring" /> Make this a recurring donation</label>
      </div> <button type="submit">Submit Donation</button>
  </form>

  <script>
    const amountButtons = document.querySelectorAll('.amount-buttons button');
    const customAmount = document.getElementById('customAmount');
    const selectedAmount = document.getElementById('selectedAmount');
    const isPledge = document.getElementById('isPledge');
    const pledgeDetails = document.getElementById('pledgeDetails');
    amountButtons.forEach(button => {
      button.addEventListener('click', () => {
        amountButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
        if (button.dataset.amount === 'other') {
          customAmount.disabled = false;
          customAmount.focus();
          selectedAmount.value = '';
        } else {
          customAmount.value = '';
          customAmount.disabled = true;
          selectedAmount.value = button.dataset.amount;
        }
      });
    });
    const phoneField = document.getElementById('phone');
    const phoneRegex = /^\d{10}$/;
    // only 10 digits customAmount.addEventListener('input', () => { if (customAmount.value.trim() !== '') { amountButtons.forEach(btn => btn.classList.remove('active'));
    //  selectedAmount.value = ''; 
    // } }); 
    // isPledge.addEventListener('change', () => { pledgeDetails.style.display = isPledge.checked ? 'block' : 'none'; });
  </script>
</body>

</html>