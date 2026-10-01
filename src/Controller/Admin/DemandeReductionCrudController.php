<?php

namespace App\Controller\Admin;

use App\Entity\DemandeReduction;
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

class DemandeReductionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DemandeReduction::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Demande de réduction')
            ->setEntityLabelInPlural('Demandes de réduction')
            ->setDefaultSort(['createdAt' => 'DESC']);
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

        yield FormField::addPanel('Demande de l\'étudiant')->setIcon('fas fa-gift');
        yield AssociationField::new('etudiant', 'Étudiant');
        yield AssociationField::new('offreCompetence', 'Offre de compétence liée');
        yield TextField::new('contextePrestationCiblee', 'Prestation ciblée')->hideOnIndex();

        yield FormField::addPanel('Décision Dis Pos')->setIcon('fas fa-clipboard-check');
        yield ChoiceField::new('statut', 'Statut')
            ->setChoices([
                'En attente' => DemandeReduction::STATUT_EN_ATTENTE,
                'Approuvée' => DemandeReduction::STATUT_APPROUVEE,
                'Appliquée' => DemandeReduction::STATUT_APPLIQUEE,
                'Refusée' => DemandeReduction::STATUT_REFUSEE,
            ])
            ->renderAsBadges([
                DemandeReduction::STATUT_EN_ATTENTE => 'secondary',
                DemandeReduction::STATUT_APPROUVEE => 'info',
                DemandeReduction::STATUT_APPLIQUEE => 'success',
                DemandeReduction::STATUT_REFUSEE => 'danger',
            ]);
        yield TextareaField::new('messageAdmin', 'Message de l\'équipe Dis Pos')->hideOnIndex();

        yield DateTimeField::new('createdAt', 'Soumise le')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Mise à jour')->hideOnForm()->hideOnIndex();
    }
}
