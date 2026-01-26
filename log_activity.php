<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once './models/VolunteerActivity.php';
include_once './core/functions.php';
include_once './notification.php';

if (isset($_POST) && isset($_POST['submit'])) {
    $volunteerActivity = new VolunteerActivity();
    $volunteerActivity->setActivityID($volunteerActivity->generateID());
    $volunteerActivity->setVolunteerID($_SESSION['username']);
    $volunteerActivity->setAnimalID($_POST["Animal_ID"]);
    $volunteerActivity->setActivityType($_POST["activityname"]);
    $volunteerActivity->setDate($_POST['Date']);
    $volunteerActivity->setDuration($_POST['Duration']);
    $volunteerActivity->setAssignedBy(null);
    $volunteerActivity->setIsDeleted("0");

    try {
        if (!$volunteerActivity->create()) {
            throw new Exception("Cannot Create!");
        } else {
            $_SESSION['notification'] = [
                'message' => "Activity Succesfully Logged!",
                'type' => 'success'
            ];
            redirectTo('./volunteeractivity.php');
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an Activity</title>
    <link rel="stylesheet" href="style2.css">
    <style>
        /* body {
            font-family: Arial, sans-serif;
            background-color: #FFF8F0;
            color: #333;
            margin: 0 auto;
            padding: 2rem;
            line-height: 1.6;
            max-width: 800px;
            position: relative;
        } */

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

    <div class="dashboard-container">
        <?php include_once "./sidebar2.php" ?>
        <div class="main-content" id="mainContent">

            <h1>Create an Activity</h1>
            <blockquote>"Make a difference in your community by participating in an activity."</blockquote>

            <form id="activityForm" action="log_activity.php" method="POST">
                <h3>Activity Details</h3>
                <input type="hidden" name="Animal_ID" value=<?php echo $_POST['Animal_ID'] ?>>
                <label for="activityname">Activity Name: <span class="required">*</span></label>
                <select name="activityname" id="activityname" required>
                    <option value="">Select a Activity...</option>
                    <option value="Feeding">Feeding</option>
                    <option value="Cleaning">Cleaning</option>
                    <option value="Walking">Walking</option>
                    <option value="Grooming">Grooming</option>
                </select>
                <label for="Date"> Date: <span class="required">*</span></label>
                <input type="date" id="activityDate" name="Date" required>

                <label for="startTime">Start Time: <span class="required">*</span></label>
                <input type="time" id="startTime" name="startTime" required>

                <label for="Endtime">End Time: <span class="required">*</span></label>
                <input type="time" id="Endtime" name="Endtime" required>

                <label for="duration">Duration:</label>
                <input type="text" id="Duration" name="Duration" readonly class="bg-gray-100 cursor-not-allowed">

                <div id="validationMessage" class="message"></div>

                <button type="submit" name="submit">Create Activity</button>
            </form>
        </div>
    </div>

    <script src="./sidebar2.js"></script>
    <script>
        const form = document.getElementById('activityForm');
        const activityDate = document.getElementById('activityDate');
        const startTime = document.getElementById('startTime');
        const endTime = document.getElementById('Endtime');
        const durationInput = document.getElementById('Duration');
        const validationMessage = document.getElementById('validationMessage');

        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        activityDate.min = today;

        // Function to update the minimum time for the time pickers
        function updateMinTime() {
            const now = new Date();
            const currentHour = now.getHours().toString().padStart(2, '0');
            const currentMinute = now.getMinutes().toString().padStart(2, '0');
            const currentTime = `${currentHour}:${currentMinute}`;

            if (activityDate.value === today) {
                startTime.min = currentTime;
                endTime.min = currentTime;
            } else {
                startTime.min = '';
                endTime.min = '';
            }
        }

        // Function to display validation messages
        function showMessage(message, isError) {
            validationMessage.textContent = message;
            validationMessage.classList.add('show');
            if (isError) {
                validationMessage.classList.add('error');
                validationMessage.classList.remove('success');
            } else {
                validationMessage.classList.add('success');
                validationMessage.classList.remove('error');
            }
        }
        // Calculate and show duration as HH:MM:SS (handles HH:MM or HH:MM:SS input)
        // If end <= start we assume the end is on the next day (cross-midnight support).
        function calculateDuration() {
            const start = startTime.value;
            const end = endTime.value;
            durationInput.value = '';

            if (!start || !end) return;

            // parse "HH:MM" or "HH:MM:SS" (returns total seconds)
            const toSeconds = (timeStr) => {
                const parts = timeStr.split(':').map(Number);
                let h = 0,
                    m = 0,
                    s = 0;
                if (parts.length === 1) h = parts[0];
                else if (parts.length === 2)[h, m] = parts;
                else [h, m, s] = parts;
                return h * 3600 + m * 60 + s;
            };

            let startSeconds = toSeconds(start);
            let endSeconds = toSeconds(end);

            // If end is not after start, assume it's the next day (cross-midnight).
            if (endSeconds <= startSeconds) {
                endSeconds += 24 * 3600;
            }

            const diffSeconds = endSeconds - startSeconds;
            if (diffSeconds <= 0) {
                showMessage('End time must be after start time.', true);
                return;
            }

            const hours = Math.floor(diffSeconds / 3600);
            const minutes = Math.floor((diffSeconds % 3600) / 60);
            const seconds = diffSeconds % 60;

            // Format as HH:MM:SS
            const hh = String(hours).padStart(2, '0');
            const mm = String(minutes).padStart(2, '0');
            const ss = String(seconds).padStart(2, '0');

            durationInput.value = `${hh}:${mm}:${ss}`;
            showMessage('Duration calculated successfully!', false);
        }


        // Main validation function called on form submission
        function validateTime() {
            const dateValue = activityDate.value;
            const startTimeValue = startTime.value;
            const endTimeValue = endTime.value;

            if (!dateValue || !startTimeValue || !endTimeValue) {
                showMessage('Please fill out all fields.', true);
                return false;
            }

            // Remove this check:
            // if (totalEndMinutes <= totalStartMinutes) {
            //     showMessage('End time must be after start time.', true);
            //     return false;
            // }

            // Instead, just check that duration is not empty and not zero
            if (!durationInput.value || durationInput.value === "00:00:00") {
                showMessage('Duration must be greater than zero.', true);
                return false;
            }

            showMessage('Form is valid and ready to submit.', false);
            return true;
        }

        // Event listeners for real-time validation and calculation
        startTime.addEventListener('input', calculateDuration);
        endTime.addEventListener('input', calculateDuration);
        activityDate.addEventListener('input', updateMinTime);

        // Event listener for form submission
        form.addEventListener('submit', function(event) {
            if (!validateTime()) {
                event.preventDefault();
            }
        });

        // Initialize min time on page load
        updateMinTime();
    </script>
</body>

</html>