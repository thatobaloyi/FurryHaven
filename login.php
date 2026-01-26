<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/controllers/AuthController.php';
include_once __DIR__ . '/./core/functions.php';
include_once './notification.php';

$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : "";

if (isLoggedIn() && ($userRole === 'Admin' || $userRole === "Vet" || $userRole === "Volunteer")) {
  redirectTo('./dashboard2.php');
} 

if(isLoggedIn() && ($userRole === "Guest")){
  redirectTo("./homepage.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>FURRYHAVEN LOGIN</title>
  <style>
    html,
    body {
      height: 100%;
      margin: 0;
      font-family: Arial, sans-serif;
    }

    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      background: linear-gradient(180deg, #FFF8F0 0%, #FFF5EE 100%);
    }

    /* Back-to-home kennel button (left of the login) */
    .home-kennel-btn {
      position: absolute;
      left: 20px;
      top: 20px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #FF8C00; /* site orange */
      color: #fff;
      padding: 10px 12px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 700;
      box-shadow: 0 8px 18px rgba(255,136,0,0.12);
      transition: transform .12s ease, background .12s ease;
      z-index: 2000;
    }
    .home-kennel-btn svg { width:20px; height:20px; display:block; }
    .home-kennel-btn:hover { transform: translateY(-2px); background:#E67E00; }
    @media (max-width:640px) {
      .home-kennel-btn { left: 12px; top: 12px; padding:8px 10px; }
    }

    .login-container {
      position: relative;
      width: 540px;
      max-width: 96%;
      padding: 50px 44px 48px;
      background: #fff;
      border-radius: 16px;
      text-align: center;
      box-shadow: 0 20px 40px rgba(8, 20, 30, 0.08);
      box-sizing: border-box;
      transition: height 0.3s ease;
      margin-top: 120px;
    }

    /* roof */
    .roof {
      position: absolute;
      top: -160px;
      left: 50%;
      transform: translateX(-50%);
      pointer-events: none;
      transition: top 0.3s ease;
    }

    .roof-line-left,
    .roof-line-right {
      position: absolute;
      width: 460px;
      height: 6px;
      background: #FF8C00;
      border-radius: 8px;
      box-shadow: 0 6px 18px rgba(255, 140, 0, 0.08);
    }

    .roof-line-left {
      right: 50%;
      transform-origin: right center;
      transform: rotate(-25deg);
    }

    .roof-line-right {
      left: 50%;
      transform-origin: left center;
      transform: rotate(25deg);
    }

    /* logo */
    .logo {
      position: absolute;
      top: -120px;
      left: 50%;
      transform: translateX(-50%);
      transition: top 0.3s ease;
    }

    .logo img {
      max-width: 180px;
      height: auto;
      display: block;
    }

    .form-title {
      color: #18436e;
      font-size: 32px;
      margin: 10px 0 6px;
    }

    .login-sub {
      color: #6b7b8d;
      font-size: 14px;
      margin-bottom: 18px;
    }

    form {
      margin-top: 8px;
    }

    input[type="text"],
    input[type="password"],
    input[type="email"] {
      width: 92%;
      padding: 14px;
      margin: 10px 0;
      border: 1px solid #e0e0e0;
      border-radius: 10px;
      font-size: 16px;
      box-sizing: border-box;
      transition: border-color .18s ease, box-shadow .18s ease;
    }

    input:focus {
      outline: none;
      border-color: #18436e;
      box-shadow: 0 10px 24px rgba(24, 67, 110, 0.08);
    }

    button {
      width: 94%;
      padding: 14px;
      margin-top: 12px;
      border-radius: 10px;
      border: none;
      background: #18436e;
      color: #fff;
      font-weight: 700;
      font-size: 16px;
      cursor: pointer;
      transition: transform .12s ease, background .18s ease, box-shadow .18s ease;
      box-shadow: 0 10px 22px rgba(24, 67, 110, 0.08);
    }

    button:hover {
      background: #FF8C00;
      transform: translateY(-2px);
      box-shadow: 0 18px 36px rgba(24, 67, 110, 0.12);
    }

    .switch-text {
      margin-top: 15px;
      font-size: 14px;
      color: #18436e;
    }

    .switch-text a {
      color: #FF8C00;
      text-decoration: none;
      font-weight: bold;
    }

    .switch-text a:hover {
      text-decoration: underline;
    }

    @media (max-width:640px) {

      .roof-line-left,
      .roof-line-right {
        width: 300px;
        height: 4px;
      }

      .roof {
        top: -120px;
      }

      .logo img {
        max-width: 120px;
      }

      .login-container {
        padding: 42px 22px;
      }
    }
  </style>
</head>

<body>

  <div class="login-container" role="main">
   
   

    <div class="roof" aria-hidden="true">
      <div class="roof-line-left"></div>
      <div class="roof-line-right"></div>
    </div>

    <div class="logo" aria-hidden="true">
      <a href="homepage.php"><img src="logo.png" alt="Logo"></a>
    </div>

    <!-- Titles -->
    <h1 id="form-title" class="form-title">Login</h1>
    <div class="login-sub" id="welcome-message">Welcome back — please sign in to continue</div>

    <!-- Login Form -->
    <form action="./controllers/AuthController.php" method="POST" id="login-form">
      <input type="hidden" name="action" value="login">
      <input type="text" name="username" placeholder="Username" />
      <input type="password" name="password" placeholder="Password" />
      <button type="submit">Login</button>
      <p class="switch-text">
        Don't have an account? <a href="#" id="show-register">Register now</a>
      </p>
      <p class="switch-text">
        <a href="#" id="show-forgot">Forgot Password?</a>
      </p>
    </form>

    <!-- Register Form (hidden initially) -->
    <form id="register-form" style="display: none;" action="./controllers/UserController.php" method="POST">
      <input type="hidden" name="action" value="create" />
      <input type="hidden" name="user_role" value="Guest" />
      <input type="text" name="first_name" placeholder="First Name" required />
      <input type="text" name="preferred_name" placeholder="Preferred Name (optional)" />
      <input type="text" name="last_name" placeholder="Last Name" required />
      <input type="text" name="username" placeholder="Username" required />
      <input type="email" name="email" placeholder="Email" required />
      <input type="text" name="phone" placeholder="Phone Number" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Register</button>
      <p class="switch-text">
        Already have an account? <a href="#" id="show-login">Login</a>
      </p>
    </form>

    <!-- Forgot Password Form (hidden initially) -->
    <form id="forgot-form" style="display: none;" action="./controllers/UserController.php" method="POST">
      <input type="hidden" name="action" value="resetPassword" />
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="New Password" required />
      <input type="password" name="password2" placeholder="Confirm New Password" required />
      <button type="submit">Reset Password</button>
      <p class="switch-text">
        Remembered? <a href="#" id="show-login2">Login</a>
      </p>
    </form>

  </div>
  
    <!-- Back to homepage button -->
   <a href="homepage.php" class="home-kennel-btn" aria-label="Back to homepage">
      <!-- simple kennel / dog-house SVG -->
      <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M12 3l9 7h-2v7a1 1 0 0 1-1 1h-4v-5H10v5H6a1 1 0 0 1-1-1V10H3l9-7z" fill="#fff"/>
      </svg>
      <span style="font-size:14px;line-height:1">Home</span>
    </a>

  <script>
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const forgotForm = document.getElementById('forgot-form');
    const showRegister = document.getElementById('show-register');
    const showLogin = document.getElementById('show-login');
    const showForgot = document.getElementById('show-forgot');
    const showLogin2 = document.getElementById('show-login2');
    const formTitle = document.getElementById('form-title');
    const roof = document.querySelector('.roof');
    const logo = document.querySelector('.logo');
    const welcomeMessage = document.getElementById('welcome-message');

    showRegister.addEventListener('click', function(e) {
      e.preventDefault();
      loginForm.style.display = 'none';
      registerForm.style.display = 'block';
      forgotForm.style.display = 'none';
      formTitle.textContent = 'Register';
      roof.style.top = '-140px';
      logo.style.top = '-120px';
      welcomeMessage.textContent = 'Create your account — fill in the details below';
    });

    showLogin.addEventListener('click', function(e) {
      e.preventDefault();
      registerForm.style.display = 'none';
      forgotForm.style.display = 'none';
      loginForm.style.display = 'block';
      formTitle.textContent = 'Login';
      roof.style.top = '-160px';
      logo.style.top = '-120px';
      welcomeMessage.textContent = 'Welcome back — please sign in to continue';
    });

    showForgot.addEventListener('click', function(e) {
      e.preventDefault();
      loginForm.style.display = 'none';
      registerForm.style.display = 'none';
      forgotForm.style.display = 'block';
      formTitle.textContent = 'Reset Password';
      roof.style.top = '-160px';
      logo.style.top = '-120px';
      welcomeMessage.textContent = 'Enter your username to reset your password';
    });

    showLogin2.addEventListener('click', function(e) {
      e.preventDefault();
      forgotForm.style.display = 'none';
      loginForm.style.display = 'block';
      formTitle.textContent = 'Login';
      roof.style.top = '-160px';
      logo.style.top = '-120px';
      welcomeMessage.textContent = 'Welcome back — please sign in to continue';
    });
  </script>

</body>

</html>