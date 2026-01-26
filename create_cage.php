<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Cage</title>
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
        input[type="text"], select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }
        input:focus, select:focus {
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
        <h2>Create New Cage</h2>
        <form action="./controllers/CageController.php" method="POST">
    <input type="hidden" name="action" value="create">
            <!-- <div class="form-group">
                <label for="animal_id">Animal ID (Optional):</label>
                <input type="text" id="animal_id" name="animal_id" placeholder="e.g., ANML-00001">
            </div> -->
            <div class="form-group">
                <label for="kennel_id">Kennel ID:</label>
                <input type="text" id="kennel_id" name="kennel_id" required value=<?php echo $_GET["kennel"] ?>>
            </div>
            <div class="form-group">
                <label for="occupancy_status">Occupancy Status:</label>
                <select id="occupancy_status" name="occupancy_status" required>
                    <option value="">-- Select --</option>
                    <option value="1">Occupied</option>
                    <option value="0">Empty</option>
                </select>
            </div>
            <!-- <div class="form-group">
                <label for="assigned_by">Assigned By (Username):</label>
                <input type="text" id="assigned_by" name="assigned_by" placeholder="e.g., admin_user">
            </div> -->
            <button type="submit">Create Cage</button>
        </form>
    </div>
</body>
</html>