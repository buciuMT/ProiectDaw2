<!DOCTYPE html>
<html>
<head>
    <title>Verify your email address for Lib4All</title>
</head>
<body>
    <h1>Welcome to Lib4All!</h1>
    <p>Hi <?php echo htmlspecialchars($name ?? 'there'); ?>,</p>
    <p>Thank you for registering with Lib4All. Please click the link below to verify your email address:</p>
    <p><a href="<?php echo htmlspecialchars($verificationLink); ?>">Verify Email Address</a></p>
    <p>If you cannot click the link, please copy and paste the following URL into your browser:</p>
    <p><?php echo htmlspecialchars($verificationLink); ?></p>
    <p>Best regards,<br>The Lib4All Team</p>
</body>
</html>