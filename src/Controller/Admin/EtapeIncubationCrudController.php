<?php

namespace App\Controller\Admin;

use App\Entity\EtapeIncubation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
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

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Étape d\'incubation')
            ->setEntityLabelInPlural('Étapes d\'incubation')
            ->setDefaultSort(['dateMiseAJour' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('statut')->add('phase');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Les 7 étapes sont générées automatiquement à la création d'un
            // dossier d'incubation (IncubationEtapesSubscriber) — l'admin ne
            // doit jamais en fabriquer une à la main.
            ->disable(Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('incubation', 'Dossier d\'incubation');
        yield ChoiceField::new('phase', 'Phase')->setChoices(array_flip(EtapeIncubation::PHASE_LABELS));
        yield ChoiceField::new('statut', 'Statut')
            ->setChoices([
                'Non démarrée' => EtapeIncubation::STATUT_NON_DEMARREE,
                'En cours' => EtapeIncubation::STATUT_EN_COURS,
                'En attente de validation' => EtapeIncubation::STATUT_EN_ATTENTE_VALIDATION,
                'Validée' => EtapeIncubation::STATUT_VALIDEE,
                'Bloquée' => EtapeIncubation::STATUT_BLOQUEE,
            ])
            ->renderAsBadges([
                EtapeIncubation::STATUT_NON_DEMARREE => 'secondary',
                EtapeIncubation::STATUT_EN_COURS => 'info',
                EtapeIncubation::STATUT_EN_ATTENTE_VALIDATION => 'warning',
                EtapeIncubation::STATUT_VALIDEE => 'success',
                EtapeIncubation::STATUT_BLOQUEE => 'danger',
            ]);
        yield AssociationField::new('referent', 'Référent')
            ->setFormTypeOption('required', false)
            ->autocomplete();
        yield TextareaField::new('commentaireAdmin', 'Commentaire interne')->hideOnIndex();
        yield DateTimeField::new('dateMiseAJour', 'Mise à jour')->hideOnForm();
    }
}
