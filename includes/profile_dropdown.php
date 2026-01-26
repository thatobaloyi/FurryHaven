<?php

if (session_status() === PHP_SESSION_NONE) session_start();

$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>


<div class="profile-icon" id="profileIcon">
  <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24">
    <circle cx="12" cy="7" r="5" />
    <path d="M2 21c0-5.5 4.5-10 10-10s10 4.5 10 10" />
  </svg>

  <div class="profile-dropdown-menu" id="profileDropdownMenu">
    <?php if (!$isLoggedIn): ?>
      <div class="dropdown-menu-header">
        <h3>Hello</h3>
        <p>Please Sign In!</p>
      </div>
      <div class="dropdown-menu-content">
        <button class="dropdown-btn dropdown-btn-primary" onclick="window.location.href='login.php'">Sign In</button>
      </div>
    <?php else: ?>
      <div class="dropdown-sidebar">
        <div class="dropdown-menu-logo">
          <a href="dashboard2.php"><img src="./logo.png" alt="FurryHaven Logo"></a>
        </div>
        <div class="dropdown-menu-header">
          <h3>Hey There,</h3>
          <p><?php echo htmlspecialchars($username); ?></p>
        </div>
        <div class="dropdown-menu-content">
          <a href="my_applications.php" class="dropdown-menu-option"><i class="fas fa-paw"></i><span>My Applications</span></a>
          <a href="my_donations.php" class="dropdown-menu-option"><i class="fas fa-heart"></i><span>My Donations</span></a>
          <a href="userAnimal.php" class="dropdown-menu-option"><i class="fas fa-heart"></i><span>Boarding</span></a>
          <a href="logout.php" class="dropdown-menu-option"><i class="fas fa-sign-out-alt"></i><span>LogOut</span></a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>