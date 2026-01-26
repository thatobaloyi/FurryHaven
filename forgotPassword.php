<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style2.css"> <!-- Global CSS -->
</head>
<body>
     <div class="main-content" id="mainContent">
    <form action="./controllers/UserController.php" method="POST">
    <input type="hidden" name="action" value="resetPassword">
        <input type="text" name="username" placeholder="username" required>
        <input type="password" name="password" placeholder="New Password" required>
        <input type="password" name="password2" placeholder="confirm Password" required>
        <input type="submit" name="resetPassword">
    </form>
</div>

</body>
</html>