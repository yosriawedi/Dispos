<?php

namespace App\Controller\Admin;

use App\Entity\ContributionProjetInterne;
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

class ContributionProjetInterneCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContributionProjetInterne::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Contribution')
            ->setEntityLabelInPlural('Contributions')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('statut');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // L'admin ne crée jamais une contribution émise par un étudiant
            // — il ne fait qu'accepter/refuser.
            ->disable(Action::NEW)
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $a) => $a->setLabel('Traiter')->setIcon('fas fa-arrow-right'))
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $a) => $a->setCssClass('text-muted'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield FormField::addPanel('Contribution de l\'étudiant')->setIcon('fas fa-toolbox');
        yield AssociationField::new('etudiant', 'Étudiant');
        yield AssociationField::new('projetInterne', 'Projet interne');
        yield TextareaField::new('message', 'Message')->hideOnIndex();

        yield FormField::addPanel('Décision Dis Pos')->setIcon('fas fa-clipboard-check');
        yield ChoiceField::new('statut', 'Statut')
            ->setChoices([
                'En attente' => ContributionProjetInterne::STATUT_EN_ATTENTE,
                'Acceptée' => ContributionProjetInterne::STATUT_ACCEPTEE,
                'Livrée' => ContributionProjetInterne::STATUT_LIVREE,
                'Refusée' => ContributionProjetInterne::STATUT_REFUSEE,
            ])
            ->renderAsBadges([
                ContributionProjetInterne::STATUT_EN_ATTENTE => 'secondary',
                ContributionProjetInterne::STATUT_ACCEPTEE => 'info',
                ContributionProjetInterne::STATUT_LIVREE => 'success',
                ContributionProjetInterne::STATUT_REFUSEE => 'danger',
            ]);
        yield TextareaField::new('noteAdmin', 'Note interne')->hideOnIndex()
            ->setHelp('Visible uniquement par l\'équipe Dis Pos, jamais par l\'étudiant.');

        yield DateTimeField::new('createdAt', 'Soumise le')->hideOnForm();
    }
}
