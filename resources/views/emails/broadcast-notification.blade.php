
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>New School Broadcast</title>
</head>

<body style="margin:0; padding:0; background:#f1f5f9; font-family:Arial, Helvetica, sans-serif; color:#334155;">

    <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
           style="background:#f1f5f9; padding:35px 12px;">

        <tr>
            <td align="center">

                <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                       style="max-width:600px; background:#ffffff; border-radius:12px; overflow:hidden;">

                    <!-- Header -->

                    <tr>
                        <td style="background:#0f172a; padding:28px 30px; text-align:center;">

                            <h1 style="margin:0; color:#ffffff; font-size:23px;">
                                Noble Hall
                            </h1>

                            <p style="margin:7px 0 0; color:#cbd5e1; font-size:13px;">
                                Leadership Academy
                            </p>

                            <p style="margin:15px 0 0; color:#93c5fd; font-size:12px; font-weight:bold; letter-spacing:1px;">
                                eRESOURCE NOTIFICATION
                            </p>

                        </td>
                    </tr>

                    <!-- Main Content -->

                    <tr>
                        <td style="padding:35px 30px;">

                            <p style="font-size:15px; margin-top:0;">
                                Dear {{ $teacher->name }},
                            </p>

                            <p style="font-size:14px; line-height:1.7;">
                                You have received a new official school broadcast
                                from Noble Hall Leadership Academy.
                            </p>

                            <div style="margin:25px 0; padding:20px; background:#f8fafc; border-left:4px solid #2563eb; border-radius:5px;">

                                <p style="margin:0 0 8px; color:#64748b; font-size:12px; font-weight:bold; text-transform:uppercase;">
                                    Broadcast Title
                                </p>

                                <h2 style="margin:0; color:#0f172a; font-size:20px;">
                                    {{ $broadcast->title }}
                                </h2>

                                <p style="margin:15px 0 0; color:#64748b; font-size:13px;">
                                    From:
                                    {{ $broadcast->sender?->name ?? 'School Administration' }}
                                </p>

                                <p style="margin:5px 0 0; color:#64748b; font-size:13px;">
                                    Date:
                                    {{ $broadcast->sent_at?->format('d M Y, h:i A') }}
                                </p>

                            </div>

                            <p style="font-size:13px; font-weight:bold; color:#475569;">
                                Message Preview
                            </p>

                            <p style="font-size:14px; line-height:1.8; color:#475569;">
                                {{ $messagePreview }}
                            </p>

                            <p style="font-size:13px; line-height:1.7; color:#64748b;">
                                Please visit the eResource platform to read the
                                complete message, access any attachments or
                                Google Sheets, and acknowledge the broadcast
                                where required.
                            </p>

                            <!-- Button -->

                            <table cellpadding="0" cellspacing="0" role="presentation"
                                   style="margin:28px auto;">

                                <tr>
                                    <td align="center" style="background:#2563eb; border-radius:7px;">

                                        <a href="{{ $broadcastUrl }}"
                                           style="display:inline-block; padding:15px 28px; color:#ffffff; text-decoration:none; font-size:14px; font-weight:bold;">

                                            Read Full Message

                                        </a>

                                    </td>
                                </tr>

                            </table>

                            <p style="font-size:12px; color:#64748b; line-height:1.7;">
                                If the button does not work, copy and paste this
                                address into your browser:
                            </p>

                            <p style="font-size:12px; word-break:break-all;">
                                <a href="{{ $broadcastUrl }}" style="color:#2563eb;">
                                    {{ $broadcastUrl }}
                                </a>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->

                    <tr>
                        <td style="background:#f8fafc; padding:22px 30px; text-align:center;">

                            <p style="margin:0; font-size:12px; color:#64748b;">
                                Noble Hall Leadership Academy
                            </p>

                            <p style="margin:7px 0 0; font-size:11px; color:#94a3b8;">
                                This is an automated notification from the
                                Noble Hall eResource platform.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>

    </table>

</body>
</html>
