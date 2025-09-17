<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Template Authorization Required</title>
</head>
<body>

    <p>The following SMS template requires your authorization:</p>

    <p>
        <strong>Template Name:</strong> {{ $templateName }}
    </p>

    <p>Please log in to the system to review and approve the template.</p>

    <p>Thank you,<br>
    {{ config('mail.from.name') }}</p>
</body>
</html>
