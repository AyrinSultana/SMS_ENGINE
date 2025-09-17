<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SMSAuthorizationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The SMS template name awaiting authorization.
     *
     * @var string
     */
    public string $templateName;

    /**
     * The email address of the authorizer (recipient).
     *
     * @var string
     */
    public string $authorizerEmail;

    /**
     * Create a new message instance.
     *
     * @param  string  $templateName
     * @param  string  $authorizerEmail
     * @return void
     */
    public function __construct(string $templateName, string $authorizerEmail)
    {
        $this->smsTemplateName = $templateName;
        $this->authorizerEmail = $authorizerEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->authorizerEmail)
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('SMS Authorization Required')
                    ->view('emails.sms_authorization')
                    ->with([
                        'templateName'    => $this->templateName,
                        'authorizerEmail' => $this->authorizerEmail,
                    ]);
    }
}
