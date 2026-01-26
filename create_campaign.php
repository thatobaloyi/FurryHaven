<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Campaign</title>
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

        form {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
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
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.3rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #FF8C00;
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 140, 0, 0.2);
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
    <h1>Create Campaign</h1>
    <blockquote>"Make a difference in your community with a well-planned campaign."</blockquote>

    <form id="campaignForm" action="./controllers/CampaignController.php" method="POST">
    <input type="hidden" name="action" value="create">
        <h3>Campaign Details</h3>
        
        <label for="campaign_name">Campaign Name: <span class="required">*</span></label>
        <input type="text" name="campaign_name" id="campaign_name" required>
            
        </select>
        <label for="campaign_description">Campaign Description: <span class="required">*</span></label>
         <textarea id="campaign_description" name="campaign_description" required></textarea><br>

        <label for="campaign_start_date">Start Date: <span class="required">*</span></label>
        <input type="date" id="campaign_start_date" name="campaign_start_date" required>
        
        <label for="campaign_end_date">End Date: <span class="required">*</span></label>
        <input type="date" id="campaign_end_date" name="campaign_end_date" required>
        <div class="error-message" id="date-error">End date must be after start date</div>
        
        <label for="target_amount">Target Amount (ZAR): <span class="required">*</span></label>
        <input type="number" id="target_amount" name="target_amount" step="0.01" min="0" required>
        
        <button type="submit">Create Campaign</button>
         <button onclick="window.location.href='campaigns.php'">Back </button>
    </form>

    <script>
        const form = document.getElementById('campaignForm');
        const startDateField = document.getElementById('campaign_start_date');
        const endDateField = document.getElementById('campaign_end_date');
        const dateError = document.getElementById('date-error');

        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        startDateField.min = today;
        endDateField.min = today;

        // Validate dates
        function validateDates() {
            if (startDateField.value && endDateField.value) {
                const startDate = new Date(startDateField.value);
                const endDate = new Date(endDateField.value);
                
                if (endDate <= startDate) {
                    dateError.style.display = 'block';
                    return false;
                } else {
                    dateError.style.display = 'none';
                    return true;
                }
            }
            return true;
        }

        // Validate form before submission
        form.addEventListener('submit', function(e) {
            if (!validateDates()) {
                e.preventDefault();
            }
        });

        // Validate dates when they change
        startDateField.addEventListener('change', validateDates);
        endDateField.addEventListener('change', validateDates);
    </script>
</body>
</html>