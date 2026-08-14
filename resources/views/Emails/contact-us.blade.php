<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>New Contact Message</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f7fb;
            font-family: Arial, Helvetica, sans-serif;
            color: #1f2937;
        }

        .wrapper {
            width: 100%;
            padding: 40px 15px;
            box-sizing: border-box;
        }

        .container {
            max-width: 620px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
        }

        .header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }

        .header h1 {
            margin: 0;
            font-size: 23px;
            font-weight: 700;
        }

        .header p {
            margin: 8px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .content {
            padding: 30px;
        }

        .label {
            display: block;
            margin-bottom: 7px;
            color: #6b7280;
            font-size: 13px;
            font-weight: 600;
        }

        .student {
            margin-bottom: 20px;
        }

        .student-name {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }

        .sent-at {
            margin-top: 5px;
            color: #9ca3af;
            font-size: 13px;
        }

        .message-section {
            margin-top: 25px;
        }

        .message-box {
            background-color: #f8fafc;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #2563eb;
            border-radius: 10px;
            padding: 20px;
        }

        .message {
            margin: 0;
            color: #374151;
            font-size: 15px;
            line-height: 1.8;
            white-space: pre-line;
        }

        .reply {
            text-align: center;
            margin-top: 30px;
        }

        .reply-button {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            padding: 13px 30px;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 700;
        }

        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e5e7eb;
            padding: 18px 25px;
            text-align: center;
        }

        .footer p {
            margin: 0;
            color: #9ca3af;
            font-size: 12px;
        }

        @media only screen and (max-width: 600px) {

            .wrapper {
                padding: 15px 8px;
            }

            .header {
                padding: 25px 20px;
            }

            .content {
                padding: 22px;
            }
        }
    </style>
</head>

<body>

<div class="wrapper">

    <div class="container">

        <!-- Header -->
        <div class="header">

            <h1>New Contact Message</h1>

            <p>
                A new message has been received from a student
            </p>

        </div>


        <!-- Content -->
        <div class="content">

            <!-- Student Information -->
            <div class="student">

                <span class="label">
                    From
                </span>

                <div class="student-name">
                    {{ $studentName }}
                </div>

                <div class="sent-at">
                    Sent on {{ $sentAt }}
                </div>

            </div>


            <!-- Message -->
            <div class="message-section">

                <span class="label">
                    Message
                </span>

                <div class="message-box">

                    <p class="message">
                        {{ $text }}
                    </p>

                </div>

            </div>


            <!-- Reply -->
            <div class="reply">

                <a
                    href="mailto:{{ $studentEmail }}"
                    class="reply-button"
                >
                    Reply to Student
                </a>

            </div>

        </div>


        <!-- Footer -->
        <div class="footer">

            <p>
                English Language Education System
            </p>

        </div>

    </div>

</div>

</body>
</html>
```
