    <!-- Mobile Bottom App Dock -->
    <?php include __DIR__ . '/bottom-nav.php'; ?>

    <!-- Scriptly Custom Alerts & Toast Subsystem -->
    <script src="../assets/js/scriptly-alerts.js"></script>

    <!-- Global App Component Interactivity -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- Sidebar Toggles ---
            const sidebar = document.getElementById('sidebar');
            const menuBtn = document.getElementById('menu-btn');
            const overlay = document.getElementById('sidebar-overlay');

            const toggleSidebar = () => {
                if (sidebar && overlay) {
                    sidebar.classList.toggle('-translate-x-full');
                    overlay.classList.toggle('hidden');
                }
            };

            if (menuBtn) menuBtn.addEventListener('click', toggleSidebar);
            if (overlay) overlay.addEventListener('click', toggleSidebar);

            // --- Dropdown Management ---
            const notifyBtn = document.getElementById('notify-btn');
            const notifyDropdown = document.getElementById('notify-dropdown');
            const profileBtn = document.getElementById('profile-btn');
            const profileDropdown = document.getElementById('profile-dropdown');

            // Toggle Notifications
            if (notifyBtn && notifyDropdown) {
                notifyBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    notifyDropdown.classList.toggle('hidden');
                    if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
                        profileDropdown.classList.add('hidden');
                    }
                });
            }

            // Toggle Profile
            if (profileBtn && profileDropdown) {
                profileBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('hidden');
                    if (notifyDropdown && !notifyDropdown.classList.contains('hidden')) {
                        notifyDropdown.classList.add('hidden');
                    }
                });
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', (e) => {
                if (notifyDropdown && !notifyBtn.contains(e.target) && !notifyDropdown.contains(e.target)) {
                    notifyDropdown.classList.add('hidden');
                }
                if (profileDropdown && !profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });

            // --- Mobile "More" Bottom Sheet Modal ---
            const moreTrigger = document.getElementById('mobile-more-trigger');
            const moreClose = document.getElementById('mobile-more-close');
            const moreBackdrop = document.getElementById('mobile-more-backdrop');
            const moreSheet = document.getElementById('mobile-more-sheet');

            const openMoreSheet = () => {
                if (moreSheet && moreBackdrop) {
                    moreBackdrop.classList.remove('opacity-0', 'pointer-events-none');
                    moreSheet.classList.remove('translate-y-full');
                }
            };

            const closeMoreSheet = () => {
                if (moreSheet && moreBackdrop) {
                    moreBackdrop.classList.add('opacity-0', 'pointer-events-none');
                    moreSheet.classList.add('translate-y-full');
                }
            };

            if (moreTrigger) moreTrigger.addEventListener('click', openMoreSheet);
            if (moreClose) moreClose.addEventListener('click', closeMoreSheet);
            if (moreBackdrop) moreBackdrop.addEventListener('click', closeMoreSheet);

            // Close on ESC
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (profileDropdown) profileDropdown.classList.add('hidden');
                    if (notifyDropdown) notifyDropdown.classList.add('hidden');
                    closeMoreSheet();
                }
            });
        });
    </script>
</body>
</html>
<?php 
if (ob_get_level() > 0) {
    ob_end_flush();
}
?>
