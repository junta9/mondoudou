<?php
namespace App\Controller;

use App\Repository\AdressesRepository;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Component\Validator\Constraints\Length;

class InvoiceController extends AbstractController {

    private $orderRepository;
    private $adressesRepository;
    private $orderItemRepository;
    private $productRepository;
    
    public function __construct(ProductRepository $productRepository ,OrderItemRepository $orderItemRepository ,OrderRepository $orderRepository, AdressesRepository $adressesRepository){
        $this->orderRepository = $orderRepository;
        $this->adressesRepository = $adressesRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productRepository = $productRepository;
    }
    public function generateInvoice($id){
        $user = $this->getUser();
        $order = $this->orderRepository->find($id);
        $orderCreated = $order->getCreatedAt();
        $formattedDate = $orderCreated->format('Y-m-d H:i:s');
        $user_lastname = $user->getLastname();
        $user_firstname = $user->getFirstname();
        $address = $order->getDeliveryAddress();
        $user_address = $this->adressesRepository->find($address);
        $user_address_rue = $user_address->getAdresse();
        $user_address_codepostal = $user_address->getPostalcode();
        $user_address_city = $user_address->getCity();
        $user_address_country = $user_address->getCountry();
        $orderItems = $this->orderItemRepository->findBy(['order_id' => $order->getId()]);
        // foreach($orderItems as $orderItem){

        //     $itemData = $orderItem->getProduct();
        //     $itemName = $itemData->getName();
        //     $itemPriceUnit = $itemData->getPrice();
        //     $itemQuantity = $itemData->getQuantity();
        //     $orderItemTotalPrice = $itemPriceUnit * $itemQuantity;

        // }
        // dd($orderItems);
        // $html2pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'fr');
        // // html2pdf->writeHTML('<h1>HelloWorld</h1>This is my first page');
        // $html2pdf->writeHTML("
        // ");
        // $html2pdf->output();

    }
}