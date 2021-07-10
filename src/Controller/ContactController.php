<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $contactFormData = $form->getData();
            
            $message = (new Email())
                ->from($contactFormData['email'])
                ->to('contact@monblogamoi.xyz')
                ->subject('Message depuis monblog')
                ->html('<h3>Message envoyé depuis https://www.monblogamoi.xyz</h3>' 
                    . '<b>Expéditeur :</b> ' 
                    . $contactFormData['pseudo'] . '<br>' 
                    . '<b>E-mail :</b> ' 
                    . $contactFormData['email'] 
                    . '<br>' . '<b>Message</b> : <p>' 
                    . $contactFormData['message'] . '</p>', 'text/plain');
            
            $mailer->send($message);

            $this->addFlash('successContact', 'Votre message a bien été envoyé ! Merci ! Je vous répondrai dès que possible.');

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            //'controller_name' => 'ContactController',
            'our_form' => $form->createView(),
            'current_menu' => 'Contact',
        ]);
    }
}
