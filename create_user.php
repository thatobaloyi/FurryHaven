<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create New User</title>
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
            max-width: 700px;
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
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .full-width {
            grid-column: 1 / -1;
        }
        label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            transition: all 0.2s ease;
            box-sizing: border-box; /* Important for consistent sizing */
        }
        input:focus, select:focus, textarea:focus {
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
        <h2>Create New User</h2>
        <form id="createUserForm" method="POST" action="./controllers/UserController.php">
    <input type="hidden" name="action" value="create">
            <div class="form-grid">
                <!-- Core User Fields -->
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required />
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required />
                </div>
                <div class="form-group">
                    <label for="preferred_name">Preferred Name:</label>
                    <input type="text" id="preferred_name" name="preferred_name" required />
                </div>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required />
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required />
                </div>
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" required />
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone" required />
                </div>
                <div class="form-group full-width">
                    <label for="user_role">User Role:</label>
                    <select id="user_role" name="user_role" required>
                        <option value="">-- Select a Role --</option>
                        <option value="Admin">Admin</option>
                        <option value="Vet">Vet</option>
                        <option value="Volunteer">Volunteer</option>
                        <!-- <option value="Donor">Donor</option>
                        <option value="Fosterer">Fosterer</option> -->
                    </select>
                </div>

                <div class="form-group full-width">
                    <button type="submit">Create User</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>