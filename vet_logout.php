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
  </head>
  <link rel="stylesheet" href="style2.css"> <!-- Global CSS -->
 <div class="main-content" id="mainContent">
  <body>
    
 <div class="logout-con">
    <h5>👋 You’ve Logged Out</h5>
    <p>Thank you for using the Vet Dashboard. See you again soon!</p>
    <a href="login.php">Back to Login</a>
  </div>
</div>
 </body>
  </html>