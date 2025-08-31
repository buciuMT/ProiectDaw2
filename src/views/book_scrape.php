<?php 
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Scrape Book</h1>
    <a href="/books" class="btn btn-secondary">Back to Books</a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Scrape Book Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/books/scrape-store">
                    <div class="mb-3">
                        <label for="url" class="form-label">Book URL</label>
                        <input type="url" class="form-control" id="url" name="url" placeholder="https://example.com/book-page" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Scrape Book</button>
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