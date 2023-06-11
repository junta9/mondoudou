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
    public function handleFormSubmission(Request $request, MailerService $mailerService): Response
    {
        if ($request->isMethod('POST')) {
            // $name = $request->request->get('name');
            $email = $request->request->get('email');
            $content = $request->request->get('message');

            if (!empty($email) && !empty($content)) {
                $data = [
                    // 'name' => $name,
                    'email' => $email,
                    'content' => $content,
                ];

                $mailerService->sendEmail($data['email'], $data['content']);
                $this->addFlash('success', 'Votre message a bien été envoyé !');
            }
            
            return $this->redirectToRoute('app_home');
        }
    }
}