<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Dompdf\Adapter\PDFLib;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user_id')->hideOnForm()->hideOnIndex(),
            IntegerField::new('total'),
            ChoiceField::new('status')
                ->setChoices([
                    'en attente' => 'en attente',
                    'en préparation' => 'en preparation',
                    'commande pris en charge par le transporteur' => 'commande pris en charge par le transporteur'
                ]),
            TextField::new('delivery_address')->hideOnIndex(),
            DateField::new('created_at')->hideOnForm(),
            DateField::new('updated_at')->hideOnIndex(),
            Field::new('invoice')->hideWhenUpdating()
            ->setTemplatePath('admin/pdf.html.twig'),
            
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, "detail");
    }
    
}
