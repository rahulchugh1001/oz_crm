 </div>

    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const sidebarDropdownConfig = [
            { id: 'sf001-dropdown', chevronId: 'sf001-chevron' },
            { id: 'sf002-dropdown', chevronId: 'sf002-chevron' },
            { id: 'sf003-dropdown', chevronId: 'sf003-chevron' },
            { id: 'profile-dropdown', chevronId: 'profile-chevron' },
            { id: 'masters-dropdown', chevronId: 'masters-chevron' },
        ];

        function getSidebarDropdownById(dropdownId) {
            return sidebarDropdownConfig.find(function(entry) {
                return entry.id === dropdownId;
            });
        }

        function setChevronState(chevronId, isOpen) {
            const chevron = document.getElementById(chevronId);
            if (!chevron) {
                return;
            }

            chevron.setAttribute('data-lucide', isOpen ? 'chevron-down' : 'chevron-right');
        }

        function openDropdownAnimated(dropdown) {
            if (!dropdown) {
                return;
            }

            dropdown.classList.remove('hidden');
            dropdown.style.overflow = 'hidden';
            dropdown.style.transition = 'max-height 220ms ease, opacity 220ms ease';
            dropdown.style.opacity = '0';
            dropdown.style.maxHeight = '0px';

            requestAnimationFrame(function() {
                dropdown.style.maxHeight = dropdown.scrollHeight + 'px';
                dropdown.style.opacity = '1';
            });

            setTimeout(function() {
                if (!dropdown.classList.contains('hidden')) {
                    dropdown.style.maxHeight = 'none';
                    dropdown.style.overflow = 'visible';
                }
            }, 240);
        }

        function closeDropdownAnimated(dropdown) {
            if (!dropdown || dropdown.classList.contains('hidden')) {
                return;
            }

            dropdown.style.overflow = 'hidden';
            dropdown.style.transition = 'max-height 220ms ease, opacity 220ms ease';
            dropdown.style.maxHeight = dropdown.scrollHeight + 'px';
            dropdown.style.opacity = '1';

            requestAnimationFrame(function() {
                dropdown.style.maxHeight = '0px';
                dropdown.style.opacity = '0';
            });

            setTimeout(function() {
                dropdown.classList.add('hidden');
            }, 240);
        }

        function toggleSidebarDropdown(dropdownId) {
            const targetDropdown = document.getElementById(dropdownId);
            const targetConfig = getSidebarDropdownById(dropdownId);

            if (!targetDropdown || !targetConfig) {
                return;
            }

            const shouldOpenTarget = targetDropdown.classList.contains('hidden');

            sidebarDropdownConfig.forEach(function(entry) {
                const currentDropdown = document.getElementById(entry.id);
                if (!currentDropdown) {
                    return;
                }

                if (entry.id === dropdownId) {
                    if (shouldOpenTarget) {
                        openDropdownAnimated(currentDropdown);
                        setChevronState(entry.chevronId, true);
                    } else {
                        closeDropdownAnimated(currentDropdown);
                        setChevronState(entry.chevronId, false);
                    }
                } else {
                    closeDropdownAnimated(currentDropdown);
                    setChevronState(entry.chevronId, false);
                }
            });

            lucide.createIcons();
        }

        function initializeSidebarDropdownState() {
            sidebarDropdownConfig.forEach(function(entry) {
                const dropdown = document.getElementById(entry.id);
                if (!dropdown) {
                    return;
                }

                if (dropdown.classList.contains('hidden')) {
                    dropdown.style.maxHeight = '0px';
                    dropdown.style.opacity = '0';
                    setChevronState(entry.chevronId, false);
                } else {
                    dropdown.style.maxHeight = 'none';
                    dropdown.style.opacity = '1';
                    setChevronState(entry.chevronId, true);
                }
            });
        }

        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();

            initializeSidebarDropdownState();

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
            
            lucide.createIcons();
        });

        // Toggle Masters Dropdown
        function toggleMastersDropdown() {
            toggleSidebarDropdown('masters-dropdown');
        }

        // Toggle SF001 Dropdown
        function toggleSF001Dropdown() {
            toggleSidebarDropdown('sf001-dropdown');
        }

        // Toggle SF002 Dropdown
        function toggleSF002Dropdown() {
            toggleSidebarDropdown('sf002-dropdown');
        }

        // Toggle SF003 Dropdown
        function toggleSF003Dropdown() {
            toggleSidebarDropdown('sf003-dropdown');
        }

        // Toggle Profile Dropdown
        function toggleProfileDropdown() {
            toggleSidebarDropdown('profile-dropdown');
        }
    </script>

    @stack('scripts')
</body>
</html>