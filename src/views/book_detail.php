<?php
ob_start();
?>

<div class="row">
    <div class="col-md-4">
        <?php
        $coverImage = !empty($book['cover_image']) ? htmlspecialchars($book['cover_image']) : 'https://via.placeholder.com/300x450?text=No+Cover';
        echo '<img src="' . $coverImage . '" class="img-fluid" alt="' . htmlspecialchars($book['title']) . '">';
        ?>
    </div>
    <div class="col-md-8">
        <h1><?php echo htmlspecialchars($book['title']); ?></h1>
        <p class="lead">by <?php echo htmlspecialchars($book['author']); ?></p>
        <p>ISBN: <?php echo htmlspecialchars($book['isbn'] ?? 'N/A'); ?></p>
        <p>Published Year: <?php echo htmlspecialchars($book['published_year'] ?? 'N/A'); ?></p>
        <p>Genre: <?php echo htmlspecialchars($book['genre'] ?? 'N/A'); ?></p>
        <p>Available copies: <?php echo htmlspecialchars($book['copies_available']) . '/' . htmlspecialchars($book['copies_total']); ?></p>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['reserved']) && $_GET['reserved'] === 'success'): ?>
            <div class="alert alert-success">Book reserved successfully!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['cancelled']) && $_GET['cancelled'] === 'success'): ?>
            <div class="alert alert-success">Reservation cancelled successfully!</div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="/books" class="btn btn-primary">Back to Books</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($isReserved): ?>
                    <button class="btn btn-secondary" disabled>Already Reserved</button>
                <?php elseif ($book['copies_available'] > 0): ?>
                    <form method="POST" action="/books/reserve?id=<?php echo $book['id']; ?>" class="d-inline">
                        <button type="submit" class="btn btn-success">Reserve Book</button>
                    </form>
                <?php else: ?>
                    <button class="btn btn-secondary" disabled>No Copies Available</button>
                <?php endif; ?>
            <?php else: ?>
                <a href="/login" class="btn btn-success">Login to Reserve</a>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="/books/edit?id=<?php echo $book['id']; ?>" class="btn btn-secondary">Edit</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = htmlspecialchars($book['title']) . ' - Lib4All';
include __DIR__ . '/layout.php';
?>