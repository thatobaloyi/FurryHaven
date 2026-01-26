
(function(){
    // single global guard to avoid double-parse/init across multiple script tags
    if (window.__sidebar2_js_loaded__) return;
    window.__sidebar2_js_loaded__ = true;
    function initSidebar() {
        var sidebar = document.getElementById('sidebar');
        if (!sidebar) return;

        var pinBtn = document.getElementById('sidebarPinBtn');
        var mainContent = document.getElementById('mainContent') || document.getElementById('main') || document.querySelector('.main-content') || document.body;

        // State: pinned or not
        var storageKey = 'furryhaven_sidebar_pinned';
        var pinned = false;
        try { pinned = localStorage.getItem(storageKey) === '1'; } catch (e) { pinned = false; }

        function setPinned(val){
            pinned = !!val;
            try { localStorage.setItem(storageKey, pinned ? '1' : '0'); } catch (e) {}
            if (pinBtn) pinBtn.setAttribute('aria-pressed', pinned ? 'true' : 'false');
            if (pinned) {
                sidebar.classList.add('pinned');
                sidebar.classList.remove('collapsed');
                sidebar.classList.add('expanded');
            } else {
                sidebar.classList.remove('pinned');
                sidebar.classList.remove('expanded');
                sidebar.classList.add('collapsed');
            }
        }

        function expandSidebar(){
            if (pinned) return;
            sidebar.classList.remove('collapsed');
            sidebar.classList.add('expanded');
            sidebar.setAttribute('aria-expanded','true');
        }

        function collapseSidebar(){
            if (pinned) return;
            sidebar.classList.remove('expanded');
            sidebar.classList.add('collapsed');
            sidebar.setAttribute('aria-expanded','false');
        }

        // Initialize according to pinned state
        setPinned(pinned);

        // Hover handlers
        sidebar.addEventListener('mouseenter', function(){
            expandSidebar();
        });
        sidebar.addEventListener('mouseleave', function(){
            // small delay to avoid flicker when moving between elements
            setTimeout(collapseSidebar, 120);
        });

        // If the user hovers main content, collapse (desktop)
        if (mainContent && mainContent.addEventListener) {
            mainContent.addEventListener('mouseenter', function(){
                collapseSidebar();
            });
        }

        // Pin button toggles pinned state
        if (pinBtn) {
            pinBtn.addEventListener('click', function(e){
                e.preventDefault();
                setPinned(!pinned);
            });
            // keyboard accessibility
            pinBtn.addEventListener('keydown', function(e){
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    setPinned(!pinned);
                }
            });
        }

        // Touch/click support: allow toggling when clicking sidebar logo area
        var logo = document.getElementById('sidebarLogo');
        if (logo) {
            logo.addEventListener('click', function(e){
                // on touch devices, toggle expanded state temporarily
                if (!pinned) {
                    if (sidebar.classList.contains('expanded')) collapseSidebar(); else expandSidebar();
                }
            });
        }

        // Search toggle (retain existing behaviour)
        var searchBtn = document.getElementById('searchAnimalBtn');
        var searchContainer = document.getElementById('searchContainer');
        var searchInput = document.getElementById('animalSearchInput');
        if (searchBtn && searchContainer && searchInput) {
            searchBtn.addEventListener('click', function(){
                searchContainer.style.display = (searchContainer.style.display === 'none' || !searchContainer.style.display) ? 'block' : 'none';
                try { searchInput.focus(); } catch (e) {}
            });

            // On Enter key, redirect to animaldatabase2.php with search query
            searchInput.addEventListener('keyup', function(e){
                if (e.key === 'Enter'){
                    var query = searchInput.value.trim();
                    if (query.length > 0) location.href = 'animaldatabase2.php?search=' + encodeURIComponent(query);
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebar);
    } else {
        try { initSidebar(); } catch (e) { /* swallow errors to avoid breaking pages */ }
    }
})();

