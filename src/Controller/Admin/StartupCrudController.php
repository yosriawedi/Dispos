<?php

namespace App\Controller\Admin;

use App\Entity\Startup;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class StartupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Startup::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Startup')
            ->setEntityLabelInPlural('Startups')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('stage')->add('sector');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom');
        yield AssociationField::new('owner', 'Fondateur');
        yield TextField::new('sector', 'Secteur');
        yield ChoiceField::new('stage', 'Stade')->setChoices([
            'Seed' => 'seed',
            'Series A' => 'series_a',
            'Series B' => 'series_b',
            'Growth' => 'growth',
        ]);
        yield TextField::new('country', 'Pays')->hideOnIndex();
        yield UrlField::new('website', 'Site web')->hideOnIndex();
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield DateTimeField::new('foundedAt', 'Fondée le')->hideOnIndex();
        yield AssociationField::new('tags', 'Tags')->hideOnIndex();
        yield DateTimeField::new('createdAt', 'Ajoutée le')->hideOnForm();
    }
}
