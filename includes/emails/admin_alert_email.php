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
 * Generate Admin Verification Alert HTML Email
 */
function getAdminVerificationAlertTemplate($provider_name, $provider_email, $nin) {
    $current_year = date('Y');
    
    return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New Provider Verification Submitted</title>
    <style type="text/css">
        body { margin: 0; padding: 0; background-color: #f8f7f5; font-family: sans-serif; color: #0A2342; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f8f7f5;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8f7f5; padding: 36px 0;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 580px; background-color: #ffffff; border-radius: 6px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <tr>
                        <td align="center" bgcolor="#0A2342" style="padding: 24px; color: #ffffff; font-size: 20px; font-weight: bold;">
                            Cliniconnect Admin Alert
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 36px; text-align: left;">
                            <h2 style="margin: 0 0 14px 0; color: #0A2342; font-size: 18px;">Pending Provider Verification</h2>
                            <p style="color: #475569; font-size: 14px; line-height: 1.6;">
                                A service provider has submitted their verification documents (NIN, ID Card scan, Selfie, and Liveness head-movement video clip) and is waiting for your review.
                            </p>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 8px 0; font-size: 13px; color: #64748b; font-weight: bold; width: 120px;">Name:</td>
                                    <td style="padding: 8px 0; font-size: 13px; color: #0A2342;"><strong>{$provider_name}</strong></td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; font-size: 13px; color: #64748b; font-weight: bold;">Email:</td>
                                    <td style="padding: 8px 0; font-size: 13px; color: #0A2342;">{$provider_email}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; font-size: 13px; color: #64748b; font-weight: bold;">NIN:</td>
                                    <td style="padding: 8px 0; font-size: 13px; color: #0A2342; font-family: monospace;">{$nin}</td>
                                </tr>
                            </table>
                            <p style="color: #475569; font-size: 14px;">
                                Please log in to the Cliniconnect Admin Panel to review their document uploads and verify the liveness video.
                            </p>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="#" style="background-color: #1952E1; color: #ffffff; font-size: 13px; font-weight: bold; text-decoration: none; padding: 12px 24px; border-radius: 3px; display: inline-block;">Go to Admin Console</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" bgcolor="#f8fafc" style="padding: 16px; border-top: 1px solid #f1f5f9; color: #94a3b8; font-size: 11px;">
                            © {$current_year} Cliniconnect Administration Subsystem
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
 * Send Alert Email to Admins
 */
function sendAdminVerificationAlertEmail($provider_name, $provider_email, $nin) {
    $html_body = getAdminVerificationAlertTemplate($provider_name, $provider_email, $nin);
    $admin_email = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'admin@cliniconnect.com';
    
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

            $mail->setFrom(defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@armorbullet.host', 'Cliniconnect System');
            $mail->addAddress($admin_email, 'Cliniconnect Admin');
            $mail->isHTML(true);
            $mail->Subject = 'PENDING VERIFICATION: ' . $provider_name;
            $mail->Body    = $html_body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer Admin Alert Email Error: " . $mail->ErrorInfo);
            
            if (function_exists('logEmailLocally')) {
                logEmailLocally($admin_email, 'PENDING VERIFICATION: ' . $provider_name, $html_body);
            }

            // Fallback
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: noreply@armorbullet.host' . "\r\n";
            @mail($admin_email, 'PENDING VERIFICATION: ' . $provider_name, $html_body, $headers);
            
            return true;
        }
    }
    return true;
}
