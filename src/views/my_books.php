<?php 
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>My Borrowed Books</h1>
    <a href="/books" class="btn btn-primary">Browse Books</a>
</div>

<?php if (!empty($borrowedBooks)): ?>
    <div class="row">
        <?php foreach ($borrowedBooks as $book): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <?php if (!empty($book['cover_image'])): ?>
                        <img src="/<?php echo htmlspecialchars($book['cover_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($book['title']); ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <span class="text-muted">No Cover</span>
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <p class="card-text">by <?php echo htmlspecialchars($book['author']); ?></p>
                        <p class="card-text"><small class="text-muted">Borrowed on: <?php echo date('M j, Y', strtotime($book['reserved_at'])); ?></small></p>
                        <?php if (!empty($book['due_date'])): ?>
                            <p class="card-text <?php echo $book['status_indicator'] === 'overdue' ? 'text-danger' : ''; ?>">
                                Due date: <?php echo date('M j, Y', strtotime($book['due_date'])); ?>
                                <?php if ($book['status_indicator'] === 'overdue'): ?>
                                    <span class="badge bg-danger">Overdue</span>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                        <div class="mt-auto">
                            <!-- Return button removed as this is handled by employees -->
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <h4>You haven't borrowed any books yet</h4>
        <p>Reserve and borrow books from our collection to see them here.</p>
        <a href="/books" class="btn btn-primary">Browse Books</a>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>