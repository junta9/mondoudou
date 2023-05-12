<?php

namespace App\Controller\Admin;

use App\Entity\Photos;
use App\Form\PhotoType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PhotosCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Photos::class;
    }

    
    public function configureFields(string $pageName): iterable
    {

        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('product'),
            ImageField::new("imageName")
            ->setBasePath("public/images/photos")
            ->setUploadDir("public/images/photos")
            ->setTemplatePath('photos.html.twig'),
            // CollectionField::new('imageName')
            //     ->setEntryType(PhotoType::class)
            //     ->setFormTypeOption('by_reference', false)
            //     ->onlyOnForms()
            //     ->hideWhenUpdating(),
            // CollectionField::new('imageName')
            //     ->hideOnForm()
            //     ->setTemplatePath('photos.html.twig'),
            
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, "detail");
    }
    
}
