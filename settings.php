
<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Settings</title>
  <header class="site-header">
</header>

<style>
.back-btn {
  text-decoration: none;
  color: white;
  font-size: 18px;
  font-weight: bold;
  margin-right: 15px;
}

.back-btn:hover {
  text-decoration: underline;
}


    body {
      font-family: Arial, sans-serif;
      background-color: #FFF8F0;
      color: #333;
      margin: 0;
      padding: 2rem;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
    }
    
    :root {
      --primary-color: #003366;
      --secondary-color: #FF8C00;
      --background-color: #f8f4e9;
      --text-color: #333333;
      --light-text: #f8f8f8;
    }

    .settings-wrapper {
      margin-top: 40px;
      background: linear-gradient(145deg, #ffffff, #f9f9f9);
      border-radius: 16px;
      padding: 30px;
      width: 100%;
      max-width: 700px;
      box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
      border-top: 5px solid;
      border-image: linear-gradient(to right, var(--primary-color), var(--secondary-color)) 1;
    }

    h1 {
      color: var(--primary-color);
      text-align: center;
      border-bottom: 2px solid var(--secondary-color);
      padding-bottom: 0.5rem;
      margin-bottom: 20px;
    }
    
    h2 {
      margin-top: 2rem;
      color: var(--primary-color);
      border-left: 4px solid var(--secondary-color);
      padding-left: 0.5rem;
      font-size: 1.3rem;
    }

    label {
      display: block;
      margin: 1rem 0 0.3rem;
      font-weight: bold;
      color: var(--primary-color);
    }

    input, select, textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 14px;
      box-sizing: border-box;
      transition: all 0.2s ease;
    }

    input:focus, select:focus, textarea:focus {
      border-color: var(--secondary-color);
      outline: none;
      box-shadow: 0 0 0 2px rgba(255, 140, 0, 0.2);
    }

    button {
      background-color: var(--secondary-color);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s;
      display: block;
      margin: 2rem auto 0;
      font-weight: bold;
      width: 200px;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    button:hover {
      background-color: #E67E00;
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .settings-section {
      margin-bottom: 25px;
    }
    
    .settings-section h2 {
        margin-top: 0;
    }

    /* Additional styles from the volunteer form */
    .required {
      color: #d32f2f;
    }
    .opportunities article {
      background: linear-gradient(145deg, #ffffff, #f9f9f9);
      border-radius: 16px;
      padding: 1.8rem;
      box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
      border-top: 5px solid;
      border-image: linear-gradient(to right, var(--primary-color), var(--secondary-color)) 1;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      position: relative;
      overflow: hidden;
    }
    .opportunities article:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);
    }
    .opportunities h3 {
      color: var(--primary-color);
    }
    .opportunities article:hover h3 {
      color: var(--secondary-color);
    }
    
    /* New styles for validation feedback */
    .error-message {
        color: #d32f2f;
        font-size: 0.85rem;
        margin-top: -10px;
        margin-bottom: 15px;
        display: none; /* Hide by default */
    }
    .error-message.show {
        display: block;
    }
  </style>
</head>

<body>
  <div class="settings-wrapper">
    <h1>Settings</h1>

    <div class="settings-section">
      <h2>Profile Settings</h2>
      <form method="POST" action="./controllers/UserController.php" id="profile-settings-form">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Your username" value="<?php echo $_SESSION['username']?>" disabled>
        <input type="hidden" value="<?php echo $_SESSION['username']?>" name ="username"> 
        <input type="hidden" name="action" value="resetPassword" id="">
        <label for="old_password"> Password</label>
        <input type="password" id="old_password" name="password" placeholder="Enter current password">

        <label for="new_password">Verify new Password</label>
        <input type="password" id="new_password" name="password2" placeholder="Enter a new password">

        <button type="submit">Save Profile</button>
      </form>
    </div>
  </div>

</body>

</html>