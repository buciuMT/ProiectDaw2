<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Lib4All!</title>
</head>
<body>
    <h1>Welcome to Lib4All!</h1>
    <p>Hi <?php echo htmlspecialchars($name ?? 'there'); ?>,</p>
    <p>Thank you for registering with Lib4All. You're almost ready to start reserving books!</p>
    <p>Please check your email for a verification link to complete your registration.</p>
    <p>Best regards,<br>The Lib4All Team</p>
</body>
</html>