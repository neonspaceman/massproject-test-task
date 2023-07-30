<?php

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class SendEmailHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger
    )
    {
    }

    public function __invoke(SendEmailMessage $message): void
    {
        $email = (new Email())
            ->from('test@test.com')
            ->to($message->getEmail())
            ->subject($message->getSubject())
            ->text($message->getText());
        try {
            $this->mailer->send($email);
            $this->logger->info('Send email: ' . $email->toString());
        } catch (TransportExceptionInterface $exception){
            $this->logger->error($exception->getMessage());
        }
    }
}
