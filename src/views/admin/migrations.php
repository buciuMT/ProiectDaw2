<?php 
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Database Migrations</h1>
    <a href="/admin/dashboard" class="btn btn-secondary">Back to Dashboard</a>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Available Migrations</h5>
        <?php if (!empty($migrations)): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Size</th>
                            <th>Last Modified</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($migrations as $migration): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($migration['name']); ?></td>
                                <td><?php echo number_format($migration['size']); ?> bytes</td>
                                <td><?php echo htmlspecialchars($migration['modified']); ?></td>
                                <td>
                                    <a href="/admin/migrations/view?file=<?php echo urlencode($migration['name']); ?>" class="btn btn-sm btn-info">View</a>
                                    <a href="/admin/migrations/run?file=<?php echo urlencode($migration['name']); ?>" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to run this migration?')">Run</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No migrations found.</p>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>