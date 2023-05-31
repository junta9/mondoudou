<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    // public function configureActions(Actions $actions): Actions
    // {
    //     return $actions
    //         // ...
    //         // this will forbid to create or delete entities in the backend
    //         ->disable(Action::NEW, Action::DELETE)
    //         ->disable(Action::NEW, Action::EDIT)
    //         ->add(Action::NEW, Action::DETAIL)
    //     ;
    // }

    
    // public function configureFields(string $pageName): iterable
    // {
    //     return [
    //         IdField::new('id')->onlyOnIndex(),
    //         AssociationField::new('user_id'),

    //     ];
    // }
    
}
