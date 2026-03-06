 </div>

    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();

            const headerDate = document.getElementById('header-current-date');
            if (headerDate) {
                const localDate = new Date();
                headerDate.textContent = new Intl.DateTimeFormat('en-US', {
                    month: 'long',
                    day: '2-digit',
                    year: 'numeric',
                }).format(localDate);
            }

            // Determine and set shift based on current time
            const currentHour = new Date().getHours();
            const shiftIcon = document.getElementById('header-shift-icon');
            const shiftName = document.getElementById('header-shift-name');
            const shiftTime = document.getElementById('header-shift-time');
            
            if (currentHour >= 8 && currentHour < 20) {
                // Morning shift: 8 AM to 8 PM
                if (shiftIcon) shiftIcon.setAttribute('data-lucide', 'sun');
                if (shiftName) shiftName.textContent = 'Shift Day';
                if (shiftTime) shiftTime.textContent = '08:00 - 20:00';
            } else {
                // Night shift: 8 PM to 8 AM
                if (shiftIcon) shiftIcon.setAttribute('data-lucide', 'moon');
                if (shiftName) shiftName.textContent = 'Shift Night';
                if (shiftTime) shiftTime.textContent = '20:00 - 08:00';
            }
            
            // Auto-expand profile dropdown if on a profile page
            const profileDropdown = document.getElementById('profile-dropdown');
            const profileChevron = document.getElementById('profile-chevron');
            if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
                if (profileChevron) {
                    profileChevron.setAttribute('data-lucide', 'chevron-down');
                }
            }
            
            lucide.createIcons();
        });

        // Toggle Masters Dropdown
        function toggleMastersDropdown() {
            const dropdown = document.getElementById('masters-dropdown');
            const chevron = document.getElementById('masters-chevron');
            dropdown.classList.toggle('hidden');
            
            if (dropdown.classList.contains('hidden')) {
                chevron.setAttribute('data-lucide', 'chevron-right');
            } else {
                chevron.setAttribute('data-lucide', 'chevron-down');
            }
            lucide.createIcons();
        }

        // Toggle SF001 Dropdown
        function toggleSF001Dropdown() {
            const dropdown = document.getElementById('sf001-dropdown');
            const chevron = document.getElementById('sf001-chevron');
            dropdown.classList.toggle('hidden');
            
            if (dropdown.classList.contains('hidden')) {
                chevron.setAttribute('data-lucide', 'chevron-right');
            } else {
                chevron.setAttribute('data-lucide', 'chevron-down');
            }
            lucide.createIcons();
        }

        // Toggle SF002 Dropdown
        function toggleSF002Dropdown() {
            const dropdown = document.getElementById('sf002-dropdown');
            const chevron = document.getElementById('sf002-chevron');
            dropdown.classList.toggle('hidden');

            if (dropdown.classList.contains('hidden')) {
                chevron.setAttribute('data-lucide', 'chevron-right');
            } else {
                chevron.setAttribute('data-lucide', 'chevron-down');
            }
            lucide.createIcons();
        }

        // Toggle SF003 Dropdown
        function toggleSF003Dropdown() {
            const dropdown = document.getElementById('sf003-dropdown');
            const chevron = document.getElementById('sf003-chevron');
            dropdown.classList.toggle('hidden');

            if (dropdown.classList.contains('hidden')) {
                chevron.setAttribute('data-lucide', 'chevron-right');
            } else {
                chevron.setAttribute('data-lucide', 'chevron-down');
            }
            lucide.createIcons();
        }

        // Toggle Profile Dropdown
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profile-dropdown');
            const chevron = document.getElementById('profile-chevron');
            dropdown.classList.toggle('hidden');
            
            if (dropdown.classList.contains('hidden')) {
                chevron.setAttribute('data-lucide', 'chevron-right');
            } else {
                chevron.setAttribute('data-lucide', 'chevron-down');
            }
            lucide.createIcons();
        }
    </script>

    @stack('scripts')
</body>
</html>