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
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>FURRYHAVEN LOGIN</title>
<style>
html, body { height: 100%; margin: 0; font-family: Arial, sans-serif; }
body {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: linear-gradient(180deg, #FFF8F0 0%, #FFF5EE 100%);
}

.login-container {
  position: relative;
  width: 540px;
  max-width: 96%;
  padding: 50px 44px 48px;
  background: #fff;
  border-radius: 16px;
  text-align: center;
  box-shadow: 0 20px 40px rgba(8,20,30,0.08);
  box-sizing: border-box;
  transition: height 0.3s ease; /* smooth height change */
}

/* roof */
.roof {
  position: absolute;
  top: -160px; /* slightly lower for bigger register form */
  left: 50%;
  transform: translateX(-50%);
  pointer-events: none;
  transition: top 0.3s ease;
}
.roof-line-left, .roof-line-right {
  position: absolute;
  width: 460px;
  height: 6px;
  background: #FF8C00;
  border-radius: 8px;
  box-shadow: 0 6px 18px rgba(255,140,0,0.08);
}
.roof-line-left { right:50%; transform-origin:right center; transform: rotate(-25deg); }
.roof-line-right { left:50%; transform-origin:left center; transform: rotate(25deg); }

/* logo */
.logo { position:absolute; top:-120px; left:50%; transform:translateX(-50%); transition: top 0.3s ease; }
.logo img { max-width:180px; height:auto; display:block; }

.form-title { color:#18436e; font-size:32px; margin:10px 0 6px; }
.login-sub { color:#6b7b8d; font-size:14px; margin-bottom:18px; }

form { margin-top: 8px; }
input[type="text"], input[type="password"], input[type="email"] {
  width:92%;
  padding:14px;
  margin:10px 0;
  border:1px solid #e0e0e0;
  border-radius:10px;
  font-size:16px;
  box-sizing:border-box;
  transition: border-color .18s ease, box-shadow .18s ease;
}
input:focus {
  outline:none;
  border-color:#18436e;
  box-shadow:0 10px 24px rgba(24,67,110,0.08);
}

button {
  width:94%;
  padding:14px;
  margin-top:12px;
  border-radius:10px;
  border:none;
  background: #18436e;/* orange button */
  color:#fff;
  font-weight:700;
  font-size:16px;
  cursor:pointer;
  transition: transform .12s ease, background .18s ease, box-shadow .18s ease;
  box-shadow: 0 10px 22px rgba(24,67,110,0.08);
}
button:hover { background:#FF8C00; transform:translateY(-2px); box-shadow: 0 18px 36px rgba(24,67,110,0.12); }

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
.switch-text a:hover { text-decoration: underline; }

@media (max-width:640px) {
  .roof-line-left, .roof-line-right { width: 300px; height:4px; }
  .roof { top:-120px; }
  .logo img { max-width:120px; }
  .login-container { padding: 42px 22px; }
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
    <img src="logo.png" alt="Logo">
  </div>

  <!-- Titles -->
  <h1 id="form-title" class="form-title">Login</h1>
 <div id="form-sub" class="login-sub">Welcome back — please sign in to continue</div>


  <!-- Login Form -->
  <form action="./controllers/AuthController.php" id="login-form" method="POST">
    <input type="hidden" name="action" value="login" />
    <input type="text" name='username' placeholder="Username" />
    <input type="password" name='password' placeholder="Password" />
    <button type="submit">Login</button>
    <p class="switch-text">
      Don't have an account? <a href="#" id="show-register">Register now</a>
    </p>
  </form>

  <!-- Register Form (hidden initially) -->
  <form action="./controllers/AuthController.php" id="register-form" style="display: none;">
    <input type="text" placeholder="First Name" />
    <input type="text" placeholder="Last Name" />
    <input type="text" placeholder="Username" />
    <input type="email" placeholder="Email" />
    <input type="password" placeholder="Password" />
    <button type="submit">Register</button>
    <p class="switch-text">
      Already have an account? <a href="#" id="show-login">Login</a>
    </p>
  </form>

</div>

<script>
const loginForm = document.getElementById('login-form');
const registerForm = document.getElementById('register-form');
const showRegister = document.getElementById('show-register');
const showLogin = document.getElementById('show-login');
const formTitle = document.getElementById('form-title');
const formSub = document.getElementById('form-sub'); // subtitle element
const roof = document.querySelector('.roof');
const logo = document.querySelector('.logo');

showRegister.addEventListener('click', function(e) {
  e.preventDefault();
  loginForm.style.display = 'none';
  registerForm.style.display = 'block';
  formTitle.textContent = 'Register';
  formSub.textContent = 'Create your account — it’s quick and easy';
  roof.style.top = '-140px';  // move roof slightly higher for register
  logo.style.top = '-120px';  // move logo a bit higher
});

showLogin.addEventListener('click', function(e) {
  e.preventDefault();
  registerForm.style.display = 'none';
  loginForm.style.display = 'block';
  formTitle.textContent = 'Login';
  formSub.textContent = 'Welcome back — please sign in to continue';
  roof.style.top = '-160px'; // reset roof
  logo.style.top = '-120px'; // reset logo
});
</script>


</body>
</html>

