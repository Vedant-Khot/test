    </main>
    </div>
    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        }

        // Close sidebar when clicking on nav links in mobile view
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });

        // Close sidebar when window is resized to desktop view
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });

        function downloadTableAsCSV(tableId, filename = 'registrations.csv') {
            const table = document.getElementById(tableId);
            const rows = Array.from(table.querySelectorAll('tr'));

            const csvContent = rows.map(row => {
                const cols = Array.from(row.querySelectorAll('td, th'));
                return cols.map(col => `"${col.innerText.replace(/"/g, '""')}"`).join(',');
            }).join('\n');

            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.setAttribute('download', filename);
            link.click();
        }

        function searchTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toLowerCase();
            let table = document.getElementById("table");
            let rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) { // skip header row
                let cells = rows[i].getElementsByTagName("td");
                let match = false;

                for (let j = 0; j < cells.length; j++) {
                    let cellValue = cells[j].textContent || cells[j].innerText;
                    if (cellValue.toLowerCase().includes(filter)) {
                        match = true;
                        break;
                    }
                }

                rows[i].style.display = match ? "" : "none";
            }
        }
    </script>
    </body>

    </html>