<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once './notification.php';
require('config/databaseconnection.php');

// if (!isset($_SESSION['username'])) {
//     header("Location: login.php");
//     exit();
// }

$currentUser = $_SESSION['username'];

// Fetch current admin profile
$myProfileQuery = "SELECT * FROM users WHERE username = '$currentUser' LIMIT 1";
$myProfileResult = $conn->query($myProfileQuery);
$myProfile = $myProfileResult->fetch_assoc();

// Fetch all other staff users except guests and current admin
$staffQuery = "SELECT * FROM users WHERE userRole IN ('Admin','Vet','Volunteer') AND username != '$currentUser' AND isDeleted = 0 ORDER BY username ASC";
$staffResult = $conn->query($staffQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profiles</title>
    <link rel="stylesheet" href="style2.css">
    <style>
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .profile-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #003366;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin-right: 1.5rem;
        }

        .profile-summary p {
            margin: 0;
            font-size: 1.1rem;
        }

        .profile-summary p.username {
            font-weight: bold;
            font-size: 1.5rem;
            color: #003366;
        }

        .profile-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .profile-details-grid p {
            margin: 0.5rem 0;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 60%;
            max-width: 700px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .modal input[type=text],
        .modal input[type=email] {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>

<body>

    <div class="dashboard-container">
        <?php include 'sidebar2.php'; ?>

        <div class="main-content" id="mainContent">
            <h1>My Profile</h1>

            <div class="card">
                <div class="profile-header">
                    <div class="profile-icon"><img width='100%' style="border-radius: 100%;" src="./uploads/profiles/<?php echo $myProfile['profilePicturePath'] ?>" alt=""></div>
                    <div class="profile-summary">
                        <p class="username"><?php echo htmlspecialchars($myProfile['preferredName'] ?? $myProfile['FirstName']); ?></p>
                        <p><?php echo htmlspecialchars($myProfile['userRole']); ?></p>
                    </div>
                    <button class="btn-primary" id="editProfileBtn" style="margin-left: auto;">Edit Profile</button>
                </div>
                <div class="profile-details-grid">
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($myProfile['username']); ?></p>
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($myProfile['FirstName'] . ' ' . $myProfile['LastName']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($myProfile['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($myProfile['phone']); ?></p>
                </div>
            </div>

            <?php if($_SESSION['user_role'] == "Admin"): ?>
            <div class="card">
                <h2>Other Staff</h2>
                <button class="btn-primary" onclick="location.href='./create_user.php'" id="editProfileBtn" style="margin-left: auto;">Add New User</button>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($staffResult->num_rows > 0) {
                            while ($row = $staffResult->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['userRole']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No other staff members found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Edit My Profile</h2>
            <form action="./controllers/UserController.php" method="POST" enctype="multipart/form-data">
                <div class="profile-icon">
                    <img width='100%' style="border-radius: 100%" src="./uploads/profiles/<?php echo $myProfile['profilePicturePath'] ?>" alt="">
                </div>
                <br><br>
                <label for="">Update Profile Picture</label>
                <input type="file" name="profilePicture">
                <br><br>
                <input type="hidden" name="action" value="updateProfile">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($myProfile['username']); ?>">
                <label>First Name</label>
                <input type="text" name="FirstName" class="search-box" value="<?php echo htmlspecialchars($myProfile['FirstName']); ?>" required>
                <label>Last Name</label>
                <input type="text" name="LastName" class="search-box" value="<?php echo htmlspecialchars($myProfile['LastName']); ?>" required>
                <label>Preferred Name</label>
                <input type="text" name="preferredName" class="search-box" value="<?php echo htmlspecialchars($myProfile['preferredName']); ?>">
                <label>Email</label>
                <input type="email" name="email" class="search-box" value="<?php echo htmlspecialchars($myProfile['email']); ?>" required>
                <label>Phone</label>
                <input type="text" name="phone" class="search-box" value="<?php echo htmlspecialchars($myProfile['phone']); ?>" required>
                <button type="submit" class="btn-primary">Save Changes</button>
            </form>
        </div>
    </div>

    <script src="sidebar2.js"></script>
    <script>
        const modal = document.getElementById("editModal");
        const btn = document.getElementById("editProfileBtn");
        const span = document.getElementById("closeModal");

        btn.onclick = function() {
            modal.style.display = "block";
        }
        span.onclick = function() {
            modal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>

</html>