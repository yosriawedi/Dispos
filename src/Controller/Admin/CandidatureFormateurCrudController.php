<?php

namespace App\Controller\Admin;

use App\Entity\CandidatureFormateur;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CandidatureFormateurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CandidatureFormateur::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Candidature formateur')
            ->setEntityLabelInPlural('Candidatures formateurs')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('statut');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // L'admin ne crée jamais une candidature émise par un étudiant/
            // talent — il ne fait que valider/refuser.
            ->disable(Action::NEW)
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $a) => $a->setLabel('Traiter')->setIcon('fas fa-arrow-right'))
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $a) => $a->setCssClass('text-muted'));
    }

    public function configureFields(string $pageName): iterable
    {
        $statutChoices = [
            'Soumise' => CandidatureFormateur::STATUT_SOUMISE,
            'En revue' => CandidatureFormateur::STATUT_EN_REVIEW,
            'Validée' => CandidatureFormateur::STATUT_VALIDEE,
            'Refusée' => CandidatureFormateur::STATUT_REFUSEE,
        ];
        $statutBadges = [
            CandidatureFormateur::STATUT_SOUMISE => 'secondary',
            CandidatureFormateur::STATUT_EN_REVIEW => 'info',
            CandidatureFormateur::STATUT_VALIDEE => 'success',
            CandidatureFormateur::STATUT_REFUSEE => 'danger',
        ];

        yield IdField::new('id')->hideOnForm();

        yield FormField::addPanel('Candidature du formateur')->setIcon('fas fa-chalkboard-teacher');
        yield AssociationField::new('candidat', 'Candidat');
        yield TextField::new('matieres', 'Matières proposées');
        yield TextareaField::new('stacks', 'Compétences techniques')->hideOnIndex();
        yield TextareaField::new('experience', 'Expérience')->hideOnIndex();
        yield TextareaField::new('diplomes', 'Diplômes')->hideOnIndex();
        yield TextField::new('cvFilename', 'CV')
            ->formatValue(static fn (?string $value) => $value
                ? sprintf('<a href="/uploads/cv/%s" target="_blank" rel="noopener"><i class="fas fa-file-arrow-down"></i> Télécharger le CV</a>', rawurlencode($value))
                : '<span class="text-muted">Aucun CV</span>')
            ->setFormTypeOption('disabled', true);
        yield IntegerField::new('disponibiliteHeures', 'Disponibilité (h/semaine)')->hideOnIndex();
        yield NumberField::new('tarifHoraire', 'Tarif horaire (TND)')->hideOnIndex();

        yield FormField::addPanel('Décision Dis Pos')->setIcon('fas fa-clipboard-check');
        yield ChoiceField::new('statut', 'Statut')
            ->setChoices($statutChoices)
            ->renderAsBadges($statutBadges);
        yield TextareaField::new('noteAdmin', 'Note interne')->hideOnIndex()
            ->setHelp('Visible uniquement par l\'équipe Dis Pos, jamais par le candidat.');

        yield DateTimeField::new('createdAt', 'Soumise le')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Mise à jour')->hideOnForm()->hideOnIndex();
    }
}
