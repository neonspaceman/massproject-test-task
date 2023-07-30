<?php

namespace App\Message;

class SendEmailMessage
{
    public function __construct(
        private string $email,
        private string $subject,
        private string $text
    )
    {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
