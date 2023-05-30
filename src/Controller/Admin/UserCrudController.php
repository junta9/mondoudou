<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('email'),
            TextField::new('firstname')->onlyOnDetail(),
            TextField::new('lastname')->onlyOnDetail(),
            TextField::new('mobile')->onlyOnDetail(),
            TextField::new('password')->onlyOnForms(),
            ChoiceField::new('roles')
            ->setChoices([
                'Admin' => 'ROLE_ADMIN',
                'User' => 'ROLE_USER',
            ]),
            ArrayField::new('roles')->onlyOnForms(),
        ];
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, "detail");
    }
}
    

