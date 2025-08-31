<?php 
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>My Reservations</h1>
    <a href="/books" class="btn btn-primary">Browse Books</a>
</div>

<?php if (!empty($reservations)): ?>
    <div class="row">
        <?php foreach ($reservations as $reservation): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <?php if (!empty($reservation['cover_image'])): ?>
                        <img src="/<?php echo htmlspecialchars($reservation['cover_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($reservation['title']); ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <span class="text-muted">No Cover</span>
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($reservation['title']); ?></h5>
                        <p class="card-text">by <?php echo htmlspecialchars($reservation['author']); ?></p>
                        <p class="card-text"><small class="text-muted">Reserved on: <?php echo date('M j, Y', strtotime($reservation['reserved_at'])); ?></small></p>
                        <?php if (!empty($reservation['due_date'])): ?>
                            <p class="card-text">Due date: <?php echo date('M j, Y', strtotime($reservation['due_date'])); ?></p>
                        <?php endif; ?>
                        <div class="mt-auto">
                            <form method="POST" action="/cancel-reservation" class="d-inline">
                                <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this reservation?')">Cancel Reservation</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <h4>You don't have any active reservations</h4>
        <p>Reserve books from our collection to see them here.</p>
        <a href="/books" class="btn btn-primary">Browse Books</a>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>