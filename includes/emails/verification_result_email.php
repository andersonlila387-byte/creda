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
 * Generate Approved Email Template
 */
function getApprovedEmailTemplate($user_name) {
    $current_year = date('Y');
    return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account Approved - Cliniconnect Pro</title>
</head>
<body style="margin:0; padding:0; background-color:#f8f7f5; font-family:sans-serif; color:#0A2342;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f8f7f5; padding:36px 0;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:580px; background-color:#ffffff; border-radius:6px; border:1px solid #e2e8f0; overflow:hidden;">
                    <tr>
                        <td align="center" bgcolor="#15803d" style="padding:24px; color:#ffffff; font-size:20px; font-weight:bold;">
                            Cliniconnect Pro Verified! ✓
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px; text-align:left;">
                            <h2 style="margin:0 0 14px 0; color:#0A2342; font-size:18px;">Congratulations, {$user_name}!</h2>
                            <p style="color:#475569; font-size:14px; line-height:1.6;">
                                We are happy to inform you that your manual NIN identity and biometric liveness review has been **approved** by our moderation team!
                            </p>
                            <p style="color:#475569; font-size:14px; line-height:1.6;">
                                Your account has been upgraded to a **Verified Pro**. You now have a Pro Badge visible on your profile and have full access to browse and bid on active client contracts!
                            </p>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top:24px;">
                                <tr>
                                    <td align="center">
                                        <a href="#" style="background-color:#15803d; color:#ffffff; font-size:13px; font-weight:bold; text-decoration:none; padding:12px 24px; border-radius:3px; display:inline-block;">Go to Provider Workspace</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" bgcolor="#f8fafc" style="padding:16px; border-top:1px solid #f1f5f9; color:#94a3b8; font-size:11px;">
                            © {$current_year} Cliniconnect Platform Inc. All rights reserved.
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
 * Generate Rejected Email Template
 */
function getRejectedEmailTemplate($user_name, $reason) {
    $current_year = date('Y');
    return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Identity Review Update - Action Required</title>
</head>
<body style="margin:0; padding:0; background-color:#f8f7f5; font-family:sans-serif; color:#0A2342;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f8f7f5; padding:36px 0;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:580px; background-color:#ffffff; border-radius:6px; border:1px solid #e2e8f0; overflow:hidden;">
                    <tr>
                        <td align="center" bgcolor="#be123c" style="padding:24px; color:#ffffff; font-size:20px; font-weight:bold;">
                            Verification Action Required
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px; text-align:left;">
                            <h2 style="margin:0 0 14px 0; color:#0A2342; font-size:18px;">Hello {$user_name},</h2>
                            <p style="color:#475569; font-size:14px; line-height:1.6;">
                                Thank you for submitting your verification details. Unfortunately, our moderation team could not approve your profile verification based on the submitted documents.
                            </p>
                            <div style="background-color:#fff1f2; border:1px solid #fecdd3; border-left:4px solid #be123c; padding:16px; margin:20px 0; border-radius:4px; font-size:13.5px; color:#9f1239;">
                                <strong>Rejection Reason:</strong> {$reason}
                            </div>
                            <p style="color:#475569; font-size:14px; line-height:1.6;">
                                Please log back in to your dashboard to review your submission details, capture a clearer selfie / liveness video, and re-submit your files.
                            </p>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top:24px;">
                                <tr>
                                    <td align="center">
                                        <a href="#" style="background-color:#be123c; color:#ffffff; font-size:13px; font-weight:bold; text-decoration:none; padding:12px 24px; border-radius:3px; display:inline-block;">Update Documents</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" bgcolor="#f8fafc" style="padding:16px; border-top:1px solid #f1f5f9; color:#94a3b8; font-size:11px;">
                            © {$current_year} Cliniconnect Platform Inc. All rights reserved.
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
 * Send Verification Approved Email
 */
function sendVerificationApprovedEmail($to_email, $user_name) {
    $html_body = getApprovedEmailTemplate($user_name);
    
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

            $mail->setFrom(defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@armorbullet.host', 'Cliniconnect Pro');
            $mail->addAddress($to_email, $user_name);
            $mail->isHTML(true);
            $mail->Subject = 'Identity Verification Approved - Welcome to Cliniconnect Pro!';
            $mail->Body    = $html_body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer Approved Email Error: " . $mail->ErrorInfo);
            
            if (function_exists('logEmailLocally')) {
                logEmailLocally($to_email, 'Identity Verification Approved - Welcome to Cliniconnect Pro!', $html_body);
            }

            // Fallback
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: noreply@armorbullet.host' . "\r\n";
            @mail($to_email, 'Identity Verification Approved!', $html_body, $headers);
            
            return true;
        }
    }
    return true;
}

/**
 * Send Verification Rejected Email
 */
function sendVerificationRejectedEmail($to_email, $user_name, $reason) {
    $html_body = getRejectedEmailTemplate($user_name, $reason);
    
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

            $mail->setFrom(defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@armorbullet.host', 'Cliniconnect Pro');
            $mail->addAddress($to_email, $user_name);
            $mail->isHTML(true);
            $mail->Subject = 'Identity Verification Update - Action Required';
            $mail->Body    = $html_body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer Rejected Email Error: " . $mail->ErrorInfo);
            
            if (function_exists('logEmailLocally')) {
                logEmailLocally($to_email, 'Identity Verification Update - Action Required', $html_body);
            }

            // Fallback
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: noreply@armorbullet.host' . "\r\n";
            @mail($to_email, 'Identity Verification Action Required', $html_body, $headers);
            
            return true;
        }
    }
    return true;
}
