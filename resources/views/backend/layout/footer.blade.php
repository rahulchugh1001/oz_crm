 </div>

    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
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
    </script>

    @stack('scripts')
</body>
</html>