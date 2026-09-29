<?php

namespace App\Controller\Admin;

use App\Entity\EtapeIncubation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class EtapeIncubationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EtapeIncubation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('incubation');
        yield ChoiceField::new('phase')->setChoices(array_flip(EtapeIncubation::PHASE_LABELS));
        yield ChoiceField::new('statut')->setChoices([
            'Non démarrée' => EtapeIncubation::STATUT_NON_DEMARREE,
            'En cours' => EtapeIncubation::STATUT_EN_COURS,
            'En attente de validation' => EtapeIncubation::STATUT_EN_ATTENTE_VALIDATION,
            'Validée' => EtapeIncubation::STATUT_VALIDEE,
            'Bloquée' => EtapeIncubation::STATUT_BLOQUEE,
        ]);
        yield AssociationField::new('referent')
            ->setFormTypeOption('required', false)
            ->autocomplete();
        yield TextareaField::new('commentaireAdmin')->hideOnIndex();
        yield DateTimeField::new('dateMiseAJour')->hideOnForm();
    }
}
