<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\PhotoType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }


    public function configureFields(string $pageName): iterable
    {



        $fields = [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name'),
            TextareaField::new('description'),
            Field::new('price'),//->setCurrency('EUR'),
            IntegerField::new('quantity'),
            AssociationField::new('category'),
            ChoiceField::new('etat')
                ->setChoices([
                    'Comme neuf' => 'comme_neuf',
                    'Moyen' => 'moyen',
                    'Mauvais' => 'mauvais',
                ]),
            // ImageField::new('imageName')->hideOnForm()
            //     ->setBasePath("/images/photos"),
            // CollectionField::new('photos')
            //     ->setEntryType(PhotoType::class)
            //     ->setFormTypeOption('by_reference', false)
            //     ->onlyOnForms()
            //     ->hideWhenUpdating(),
            CollectionField::new('photos')
            //->onlyOnIndex()
            //->onlyOnDetail()
            ->hideOnForm()
            //->hideWhenUpdating()
            ->setTemplatePath('product.html.twig'),
        ];
        return $fields;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, "detail");
    }
}
