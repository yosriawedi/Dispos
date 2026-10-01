<?php

namespace App\Controller\Admin;

use App\Entity\InscriptionSession;
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

class InscriptionSessionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return InscriptionSession::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Inscription')
            ->setEntityLabelInPlural('Inscriptions')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('statut');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $a) => $a->setCssClass('text-muted'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('etudiant', 'Étudiant');
        yield AssociationField::new('session', 'Session de révision');
        yield ChoiceField::new('statut', 'Statut')
            ->setChoices([
                'Confirmée' => InscriptionSession::STATUT_CONFIRMEE,
                'En attente' => InscriptionSession::STATUT_EN_ATTENTE,
                'Annulée' => InscriptionSession::STATUT_ANNULEE,
            ])
            ->renderAsBadges([
                InscriptionSession::STATUT_CONFIRMEE => 'success',
                InscriptionSession::STATUT_EN_ATTENTE => 'secondary',
                InscriptionSession::STATUT_ANNULEE => 'danger',
            ]);
        yield TextareaField::new('noteEtudiant', 'Note de l\'étudiant')->hideOnIndex();
        yield DateTimeField::new('createdAt', 'Inscrit le')->hideOnForm();
    }
}
