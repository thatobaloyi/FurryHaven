<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/config/databaseconnection.php';
include_once __DIR__ . '/models/Animal.php';
include_once __DIR__ . '/models/MedicalProcedure.php';
include_once __DIR__ . '/models/AnimalMedia.php';

// Instantiate models
$animalModel = new Animal();
$medicalProcedureModel = new MedicalProcedure();
$media = new AnimalMedia();

$animal = null;

$animalImages = [];
$user_role = $_SESSION['user_role'] ?? '';

// Check if an animal ID is provided
if (isset($_GET['details'])) {
    $animalID = $_GET['details'];
    $animal = $animalModel->findOne($animalID);

    if ($animal) {
        $medicalRecords = $medicalProcedureModel->findByAnimalId($animalID);
        $animalImages = $media->getImagesByAnimalId($animalID);
    }
}

// Define data for form fields (this belongs in the logic file)
$animalFields = [
    'CageID' => 'Cage ID',
    'Animal_Name' => 'Name',
    'Animal_Type' => 'Type',
    'Animal_Breed' => 'Breed',
    'Animal_Gender' => 'Gender',
    'Animal_AgeGroup' => 'Age Group',
    'Animal_HealthStatus' => 'Health Status',
    'Animal_Vacc_Status' => 'Vaccination Status',
    'Animal_RescueDate' => 'Rescue Date',
    'Animal_RescueLocation' => 'Rescue Location',
    'RegisteredBy' => 'Registered By',
    'intakeType' => 'Intake Type',
    'outtakeType' => 'Outtake Type',
    'IsSpayNeutered' => 'Spay / Neutered'
];

$dropdownOptions = [
    'Animal_Type' => ['Dog', 'Cat', 'Bird', 'Livestock', 'Other'],
    'Animal_Gender' => ['Female', 'Male', 'Unknown'],
    'Animal_AgeGroup' => ['Juvenile', 'Adult', 'Senior'],
    'Animal_HealthStatus' => ['Healthy', 'Sick', 'Injured', 'Recovering', 'Under Observation'],
    'Animal_Vacc_Status' => ['Vaccinated', 'Not Vaccinated', 'Partially Vaccinated'],
    'IsSpayNeutered' => ["1" => "Yes", "0" => "No"],
    'intakeType' => ['Stray', 'Surrender', 'Born in Care', 'Boarding'],
    'outtakeType' => ['','Death in care', 'Euthanasia', 'Adoption', 'Return to owner']
];


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal Details - <?= htmlspecialchars($animal['Animal_Name'] ?? 'Not Found') ?></title>
    <link rel="stylesheet" href="style2.css">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        #animalPopup>input[type="text"],
        #animalPopup>textarea,
        #animalPopup>select {
            field-sizing: content;
            width: 100%;
            padding: 8px 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }


        #animalPopup h3 {
            margin-bottom: 15px;
            border-bottom: 2px solid #FF8C00;
            text-align: center;
            padding-bottom: 0.5rem;
            margin-top: 0;
        }


        #animalPopup ul {
            list-style: none;
        }

        #animalPopup {
            transition: ease-in-out all 1s;
            /* display: none; */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            background-color: #FFF8F0;
            border-radius: 15px;
            /* round borders */
            border: 3px solid #FF8C00;

            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
            font-family: 'Lexend', sans-serif;
            border-radius: 0.5em;
            padding: 15px;
            width: 70%;
            transition: ease-in-out 0.5s;
            /* max-width: 600px; */
            max-height: 80vh;
            overflow-y: auto;
        }


        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            /* slightly larger gap */
            justify-content: center;
            margin-bottom: 5px;
            padding: 10px;
        }

        .image-gallery img {
            width: 220px;
            /* fixed width for uniform look */
            height: 180px;
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

        .image-gallery img:hover {
            transform: translateY(-4px) scale(1.03);
            /* subtle lift */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            /* deeper shadow */
        }


        /* Flex container to center buttons side by side */
        .button-container {
            display: flex;
            justify-content: center;
            /* center horizontally */
            align-items: center;
            /* center vertically if needed */
            gap: 15px;
            /* space between buttons */
            margin-top: 20px;
        }

        /* Save button */
        .savebtn {
            background-color: #98b06f;
            /* green */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .savebtn:hover {
            background-color: rgb(87, 100, 63);
        }

        /* Delete button */
        .deletebtn {
            background-color: #df7100;
            /* orange */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .deletebtn:hover {
            background-color: rgb(194, 120, 46);
        }

        /* Optional: remove default margins on forms inside button container */
        .button-container form {
            margin: 0;
        }

        .animal-fieldset {
            border: 2px solid #df7100;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .animal-fieldset legend {
            font-weight: bold;
            font-size: 18px;
            padding: 0 10px;
            color: #18436e;
            /* dark blue */
        }

        .medical-fieldset {
            border: 2px solid #df7100;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .medical-fieldset legend {
            font-weight: bold;
            font-size: 18px;
            padding: 0 10px;
            color: #18436e;
            /* dark blue */
        }


        .details-list li {
            display: flex;
            align-items: center;
            /* vertically center label and input */
            margin-bottom: 12px;
        }

        .details-list li strong {
            width: 150px;
            /* fixed width for all labels */
            font-weight: 600;
            color: #18436e;
            margin-right: 10px;
            /* gap between label and input */
            font-size: 1rem;
        }

        .details-list li input,
        .details-list li select,
        .details-list li span {
            flex: 1;
            /* take remaining space */
            max-width: 400px;
            /* limits width */
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.95rem;
            font-family: 'Lexend', sans-serif;
            box-sizing: border-box;
            transition: 0.3s ease;
        }

        .details-list li input:hover,
        .details-list li select:hover,
        .details-list li span:hover {
            border-color: #FF8C00;
            box-shadow: 0 0 5px rgba(255, 140, 0, 0.3);
            background-color: #fff4e6;
        }

        /* table {
            width: 100%;
        } */

        .table-container {
            overflow-x: auto;
        }

        input[type="file"]::file-selector-button {
            content: 'Add Image';
            background-color: #FF8C00;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        input[type="file"]::file-selector-button:hover {
            color: white;
            background-color: #FF8C00;
        }

        .image-wrapper {
            position: relative;
            display: flex;
            /* use flex for cleaner vertical alignment */
            flex-direction: column;
            align-items: center;
        }
    </style>
</head>

<body>
    <section class="animal-media">
        <?php if (!empty($animalImages)) : ?>
            <div class="image-gallery">
                <?php foreach ($animalImages as $image) : ?>
                    <div class="image-wrapper">
                        <img width="50%" src='./images/animals/<?= htmlspecialchars($image) ?>' alt='Image of <?= htmlspecialchars($animal['Animal_Name']) ?>'>
                        <?php if ($user_role === 'Admin') : // Only Admin can delete images 
                        ?>
                            <form action="./controllers/AnimalController.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?');" style="margin-top: 5px; text-align: center;">
                                <input type="hidden" name="action" value="deleteImage">
                                <input type="hidden" name="animalID" value="<?= htmlspecialchars($animal['Animal_ID']) ?>">
                                <input type="hidden" name="image_filename" value="<?= htmlspecialchars($image) ?>">
                                <button type="submit" class="delete-image-btn" style="background-color: #df7100; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 0.8rem;">
                                    Delete Image
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($user_role === 'Admin') : // Only Admin can add images 
                        ?>
        <form action="./controllers/AnimalController.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="animalID" value="<?= htmlspecialchars($animal['Animal_ID']) ?>">
            <input type="file" name="animalImages[]" multiple>
            <input type="hidden" name="action" value="updateProfile">
            <br>
            <br>
            <button type="submit" class="savebtn">Save</button>
        </form>
         <?php endif; ?>
    </section>

    <section class="animal-details">
        <header class="animal-header">
            <h1><?= htmlspecialchars($animal['Animal_Name']) ?></h1>

        </header>

        <form method="POST" action="./controllers/AnimalController.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="Animal_ID" value="<?= htmlspecialchars($animal['Animal_ID']) ?>">

            <fieldset class="animal-fieldset">
                <legend>Animal Details</legend>

                <ul class="details-list">
                    <li>
                        <strong> Animal ID:</strong>
                        <span><?= htmlspecialchars($animal['Animal_ID']) ?></span>
                    </li>
                    <?php foreach ($animalFields as $field => $label) : ?>
                        <li>
                            <strong><?= htmlspecialchars($label) ?>:</strong>
                            <?php if ($user_role === 'Admin' || $user_role === "Vet") : ?>
                                <?php if (isset($dropdownOptions[$field])) : ?>
                                    <select name="<?= htmlspecialchars($field) ?>" <?php
                                                                                    $disabled = false; // Initialize flag

                                                                                    // Admin: Disable Animal_Vacc_Status and IsSpayNeutered
                                                                                    if ($user_role === 'Admin' && ($field === 'Animal_Vacc_Status' || $field === "IsSpayNeutered")) {
                                                                                        echo 'disabled';
                                                                                        $disabled = true;
                                                                                    }
                                                                                    // Vet: Disable everything EXCEPT Animal_Vacc_Status and IsSpayNeutered
                                                                                    elseif ($user_role === 'Vet' && ($field !== 'Animal_Vacc_Status' && $field !== "IsSpayNeutered")) {
                                                                                        echo 'disabled';
                                                                                        $disabled = true;
                                                                                    }
                                                                                    ?>>
                                        <?php foreach ($dropdownOptions[$field] as $key => $option) : ?>
                                            <?php $value = is_string($key) ? $key : $option; ?>
                                            <option value="<?= htmlspecialchars($value) ?>" <?= ($animal[$field] == $value) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($option) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <?php
                                    // Add hidden field IF the select was disabled, ensuring the value is submitted
                                    if ($disabled): ?>
                                        <input type="hidden" name="<?= htmlspecialchars($field) ?>" value="<?= htmlspecialchars($animal[$field]) ?>">
                                    <?php endif; ?>

                                <?php else : ?>
                                    <input type="text" name="<?= htmlspecialchars($field) ?>" value="<?= htmlspecialchars($animal[$field]) ?>"
                                        <?php
                                        // Vet: Make all non-dropdown fields (which are not Animal_Vacc_Status/IsSpayNeutered) readonly
                                        if ($user_role == "Vet") {
                                            echo "readonly";
                                        }
                                        // Admin: Only Animal_Vacc_Status and IsSpayNeutered are handled by the dropdown logic, 
                                        // so all other text fields for Admin are enabled (no attribute needed).
                                        ?>>
                                <?php endif; ?>
                            <?php else : ?>
                                <span><?= $animal[$field] ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>


                <div class="button-container">
                    <?php if ($user_role === 'Admin' || $user_role === 'Vet') : ?>
                        <!-- Save Changes button -->

                        <button type="submit" class="savebtn">Save Changes</button>


                        <!-- Delete Animal button -->
                    <?php endif; ?>
                </div>
            </fieldset>
        </form>
        <?php if ($user_role === 'Admin') : ?>
            <form method="POST" action="./controllers/AnimalController.php" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this animal?');">
                <input type="hidden" name="action" value="soft_delete">
                <input type="hidden" name="Animal_ID" value="<?= htmlspecialchars($animal['Animal_ID']) ?>">
                <button type="submit" class="deletebtn">Delete Animal</button>
            </form>
        <?php endif; ?>
    </section>




    <section class="medical-records">
        <form method="POST" action="./controllers/MedicalProcedureController.php">
            <input type="hidden" name="action" value="updateBatch">
            <fieldset class="medical-fieldset">
                <legend>Medical Procedures</legend>

                <?php if ($user_role === 'Admin' || $user_role === 'Vet' || $user_role === "Volunteer") : ?>
                    <?php if ($medicalRecords->num_rows > 0) : ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Medical ID</th>
                                        <th>Vet ID</th>
                                        <th>Procedure Type</th>
                                        <th>Procedure Outcome</th>
                                        <th>Procedure Date</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($record = $medicalRecords->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($record['medicalID']) ?>
                                                <input type="hidden" name="medicalID[<?= htmlspecialchars($record['medicalID']) ?>]" value="<?= htmlspecialchars($record['medicalID']) ?>">
                                            </td>
                                            <td><?= htmlspecialchars($record['vetID']) ?></td>
                                            <td><input type="text" name="procedureType[<?= htmlspecialchars($record['medicalID']) ?>]" value="<?= htmlspecialchars($record['procedureType']) ?>" disabled></td>
                                            <td>
                                                <select name="procedureOutcome[<?= htmlspecialchars($record['medicalID']) ?>]" <?= ($user_role === 'Vet') ? '' : 'disabled' ?>>
                                                    <?php
                                                    $outcomeOptions = ['Successful', 'Ongoing', 'Failed', 'Follow-up Required'];
                                                    foreach ($outcomeOptions as $outcome) : ?>
                                                        <option value="<?= htmlspecialchars($outcome) ?>" <?= ($record['procedureOutcome'] === $outcome) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($outcome) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td><input type="text" name="procedureDate[<?= htmlspecialchars($record['medicalID']) ?>]" value="<?= htmlspecialchars($record['procedureDate']) ?>" disabled></td>
                                            <td><textarea name="details[<?= htmlspecialchars($record['medicalID']) ?>]" <?= ($user_role === 'Vet') ? '' : 'disabled' ?>><?= htmlspecialchars($record['details']) ?></textarea></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($user_role === 'Vet'): ?>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-save">Save All Changes</button>
                            </div>
                        <?php endif; ?>

                    <?php else : ?>
                        <p class="no-records">No medical procedures found for this animal.</p>
                    <?php endif; ?>

            </fieldset>
        </form>
        <?php if ($user_role === 'Vet') : ?>
            <a href="add_record.php?Animal_ID=<?php echo htmlspecialchars($animal['Animal_ID']); ?>" class="btn btn-add" style="display: inline-block; text-decoration: none; background-color: #FF8C00; color: white; padding: 10px 15px; border-radius: 5px; margin-top: 1rem;">Add New Medical Procedure</a>
        <?php endif; ?>

        <?php
                    if ($user_role === 'Admin'):
                        include_once __DIR__ . '/models/User.php';
                        $userModel = new User();
                        $vets = $userModel->getVets();
        ?>
            <div style="margin-top: 2rem;">
                <h3>Notify Vet of Incomplete Treatments</h3>
                <form action="./controllers/NotificationController.php" method="POST">
                    <input type="hidden" name="action" value="sendVetNotification">
                    <input type="hidden" name="animal_id" value="<?= htmlspecialchars($animal['Animal_ID']) ?>">
                    <input type="hidden" name="animal_name" value="<?= htmlspecialchars($animal['Animal_Name']) ?>">
                    <label for="vet_username">Select Vet:</label>
                    <select name="vet_username" id="vet_username" class="filter-select" required>
                        <option value="">-- Select a Vet --</option>
                        <?php while ($vet = $vets->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($vet['username']) ?>"><?= htmlspecialchars($vet['username']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit" class="btn-primary">Send Notification</button>
                </form>
            </div>
        <?php endif; ?>

        <form action="./foster.php" method="POST">
            <input type="hidden" name="Animal_ID" value=<?php echo $animal['Animal_ID'] ?>>
            <?php if ($user_role === 'Volunteer') : ?>
                <button type="submit">Foster</button>
            <?php endif; ?>
        </form>
        <form action="./log_activity.php" method="POST">
            <input type="hidden" name="Animal_ID" value=<?php echo $animal['Animal_ID'] ?>>
            <?php if ($user_role === 'Volunteer') : ?>
                <button type="submit">Log Activity</button>
            <?php endif; ?>
        </form>
    <?php endif; ?>
    </section>

    <?php if ($animal) : ?>



    <?php else : ?>
        <p class="not-found">Animal not found.</p>
    <?php endif; ?>


</body>

</html>