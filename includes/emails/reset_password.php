<?php
/**
 * Generate Professional Full-Bleed Password Reset HTML Email Template (Plus Jakarta Sans + Playfair Display)
 */
function getPasswordResetEmailTemplate($user_name, $reset_url, $expires_minutes = 60) {
    $current_year = date('Y');

    return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Your Password - Scriptly</title>
    <!-- Load Google Fonts for Web Email Clients -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">
    <style type="text/css">
        body, table, td, p, a, li, blockquote { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #f8f7f5; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #0A2342; }
        
        @media only screen and (max-width: 600px) {
            .outer-cell { padding: 0 !important; }
            .email-container { width: 100% !important; max-width: 100% !important; border-radius: 0 !important; border: 0 !important; }
            .header-cell { padding: 24px 16px !important; }
            .content-cell { padding: 24px 16px !important; }
            .footer-cell { padding: 20px 16px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f8f7f5; width: 100% !important; min-height: 100vh;">

    <!-- Outer Wrapper Table -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="background-color: #f8f7f5; width: 100%; height: 100%; min-height: 100vh;">
        <tr>
            <td align="center" valign="top" class="outer-cell" style="padding: 36px 0; background-color: #f8f7f5;">
                
                <!-- Inner Main Card Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="email-container" style="max-width: 580px; width: 100%; background-color: #ffffff; border-radius: 6px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 20px rgba(10, 35, 66, 0.07);">
                    
                    <!-- Professional Dark Header -->
                    <tr>
                        <td align="center" bgcolor="#0A2342" class="header-cell" style="padding: 32px 36px; background-color: #0A2342; text-align: center;">
                            <a href="https://scriptly.com" target="_blank" style="color: #ffffff; font-size: 28px; font-weight: 800; text-decoration: none; letter-spacing: -0.5px; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif; display: inline-flex; align-items: center;">
                                <span style="font-size: 30px; color: #60a5fa; margin-right: 6px; font-family: sans-serif; font-weight: 900; line-height: 1;">&#8734;</span>
                                <span>Scriptly</span>
                                <span style="display: inline-block; width: 20px; height: 20px; background-color: #2563eb; color: #ffffff; border-radius: 50%; text-align: center; font-size: 11px; font-weight: bold; line-height: 20px; vertical-align: middle; margin-left: 5px;">✓</span>
                            </a>
                            <div style="color: #93c5fd; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-top: 6px; font-family: 'Plus Jakarta Sans', sans-serif;">Security & Password Recovery</div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td class="content-cell" style="padding: 40px 36px; text-align: left; background-color: #ffffff;">
                            
                            <h1 style="margin: 0 0 14px 0; color: #0A2342; font-size: 24px; font-weight: 800; line-height: 1.3; font-family: 'Playfair Display', Georgia, serif; letter-spacing: -0.3px;">Reset Your Password</h1>
                            
                            <p style="margin: 0 0 16px 0; color: #334155; font-size: 15px; font-weight: 600; line-height: 1.6; font-family: 'Plus Jakarta Sans', sans-serif;">Hello <strong>{$user_name}</strong>,</p>
                            
                            <p style="margin: 0 0 24px 0; color: #475569; font-size: 14px; font-weight: 400; line-height: 1.6; font-family: 'Plus Jakarta Sans', sans-serif;">
                                We received a request to reset the password for your Scriptly account. Click the button below to choose a new password:
                            </p>

                            <!-- Single Clear Reset CTA Button -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 28px 0 24px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{$reset_url}" target="_blank" style="display: inline-block; padding: 16px 38px; background-color: #0A2342; color: #ffffff !important; font-size: 14px; font-weight: 800; text-decoration: none; border-radius: 50px; box-shadow: 0 4px 14px rgba(10, 35, 66, 0.18); font-family: 'Plus Jakarta Sans', sans-serif;">Reset Password →</a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Fallback Link Box -->
                            <p style="margin: 20px 0 6px 0; color: #64748b; font-size: 12px; line-height: 1.5; font-family: 'Plus Jakarta Sans', sans-serif;">
                                If the button above does not work, copy and paste this link into your browser:
                            </p>
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 12px; font-size: 11px; word-break: break-all; color: #2563eb; font-family: 'JetBrains Mono', Consolas, monospace;">
                                {$reset_url}
                            </div>

                            <!-- Notice Box -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 24px;">
                                <tr>
                                    <td bgcolor="#eff6ff" style="padding: 16px; background-color: #eff6ff; border: 1px solid #dbeafe; border-left: 4px solid #2563eb; border-radius: 4px; color: #1e40af; font-size: 12px; line-height: 1.6; font-family: 'Plus Jakarta Sans', sans-serif;">
                                        <strong style="font-weight: 700;">⏰ Security Expiration:</strong> This password reset link expires in <strong>{$expires_minutes} minutes</strong>. If you did not request a password reset, please ignore this email.
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer Baseline -->
                    <tr>
                        <td align="center" bgcolor="#f8fafc" class="footer-cell" style="padding: 24px 36px; background-color: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center; color: #94a3b8; font-size: 12px; line-height: 1.5; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <p style="margin: 0 0 4px 0; font-weight: 600;">© {$current_year} Scriptly Platform Inc. All rights reserved.</p>
                            <p style="margin: 0;">Developed with excellence by <strong style="color: #0A2342; font-weight: 700;">Scriptly Team</strong></p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
HTML;
}
