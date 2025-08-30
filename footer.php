<footer class="footer mt-auto py-3 bg-dark text-white d-print-none">
        <div class="container text-center">
            <a href="mailto:rmkdunn@gmail.com" class="text-white">Suggestions? Questions? Contact me</a>
        </div>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Theme switcher logic (global)
            const themeSwitcher = document.querySelector('.theme-switcher');
            const body = document.body;
            const themeIcon = document.getElementById('theme-icon');

            const applyTheme = (theme) => {
                if (theme === 'dark') {
                    body.classList.add('dark-mode');
                    body.classList.remove('bg-light');
                    themeIcon.textContent = '☀️';
                } else {
                    body.classList.remove('dark-mode');
                    body.classList.add('bg-light');
                    themeIcon.textContent = '🌙';
                }
            };

            themeSwitcher.addEventListener('click', () => {
                const isDarkMode = body.classList.contains('dark-mode');
                const newTheme = isDarkMode ? 'light' : 'dark';
                localStorage.setItem('theme', newTheme);
                applyTheme(newTheme);
            });

            // Apply saved theme on page load
            const savedTheme = localStorage.getItem('theme') || 'light';
            applyTheme(savedTheme);
        });
    </script>
</body>
</html>