<?php

namespace App\Service\utils;

use App\Dto\messages\SendEmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class MailService
{
    public function __construct(
        private MessageBusInterface $bus // Remplacement du Mailer par le Bus Messenger
    ) {}

    public function sendEmail(string $to, string $subject, string $body): void
    {
        // Au lieu d'envoyer le mail, on met l'action en file d'attente RabbitMQ
        $this->bus->dispatch(new SendEmailMessage($to, $subject, $body));
    }

    public function getHtmlMail(string $nom, string $message): string
    {
        return "
            <html>
                <body style='font-family: Arial, sans-serif'>
                    <h2>Bonjour $nom,</h2>
                    <p>$message</p>
                    <br>
                    <p>Cordialement,<br>Mesupres</p>
                </body>
            </html>
        ";
    }
}