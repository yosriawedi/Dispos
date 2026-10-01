<?php

namespace App\Controller\Admin;

use App\Entity\Matiere;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MatiereCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Matiere::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Matière')
            ->setEntityLabelInPlural('Matières')
            ->setDefaultSort(['nom' => 'ASC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('filiere')->add('niveau')->add('active');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('nom', 'Nom');
        yield TextField::new('filiere', 'Filière');
        yield TextField::new('niveau', 'Niveau');
        yield TextField::new('icone', 'Icône')->hideOnIndex()
            ->setHelp('Un emoji affiché à côté du nom de la matière sur le site public.');
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield BooleanField::new('active', 'Active')
            ->renderAsSwitch(false)
            ->setHelp('Une matière inactive n\'apparaît plus dans les choix proposés sur le site public.');
    }
}
