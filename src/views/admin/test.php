<?php 
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>System Tests</h1>
    <a href="/admin/dashboard" class="btn btn-secondary">Back to Dashboard</a>
</div>

<div class="card">
    <div class="card-header">
        <h5>Available Tests</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Email Configuration Test</h5>
                        <p class="card-text">Test the email configuration without sending an actual email.</p>
                        <button class="btn btn-primary test-btn" data-test="mail">Run Test</button>
                        <div class="test-result mt-3" id="mail-result" style="display: none;"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Send Test Email</h5>
                        <p class="card-text">Send a test email to any email address.</p>
                        <div class="mb-3">
                            <label for="test-email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="test-email" placeholder="recipient@example.com">
                        </div>
                        <button class="btn btn-primary test-btn" data-test="send-mail">Send Test Email</button>
                        <div class="test-result mt-3" id="send-mail-result" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to test buttons
    document.querySelectorAll('.test-btn').forEach(button => {
        button.addEventListener('click', function() {
            const test = this.getAttribute('data-test');
            runTest(test);
        });
    });
    
    function runTest(test) {
        // Show loading state
        const resultDiv = document.getElementById(test + '-result');
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = '<div class="alert alert-info">Running test...</div>';
        
        // Prepare URL and parameters
        let url = '/api/test?test=' + test;
        
        // For send-mail test, add the email parameter
        if (test === 'send-mail') {
            const email = document.getElementById('test-email').value;
            if (!email) {
                resultDiv.innerHTML = '<div class="alert alert-danger">Please enter an email address.</div>';
                return;
            }
            url += '&email=' + encodeURIComponent(email);
        }
        
        // Make API call
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    resultDiv.innerHTML = `
                        <div class="alert alert-success">
                            <h6>Test Result: ${data.message}</h6>
                            ${data.data ? '<pre>' + JSON.stringify(data.data, null, 2) + '</pre>' : ''}
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <h6>Test Failed: ${data.message}</h6>
                        </div>
                    `;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h6>Error: ${error.message}</h6>
                    </div>
                `;
            });
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>