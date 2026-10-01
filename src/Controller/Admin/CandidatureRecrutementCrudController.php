<?php

namespace App\Controller\Admin;

use App\Entity\CandidatureRecrutement;
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

class CandidatureRecrutementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CandidatureRecrutement::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Candidature recrutement')
            ->setEntityLabelInPlural('Candidatures recrutement')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('statut');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // L'admin ne crée jamais une candidature émise par un candidat —
            // il ne fait que suivre son statut.
            ->disable(Action::NEW)
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $a) => $a->setLabel('Traiter')->setIcon('fas fa-arrow-right'))
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $a) => $a->setCssClass('text-muted'));
    }

    public function configureFields(string $pageName): iterable
    {
        $statutChoices = [
            'Soumise' => CandidatureRecrutement::STATUT_SOUMISE,
            'Vue' => CandidatureRecrutement::STATUT_VUE,
            'Shortlistée' => CandidatureRecrutement::STATUT_SHORTLISTEE,
            'Refusée' => CandidatureRecrutement::STATUT_REFUSEE,
            'Acceptée' => CandidatureRecrutement::STATUT_ACCEPTEE,
        ];
        $statutBadges = [
            CandidatureRecrutement::STATUT_SOUMISE => 'secondary',
            CandidatureRecrutement::STATUT_VUE => 'info',
            CandidatureRecrutement::STATUT_SHORTLISTEE => 'warning',
            CandidatureRecrutement::STATUT_REFUSEE => 'danger',
            CandidatureRecrutement::STATUT_ACCEPTEE => 'success',
        ];

        yield IdField::new('id')->hideOnForm();

        yield FormField::addPanel('Candidature au poste')->setIcon('fas fa-envelope-open-text');
        yield AssociationField::new('candidat', 'Candidat');
        yield AssociationField::new('offre', 'Offre');
        yield TextareaField::new('lettreMotivation', 'Lettre de motivation')->hideOnIndex();
        yield TextField::new('cvFilename', 'CV')
            ->formatValue(static fn (?string $value) => $value
                ? sprintf('<a href="/uploads/cv/%s" target="_blank" rel="noopener"><i class="fas fa-file-arrow-down"></i> Télécharger le CV</a>', rawurlencode($value))
                : '<span class="text-muted">Aucun CV</span>')
            ->setFormTypeOption('disabled', true);

        yield FormField::addPanel('Suivi de la candidature')->setIcon('fas fa-clipboard-check');
        yield ChoiceField::new('statut', 'Statut')
            ->setChoices($statutChoices)
            ->renderAsBadges($statutBadges);
        yield TextareaField::new('messageEntreprise', 'Message de l\'entreprise')->hideOnIndex()
            ->setHelp('Retour laissé par l\'entreprise sur cette candidature.');

        yield DateTimeField::new('createdAt', 'Soumise le')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Mise à jour')->hideOnForm()->hideOnIndex();
    }
}
