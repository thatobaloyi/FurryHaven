<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/notification.php';
include_once __DIR__ . '/controllers/BoardingAnimalController.php';
include_once __DIR__ . '/controllers/BoardingController.php';

$controller = new BoardingController();

$userActiveBookings = [];
$userUpcomingBookings = [];
$userCompletedBookings = [];

if (isset($_SESSION['username'])) {
    $userActiveBookings = $controller->displayUserActiveBookings($_SESSION['username']);
    $userUpcomingBookings = $controller->displayUserUpcomingBookings($_SESSION['username']);
    $userCompletedBookings = $controller->displayUserCompletedBookings($_SESSION['username']); // new
}

$animals = [];
if (isset($_SESSION['username'])) {
    $controller = new BoardingAnimalController();
    $animals = $controller->showAllAnimals($_SESSION['username']);
}

$animalRates = [
    "Cats" => 55,
    "Small Dogs" => 60,
    "Medium Dogs" => 65,
    "Large Dogs" => 70,
    "Extra Large Dogs" => 75,
    "Puppies" => 55
];

// Combine active and upcoming bookings into one array for the calendar
$calendarBookings = [];
if ($userActiveBookings && $userActiveBookings->num_rows > 0) {
    while ($row = $userActiveBookings->fetch_assoc()) {
        $calendarBookings[] = [
            'title' => $row['animal_name'] . ' (Active)',
            'start' => $row['booking_start_date'],
            'end' => date('Y-m-d', strtotime($row['booking_end_date'] . ' +1 day')),
            'color' => '#98b06f',            // Save button green (Active)
            'textColor' => '#ffffff',
            'extendedProps' => [
                'status' => $row['status'],
                'cage' => $row['cageNumber'],
                'owner' => $row['owner_first'] . ' ' . $row['owner_last']
            ]
        ];
    }
}
if ($userUpcomingBookings && $userUpcomingBookings->num_rows > 0) {
    while ($row = $userUpcomingBookings->fetch_assoc()) {
        $calendarBookings[] = [
            'title' => $row['animal_name'] . ' (Upcoming)',
            'start' => $row['booking_start_date'],
            'end' => date('Y-m-d', strtotime($row['booking_end_date'] . ' +1 day')),
            'color' => '#FF8C00',            // System orange (Upcoming)
            'textColor' => '#ffffff',
            'extendedProps' => [
                'status' => $row['status'],
                'cage' => $row['cageNumber'],
                'owner' => $row['owner_first'] . ' ' . $row['owner_last']
            ]
        ];
    }
}
// Completed bookings: show with muted gray matching board-this-animal button
if ($userCompletedBookings && $userCompletedBookings->num_rows > 0) {
    while ($row = $userCompletedBookings->fetch_assoc()) {
        $calendarBookings[] = [
            'title' => $row['animal_name'] . ' (Completed)',
            'start' => $row['booking_start_date'],
            'end' => date('Y-m-d', strtotime($row['booking_end_date'] . ' +1 day')),
            'color' => '#4b5563',            // Gray (Completed) - matches other gray used in UI
            'textColor' => '#ffffff',
            'extendedProps' => [
                'status' => $row['status'],
                'cage' => $row['cageNumber'],
                'owner' => $row['owner_first'] . ' ' . $row['owner_last']
            ]
        ];
    }
}
?>









<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Board Your Animal</title>
    <!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style2.css">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Page Layout */
        .content-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .page-title-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .page-header h1 {
            border-bottom: none;
            text-align: left;
            margin: 0;
            color: #18436e; /* Match table/calendar header background for consistency */
        }
        .header-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .action-btn {
            background-color: #18436e;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Lexend', sans-serif;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .action-btn:hover {
            background-color: #0f2c4d;
        }
        .back-btn {
            background-color: #FF8C00;
            color: white;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-weight: bold;
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        .back-btn:hover {
            background-color: #E67E00;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        .back-btn:active {
            transform: translateY(0);
        }
        .back-btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .back-btn:hover::before {
            left: 100%;
        }
        .back-icon {
            font-size: 1.2em;
            transition: transform 0.3s ease;
        }
        .back-btn:hover .back-icon {
            transform: translateX(-3px);
        }

        /* Add Animal Modal Styling */
        #animalModal .modal-content {
            background-color: #FFF8F0;
            border: 3px solid #FF8C00;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
            max-width: 700px;
            width: 90%;
            margin: 3% auto;
            padding: 22px;
            color: #18436e;
            max-height: 80vh;
            overflow-y: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            font-family: 'Lexend', Arial, sans-serif;
        }
        /* Ensure field name (label) sits next to the input */
        #animalModal .form-row,
        #animalModal .details-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }
        #animalModal .form-label,
        #animalModal .details-list li strong {
            width: 180px;            /* label column width */
            flex-shrink: 0;
            font-weight: 600;
            color: #18436e;
            font-size: 0.95rem;
        }
        #animalModal .form-row input[type="text"],
        #animalModal .form-row input[type="tel"],
        #animalModal .form-row input[type="email"],
        #animalModal .form-row select,
        #animalModal .form-row textarea {
            flex: 1;
            min-width: 120px;
        }
        /* ensure file input still lines up */
        #animalModal .form-row input[type="file"] { flex: 1; }

        /* Simple modal styles */
        #animalModal {
            display: none; position: fixed; z-index: 2500; left: 0; top: 0;
            width: 100%; height: 100%; overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        #animalProfileModal {
            display: none; position: fixed; z-index: 2500; left: 0; top: 0;
            width: 100%; height: 100%; background: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #ffffff;
            margin: 3% auto; padding: 20px; border: 1px solid #888; width: 400px;
            max-height: 80vh; /* A bit smaller to prevent cutoff */
            overflow-y: auto; /* Add vertical scrollbar when needed */
            border-radius: 8px;
        }
        #animalProfileModal .modal-content {
            background-color: #FFF8F0;
            border: 3px solid #FF8C00;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
            max-width: 700px;
            width: 90%;
            margin: 3% auto;
            padding: 22px;
            color: #18436e;
            max-height: 80vh;
            overflow-y: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            font-family: 'Lexend', Arial, sans-serif;
        }
        #animalProfileModal h3 {
            text-align: center;
            color: #18436e;
            border-bottom: 2px solid #FF8C00;
            padding-bottom: 0.5rem;
            margin-top: 0;
        }
        .profile-image-container {
            text-align: center;
            margin-bottom: 1rem;
        }
        .profile-image-container img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #FF8C00;
        }
        .animal-fieldset {
            border: 2px solid #df7100;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .animal-fieldset legend {
            font-weight: bold;
            font-size: 1.2rem;
            padding: 0 10px;
            color: #18436e;
        }
        .details-list {
            list-style: none;
            padding: 0;
        }
        .details-list li {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        .details-list li strong {
            width: 220px;
            font-weight: 600;
            color: #18436e;
            margin-right: 10px;
            font-size: 0.95rem;
            flex-shrink: 0;
        }
        .details-list li span {
            flex: 1;
            font-size: 0.95rem;
            border-radius: 8px;
        }
        #animalProfileModal .details-list li input,
        #animalProfileModal .details-list li select,
        #animalProfileModal .details-list li textarea {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        .savebtn {
            background-color: #98b06f; color: white; border: none; padding: 10px 20px; border-radius: 6px;
            font-size: 16px; cursor: pointer; transition: background 0.2s;
        }
        .savebtn:hover { background-color: rgb(87, 100, 63); }
        .deletebtn {
            background-color: #df7100; color: white; border: none; padding: 10px 20px; border-radius: 6px;
            font-size: 16px; cursor: pointer; transition: background 0.2s;
        }
        .animal-row {
            cursor: pointer;
        }
        .table-container {
            margin: 20px 0;
        }
        table {
            width: 100%; border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd; padding: 8px; text-align: left;
        }
        #user-animals-table-container th {
            background-color: #18436e; /* Match calendar header */
            color: #ffffff;
        }
        /* Booking modal styles */
        #bookingModal {
            display: none; position: fixed; left: 0; top: 0;
            width: 100%; height: 100%; background: rgba(0,0,0,0.4);
            z-index: 1000;
        }
        .modal-content {
            background: #fff; margin: 10% auto; padding: 20px;
            width: 350px; position: relative; border-radius: 12px;
        }
        .close-modal {
            position: absolute; right: 10px; top: 10px;
            cursor: pointer; font-size: 1.5em;
        }
        /* Edit Animal Modal styles */
        #editAnimalModal {
            display: none;
            position: fixed;
            z-index: 2000; /* higher than calendar */
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        #editAnimalModal .modal-content {
            background: #fff;
            margin: 10% auto;
            padding: 20px;
            width: 400px;
            border-radius: 12px;
            position: relative;
            z-index: 2001;
        }
        
        /* Booking Details Modal - match other modals (animalModal / animalProfileModal) */
        #bookingDetailsModal {
            display: none;
            position: fixed;
            z-index: 3000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            font-family: 'Lexend', Arial, sans-serif;
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-sizing: border-box;
        }
        #bookingDetailsModal .modal-content {
            background: #FFF8F0;
            border: 3px solid #FF8C00;
            border-radius: 14px;
            max-width: 520px;
            width: 100%;
            margin: 6% auto;
            padding: 20px;
            color: #18436e;
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
            position: relative;
            max-height: 85vh;
            overflow-y: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        #bookingDetailsModal .modal-content h4 {
            margin: 0 0 8px 0;
            font-size: 1.2rem;
            font-weight: 700;
            color: #18436e;
        }
        #bookingDetailsModal .close {
            position: absolute;
            right: 14px;
            top: 10px;
            cursor: pointer;
            font-size: 20px;
            color: #333;
            background: transparent;
            border: none;
        }
        #bookingDetailsModal .modal-body {
            color: #4b5563;
            margin-top: 8px;
            line-height: 1.45;
        }
        #bookingDetailsModal .btn-row {
            display:flex;
            gap:12px;
            justify-content:flex-end;
            margin-top:18px;
        }
        #bookingDetailsModal .btn {
            padding:10px 14px;
            border-radius:8px;
            border:none;
            cursor:pointer;
            font-weight:600;
        }
        #bookingDetailsModal .btn-primary {
            background:#98b06f;
            color:#fff;
        }
        #bookingDetailsModal .btn-secondary {
            background:#df7100;
            color:#fff;
        }

        /* ensure existing generic .modal-content rules do not override styling for bookingDetailsModal */

      /* Make all popups use the Booking modal visual style */
      .modal {
        display: none; /* hidden by default */
        position: fixed;
        z-index: 3000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
        padding: 20px;
        box-sizing: border-box;
        font-family: 'Lexend', Arial, sans-serif;
      }

      .modal .modal-content {
        background: #FFF8F0;
        border: 3px solid #FF8C00;
        border-radius: 14px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.12);
        max-width: 700px;
        width: 90%;
        margin: 0 auto;
        padding: 20px 22px;
        color: #18436e;
        max-height: 85vh;
        overflow-y: auto;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
      }

      /* Close button consistency */
      .modal .close,
      .modal .close-modal,
      .modal .close-btn {
        position: absolute;
        right: 14px;
        top: 10px;
        cursor: pointer;
        font-size: 20px;
        color: #333;
        background: transparent;
        border: none;
      }

      /* Ensure smaller dialogs keep good proportions */
      .modal.small .modal-content { max-width: 520px; }

      /* Make donation/pledge/installment tables fill their container */
      .donation-table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
      }
      .donation-table th,
      .donation-table td {
        padding: 0.8rem;
        border: 1px solid #e5e7eb;
        vertical-align: middle;
        word-wrap: break-word;
        white-space: normal;
      }
      /* ensure header stands out but still fits */
      .donation-table thead th {
        background-color: #18436e;
        color: #ffffff;
        font-weight: 600;
      }

      /* Make tables responsive on narrow screens */
      @media (max-width: 700px) {
        .donation-table th, .donation-table td { padding: 0.6rem; font-size: 0.95rem; }
        .modal .modal-content { width: 96%; padding: 16px; max-width: 520px; }
      }

    /* NEW: make animalModal form fields match animalProfileModal inputs */
    #animalModal .modal-content input[type="text"],
    #animalModal .modal-content input[type="email"],
    #animalModal .modal-content input[type="tel"],
    #animalModal .modal-content select,
    #animalModal .modal-content textarea {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      background: #ffffff;
      color: #18436e;
      font-family: 'Lexend', Arial, sans-serif;
      font-size: 0.95rem;
      box-sizing: border-box;
      transition: border-color 0.12s ease, box-shadow 0.12s ease;
    }

    #animalModal .modal-content input:focus,
    #animalModal .modal-content select:focus,
    #animalModal .modal-content textarea:focus {
      outline: none;
      border-color: #98b06f;
      box-shadow: 0 0 0 4px rgba(152,176,111,0.08);
    }

    #animalModal .modal-content label {
      display: block;
      margin-bottom: 6px;
      color: #18436e;
      font-weight: 600;
    }

    /* ensure file input looks consistent */
    #animalModal .modal-content input[type="file"] {
      padding: 6px 8px;
      border-radius: 8px;
      border: 1px solid #d1d5db;
      background: #fff;
    }
    </style>
</head>
<body>

<div class="content-wrapper">
    <div class="page-header">
        <div class="page-title-section">
            <button id="backButton" class="action-btn ">
                <span class="action-btn">←</span> Back
            </button>
            <h1>Board Your Animal</h1>
        </div>
        <div class="header-buttons">
            <button id="btn-add-boarding-animal" class="action-btn">Add Animal To Profile</button>
        </div>
    </div>

    <!-- Add Animal Modal -->
    <div id="animalModal">
        <div class="modal-content">
            <form method="POST" action="./controllers/BoardingAnimalController.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">
                <h3>Add Animal</h3>
                <fieldset class="animal-fieldset">
                    <legend>Basic Information</legend>
                <div class="form-row">
                    <label class="form-label" for="name">Name:</label>
                    <input id="name" type="text" name="name" required>
                </div>
                <div class="form-row">
                    <label class="form-label" for="breed">Breed:</label>
                    <input id="breed" type="text" name="breed" required>
                </div>
                <div class="form-row">
                    <label class="form-label" for="age_group">Age Group:</label>
                    <select id="age_group" name="age_group" required>
                        <option value="Junior">Junior</option>
                        <option value="Adult">Adult</option>
                        <option value="Senior">Senior</option>
                    </select>
                </div>
                <div class="form-row">
                    <label class="form-label" for="animal_type">Animal Type:</label>
                    <select id="animal_type" name="animal_type" required>
                       <option value="Cats">Cat</option>
                       <option value="Small Dogs">Small Dog</option>
                       <option value="Medium Dogs">Medium Dog</option>
                       <option value="Large Dogs">Large Dog</option>
                       <option value="Extra Large Dogs">Extra Large Dog</option>
                       <option value="Puppies">Puppy</option>
                    </select>
                </div>
                <div class="form-row">
                    <label class="form-label" for="image">Upload Image:</label>
                    <input id="image" type="file" name="image" accept="image/*">
                </div>
                 </fieldset>
                 <fieldset class="animal-fieldset">
                     <legend>Additional Information</legend>
                <div class="form-row">
                    <label class="form-label" for="emergency_contact_fname">Emergency First Name:</label>
                    <input id="emergency_contact_fname" type="text" name="emergency_contact_fname" required>
                </div>
                <div class="form-row">
                    <label class="form-label" for="emergency_contact_lname">Emergency Last Name:</label>
                    <input id="emergency_contact_lname" type="text" name="emergency_contact_lname" required>
                </div>
                <div class="form-row">
                    <label class="form-label" for="emergency_contact_phone">Emergency Phone:</label>
                    <input id="emergency_contact_phone" type="tel" name="emergency_contact_phone" required>
                </div>
                <div class="form-row">
                    <label class="form-label" for="emergency_contact_email">Emergency Email:</label>
                    <input id="emergency_contact_email" type="email" name="emergency_contact_email">
                </div>
                <div class="form-row">
                    <label class="form-label" for="primary_vet_name">Primary Vet Name:</label>
                    <input id="primary_vet_name" type="text" name="primary_vet_name">
                </div>
                <div class="form-row">
                    <label class="form-label" for="primary_vet_phone">Primary Vet Phone:</label>
                    <input id="primary_vet_phone" type="tel" name="primary_vet_phone">
                </div>
                <div class="form-row">
                    <label class="form-label" for="medical_conditions">Medical Conditions:</label>
                    <textarea id="medical_conditions" name="medical_conditions"></textarea>
                </div>
                <div class="form-row">
                    <label class="form-label" for="behavioural_notes">Behavioural Notes:</label>
                    <textarea id="behavioural_notes" name="behavioural_notes"></textarea>
                </div>
                <div class="form-row">
                    <label class="form-label" for="allergies">Allergies:</label>
                    <textarea id="allergies" name="allergies"></textarea>
                </div>
                <div class="form-row">
                    <label class="form-label" for="dietary_requirements">Dietary Requirements:</label>
                    <textarea id="dietary_requirements" name="dietary_requirements"></textarea>
                </div>
                 </fieldset>
                <input type="hidden" name="owner_id" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
                <div class="button-container">
                    <button type="submit" name="add_animal" class="savebtn">Save</button>
                    <button type="reset" class="deletebtn" style="background-color: #f0ad4e;">Clear</button>
                    <button type="button" id="closeModal" class="deletebtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Animal Profile Modal -->
    <div id="animalProfileModal">
       <div class="modal-content">
            <span id="closeProfileModal" style="cursor:pointer; float:right; font-size: 1.5em;">&times;</span>
            <form id="animalProfileForm" method="POST" action="./controllers/BoardingAnimalController.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="boardAnimalID" id="profile_boardAnimalID">

                <div id="animalDetails"></div>

                <div class="button-container">
                    <button type="submit" name="update_animal" class="savebtn">Save Changes</button>
                    <button type="button" id="boardThisAnimalBtn" style="display:none;">Board This Animal</button>
                    <button type="button" id="deleteThisAnimalBtn" class="deletebtn">Delete Profile</button>
                </div>

            </form>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div id="bookingDetailsModal">
      <div class="modal-content" id="bookingDetailsContent">
        <button id="closeBookingDetailsModal" class="close" aria-label="Close">&times;</button>
        <div class="modal-body" id="bookingDetailsBody"></div>
      </div>
    </div>

    <div class="table-container" id="user-animals-table-container">
        <table>
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Breed</th>
                    <th>Age Group</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($animals)): ?>
                    <?php foreach ($animals as $animal): ?>
                    <tr class="animal-row" data-animal='<?php echo htmlspecialchars(json_encode($animal), ENT_QUOTES, 'UTF-8'); ?>'>
                        <td>
                            <?php if (!empty($animal['board_animal_photo'])): ?>
                                <img src="<?php echo htmlspecialchars($animal['board_animal_photo']); ?>" alt="Photo" style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                            <?php else: ?>
                                <span>No Photo</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($animal['name']); ?></td>
                        <td><?php echo htmlspecialchars($animal['animalType']); ?></td>
                        <td><?php echo htmlspecialchars($animal['breed']); ?></td>
                        <td><?php echo htmlspecialchars($animal['ageGroup']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">You have not added any animal profiles yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Hidden form for boarding an animal -->
    <form id="boardAnimalForm" method="POST" action="boarding.php" style="display:none;">
        <input type="hidden" name="animal_id" id="hiddenAnimalId">
        <input type="hidden" name="animal_type" id="hiddenAnimalType">
        <input type="hidden" name="daily_rate" id="hiddenDailyRate">
    </form>

    <!-- Hidden form for deleting an animal -->
    <form id="deleteAnimalForm" method="POST" action="./controllers/BoardingAnimalController.php" style="display:none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="boardAnimalID" id="delete_boardAnimalID">
    </form>

    <section class="calendar-section">
        <div id="userBookingCalendar" class="calendar"></div>
    </section>

</div> <!-- end content-wrapper -->

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Constants for DOM Elements ---
        const addAnimalModal = document.getElementById('animalModal');
        const profileModal = document.getElementById('animalProfileModal');
        const animalDetailsDiv = document.getElementById('animalDetails');
        const boardBtn = document.getElementById('boardThisAnimalBtn');
        const boardAnimalForm = document.getElementById('boardAnimalForm');
        const hiddenAnimalIdInput = document.getElementById('hiddenAnimalId');
        const hiddenAnimalTypeInput = document.getElementById('hiddenAnimalType');
        const hiddenDailyRateInput = document.getElementById('hiddenDailyRate');
        const editAnimalModal = document.getElementById('editAnimalModal');
        const deleteAnimalForm = document.getElementById('deleteAnimalForm');
        const bookingDetailsModal = document.getElementById('bookingDetailsModal');
        const bookingDetailsBody = document.getElementById('bookingDetailsBody');
        const backButton = document.getElementById('backButton');
        const animalRates = <?php echo json_encode($animalRates); ?>;

        // Buttons
        const btnAddAnimal = document.getElementById('btn-add-boarding-animal');
        const btnCloseAddModal = document.getElementById('closeModal');
        const btnCloseProfileModal = document.getElementById('closeProfileModal');
        const btnCloseBookingDetailsModal = document.getElementById('closeBookingDetailsModal');


        // --- Functions ---

        // Function to show the animal profile modal with details
        function showAnimalProfile(animal) {
            // 1. Populate the hidden ID in the form
            document.getElementById('profile_boardAnimalID').value = animal.boardAnimalID;

            // 2. Generate the form fields HTML
            let imageHtml = '';
            if (animal.board_animal_photo) {
                imageHtml = `
                    <div class="profile-image-container">
                        <img src="${animal.board_animal_photo}" alt="Photo of ${animal.name}">
                    </div>
                    <label>Upload New Image: <input type="file" name="image" accept="image/*"></label><br>`;
            } else {
                imageHtml = `<label>Upload Image: <input type="file" name="image" accept="image/*"></label><br>`;
            }

            const detailsHtml = `
                ${imageHtml}
                <fieldset class="animal-fieldset">
                    <legend>${animal.name}'s Details</legend>
                    <ul class="details-list">
                        <li><strong>Name:</strong> <input type="text" name="name" value="${animal.name || ''}" required></li>
                        <li><strong>Breed:</strong> <input type="text" name="breed" value="${animal.breed || ''}" required></li>
                        <li>
                            <strong>Age Group:</strong>
                            <select name="age_group" required>
                                <option value="Junior" ${animal.ageGroup === 'Junior' ? 'selected' : ''}>Junior</option>
                                <option value="Adult" ${animal.ageGroup === 'Adult' ? 'selected' : ''}>Adult</option>
                                <option value="Senior" ${animal.ageGroup === 'Senior' ? 'selected' : ''}>Senior</option>
                            </select>
                        </li>
                        <li>
                            <strong>Animal Type:</strong>
                            <select name="animal_type" required>
                               <option value="Cats" ${animal.animalType === 'Cats' ? 'selected' : ''}>Cat</option>
                               <option value="Small Dogs" ${animal.animalType === 'Small Dogs' ? 'selected' : ''}>Small Dog</option>
                               <option value="Medium Dogs" ${animal.animalType === 'Medium Dogs' ? 'selected' : ''}>Medium Dog</option>
                               <option value="Large Dogs" ${animal.animalType === 'Large Dogs' ? 'selected' : ''}>Large Dog</option>
                               <option value="Extra Large Dogs" ${animal.animalType === 'Extra Large Dogs' ? 'selected' : ''}>Extra Large Dog</option>
                               <option value="Puppies" ${animal.animalType === 'Puppies' ? 'selected' : ''}>Puppy</option>
                            </select>
                        </li>
                    </ul>
                </fieldset>
                <fieldset class="animal-fieldset">
                    <legend>Contact & Medical</legend>
                    <ul class="details-list">
                         <li><strong>Emergency First Name:</strong> <input type="text" name="emergency_contact_fname" value="${animal.emergency_first_name || ''}" required></li>
                         <li><strong>Emergency Last Name:</strong> <input type="text" name="emergency_contact_lname" value="${animal.emergency_last_name || ''}" required></li>
                         <li><strong>Emergency Phone:</strong> <input type="tel" name="emergency_contact_phone" value="${animal.emergency_phone || ''}" required></li>
                         <li><strong>Emergency Email:</strong> <input type="email" name="emergency_contact_email" value="${animal.emergency_email || ''}"></li>
                         <li><strong>Primary Vet Name:</strong> <input type="text" name="primary_vet_name" value="${animal.primary_vet_name || ''}"></li>
                         <li><strong>Primary Vet Phone:</strong> <input type="tel" name="primary_vet_phone" value="${animal.primary_vet_phone || ''}"></li>
                         <li><strong>Medical Conditions:</strong> <textarea name="medical_conditions">${animal.medical_conditions || ''}</textarea></li>
                         <li><strong>Behavioural Notes:</strong> <textarea name="behavioural_notes">${animal.behavioural_notes || ''}</textarea></li>
                         <li><strong>Allergies:</strong> <textarea name="allergies">${animal.allergies || ''}</textarea></li>
                         <li><strong>Dietary Requirements:</strong> <textarea name="dietary_requirements">${animal.dietary_requirements || ''}</textarea></li>
                    </ul>
                </fieldset>
            `;
            animalDetailsDiv.innerHTML = detailsHtml;

            // Set up buttons
            const boardBtn = document.getElementById('boardThisAnimalBtn'); // Re-select the new button
            if (animal.boardAnimalID) {
                boardBtn.dataset.animalId = animal.boardAnimalID;
                boardBtn.dataset.animalType = animal.animalType;
                boardBtn.style.display = 'inline-block';
            } else {
                boardBtn.style.display = 'none';
            }

            profileModal.style.display = 'block';
        }

        // --- Event Listeners ---

        // Add Animal Modal
        if (btnAddAnimal) btnAddAnimal.onclick = () => { addAnimalModal.style.display = 'block'; };
        if (btnCloseAddModal) btnCloseAddModal.onclick = () => { addAnimalModal.style.display = 'none'; };

        // Profile Modal
        if (btnCloseProfileModal) btnCloseProfileModal.onclick = () => { profileModal.style.display = 'none'; };

        // Booking Details Modal
        if (btnCloseBookingDetailsModal) {
            btnCloseBookingDetailsModal.onclick = () => { bookingDetailsModal.style.display = 'none'; };
        }
        // also allow clicking the visual close button inside modal (keeps existing id)
        const bookingCloseBtn = document.getElementById('closeBookingDetailsModal');
        if (bookingCloseBtn) bookingCloseBtn.addEventListener('click', () => { document.getElementById('bookingDetailsModal').style.display = 'none'; });

        // Back Button
        if (backButton) {
            backButton.addEventListener('click', function() {
                // Check if there's a previous page in history
                if (document.referrer && document.referrer.includes(window.location.hostname)) {
                    window.history.back();
                } else {
                    // If no history or coming from external site, redirect to a default page
                    window.location.href = './index.php'; // Change to your desired default page
                }
            });
        }

        // Use event delegation for dynamically added cards
        document.getElementById('user-animals-table-container').addEventListener('click', function (e) {
            const row = e.target.closest('.animal-row');
            if (!row) return;

            const animalData = JSON.parse(row.dataset.animal);
            showAnimalProfile(animalData);
        });

        // Attach click event to the "Board This Animal" button
        if (boardBtn) {
            boardBtn.onclick = function() {
                hiddenAnimalIdInput.value = this.dataset.animalId;
                hiddenAnimalTypeInput.value = this.dataset.animalType;
                hiddenDailyRateInput.value = animalRates[this.dataset.animalType] || 0;
                boardAnimalForm.submit();
            };
        }

        // Delete button inside the profile modal
        document.getElementById('deleteThisAnimalBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this animal profile?')) {
                const animalID = document.getElementById('profile_boardAnimalID').value;
                if (animalID) {
                    document.getElementById('delete_boardAnimalID').value = animalID;
                    deleteAnimalForm.submit();
                }
            }
        });


        window.onclick = function(event) {
            // Board Animal Modal
            if (event.target == document.getElementById('animalModal')) {
                document.getElementById('animalModal').style.display = 'none';
            }
            // Animal Profile Modal
            if (event.target == document.getElementById('animalProfileModal')) {
                document.getElementById('animalProfileModal').style.display = 'none';
            }
            // Booking Details Modal
            if (event.target == document.getElementById('bookingDetailsModal')) {
                document.getElementById('bookingDetailsModal').style.display = 'none';
            }
        };

        // --- FullCalendar Initialization ---
        var calendarEl = document.getElementById('userBookingCalendar');
        if (calendarEl) {
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 600,
                events: <?php echo json_encode($calendarBookings); ?>,
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
                eventClick: function(info) {
                    var props = info.event.extendedProps;
                    var details = `
                        <b>Animal:</b> ${info.event.title}<br>
                        <b>Status:</b> ${props.status}<br>
                        <b>Cage:</b> ${props.cage}<br>
                        <b>Owner:</b> ${props.owner}<br>
                        <b>Start:</b> ${info.event.start.toLocaleString()}<br>
                        <b>End:</b> ${info.event.end ? info.event.end.toLocaleString() : ''}<br>
                    `;
                    document.getElementById('bookingDetailsBody').innerHTML = details;
                    document.getElementById('bookingDetailsModal').style.display = 'block';
                }
            });
            calendar.render();
        }
    });
</script>
</body>
</html>