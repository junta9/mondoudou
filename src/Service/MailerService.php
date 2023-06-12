<?php 

namespace App\Service;

use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService {

    private $mailer;

    public function __construct(MailerInterface $mailer){
        $this->mailer = $mailer;
    }

    public function sendEmail($mail, $message)
    {
        $email = (new Email())
            ->from($mail)
            ->to('contact@mondoudou.org')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Message du site Mondoudou')
            // ->text('Sending emails is fun again!')
            ->html($message);

        $this->mailer->send($email);

        // ...
    }

    public function replyMessageSend($mail){

        $message = "Nous avons bien recu votre message, nous vous repondrons dans les plus bref délai.";

        $email = (new Email())
            ->from('contact@mondoudou.org')
            ->to($mail)
            ->subject('Bonne reception de votre message')
            ->html($message);

        $this->mailer->send($email);
    }
}