<?php

namespace App\Controller\Admin;

use App\Entity\Incubation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class IncubationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Incubation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Dossier d\'incubation')
            ->setEntityLabelInPlural('Dossiers d\'incubation')
            ->setDefaultSort(['dateDebut' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('statutGlobal')->add('stadeMaturite');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('entreprise', 'Entreprise');
        yield TextField::new('secteurActivite', 'Secteur d\'activité');
        yield ChoiceField::new('stadeMaturite', 'Stade de maturité')->setChoices([
            'Idée' => Incubation::STADE_IDEE,
            'MVP' => Incubation::STADE_MVP,
            'Lancé' => Incubation::STADE_LANCE,
        ]);
        yield ChoiceField::new('statutGlobal', 'Statut')
            ->setChoices([
                'En cours' => Incubation::STATUT_EN_COURS,
                'Terminée' => Incubation::STATUT_TERMINEE,
                'Suspendue' => Incubation::STATUT_SUSPENDUE,
            ])
            ->renderAsBadges([
                Incubation::STATUT_EN_COURS => 'info',
                Incubation::STATUT_TERMINEE => 'success',
                Incubation::STATUT_SUSPENDUE => 'danger',
            ]);
        yield DateTimeField::new('dateDebut', 'Débutée le')->hideWhenUpdating();
    }
}
