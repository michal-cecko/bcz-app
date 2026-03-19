<!DOCTYPE html>
<html lang="sk" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>{{ $emailSubject ?? '' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap');

        :root {
            color-scheme: light dark;
            supported-color-schemes: light dark;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            background-color: #f0f0f0;
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        table { border-spacing: 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { border: 0; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
        a { color: #2563eb; }
        h1, h2, h3 { color: #1A1A1A; margin: 0; }
        p { color: #555555; margin: 0; }
        ul, ol { color: #555555; }

        @media (prefers-color-scheme: dark) {
            body, .body-bg { background-color: #0A0A0A !important; }
            .header-bg { background-color: #111111 !important; border-bottom-color: #222222 !important; }
            .content-bg { background-color: #0D0D0D !important; }
            h1, h2, h3, .heading-text { color: #f9fafb !important; }
            p, .body-text { color: #AAAAAA !important; }
            a { color: #60a5fa !important; }
            a.btn-cta { color: #FFFFFF !important; }
            .divider-line { border-top-color: #222222 !important; }
            .footer-bg { background-color: #111111 !important; border-top-color: #222222 !important; }
            .footer-team-name { color: #FFFFFF !important; }
            .footer-contact { color: #888888 !important; }
            .footer-divider { background-color: #222222 !important; }
            .footer-copyright { color: #555555 !important; }
            .powered-divider { background-color: #1A1A1A !important; }
            .powered-text { color: #444444 !important; }
            .powered-brand { color: #666666 !important; }
            .bcz-logo-light { display: none !important; }
            .bcz-logo-dark { display: inline !important; }
            .info-box { background-color: #1A1A1A !important; }
        }

        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; border-radius: 0 !important; }
            .content-padding { padding: 24px 16px !important; }
            .header-padding { padding: 20px 16px !important; }
            .footer-padding { padding: 24px 16px !important; }
        }
    </style>
</head>
<body class="body-bg" style="margin: 0; padding: 0; background-color: #f0f0f0;">

<table role="presentation" cellpadding="0" cellspacing="0" width="100%" class="body-bg" style="background-color: #f0f0f0;">
    <tr>
        <td align="center" style="padding: 32px 16px;">

            <table role="presentation" cellpadding="0" cellspacing="0" width="640" class="container" style="max-width: 640px; width: 100%; border-radius: 16px; overflow: hidden;">

                <!-- Header -->
                <tr>
                    <td class="header-bg header-padding" align="center" style="background-color: #FFFFFF; padding: 24px 40px; border-bottom: 1px solid #E0E0E0;">
                        @if(!empty($teamLogoUrl))
                            <a href="{{ $teamUrl ?? '#' }}" target="_blank" style="text-decoration: none;">
                                <img src="{{ $teamLogoUrl }}" alt="{{ $teamName ?? '' }}" style="max-width: 200px; max-height: 60px; height: auto; display: block;" />
                            </a>
                        @endif
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td class="content-bg content-padding" style="background-color: #FAFAFA; padding: 40px;">
                        @hasSection('content')
                            @yield('content')
                        @elseif(!empty($emailBody))
                            {!! $emailBody !!}
                        @endif
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td class="footer-bg footer-padding" style="background-color: #FFFFFF; padding: 32px 40px; border-top: 1px solid #E0E0E0;">
                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                            @if(!empty($teamName))
                                <tr>
                                    <td align="center" style="padding-bottom: 20px;">
                                        <span class="footer-team-name" style="font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 16px; font-weight: 700; color: #1A1A1A; letter-spacing: 1px;">
                                            {{ $teamName }}
                                        </span>
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td style="padding-bottom: 20px;">
                                    <div class="footer-divider" style="height: 1px; background-color: #E5E5E5;"></div>
                                </td>
                            </tr>

                            @if(!empty($teamEmail) || !empty($teamPhone) || !empty($teamWebsite))
                                <tr>
                                    <td align="center" style="padding-bottom: 20px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0">
                                            @if(!empty($teamEmail))
                                                <tr>
                                                    <td style="padding: 5px 0;">
                                                        <span class="footer-contact" style="font-family: 'DM Sans', sans-serif; font-size: 12px; color: #777777;">&#9993;&nbsp; {{ $teamEmail }}</span>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(!empty($teamPhone))
                                                <tr>
                                                    <td style="padding: 5px 0;">
                                                        <span class="footer-contact" style="font-family: 'DM Sans', sans-serif; font-size: 12px; color: #777777;">&#9742;&nbsp; {{ $teamPhone }}</span>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(!empty($teamWebsite))
                                                <tr>
                                                    <td style="padding: 5px 0;">
                                                        <span class="footer-contact" style="font-family: 'DM Sans', sans-serif; font-size: 12px; color: #777777;">&#127760;&nbsp; {{ $teamWebsite }}</span>
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding-bottom: 20px;">
                                        <div class="footer-divider" style="height: 1px; background-color: #E5E5E5;"></div>
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td align="center" style="padding-bottom: 8px;">
                                    <span class="footer-copyright" style="font-family: 'DM Sans', sans-serif; font-size: 11px; color: #999999;">&copy; {{ date('Y') }} {{ $teamName ?? config('app.name') }}. Všetky práva vyhradené.</span>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding-top: 16px;">
                                    <div class="powered-divider" style="height: 1px; background-color: #EEEEEE; margin-bottom: 12px;"></div>
                                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td align="center">
                                                <span class="powered-text" style="font-family: 'DM Sans', sans-serif; font-size: 10px; color: #AAAAAA;">Odoslané cez</span>
                                                &nbsp;
                                                <a href="{{ config('app.url') }}" target="_blank" style="text-decoration: none; vertical-align: middle;">
                                                    <img class="bcz-logo-light" src="{{ asset('images/bcz-logo.png') }}" alt="BCZ App" height="14" style="height: 14px; width: auto; vertical-align: middle; display: inline;" />
                                                    <img class="bcz-logo-dark" src="{{ asset('images/bcz-logo-white.png') }}" alt="BCZ App" height="14" style="height: 14px; width: auto; vertical-align: middle; display: none;" />
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
