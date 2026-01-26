    <?php
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
    // include_once __DIR__ . '/config/databaseconnection.php';
    include_once __DIR__ . '/models/Animal.php';
    include_once __DIR__ . '/models/MedicalProcedure.php';
    include_once __DIR__ . '/models/AnimalMedia.php';

    $animalModel = new Animal();
    $medicalProcedureModel = new MedicalProcedure();
    $media = new AnimalMedia();

    if (isset($_GET['details'])) {
        $animalID = $_GET['details'];
        $animal = $animalModel->findOne($animalID);
        $medicalRecords = $medicalProcedureModel->findByAnimalId($animalID);
        $animalImages = $media->getImagesByAnimalId($animalID);

        if ($animal) {
            if (isset($_SESSION['user_role'])) {
                $user_role = $_SESSION['user_role'];
            } else {
                $user_role = '';
            }

            foreach ($animalImages as $image) {
                echo "<div>";
                echo "<img src='images/animals/" . htmlspecialchars($image) . "' width=50%>";
                echo "</div>";
            }

            echo "<h3>{$animal['Animal_Name']}</h3>";
            echo "<form method='POST' action='animals/process-update'>";
            echo "<input type='hidden' name='Animal_ID' value='{$animal['Animal_ID']}'>";


            // Animal Details
            echo "<h4>Animal Details</h4>";
            echo "<p><strong>Animal ID:</strong>  $animal[Animal_ID]";
            $animalFields = [

                'CageID' => 'Cage ID',
                'Animal_Name' => 'Species',
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
                'outtakeType' => ['Death in care', 'Euthanasia', 'Adoption', 'Return to owner']
            ];

            foreach ($animalFields as $field => $label) {
                echo "<p><strong>$label:</strong> ";
                if ($user_role === 'Admin') {
                    if (isset($dropdownOptions[$field])) {
                        echo "<select name='$field'>";
                        foreach ($dropdownOptions[$field] as $option) {
                            $selected = ($animal[$field] == $option) ? 'selected' : '';
                            echo "<option value='$option' $selected>$option</option>";
                        }
                        echo "</select>";
                    } else {
                        echo "<input type='text' name='$field' value='{$animal[$field]}'>";
                    }
                } else {
                    echo $animal[$field];
                }
                echo "</p>";
            }

            // Medical Records
            if ($user_role === 'Admin' || $user_role === 'Vet') {
                echo "<h4>Medical Records</h4>";
                if ($medicalRecords) {
                    foreach ($medicalRecords as $record) {
                        echo "<div class='medical-record'>";
                        echo "<input type='hidden' name='medicalID[]' value='{$record['medicalID']}'>";
                        $medicalFields = [
                            'medicalID' => 'Medical ID',
                            'vetID' => 'Vet ID',
                            'procedureType' => 'Procedure Type',
                            'procedureOutcome' => 'Procedure Outcome',
                            'procedureDate' => 'Procedure Date',
                            'details' => 'Details'
                        ];

                        foreach ($medicalFields as $field => $label) {
                            echo "<p><strong>$label:</strong> ";
                            if ($user_role === 'Vet') {
                                echo "<input type='text' name='{$field}[]' value='{$record[$field]}'>";
                            } else {
                                echo $record[$field];
                            }
                            echo "</p>";
                        }
                        echo "</div>";
                    }
                } else {
                    echo "<p>No medical records found for this animal.</p>";
                    echo "<button id='openModalBtn'>Add Record</button><br>";
                }
            }

            if ($user_role === 'Admin' || $user_role === 'Vet') {
                echo "<button type='submit' name='update'>Save Changes</button>";
            }

            echo "</form>";

            if ($user_role === 'Admin') {
                echo "<form method='POST' action='/animals/delete'>
                    <input type='hidden' name='Animal_ID' value='{$animal['Animal_ID']}'>
                    <button type='submit' name='deleteAnimal'>Delete</button>
                  </form>";
            }
        } else {
            echo "Animal not found.";
        }
    }
    ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
        }

        .modal-content {
            background: #fff;
            margin: 10% auto;
            padding: 20px;
            width: 40%;
            border-radius: 8px;
        }

        .closeBtn {
            float: right;
            font-size: 22px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div id="recordModal" class="modal">
        <div class="modal-content">
            <span class="closeBtn">&times;</span>
            <h2>Add New Medical Record</h2>
            <form action="" method="POST">
                Animal ID: <input type="text" name="animalID" required><br>

                Procedure Type:
                <select name="procedureType" required>
                    <option value="">-- Select --</option>
                    <option value="Vaccination">Vaccination</option>
                    <option value="Surgery">Surgery</option>
                    <option value="Dental">Dental</option>
                    <option value="Sterilisation">Sterilisation</option>
                    <option value="Check-up">Check-up</option>
                </select><br><br>

                Procedure Outcome:
                <select name="procedureOutcome" required>
                    <option value="">-- Select --</option>
                    <option value="Successful">Successful</option>
                    <option value="Ongoing">Ongoing</option>
                    <option value="Failed">Failed</option>
                    <option value="Follow-up Required">Follow-up Required</option>
                </select><br><br>

                Procedure Date: <input type="datetime-local" name="procedureDate" required><br>
                Details: <textarea name="details"></textarea><br><br>

                <button type="submit" name="add">Save Record</button>
            </form>
        </div>
    </div>

    <script>
        // Get modal elements
        
        var modal = document.getElementById("recordModal");
        var btn = document.getElementById("openModalBtn");
        var span = document.getElementsByClassName("closeBtn")[0];

        // Open modal
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // Close modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Close when clicking outside modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>