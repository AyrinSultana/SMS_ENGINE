<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    public function sendAuthorizationEmail($recipientEmail, $templateName)
    {
        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST', '192.168.1.226');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME', 'currency.management@ificbankbd.com');
            $mail->Password   = env('MAIL_PASSWORD', '+3HVcneUU');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'TLS');
            $mail->Port       = env('MAIL_PORT', 587);

            // Sender & Recipient
            $mail->setFrom(
                env('MAIL_FROM_ADDRESS', 'currency.management@ificbankbd.com'),
                env('MAIL_FROM_NAME', 'CM')
            );
            $mail->addAddress($recipientEmail);

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = "Template Authorization Request: $templateName";
            $mail->Body    = $this->getEmailBody($templateName);
            $mail->AltBody = $this->getPlainTextBody($templateName);

            $mail->send();
            return true;
        } catch (Exception $e) {
            \Log::error("Email failed: " . $mail->ErrorInfo);
            return false;
        }
    }

    private function getEmailBody($templateName)
    {
        return view('emails.template_authorization', ['templateName' => $templateName])->render();
    }

    private function getPlainTextBody($templateName)
    {
        return "Please authorize the template: $templateName";
    }
}