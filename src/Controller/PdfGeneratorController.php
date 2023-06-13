<?php

namespace App\Controller;

use App\Repository\AdressesRepository;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;

class PdfGeneratorController extends AbstractController
{
    /**
     * @Route("/pdf/generator/{id}", name="app_pdf_generator")
     */
    public function generateInvoice(
        $id,
        OrderRepository $orderRepository,
        AdressesRepository $adressesRepository,
        OrderItemRepository $orderItemRepository,
        PaymentRepository $paymentRepository
    ): Response
    {

        //Recuperation des données de la commande
        $user = $this->getUser();
        $order = $orderRepository->find($id);
        // $orderCreated = $order->getCreatedAt();
        // $formattedDate = $orderCreated->format('Y-m-d H:i:s');
        $user_lastname = $user->getLastname();
        $user_firstname = $user->getFirstname();
        $address = $order->getDeliveryAddress();
        $user_address = $adressesRepository->find($address);
        $user_address_lastname = $user_address->getLastname();
        $user_address_firstname = $user_address->getFirstname();
        $user_address_rue = $user_address->getAdresse();
        $user_address_codepostal = $user_address->getPostalcode();
        $user_address_city = $user_address->getCity();
        $user_address_country = $user_address->getCountry();
        $orderItems = $orderItemRepository->findBy(['order_id' => $order->getId()]);
        $payment = $paymentRepository->findOneBy(['order_id' => $order->getId()]);
        // dd($payment->getId());


        //Injection des données dans la variable data 
        $data = [
            'imageSrc'  => $this->imageToBase64($this->getParameter('kernel.project_dir') . '/public/images/logo-mondoudou.png'),
            'name'         => $user_address_lastname . ' ' . $user_address_firstname,
            'address_rue'      => $user_address_rue,
            'address_code_postal'      => $user_address_codepostal,
            'address_city'      => $user_address_city,
            'address_country'      => $user_address_country,
            'email'        => $user->getUserIdentifier(),
            'orderItems' => $orderItems,
            'transporteur' => $order->getDeliveryPrice(),
            'totalPrice' => $order->getTotal(),
            'commande' => $order->getId(),
            'payment' => $payment->getId(),
        ];
        $html =  $this->renderView('pdf_generator/index.html.twig', $data);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        return new Response(
            $dompdf->stream('facture', ["Attachment" => false]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
    }

    private function imageToBase64($path)
    {
        $path = $path;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
}
