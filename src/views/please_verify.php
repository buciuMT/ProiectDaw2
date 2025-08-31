<?php 
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Email Verification Required</h3>
            </div>
            <div class="card-body text-center">
                <div class="mb-4">
                    <i class="fas fa-envelope-open-text fa-3x text-warning"></i>
                </div>
                <h4>Email Verification Required</h4>
                <p class="lead">Please check your email for a verification link to complete your registration.</p>
                <p>You must verify your email address before you can log in to your account.</p>
                
                <div class="alert alert-info mt-4" role="alert">
                    <h5><i class="fas fa-info-circle"></i> Didn't receive the email?</h5>
                    <ul class="text-start">
                        <li>Check your spam or junk folder</li>
                        <li>Make sure you entered the correct email address</li>
                        <li>Wait a few minutes for the email to arrive</li>
                    </ul>
                </div>
                
                <div class="mt-4">
                    <a href="/resend-verification" class="btn btn-primary">Resend Verification Email</a>
                    <a href="/login" class="btn btn-secondary">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>