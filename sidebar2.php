<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$userRole = $_SESSION['user_role'];
?>


<div class="sidebar collapsed" id="sidebar">
  <div class="sidebar-header">
    <button id="sidebarPinBtn" class="sidebar-pin" aria-pressed="false" title="Pin sidebar" aria-label="Pin sidebar">
      <i class="fa fa-thumbtack" aria-hidden="true"></i>
    </button>
    <a href="dashboard2.php" class="logo" id="sidebarLogo">
      <img src="./logo.png" alt="FurryHaven Logo" class="sidebar-logo">
    </a>
  </div>

  <style>
    /* Sidebar states */
    .sidebar { transition: width 180ms ease, box-shadow 180ms ease; width: 64px; overflow: hidden; }
    .sidebar.collapsed { width: 64px; }
    .sidebar.expanded { width: 220px; }
    .sidebar.pinned { box-shadow: 0 2px 10px rgba(0,0,0,0.08); }

    /* Pin button */
    .sidebar-pin { background: transparent; border: none; color: #4b5563; font-size: 14px; padding: 8px; cursor: pointer; }
    .sidebar-pin:focus { outline: 2px solid #df7100; border-radius: 6px; }
    .sidebar-header { display:flex; align-items:center; gap:8px; padding:10px; }
    .sidebar-logo { max-height:36px; display:block; }

    /* Hide long link text when collapsed */
    .sidebar.collapsed .link-text { display: none; }
    .sidebar.expanded .link-text { display: inline-block; }

    /* Make sure search container sits correctly */
    #searchContainer { padding: 6px 8px; }
  </style>

  <div class="sidebar-content">
    <ul class="nav-links top-links">
      <li>
        <a href="./homepage.php">
          <svg xmlns="http://www.w3.org/2000/svg"
            width="20" height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="icon">
            <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 1 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z" />
          </svg>
          <span class="link-text">The Homepage</span>
        </a>
      </li>

      <?php if ($_SESSION['user_role'] == 'Admin' || $_SESSION['user_role'] == 'Volunteer' || $_SESSION['user_role'] == 'Vet'): ?>
        <li>
          <a href="./animaldatabase2.php">
            <svg xmlns="http://www.w3.org/2000/svg"
              width="20" height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="icon">
              <line x1="3" x2="21" y1="6" y2="6" />
              <line x1="3" x2="21" y1="12" y2="12" />
              <line x1="3" x2="21" y1="18" y2="18" />
            </svg>
            <span class="link-text">Animal Database</span>
          </a>
        </li>
      <?php endif; ?>
      <li>
        <a href="./my_donations.php">
          <svg xmlns="http://www.w3.org/2000/svg"
            width="20" height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="icon">
            <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 1 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z" />
          </svg>
          <span class="link-text">My Donations</span>
        </a>
      </li>

      <?php if ($userRole == "Vet"): ?>
        <li>
          <a href="vet_history.php">
            <svg xmlns="http://www.w3.org/2000/svg"
              width="20" height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="icon">
              <path d="M20 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M4 21v-2a4 4 0 0 1 3-3.87"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span class="link-text">Vet Activities</span>
          </a>
        </li>

      <?php endif; ?>


      <?php if ($userRole == "Volunteer"): ?>
        <li>
          <a href="volunteeractivity.php">
            <svg xmlns="http://www.w3.org/2000/svg"
              width="20" height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="icon">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            <span class="link-text">Volunteer Activities</span>
          </a>
        </li>

      <?php endif; ?>

      <li>
        <a href="my_applications.php">
          <svg xmlns="http://www.w3.org/2000/svg"
            width="20" height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="icon">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <line x1="10" y1="9" x2="9" y2="9"></line>
          </svg>
          <span class="link-text">My Applications</span>
        </a>
      </li>


      <?php if ($userRole == 'Admin'): ?>
        <li>
          <a href="registration2.php">
            <svg xmlns="http://www.w3.org/2000/svg"
              width="20" height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="icon">
              <path d="M12 20h9" />
              <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z" />
            </svg>
            <span class="link-text">Register Animal</span>
          </a>
        </li>

        <li><a href="reportcruelty2.php"><span class="icon">&#9888;</span> <span class="link-text">Cruelty Reports</span></a></li>

        <li>
          <a href="approvedvolunteers2.php">
            <svg xmlns="http://www.w3.org/2000/svg"
              width="20" height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="icon">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            <span class="link-text">Volunteers</span>
          </a>
        </li>

        <li>
          <a href="approvedadopters2.php">
            <svg xmlns="http://www.w3.org/2000/svg"
              width="20" height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="icon">
              <path d="M12 21a9 9 0 1 1 9-9" />
              <path d="M16 17l2 2 4-4" />
              <path d="M21.9 12.1A5 5 0 0 0 17 7h-1.5" />
              <path d="M8 11l4 4 3-3" />
            </svg>
            <span class="link-text">Adopters/Fosters</span>
          </a>
        </li>

        <li>
          <a href="approvedboarding2.php">
            <svg xmlns="http://www.w3.org/2000/svg"
              width="20" height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="icon">
              <path d="M3 9l9-7 9 7" />
              <path d="M9 22V12h6v10" />
            </svg>
            <span class="link-text">Boarders</span>
          </a>
        </li>

        <li>
          <a href="donaters2.php">
            <svg xmlns="http://www.w3.org/2000/svg"
              width="20" height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="icon">
              <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 1 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z" />
            </svg>
            <span class="link-text">Donors</span>
          </a>
        </li>

        <li>
          <a href="analytics.php">
            <svg xmlns="http://www.w3.org/2000/svg"
              width="20" height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="icon">
              <line x1="18" y1="20" x2="18" y2="10"></line>
              <line x1="12" y1="20" x2="12" y2="4"></line>
              <line x1="6" y1="20" x2="6" y2="14"></line>
            </svg>
            <span class="link-text">Analytics</span>
          </a>
        </li>

        <li>
          <a href="kennel2.php">
            <svg xmlns="http://www.w3.org/2000/svg"
              width="20" height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="icon">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
            </svg>
            <span class="link-text">Kennel Assignment</span>
          </a>
        </li>

        <li>
          <a href="campaigns.php">
            <svg xmlns="http://www.w3.org/2000/svg"
              width="20" height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="icon">
              <path d="M3 11v2h4l7 5V6l-7 5H3z"></path>
              <path d="M14 4l7-2v20l-7-2"></path>
            </svg>
            <span class="link-text">Campaigns</span>
          </a>
        </li>

        <li>
          <a href="applications2.php">
            <svg xmlns="http://www.w3.org/2000/svg"
              width="20" height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="icon">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
              <line x1="16" y1="13" x2="8" y2="13"></line>
              <line x1="16" y1="17" x2="8" y2="17"></line>
              <line x1="10" y1="9" x2="9" y2="9"></line>
            </svg>
            <span class="link-text">Applications</span>
          </a>
        </li>

      <?php endif; ?>
    </ul>

    <div class="sidebar-separator"></div>

    <ul class="nav-links bottom-links">

      <li id="searchAnimalItem">
        <a href="javascript:void(0);" id="searchAnimalBtn">
          <svg xmlns="http://www.w3.org/2000/svg"
            width="20" height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="icon">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <span class="link-text">Search Animal</span>
        </a>

        <!-- Inline search input -->
        <div id="searchContainer" style="display:none; margin-top:10px;">
          <input type="text" id="animalSearchInput" placeholder="Enter animal name..." />
        </div>
      </li>



      <li>
        <a href="staffprofile.php">
          <svg xmlns="http://www.w3.org/2000/svg"
            width="20" height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="icon">
            <path d="M20 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M4 21v-2a4 4 0 0 1 3-3.87"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
          <span class="link-text">My Profile</span>
        </a>
      </li>

      <li>
        <a href="settings.php">
          <svg xmlns="http://www.w3.org/2000/svg"
            width="20" height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="icon">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
          </svg>
          <span class="link-text">Systems Settings</span>
        </a>
      </li>

      <li class="bottom-link">
        <a href="helpsupport.html">
          <svg xmlns="http://www.w3.org/2000/svg"
            width="20" height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="icon">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M12 16v-4"></path>
            <path d="M12 8h.01"></path>
          </svg>
          <span class="link-text">Help & Support</span>
        </a>
      </li>

      <li class="bottom-link">
        <a href="deleted_records.php">
          <svg xmlns="http://www.w3.org/2000/svg"
            width="20" height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="icon">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
          </svg>
          <span class="link-text">Trash</span>
        </a>
      </li>

      <li class="bottom-link">
        <a href="./controllers/AuthController.php?action=logout">
          <svg xmlns="http://www.w3.org/2000/svg"
            width="20" height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="icon">
            <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
            <line x1="12" y1="2" x2="12" y2="12"></line>
          </svg>
          <span class="link-text">Logout</span>
        </a>
      </li>
    </ul>
  </div>
</div>

<!-- Load sidebar behaviour JS (deferred) -->
<script id="sidebar2-js" src="sidebar2.js" defer></script>

<!-- <div class="sidebar collapsed" id="sidebar">
  <div class="sidebar-header">
    <a href="dashboard2.php" class="logo" id="sidebarLogo">
      <img src="./logo.png" alt="FurryHaven Logo" class="sidebar-logo">
    </a>
  </div>

  <ul class="nav-links">

    Top Section -->
<!-- <div>
      <div class="sidebar-separator"></div>




    </div>



    <div> -->

<!-- Separator -->
<!-- <li class="sidebar-separator"></li> -->

<!-- Bottom Section -->

<!-- </div>

  </ul>


</div> -->