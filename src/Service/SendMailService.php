<?php
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class SendMailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(string $fromEmail,string $fromName, string $to, string $subject, string $template, array $context): void
    {
        // On crée le mail
        $email = (new TemplatedEmail())
        ->from(new Address('Storeship@gmail.com', $fromName))
        ->to($to)
        ->subject($subject)
        ->htmlTemplate('emails/' . $template .'.html.twig')
        ->context($context);

        // On envoie le mail
        $this->mailer->send($email);
    }

    public function sendMail(string $fromEmail,string $fromName, string $to, string $subject, string $template, array $data = []): void
    {
        // On crée le mail
        $email = (new TemplatedEmail())
        ->from(new Address('Storeship@gmail.com', $fromName))
        ->to($to)
        ->subject($subject)
        ->htmlTemplate('emails/' . $template .'.html.twig')
        ->context($data);

        // On envoie le mail
        $this->mailer->send($email);
    }


}