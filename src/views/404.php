<?php
$title = 'Page Not Found - Lib4All';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <h1>404 - Page Not Found</h1>
        <p>The page you are looking for does not exist.</p>
        <a href="/" class="btn btn-primary">Go Home</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>