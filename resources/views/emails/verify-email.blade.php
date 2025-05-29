<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email</title>
</head>
<body>
    <p>Hi {{ $name }},</p>

    <p>Thank you for registering. Please click the link below to verify your email address:</p>

    <p>
        <a href="{{ $verificationUrl }}" style="padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none;">
            Verify Email
        </a>
    </p>

    <p>If you didn’t request this, just ignore this email.</p>

    <p>Thanks,<br>Student eMarket Team</p>
</body>
</html>
