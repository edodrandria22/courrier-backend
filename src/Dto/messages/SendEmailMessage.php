<?php

namespace App\Dto\messages;

class SendEmailMessage
{
    public function __construct(
        private string $to,
        private string $subject,
        private string $body
    ) {}

    public function getTo(): string { return $this->to; }
    public function getSubject(): string { return $this->subject; }
    public function getBody(): string { return $this->body; }
}