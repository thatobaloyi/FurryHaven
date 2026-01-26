<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/config/databaseconnection.php';
if (!isset($conn) || $conn->connect_error) {
    die("DB error");
}

$id = $_GET['id'] ?? '';
if (!$id) {
    die("Missing application ID");
}

// Save changes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['applicationStatus'] ?? 'Pending';
    $notes = $_POST['screeningNotes'] ?? '';

    $stmt = $conn->prepare("UPDATE animalapplication SET applicationStatus=?, screeningNotes=? WHERE animalappID=?");
    $stmt->bind_param("sss", $status, $notes, $id);
    $stmt->execute();
    $stmt->close();

    echo "<script>window.opener.location.reload(); window.close();</script>";
    exit;
}

// Load record
$stmt = $conn->prepare("SELECT * FROM animalapplication WHERE animalappID=?");
$stmt->bind_param("s", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$data) { die("Application not found"); }

// Load animal
$animalStmt = $conn->prepare("SELECT * FROM animal WHERE Animal_ID = ?");
if ($animalStmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$animalStmt->bind_param("s", $data['animalID']);
$animalStmt->execute();
$animalData = $animalStmt->get_result()->fetch_assoc();
$animalStmt->close();

// Load animal media
$mediaStmt = $conn->prepare("SELECT * FROM animalmedia WHERE Animal_ID=?");
if ($mediaStmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$mediaStmt->bind_param("s", $data['animalID']);
$mediaStmt->execute();
$mediaData = $mediaStmt->get_result()->fetch_assoc();
$mediaStmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Animal Application</title>
    <link rel="stylesheet" href="style2.css">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Lexend', sans-serif;
            background-color: #FFF8F0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #18436e;
            margin-bottom: 20px;
        }

        .animal-fieldset {
            border: 2px solid #df7100;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            background-color: #fff;
        }

        .animal-fieldset legend {
            font-weight: bold;
            font-size: 18px;
            padding: 0 10px;
            color: #18436e;
        }

        .details-list li {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .details-list li strong {
            width: 200px;
            font-weight: 600;
            color: #18436e;
            margin-right: 10px;
        }

        .details-list li span,
        .details-list li textarea,
        .details-list li select {
            flex: 1;
            max-width: 400px;
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.95rem;
            font-family: 'Lexend', sans-serif;
        }
        
        .details-list li textarea {
            resize: vertical;
            min-height: 80px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .savebtn {
            background-color: #98b06f;
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

        .animal-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .animal-info img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #df7100;
            margin-bottom: 10px;
        }

        .animal-info h3 {
            margin: 0;
            color: #18436e;
            font-size: 24px;
        }
    </style>
</head>

<body>
    <h2>Edit Animal Application</h2>

    <?php if ($animalData) : ?>
        <div class="animal-info">
            <?php if ($mediaData) : ?>
                <a href="showAnimalDetails.php?details=<?= htmlspecialchars($animalData['Animal_ID']) ?>">
                    <img src="./images/animals/<?= htmlspecialchars($mediaData['media_name']) ?>" alt="<?= htmlspecialchars($animalData['Animal_Name']) ?>">
                </a>
            <?php endif; ?>
            <h3><a href="showAnimalDetails.php?details=<?= htmlspecialchars($animalData['Animal_ID']) ?>"><?= htmlspecialchars($animalData['Animal_Name']) ?></a></h3>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="animalappID" value="<?= $data['animalappID'] ?>">

        <fieldset class="animal-fieldset">
            <legend>Application Details</legend>
            <ul class="details-list">
                <li><strong>Name:</strong> <span><?= htmlspecialchars($data['username']) ?></span></li>
                <li><strong>Application Date:</strong> <span><?= htmlspecialchars($data['applicationDate']) ?></span></li>
                <li><strong>Application Type:</strong> <span><?= htmlspecialchars($data['fosterDuration']) ? 'Foster' : 'Adoption' ?></span></li>
                
                <?php if ($data['fosterDuration']) : ?>
                    <li><strong>Foster Duration:</strong> <span><?= htmlspecialchars($data['fosterDuration']) ?></span></li>
                <?php else : ?>
                    <li><strong>ID Number:</strong> <span><?= htmlspecialchars($data['IDnumber']) ?></span></li>
                    <li><strong>Passport Number:</strong> <span><?= htmlspecialchars($data['passportNumber']) ?></span></li>
                    <li><strong>Age:</strong> <span><?= htmlspecialchars($data['age']) ?></span></li>
                    <li><strong>City:</strong> <span><?= htmlspecialchars($data['city']) ?></span></li>
                    <li><strong>Province:</strong> <span><?= htmlspecialchars($data['province']) ?></span></li>
                    <li><strong>Postal Code:</strong> <span><?= htmlspecialchars($data['postalCode']) ?></span></li>
                    <li><strong>Country:</strong> <span><?= htmlspecialchars($data['country']) ?></span></li>
                    <li><strong>Housing Type:</strong> <span><?= htmlspecialchars($data['housingType']) ?></span></li>
                    <li><strong>Home Ownership Status:</strong> <span><?= htmlspecialchars($data['homeOwnershipStatus']) ?></span></li>
                    <li><strong>Has Fenced Yard:</strong> <span><?= htmlspecialchars($data['hasFencedYard']) ?></span></li>
                    <li><strong>Allergic Household:</strong> <span><?= htmlspecialchars($data['allergicHousehold']) ?></span></li>
                    <li><strong>Has Other Pets:</strong> <span><?= htmlspecialchars($data['hasOtherPets']) ?></span></li>
                    <li><strong>Number of Pets:</strong> <span><?= htmlspecialchars($data['numberOfPets']) ?></span></li>
                <?php endif; ?>

                <li><strong>Why Foster or Adopt:</strong> <span><?= nl2br(htmlspecialchars($data['whyFosterOrAdopt'])) ?></span></li>

                <li>
                    <strong>Screening Notes:</strong>
                    <textarea name="screeningNotes"><?= htmlspecialchars($data['screeningNotes']) ?></textarea>
                </li>

                <li>
                    <strong>Status:</strong>
                    <select name="applicationStatus">
                        <?php foreach (['Pending', 'Accepted', 'Rejected'] as $s) : ?>
                            <option value="<?= $s ?>" <?= ($data['applicationStatus'] === $s ? 'selected' : '') ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </li>
            </ul>
        </fieldset>

        <div class="button-container">
            <button type="submit" class="savebtn">Save Changes</button>
        </div>
    </form>
</body>

</html>