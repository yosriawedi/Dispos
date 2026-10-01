<?php

namespace App\Controller\Admin;

use App\Entity\DemandeEncadrement;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DemandeEncadrementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DemandeEncadrement::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Demande d\'encadrement')
            ->setEntityLabelInPlural('Demandes d\'encadrement')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('statut')
            ->add('type');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // L'admin ne crée jamais une demande émise par un étudiant — il
            // ne fait qu'accepter/refuser/affecter un encadreur.
            ->disable(Action::NEW)
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $a) => $a->setLabel('Traiter')->setIcon('fas fa-arrow-right'))
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $a) => $a->setCssClass('text-muted'));
    }

    public function configureFields(string $pageName): iterable
    {
        $statutChoices = [
            'En attente' => DemandeEncadrement::STATUT_EN_ATTENTE,
            'Acceptée' => DemandeEncadrement::STATUT_ACCEPTEE,
            'Refusée' => DemandeEncadrement::STATUT_REFUSEE,
            'En cours' => DemandeEncadrement::STATUT_EN_COURS,
            'Terminée' => DemandeEncadrement::STATUT_TERMINEE,
        ];
        $statutBadges = [
            DemandeEncadrement::STATUT_EN_ATTENTE => 'secondary',
            DemandeEncadrement::STATUT_ACCEPTEE => 'success',
            DemandeEncadrement::STATUT_REFUSEE => 'danger',
            DemandeEncadrement::STATUT_EN_COURS => 'info',
            DemandeEncadrement::STATUT_TERMINEE => 'success',
        ];

        $typeChoices = [
            'PFE' => DemandeEncadrement::TYPE_PFE,
            'PFA' => DemandeEncadrement::TYPE_PFA,
            'Doctorat' => DemandeEncadrement::TYPE_DOCTORAT,
        ];

        yield IdField::new('id')->hideOnForm();

        yield FormField::addPanel('Demande de l\'étudiant')->setIcon('fas fa-user-graduate');
        yield AssociationField::new('etudiant', 'Étudiant');
        yield ChoiceField::new('type', 'Type')->setChoices($typeChoices);
        yield TextField::new('sujet', 'Sujet');
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield TextareaField::new('stackTech', 'Stack technique')->hideOnIndex();
        yield TextField::new('etablissement', 'Établissement')->hideOnIndex();
        yield TextField::new('niveauEtude', 'Niveau d\'étude')->hideOnIndex();

        yield FormField::addPanel('Décision Dis Pos')->setIcon('fas fa-clipboard-check');
        yield ChoiceField::new('statut', 'Statut')
            ->setChoices($statutChoices)
            ->renderAsBadges($statutBadges);
        yield AssociationField::new('encadreur', 'Encadreur assigné')->hideOnIndex();
        yield TextareaField::new('noteAdmin', 'Note interne')->hideOnIndex()
            ->setHelp('Visible uniquement par l\'équipe Dis Pos, jamais par l\'étudiant.');

        yield DateTimeField::new('createdAt', 'Soumise le')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Mise à jour')->hideOnForm()->hideOnIndex();
    }
}
