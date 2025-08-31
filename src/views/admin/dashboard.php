<?php 
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Admin Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Users</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $all_stats['users']['total_users'] ?? 0; ?></h5>
                <p class="card-text">Total registered users</p>
                <a href="/admin/users" class="btn btn-light btn-sm">Manage Users</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Books</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $all_stats['books']['total_books'] ?? 0; ?></h5>
                <p class="card-text">Total books in library</p>
                <a href="/books" class="btn btn-light btn-sm">Manage Books</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-header">Reservations</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $all_stats['reservations']['total_reservations'] ?? 0; ?></h5>
                <p class="card-text">Total reservations</p>
                <a href="/admin/reservations" class="btn btn-light btn-sm">View Reservations</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-header">Loans</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $all_stats['loans']['total_loans'] ?? 0; ?></h5>
                <p class="card-text">Total loans</p>
                <a href="/admin/loans" class="btn btn-light btn-sm">View Loans</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Visit Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3><?php echo $visit_stats['total_visits'] ?? 0; ?></h3>
                            <p>Total Visits</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3><?php echo $visit_stats['logged_in_visits'] ?? 0; ?></h3>
                            <p>Logged-in Users</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3><?php echo $visit_stats['non_logged_in_visits'] ?? 0; ?></h3>
                            <p>Guest Visitors</p>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 text-center">
                        <a href="/admin/visit-statistics" class="btn btn-primary">View Detailed Statistics</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Export Statistics</h5>
            </div>
            <div class="card-body">
                <p>Export detailed library statistics to PDF format.</p>
                <a href="/admin/export-statistics" class="btn btn-success">Export to PDF</a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">
                <h5>User Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="userChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Reservation Status</h5>
            </div>
            <div class="card-body">
                <canvas id="reservationChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
// User distribution chart
document.addEventListener('DOMContentLoaded', function() {
    var userCtx = document.getElementById('userChart').getContext('2d');
    var userChart = new Chart(userCtx, {
        type: 'pie',
        data: {
            labels: ['Admin', 'Librarian', 'Member'],
            datasets: [{
                data: [
                    <?php 
                    $adminCount = 0;
                    $librarianCount = 0;
                    $memberCount = 0;
                    foreach (($all_stats['users']['users_by_role'] ?? []) as $role) {
                        if ($role['role'] == 'admin') $adminCount = $role['count'];
                        if ($role['role'] == 'librarian') $librarianCount = $role['count'];
                        if ($role['role'] == 'member') $memberCount = $role['count'];
                    }
                    echo $adminCount . ', ' . $librarianCount . ', ' . $memberCount;
                    ?>
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 205, 86, 0.8)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 205, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'User Distribution by Role'
                }
            }
        }
    });

    // Reservation status chart
    var reservationCtx = document.getElementById('reservationChart').getContext('2d');
    var reservationChart = new Chart(reservationCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Completed', 'Cancelled'],
            datasets: [{
                data: [
                    <?php 
                    $activeReservations = 0;
                    $completedReservations = 0;
                    $cancelledReservations = 0;
                    foreach (($all_stats['reservations']['reservations_by_status'] ?? []) as $status) {
                        if ($status['status'] == 'active') $activeReservations = $status['count'];
                        if ($status['status'] == 'completed') $completedReservations = $status['count'];
                        if ($status['status'] == 'cancelled') $cancelledReservations = $status['count'];
                    }
                    echo $activeReservations . ', ' . $completedReservations . ', ' . $cancelledReservations;
                    ?>
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Reservation Status Distribution'
                }
            }
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>