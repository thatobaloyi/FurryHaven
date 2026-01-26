<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Kennel</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f4e9;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background-color: #fffdf8;
            border: 1px solid #e0d6c2;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #0a2463;
            margin-bottom: 25px;
            border-bottom: 2px solid #fb8500;
            padding-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        input:focus,
        textarea:focus {
            border-color: #fb8500;
            outline: none;
            box-shadow: 0 0 0 3px rgba(251, 133, 0, 0.1);
        }

        button {
            background-color: #FF8C00;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-weight: bold;
            width: 100%;
            margin-top: 20px;
        }

        button:hover {
            background-color: #e06e00;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Create New Kennel</h2>
        <form action="./controllers/KennelController.php" method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="kennel_type">Type:</label>
                <select id="kennel_type" name="kennel_type" required>
                    <option value="">--Select--</option>
                    <option value="Dog">Dog</option>
                    <option value="Cat">Cat</option>
                    <option value="Livestock">Livestock</option>
                    <option value="Boarded Animals">Boarded Animals</option>
                    <option value="Surgery Pens">Surgery Pens</option>
                    <option value="Puppy (Modern Kennels)">Puppy (Modern Kennels)</option>
                    <option value="Isolation Block">Isolation Block</option>
                </select>
            </div>

            <div class="form-group">
                <label for="kennel_name">Kennel Name:</label>
                <input type="text" id="kennel_name" name="kennel_name" required>
            </div>
            <div class="form-group">
                <label for="kennel_address">Address:</label>
                <input type="text" id="kennel_address" name="kennel_address" required>
            </div>
            <div class="form-group">
                <label for="kennel_capacity">Capacity:</label>
                <input type="number" id="kennel_capacity" name="kennel_capacity" min="1" required>
            </div>
            <div class="form-group">
                <label for="kennel_occupancy">Current Occupancy:</label>
                <input type="number" id="kennel_occupancy" name="kennel_occupancy" min="0" value="0" required>
            </div>
            <div class="form-group">
                <label for="kennel_contact_details">Contact Details:</label>
                <input type="text" id="kennel_contact_details" name="kennel_contact_details">
            </div>
            <!-- <input type="hidden" name="action" name="create"> -->

            <div class="form-group">
                <label for="full_capacity">Full Capacity (Boolean, 0 or 1):</label>
                <input type="number" id="full_capacity" name="full_capacity" min="0" max="1" value="0" required>
            </div>
            <button type="submit">Create Kennel</button>
        </form>
    </div>
</body>

</html>