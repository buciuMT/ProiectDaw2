<?php 
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Reservation History</h1>
    <a href="/books" class="btn btn-primary">Browse Books</a>
</div>

<?php if (!empty($history)): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Reserved Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                        <td><?php echo htmlspecialchars($item['author']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($item['reserved_at'])); ?></td>
                        <td><?php echo !empty($item['due_date']) ? date('M j, Y', strtotime($item['due_date'])) : 'N/A'; ?></td>
                        <td>
                            <?php if ($item['status'] === 'active'): ?>
                                <span class="badge bg-warning text-dark">Active</span>
                            <?php elseif ($item['status'] === 'completed'): ?>
                                <span class="badge bg-success">Returned</span>
                            <?php elseif ($item['status'] === 'cancelled'): ?>
                                <span class="badge bg-secondary">Cancelled</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <h4>No reservation history</h4>
        <p>Your reservation history will appear here once you start reserving books.</p>
        <a href="/books" class="btn btn-primary">Browse Books</a>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>