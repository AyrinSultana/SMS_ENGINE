<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title> Authorization Required for Sending SMS</title>
</head>
<body>
   

    <p>The following SMS requires your authorization for Sending the sms :</p>

    <p>
        <strong>Template Name:</strong> {{ $templateName }}
    </p>

    <p>Please log in to the system to review and approve the SMS.</p>

    <p>Thank you,<br>
    {{ config('mail.from.name') }}</p>
</body>
</html>