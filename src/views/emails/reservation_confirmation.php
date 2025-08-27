<!DOCTYPE html>
<html>
<head>
    <title>Book Reservation Confirmation</title>
</head>
<body>
    <h1>Book Reservation Confirmation</h1>
    <p>Hi <?php echo htmlspecialchars($name ?? 'there'); ?>,</p>
    <p>You have successfully reserved the book: <strong><?php echo htmlspecialchars($bookTitle ?? 'Unknown Book'); ?></strong></p>
    <p>Please pick it up within 3 days.</p>
    <p>Best regards,<br>The Lib4All Team</p>
</body>
</html>