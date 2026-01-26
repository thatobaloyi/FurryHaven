document.addEventListener('DOMContentLoaded', function() {
  const profileIcon = document.getElementById('profileIcon') || document.querySelector('.profile-icon');
  const dropdownMenu = document.getElementById('profileDropdownMenu') || document.querySelector('.profile-dropdown-menu');

  if (!profileIcon) return;
  if (!profileIcon.hasAttribute('tabindex')) profileIcon.setAttribute('tabindex', '0');

  profileIcon.addEventListener('click', function(e) {
    e.stopPropagation();
    profileIcon.classList.toggle('active');
    if (dropdownMenu) dropdownMenu.classList.toggle('active');
  });

  profileIcon.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      profileIcon.classList.toggle('active');
      if (dropdownMenu) dropdownMenu.classList.toggle('active');
    }
  });

  document.addEventListener('click', function(e) {
    if (!profileIcon.contains(e.target) && !(dropdownMenu && dropdownMenu.contains(e.target))) {
      profileIcon.classList.remove('active');
      if (dropdownMenu) dropdownMenu.classList.remove('active');
    }
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      profileIcon.classList.remove('active');
      if (dropdownMenu) dropdownMenu.classList.remove('active');
    }
  });
});