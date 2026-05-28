<?php

namespace App\Service\utils;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    public function __construct(
        private MailerInterface $mailer,
        private string $mailFrom,
        private string $mailName
    ) {}

    public function sendEmail(string $to, string $subject, string $body): void
    {
        $email = (new Email())
            ->from($this->mailName.' <'.$this->mailFrom.'>')
            ->to($to)
            ->subject($subject)
            ->html($body);

        $this->mailer->send($email);
    }
    public function getHtmlMail(string $nom, string $message): string
    {
        return "
            <html>
                <body style='font-family: Arial, sans-serif'>
                    <h2>Bonjour $nom</h2>
                    <p>$message</p>
                    <br>
                    <p>Cordialement,<br>Espa Vontovorona</p>
                </body>
            </html>
        ";
    }
    
}
