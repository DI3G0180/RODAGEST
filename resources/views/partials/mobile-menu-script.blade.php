<script>
    window.toggleMobileMenu = function(open) {
        var drawer = document.getElementById('mobile-nav-drawer');
        if (!drawer) return;
        var shouldOpen = typeof open === 'boolean' ? open : (drawer.style.display === 'none' || drawer.style.display === '');
        drawer.style.display = shouldOpen ? 'block' : 'none';
        document.body.style.overflow = shouldOpen ? 'hidden' : '';
        drawer.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');
        document.querySelectorAll('[aria-controls="mobile-nav-drawer"]').forEach(function(button) {
            button.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
        });
    };

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[data-mobile-menu-open]').forEach(function(button) {
            button.addEventListener('click', function() { window.toggleMobileMenu(true); });
        });
        document.querySelectorAll('[data-mobile-menu-close]').forEach(function(button) {
            button.addEventListener('click', function() { window.toggleMobileMenu(false); });
        });
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') window.toggleMobileMenu(false);
        });
    }, { once: true });
</script>
