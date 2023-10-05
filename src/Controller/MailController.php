<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use RuntimeException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class MailController extends MainController 
{

    public function defaultMethod()
    {

    }

    public function sendMailMethod($recipient, $subject, $message)
    {
        $mail = new PHPMailer(true);
        
        try {
            // Configuration du serveur SMTP
            $mail->isSMTP();
            $mail->Host = 'tristanriedinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'alexisbateaux';
            $mail->Password = 'Ovulydud89';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Destinataire, sujet et contenu du message
            $mail->setFrom('tristanriedinger@gmail.com', 'Your Name');
            $mail->addAddress($recipient);
            $mail->Subject = $subject;
            $mail->Body = $message;

            // Envoi de l'e-mail
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}
