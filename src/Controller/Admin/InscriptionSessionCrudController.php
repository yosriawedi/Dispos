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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
            // L'admin ne crée jamais une inscription à la place de l'étudiant
            // — il ne fait que suivre/gérer les places.
            ->disable(Action::NEW)
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
        yield TextField::new('methodePaiement', 'Méthode de paiement')->hideOnIndex()
            ->formatValue(static fn (?string $value) => match ($value) {
                InscriptionSession::PAIEMENT_D17 => 'D17',
                InscriptionSession::PAIEMENT_RIB => 'Virement bancaire (RIB)',
                default => '—',
            })
            ->setFormTypeOption('disabled', true)
            ->setHelp('Paiement simulé — aucune transaction réelle n\'est effectuée sur ce parcours.');
        yield DateTimeField::new('createdAt', 'Inscrit le')->hideOnForm();
    }
}
