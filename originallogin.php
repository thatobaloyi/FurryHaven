<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/controllers/AuthController.php';
include_once __DIR__ . '/./core/functions.php';
include_once './notification.php';

if(isLoggedIn()){
  redirectTo('./dashboard2.php');
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Auth Page</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #001f3f;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    section {
      display: flex;
      width: 950px;
      height: 650px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    }

    article {
      background-color: #fdf5e6;
      padding: 40px;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    aside {
      flex: 1;
      background: url('image.webp') no-repeat center center;
      background-size: cover;
    }

    h1 {
      font-size: 28px;
      margin-bottom: 10px;
      color: #333;
      text-align: center;
    }

    p {
      font-size: 16px;
      color: #666;
      margin-bottom: 20px;
      text-align: center;
    }

    input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    button {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 10px;
      color: white;
    }

    /* ===== CREATE ACCOUNT FORM STYLING ===== */
    #signupForm .createBtn {
      background-color: #0059b3;
      /* Blue for Create */
    }

    #signupForm .toggleBtn {
      background-color: #FF8C00;
      /* Orange for "Already have account?" */
    }

    /* ===== LOGIN FORM STYLING ===== */
    #loginForm .loginBtn {
      background-color: #FF8C00;
      /* Orange for Login */
    }

    #loginForm .toggleBtn {
      background-color: #0059b3;
      /* Blue for "Create new account" */
    }

    button:hover {
      opacity: 0.9;
    }

    a {
      display: block;
      color: #001f3f;
      text-align: center;
      margin: 10px auto;
    }

    small {
      display: block;
      margin-top: 20px;
      font-size: 12px;
      color: #999;
      text-align: center;
    }

    /* Hide forms initially */
    .hidden {
      display: none;
    }

    select {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
      background-color: #fff;
      font-size: 14px;
      color: #333;
    }

    select:focus {
      border-color: #FF8C00;
      /* matches button orange */
      outline: none;
    }
  </style>
</head>

<body>

  <section>
    <article>
      <!-- LOGIN FORM (default visible now) -->
      <div id="loginForm">
        <h1>Welcome Back</h1>
        <p>Sign in to your account.</p>
        <form action="controllers/AuthController.php" method="POST">
          <input type="hidden" name="action" value="login">
          <input type="text" placeholder="Username" name="username" required>
          <input type="password" placeholder="Password" name="password" required>
          <button class="loginBtn" type="submit" name="login">Login</button>
        </form>
        <a href="forgotPassword.php">Forgot Password?</a>
        <h3 style="text-align:center">OR</h3>
        <button class="toggleBtn" onclick="toggleForms()">Create a new account</button>
      </div>
      
      <!-- CREATE ACCOUNT FORM (hidden by default) -->
      <div id="signupForm" class="hidden">
        <h1>Create Account</h1>
        <p>Fill in your details below.</p>
        <form action="/scripts/" method="POST">
          <input type="hidden" name="user_role" value="Guest">
          <input type="text" placeholder="First Name" name="first_name" required>
          <input type="text" placeholder="Surname" name="last_name" required>
          <?php include_once "./notification.php"; ?>
          <input type="text" placeholder="Username" name="username" required>
            <input type="email" placeholder="Email" name="email" required>
            <input type="phone" placeholder="Phone" name="phone" required>
            <input type="password" placeholder="Password" name="password" required>
            <button class="createBtn" type="submit">Create Account</button>
        </form>
        <h3 style="text-align:center">OR</h3>
        <button class="toggleBtn" onclick="toggleForms()">Already have an account? Login</button>
      </div>
      <small>By signing up, you agree to the Terms & Conditions.</small>
    </article>
    <aside></aside>
  </section>

  <script>
    function toggleForms() {
      document.getElementById('signupForm').classList.toggle('hidden');
      document.getElementById('loginForm').classList.toggle('hidden');
    }
  </script>

</body>

</html>