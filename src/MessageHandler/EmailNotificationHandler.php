<?php
namespace App\MessageHandler;

use App\Message\EmailNotification;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EmailNotificationHandler
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(EmailNotification $notification)
    {
        $email = (new Email())
            ->from('noreply@yourdomain.com')
            ->to($notification->getEmail())
            ->subject($notification->getSubject())
            ->text($notification->getBody());

        $this->mailer->send($email);
    }
}