<?php 
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Visit Statistics</h1>
    <a href="/admin" class="btn btn-secondary">Back to Dashboard</a>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Overview</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-primary text-white rounded">
                            <h3><?php echo $visit_stats['total_visits'] ?? 0; ?></h3>
                            <p>Total Visits</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-success text-white rounded">
                            <h3><?php echo $visit_stats['logged_in_visits'] ?? 0; ?></h3>
                            <p>Logged-in Users</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-info text-white rounded">
                            <h3><?php echo $visit_stats['non_logged_in_visits'] ?? 0; ?></h3>
                            <p>Guest Visitors</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-warning text-white rounded">
                            <h3><?php echo $visit_stats['unique_visitors'] ?? 0; ?></h3>
                            <p>Unique Visitors</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Top Pages</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($visit_stats['top_pages'])): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Page URL</th>
                                    <th>Visits</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($visit_stats['top_pages'] as $page): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($page['page_url']); ?></td>
                                        <td><?php echo $page['visits']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No page visit data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Visit Trend (Last 30 Days)</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($visit_trend)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total Visits</th>
                                    <th>Logged-in Users</th>
                                    <th>Guest Visitors</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($visit_trend as $trend): ?>
                                    <tr>
                                        <td><?php echo $trend['date']; ?></td>
                                        <td><?php echo $trend['total_visits']; ?></td>
                                        <td><?php echo $trend['logged_in_visits']; ?></td>
                                        <td><?php echo $trend['non_logged_in_visits']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No trend data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>