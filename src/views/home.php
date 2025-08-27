<?php
$title = 'Home - Lib4All';

// Function to render a book card
function renderBookCard($book, $showEditButton = false) {
    $coverImage = !empty($book['cover_image']) ? '/' . htmlspecialchars($book['cover_image']) : 'https://via.placeholder.com/200x300?text=No+Cover';
    $editButton = $showEditButton && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin' 
        ? '<a href="/books/edit?id=' . $book['id'] . '" class="btn btn-sm btn-primary mt-2">Edit</a>' 
        : '';
    
    return '
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100 book-card">
                <a href="/books/detail?id=' . $book['id'] . '" class="text-decoration-none">
                    <img src="' . $coverImage . '" class="card-img-top" alt="' . htmlspecialchars($book['title']) . '" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-dark">' . htmlspecialchars($book['title']) . '</h5>
                        <p class="card-text flex-grow-1 text-dark">by ' . htmlspecialchars($book['author']) . '</p>
                        <div class="mt-auto">
                            <p class="card-text"><small class="text-muted">Published: ' . htmlspecialchars($book['published_year'] ?? 'N/A') . '</small></p>
                            <p class="card-text">Available: ' . htmlspecialchars($book['copies_available']) . '/' . htmlspecialchars($book['copies_total']) . '</p>
                            ' . $editButton . '
                        </div>
                    </div>
                </a>
            </div>
        </div>';
}

ob_start();
?>

<div class="jumbotron">
    <h1 class="display-4">Lib4All Library</h1>
    <p class="lead">Welcome to Lib4All, a library management system for everyone</p>
    <hr class="my-4">
    <h2>Featured Books</h2>
</div>

<div class="row">
    <?php
    if (!empty($books)) {
        foreach ($books as $book) {
            echo renderBookCard($book, true);
        }
    } else {
        echo '<p>No books found in the library.</p>';
    }
    ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>