<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TemplateAuthorizationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The template name awaiting authorization.
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
        $this->templateName    = $templateName;
        $this->authorizerEmail = $authorizerEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->authorizerEmail)  // set the recipient here
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    // ->replyTo(config('mail.from.address'), config('mail.from.name')) // optional
                    ->subject('Template Authorization Required')
                    ->view('emails.template_authorization')
                    ->with([
                        'templateName'    => $this->templateName,
                        'authorizerEmail' => $this->authorizerEmail,
                    ]);
    }
}
