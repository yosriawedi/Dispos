<?php

namespace App\Controller\Admin;

use App\Entity\DemandeConsultation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class DemandeConsultationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DemandeConsultation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Demande de consultation')
            ->setEntityLabelInPlural('Demandes de consultation')
            ->setDefaultSort(['dateDemande' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('statut');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $a) => $a->setLabel('Traiter')->setIcon('fas fa-arrow-right'))
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $a) => $a->setCssClass('text-muted'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield FormField::addPanel('Demande de l\'entreprise')->setIcon('fas fa-comments');
        yield AssociationField::new('incubation', 'Dossier d\'incubation')->hideOnForm();
        yield TextareaField::new('message', 'Message')->hideOnForm();

        yield FormField::addPanel('Décision Dis Pos')->setIcon('fas fa-clipboard-check');
        yield ChoiceField::new('statut', 'Statut')
            ->setChoices([
                'En attente' => DemandeConsultation::STATUT_EN_ATTENTE,
                'Traitée' => DemandeConsultation::STATUT_TRAITEE,
                'Refusée' => DemandeConsultation::STATUT_REFUSEE,
            ])
            ->renderAsBadges([
                DemandeConsultation::STATUT_EN_ATTENTE => 'secondary',
                DemandeConsultation::STATUT_TRAITEE => 'success',
                DemandeConsultation::STATUT_REFUSEE => 'danger',
            ]);
        yield TextareaField::new('reponseAdmin', 'Réponse de l\'équipe Dis Pos')->hideOnIndex();

        yield DateTimeField::new('dateDemande', 'Demandée le')->hideOnForm();
    }
}
