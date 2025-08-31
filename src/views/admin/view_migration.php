<?php 
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>View Migration</h1>
    <a href="/admin/migrations" class="btn btn-secondary">Back to Migrations</a>
</div>

<div class="card">
    <div class="card-header">
        <h5><?php echo htmlspecialchars($filename); ?></h5>
    </div>
    <div class="card-body">
        <pre><code><?php echo htmlspecialchars($content); ?></code></pre>
    </div>
    <div class="card-footer">
        <a href="/admin/migrations/run?file=<?php echo urlencode($filename); ?>" class="btn btn-success" onclick="return confirm('Are you sure you want to run this migration?')">Run Migration</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>