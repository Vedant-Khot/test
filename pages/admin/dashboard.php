<?php
// session_start();
// Check if the user is logged in
require_once 'header.php';

// i want to fetch data of the no. of rows in the gitas_quest table

// Database credentials
$host = 'localhost';
$db   = 'iskcon_db';
$user = 'root'; // use your DB username
$pass = '';     // use your DB password


// mysqli connection
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ---- Gita ConQuest registrations for current year use pdo ----
// File: /admin/index.php  (or wherever your dashboard lives)

/* 1.  Current quest year (hard-code or auto) */
$quest_year = date('Y');        // e.g. 2025

/* 2.  ALL counts come only from `registrations` */
$total_reg  = (int)$conn->query("SELECT COUNT(*) FROM registrations")->fetch_row()[0];
$year_reg   = (int)$conn->query(
    "SELECT COUNT(*) FROM registrations "
)->fetch_row()[0];

/* 3.  Table rows for the current year */
$sql   = "SELECT * FROM registrations ORDER BY created_at DESC";
$data  = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-title">Total Registrations</div>
                        <div class="stat-value"><?= $total_reg ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title"><?= $quest_year ?> Registrations</div>
                        <div class="stat-value"><?= $year_reg ?></div>
                    </div>
                </div>


                <!-- Content Cards -->
                <div class="content-grid">
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Participants</h3>
                            <button class="btn btn-primary" onclick="downloadTableAsCSV('table')">Export CSV</button>
                        </div>
                        <div class="card-body">
                            <div class="table-container">
                                <!-- Table -->
                                <table id="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>College</th>
                                            <th>Standard</th>
                                            <th>City</th>
                                            <th>State</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sr = 1;
                                        foreach ($data as $row): ?>
                                            <tr>
                                                <td><?= $sr++ ?></td>
                                                <td><?= htmlspecialchars($row['name']) ?></td>
                                                <td><?= htmlspecialchars($row['college_name']) ?></td>
                                                <td><?= htmlspecialchars($row['standard']) ?></td>
                                                <td><?= htmlspecialchars($row['city']) ?></td>
                                                <td><?= htmlspecialchars($row['state']) ?></td>
                                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Activity</h3>
                        </div>
                        <div class="card-body">
                            <div class="activity-item">
                                <div class="activity-icon">📊</div>
                                <div class="activity-content">
                                    <div class="activity-title">New report generated</div>
                                    <div class="activity-time">2 hours ago</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">👤</div>
                                <div class="activity-content">
                                    <div class="activity-title">New user registered</div>
                                    <div class="activity-time">4 hours ago</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">🛒</div>
                                <div class="activity-content">
                                    <div class="activity-title">Order #12349 placed</div>
                                    <div class="activity-time">6 hours ago</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">💰</div>
                                <div class="activity-content">
                                    <div class="activity-title">Payment received</div>
                                    <div class="activity-time">8 hours ago</div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
                <section class="section">
                    <!-- <div class="container">
        <div class="profile-header" style="margin-bottom: 2rem;">
            <h2>Gita's Quest Participants (<?php echo $quest_year; ?>)</h2>
            <p class="text-muted">A complete list of all registered participants for the current year's contest.</p>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>College</th>
                        <th>Standard</th>
                        <th>City</th>
                        <th>State</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($participants)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center;">No participants have registered for this year's quest yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($participants as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['name']); ?></td>
                                <td><?php echo htmlspecialchars($p['email']); ?></td>
                                <td><?php echo htmlspecialchars($p['phone'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($p['age'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($p['gender'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($p['college_name']); ?></td>
                                <td><?php echo htmlspecialchars($p['standard']); ?></td>
                                <td><?php echo htmlspecialchars($p['city'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($p['state'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div> -->
                </section>
            </main>
        </div>
    </div>
<?php require_once 'footer.php'; ?>
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
    </script>
</body>

</html>