<!DOCTYPE html>
<html>
<head>
    <title>Thank You for Contacting Lib4All</title>
</head>
<body>
    <h1>Thank You for Contacting Lib4All!</h1>
    <p>Hi <?php echo htmlspecialchars($name ?? 'there'); ?>,</p>
    <p>We have received your message and will get back to you as soon as possible.</p>
    <p>Here's a summary of your message:</p>
    <ul>
        <li><strong>Subject:</strong> <?php echo htmlspecialchars($subject ?? ''); ?></li>
        <li><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($message ?? '')); ?></li>
    </ul>
    <p>Best regards,<br>The Lib4All Team</p>
</body>
</html>