    <!-- Scriptly Custom Alerts & Toast Subsystem -->
    <script src="../../assets/js/scriptly-alerts.js"></script>

    <!-- Provider Drawer & Dropdown Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const moreTrigger = document.getElementById('provider-more-trigger');
            const moreDrawer = document.getElementById('provider-more-drawer');
            const moreBackdrop = document.getElementById('provider-more-backdrop');
            const drawerClose = document.getElementById('provider-drawer-close');
            const drawerHandle = document.getElementById('provider-drawer-handle');

            function openDrawer() {
                if (moreDrawer && moreBackdrop) {
                    moreBackdrop.classList.remove('opacity-0', 'pointer-events-none');
                    moreBackdrop.classList.add('opacity-100', 'pointer-events-auto');
                    moreDrawer.classList.remove('translate-y-full');
                    moreDrawer.classList.add('translate-y-0');
                    document.body.classList.add('overflow-hidden');
                }
            }

            function closeDrawer() {
                if (moreDrawer && moreBackdrop) {
                    moreDrawer.classList.remove('translate-y-0');
                    moreDrawer.classList.add('translate-y-full');
                    moreBackdrop.classList.remove('opacity-100', 'pointer-events-auto');
                    moreBackdrop.classList.add('opacity-0', 'pointer-events-none');
                    document.body.classList.remove('overflow-hidden');
                }
            }

            if (moreTrigger) moreTrigger.addEventListener('click', openDrawer);
            if (drawerClose) drawerClose.addEventListener('click', closeDrawer);
            if (moreBackdrop) moreBackdrop.addEventListener('click', closeDrawer);
            if (drawerHandle) drawerHandle.addEventListener('click', closeDrawer);

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeDrawer();
            });
        });
    </script>
    <!-- Fade-out Preloader -->
    <script>
        window.addEventListener('load', () => {
            const preloader = document.getElementById('page-preloader');
            if (preloader) {
                preloader.style.opacity = '0';
                setTimeout(() => preloader.style.display = 'none', 300);
            }
        });
    </script>
</body>
</html>
