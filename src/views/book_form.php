<?php
$book = $book ?? null;
$isEdit = !empty($book);
$title = $isEdit ? 'Edit Book' : 'Add New Book';

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3><?php echo $title; ?></h3>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo $isEdit ? '/books/update?id=' . $book['id'] : '/books/store'; ?>" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($book['title'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label">Author</label>
                        <input type="text" class="form-control" id="author" name="author" value="<?php echo htmlspecialchars($book['author'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="isbn" class="form-label">ISBN</label>
                        <input type="text" class="form-control" id="isbn" name="isbn" value="<?php echo htmlspecialchars($book['isbn'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="published_year" class="form-label">Published Year</label>
                        <input type="number" class="form-control" id="published_year" name="published_year" value="<?php echo htmlspecialchars($book['published_year'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="genre" class="form-label">Genre</label>
                        <input type="text" class="form-control" id="genre" name="genre" value="<?php echo htmlspecialchars($book['genre'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="copies_total" class="form-label">Total Copies</label>
                        <input type="number" class="form-control" id="copies_total" name="copies_total" value="<?php echo htmlspecialchars($book['copies_total'] ?? '1'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="copies_available" class="form-label">Available Copies</label>
                        <input type="number" class="form-control" id="copies_available" name="copies_available" value="<?php echo htmlspecialchars($book['copies_available'] ?? '1'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="cover_image" class="form-label">Cover Image</label>
                        <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                        <?php if (!empty($book['cover_image'])): ?>
                            <div class="mt-2">
                                <img src="/<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Current cover" style="max-width: 200px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Update Book' : 'Add Book'; ?></button>
                    <a href="/books" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>