<?php

namespace App\Controller\Admin;

use App\Entity\Incubation;
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

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('entreprise');
        yield TextField::new('secteurActivite', 'Secteur d\'activité');
        yield ChoiceField::new('stadeMaturite', 'Stade de maturité')->setChoices([
            'Idée' => Incubation::STADE_IDEE,
            'MVP' => Incubation::STADE_MVP,
            'Lancé' => Incubation::STADE_LANCE,
        ]);
        yield ChoiceField::new('statutGlobal', 'Statut')->setChoices([
            'En cours' => Incubation::STATUT_EN_COURS,
            'Terminée' => Incubation::STATUT_TERMINEE,
            'Suspendue' => Incubation::STATUT_SUSPENDUE,
        ]);
        yield DateTimeField::new('dateDebut')->hideWhenUpdating();
    }
}
