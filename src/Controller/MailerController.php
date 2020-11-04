<?php 
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    /**
     * @Route("/comment-validated", 
     *        name="comment_validated")
     */
    public function sendEmail(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('ksowa@outlook.fr')
            ->to('ksowa@outlook.fr')
            ->subject('Kiki ! Modère le commentaire')
            ->text('Un commentaire a été posté sur SymfonyCMS')
            ->html('<p>Coucou Kiki, tu as reçu un petit message de.. toi-même.<br>Fonce le modérer !</p>');

        $mailer->send($email);

        // try {
        //     $mailer->send($email);
        // } catch (TransportExceptionInterface $e) {
        //     // error message
        // }

        return $this->render('comment/validated.html.twig');
    }
}