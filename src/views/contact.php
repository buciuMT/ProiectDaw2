<?php
$title = 'Contact Us - Lib4All';

ob_start();
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="text-center mb-4">Contact Us</h1>
            
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['flash_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
            <?php endif; ?>
            
            <div class="card mb-4">
                <div class="card-body">
                    <p class="card-text mb-4">Have questions or feedback? We'd love to hear from you! Fill out the form below and we'll get back to you as soon as possible.</p>
                    
                    <form method="POST" action="/contact">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Your name">
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="your.email@example.com">
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required placeholder="What is this regarding?">
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Your message here..."></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="fs-1 mb-2">📍</div>
                            <h5 class="card-title">Address</h5>
                            <p class="card-text">123 Library Street<br>Bookville, BK 12345</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="fs-1 mb-2">📞</div>
                            <h5 class="card-title">Phone</h5>
                            <p class="card-text">+1 (555) 123-4567</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="fs-1 mb-2">✉️</div>
                            <h5 class="card-title">Email</h5>
                            <p class="card-text">info@lib4all.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>