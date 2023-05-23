<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="app_contact")
     */
    public function handleFormSubmission(Request $request, EntityManagerInterface $em, MailerService $mailerService): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $content = $request->request->get('message');

            if (!empty($name) || !empty($email) || !empty($message)) {
                // $data = [
                //     'name' => $name,
                //     'email' => $email,
                //     'content' => $content,
                // ];

                // $message = new Messages();
                // $message->setName($name);
                // $message->setEmail($email);
                // $message->setContent($content);
                // $message->setCreatedAt(new \DateTimeImmutable());

                // $em->persist($message);
                // $em->flush();

                $mailerService->sendEmail($email, $content);

            }

            // $emailMessage = (new \Swift_Message('Nouveau message de contact'))
            //     ->setFrom($email)
            //     ->setTo('votre_adresse_email@gmail.com') // Remplacer par votre adresse email
            //     ->setBody(
            //         $this->renderView(
            //             'emails/contact.html.twig', // Créez le template Twig pour votre email
            //             [
            //                 'name' => $name,
            //                 'email' => $email,
            //                 'message' => $message
            //             ]
            //         ),
            //         'text/html'
            //     );

            // $mailer->send($emailMessage);

            $this->addFlash('success', 'Votre message a bien été envoyé !');
            return $this->redirectToRoute('app_home');
        }

        // return $this->render('contact/index.html.twig', [
        //     'controller_name' => 'ContactController',

        // ]);
    }
}

// return $this->render('contact/index.html.twig', [
//     'controller_name' => 'ContactController',
// ]);