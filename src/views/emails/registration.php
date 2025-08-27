<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Lib4All!</title>
</head>
<body>
    <h1>Welcome to Lib4All!</h1>
    <p>Hi <?php echo htmlspecialchars($name ?? 'there'); ?>,</p>
    <p>Thank you for registering with Lib4All. You can now start reserving books!</p>
    <p>Best regards,<br>The Lib4All Team</p>
</body>
</html>