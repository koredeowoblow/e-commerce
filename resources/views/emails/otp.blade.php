<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email</title>
</head>
<body>
    <p>Hello {{ $name }},</p>

    <p>Thank you for registering. Please click the button below to verify your email address:</p>

    <p><a href="{{ $verificationUrl }}" style="padding: 10px 20px; background: #2d3748; color: #fff; text-decoration: none;">Verify Email</a></p>

    <p>If you did not create an account, you can ignore this email.</p>
</body>
</html>
