<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4; font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding:40px 0;">
    <tr>
        <td align="center">
            <table width="500" cellpadding="0" cellspacing="0"
                   style="background-color:#ffffff; border-radius:10px; padding:40px;">

                <tr>
                    <td align="center">
                        <h2 style="color:#333;">Email Verification</h2>
                    </td>
                </tr>

                <tr>
                    <td style="padding-top:20px; color:#555; font-size:16px; line-height:24px;">
                        Use the following verification code to continue:
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:30px 0;">
                        <div style="
                            display:inline-block;
                            padding:15px 30px;
                            background-color:#2563eb;
                            color:white;
                            font-size:32px;
                            font-weight:bold;
                            letter-spacing:8px;
                            border-radius:8px;">
                            {{ $otp }}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="color:#555; font-size:15px; line-height:24px;">
                        This code will expire in <strong>5 minutes</strong>.
                    </td>
                </tr>

                <tr>
                    <td style="padding-top:20px; color:#777; font-size:14px; line-height:22px;">
                        If you did not request this code, you can safely ignore this email.
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
