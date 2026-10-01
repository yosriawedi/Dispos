<?php

namespace App\Controller\Admin;

use App\Entity\OffreCompetence;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OffreCompetenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OffreCompetence::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Offre de compétence')
            ->setEntityLabelInPlural('Offres de compétences')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('statut');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // L'admin ne crée jamais une offre émise par un étudiant — il
            // ne fait que valider/refuser.
            ->disable(Action::NEW)
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $a) => $a->setLabel('Traiter')->setIcon('fas fa-arrow-right'))
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $a) => $a->setCssClass('text-muted'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield FormField::addPanel('Proposition de l\'étudiant')->setIcon('fas fa-handshake');
        yield AssociationField::new('etudiant', 'Étudiant');
        yield TextField::new('titre', 'Titre');
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield TextareaField::new('stack', 'Stack technique')->hideOnIndex();
        yield IntegerField::new('heuresEstimees', 'Heures estimées')->hideOnIndex();

        yield FormField::addPanel('Décision Dis Pos')->setIcon('fas fa-clipboard-check');
        yield ChoiceField::new('statut', 'Statut')
            ->setChoices([
                'Soumise' => OffreCompetence::STATUT_SOUMISE,
                'Validée' => OffreCompetence::STATUT_VALIDEE,
                'Refusée' => OffreCompetence::STATUT_REFUSEE,
            ])
            ->renderAsBadges([
                OffreCompetence::STATUT_SOUMISE => 'secondary',
                OffreCompetence::STATUT_VALIDEE => 'success',
                OffreCompetence::STATUT_REFUSEE => 'danger',
            ]);
        yield TextareaField::new('noteAdmin', 'Note interne')->hideOnIndex()
            ->setHelp('Visible uniquement par l\'équipe Dis Pos, jamais par l\'étudiant.');

        yield DateTimeField::new('createdAt', 'Soumise le')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Mise à jour')->hideOnForm()->hideOnIndex();
    }
}
