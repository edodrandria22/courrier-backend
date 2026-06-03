<?php

namespace App\Handler;

use App\Dto\messages\SendEmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendEmailMessageHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private string $mailFrom, // Injectés depuis vos paramètres services.yaml
        private string $mailName
    ) {}

    public function __invoke(SendEmailMessage $message)
    {
        // C'est ici qu'on effectue le véritable envoi SMTP
        $email = (new Email())
            ->from($this->mailName.' <'.$this->mailFrom.'>')
            ->to($message->getTo())
            ->subject($message->getSubject())
            ->html($message->getBody());

        $this->mailer->send($email);
    }
}