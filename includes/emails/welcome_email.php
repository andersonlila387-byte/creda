<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

if (file_exists(__DIR__ . '/../../config/smtp.php')) {
    require_once __DIR__ . '/../../config/smtp.php';
}

/**
 * Generate Cliniconnect Welcome Email HTML Template
 */
function getWelcomeEmailTemplate($user_name) {
    $current_year = date('Y');
    
    return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Welcome to Cliniconnect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800&display=swap" rel="stylesheet">
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
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" bgcolor="#0A2342" class="header-cell" style="padding: 32px 36px; background-color: #0A2342; text-align: center;">
                            <a href="#" style="color: #ffffff; font-size: 26px; font-weight: 800; text-decoration: none; letter-spacing: -0.5px; font-family: 'Plus Jakarta Sans', sans-serif; display: inline-flex; align-items: center;">
                                <span style="font-size: 28px; color: #1952E1; margin-right: 8px; font-family: sans-serif; font-weight: 900; line-height: 1;">✓</span>
                                <span>Cliniconnect</span>
                            </a>
                            <div style="color: #94a3b8; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-top: 6px; font-family: 'Plus Jakarta Sans', sans-serif;">Verified Professional Marketplace</div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td class="content-cell" style="padding: 40px 36px; text-align: left; background-color: #ffffff;">
                            
                            <h1 style="margin: 0 0 14px 0; color: #0A2342; font-size: 24px; font-weight: 800; line-height: 1.3; font-family: 'Playfair Display', Georgia, serif; letter-spacing: -0.3px;">Welcome to Cliniconnect!</h1>
                            
                            <p style="margin: 0 0 16px 0; color: #334155; font-size: 15px; font-weight: 600; line-height: 1.6; font-family: 'Plus Jakarta Sans', sans-serif;">Hello <strong>{$user_name}</strong>,</p>
                            
                            <p style="margin: 0 0 20px 0; color: #475569; font-size: 14px; font-weight: 400; line-height: 1.6; font-family: 'Plus Jakarta Sans', sans-serif;">
                                We are thrilled to welcome you to **Cliniconnect**! Our platform connects Nigeria's top vetted service providers with clients seeking quality work, protected by secure milestone escrows and verified skill badging.
                            </p>

                            <p style="margin: 0 0 20px 0; color: #475569; font-size: 14px; font-weight: 400; line-height: 1.6; font-family: 'Plus Jakarta Sans', sans-serif;">
                                **What you can do on Cliniconnect:**
                            </p>

                            <ul style="margin: 0 0 24px 0; padding-left: 20px; color: #475569; font-size: 13.5px; line-height: 1.7; font-family: 'Plus Jakarta Sans', sans-serif;">
                                <li style="margin-bottom: 8px;"><strong>As a Client:</strong> Post projects, browse vetted talent profiles, schedule secure in-app meetings, and manage payments safely via escrow.</li>
                                <li style="margin-bottom: 8px;"><strong>As a Provider:</strong> Pass technical domain quizzes, complete your manual NIN & liveness video verification, display your Verified Pro badge, and submit proposals for high-paying contracts.</li>
                            </ul>

                            <!-- CTA Button -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 28px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="#" style="background-color: #1952E1; color: #ffffff; font-size: 13px; font-weight: 800; text-decoration: none; padding: 14px 28px; border-radius: 3px; display: inline-block; font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(25, 82, 225, 0.2);">Explore Your Dashboard</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.6; font-family: 'Plus Jakarta Sans', sans-serif;">
                                If you have any questions or need assistance setting up, please contact our support desk or read our onboarding guide. Welcome aboard!
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" bgcolor="#f8fafc" class="footer-cell" style="padding: 24px 36px; background-color: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center; color: #94a3b8; font-size: 12px; line-height: 1.5; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <p style="margin: 0 0 4px 0; font-weight: 600;">© {$current_year} Cliniconnect Platform Inc. All rights reserved.</p>
                            <p style="margin: 0;">Developed with excellence by <strong style="color: #0A2342; font-weight: 700;">Cliniconnect Team</strong></p>
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

/**
 * Send Cliniconnect Welcome Email
 */
function sendCliniconnectWelcomeEmail($to_email, $user_name) {
    $html_body = getWelcomeEmailTemplate($user_name);
    
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = defined('SMTP_HOST') ? SMTP_HOST : 'armorbullet.host';
            $mail->SMTPAuth   = true;
            $mail->Username   = defined('SMTP_USER') ? SMTP_USER : 'noreply@armorbullet.host';
            $mail->Password   = defined('SMTP_PASS') ? SMTP_PASS : '$Helicopter123';
            $mail->SMTPSecure = defined('SMTP_SECURE') && SMTP_SECURE === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = defined('SMTP_PORT') ? SMTP_PORT : 587;
            $mail->Timeout    = 5;

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom(defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@armorbullet.host', 'Cliniconnect Platform');
            $mail->addAddress($to_email, $user_name);
            $mail->isHTML(true);
            $mail->Subject = 'Welcome to Cliniconnect, ' . $user_name . '!';
            $mail->Body    = $html_body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer Welcome Email Error: " . $mail->ErrorInfo);
            
            if (function_exists('logEmailLocally')) {
                logEmailLocally($to_email, 'Welcome to Cliniconnect, ' . $user_name . '!', $html_body);
            }

            // Fallback
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: noreply@armorbullet.host' . "\r\n";
            @mail($to_email, 'Welcome to Cliniconnect!', $html_body, $headers);
            
            return true;
        }
    }
    return true;
}
