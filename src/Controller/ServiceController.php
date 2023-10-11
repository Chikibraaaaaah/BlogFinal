<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use RuntimeException;
use Swift_Message;
use Swift_Mailer;
use Swift_SmtpTransport;

class ServiceController extends MainController 
{

    public function defaultMethod()
    {

    }

    public function sendMailMethod()
    {

        $receiver = $this->getPost("email");
        $subject = $this->getGet("subject");
        $content = ($subject === "password") ? "Réinitialisez votre mot de passe.\nAfin de réinitialiser votre mot de passe, cliquez sur le lien suivant : http://localhost:8888/Blog/BlogFinal/public/index.php?access=auth_resetPassword\nBisous carresse" : "Bonjour, ceci est un message de test";        // Créer un objet de transport SMTP
        $transport = new Swift_SmtpTransport("smtp.gmail.com", 587, "tls");
        $transport->setUsername("tristanriedinger@gmail.com");
        $transport->setPassword("xvajpmjxxczmfnxb");

        // Créer un objet de messagerie
        $mailer = new Swift_Mailer($transport);

        // Créer un objet de message
        $message = new Swift_Message("Sujet du message");
        $message->setFrom("tristanriedinger@gmail.com", "Tristan Riedinger - Admin Blog");
        $message->setTo($receiver);
        $message->setBody($content);

        // Envoyer le message
        $result = $mailer->send($message);

        // Vérifier le résultat de l"envoi
        if ($result) {
            echo "Message envoyé avec succès !";
        } else {
            echo "Le message n\'a pas pu être envoyé.";
        }

    }

}
