<?php

namespace App\Controller;

use App\Form\NewslettersType;
use App\Service\MailerService;
use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController
{
    /**
     * @Route("/newsletter", name="app_newsletter")
     */
    public function index(Request $request, MailerService $mailerService)
    {
        $form = $this->createForm(NewslettersType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $data = $form->getData();
            $email = $data['email'];
            // $mailerService->sendEmail($email);
        }

        return $this->renderForm('newsletter/index.html.twig', [
            'controller_name' => 'NewsletterController',
            'formulaire' => $form,
        ]);
    }
}
