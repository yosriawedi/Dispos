<?php

namespace App\Controller\Admin;

use App\Entity\SessionRevision;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SessionRevisionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SessionRevision::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        // Tri sur la date de début (prochaine session en premier) plutôt que
        // createdAt : c'est un planning, pas une file de demandes à traiter.
        return $crud
            ->setEntityLabelInSingular('Session de révision')
            ->setEntityLabelInPlural('Sessions de révision')
            ->setDefaultSort(['dateDebut' => 'ASC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('statut')->add('matiere');
    }

    public function configureFields(string $pageName): iterable
    {
        $statutChoices = [
            'Planifiée' => SessionRevision::STATUT_PLANIFIEE,
            'En cours' => SessionRevision::STATUT_EN_COURS,
            'Terminée' => SessionRevision::STATUT_TERMINEE,
            'Annulée' => SessionRevision::STATUT_ANNULEE,
        ];
        $statutBadges = [
            SessionRevision::STATUT_PLANIFIEE => 'secondary',
            SessionRevision::STATUT_EN_COURS => 'info',
            SessionRevision::STATUT_TERMINEE => 'success',
            SessionRevision::STATUT_ANNULEE => 'danger',
        ];

        yield IdField::new('id')->hideOnForm();
        yield TextField::new('titre', 'Titre');
        yield AssociationField::new('matiere', 'Matière');
        yield AssociationField::new('formateur', 'Formateur');
        yield DateTimeField::new('dateDebut', 'Début');
        yield DateTimeField::new('dateFin', 'Fin')->hideOnIndex();
        yield TextField::new('lieu', 'Lieu / lien visio')->hideOnIndex();
        yield IntegerField::new('placesMax', 'Places max')->hideOnIndex();
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield ChoiceField::new('statut', 'Statut')
            ->setChoices($statutChoices)
            ->renderAsBadges($statutBadges);
    }
}
