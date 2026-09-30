<?php

namespace App\Controller\Admin;

use App\Entity\OffreRecrutement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OffreRecrutementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OffreRecrutement::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Offre de recrutement')
            ->setEntityLabelInPlural('Offres de recrutement')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('statut')
            ->add('typeContrat');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $a) => $a->setLabel('Traiter')->setIcon('fas fa-arrow-right'))
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $a) => $a->setCssClass('text-muted'));
    }

    public function configureFields(string $pageName): iterable
    {
        $typeChoices = [
            'CDI' => OffreRecrutement::TYPE_CDI,
            'CDD' => OffreRecrutement::TYPE_CDD,
            'Stage' => OffreRecrutement::TYPE_STAGE,
            'Freelance' => OffreRecrutement::TYPE_FREELANCE,
            'Alternance' => OffreRecrutement::TYPE_ALTERNANCE,
        ];

        $statutChoices = [
            'Publiée' => OffreRecrutement::STATUT_PUBLIEE,
            'Pourvue' => OffreRecrutement::STATUT_POURVUE,
            'Expirée' => OffreRecrutement::STATUT_EXPIREE,
        ];
        $statutBadges = [
            OffreRecrutement::STATUT_PUBLIEE => 'info',
            OffreRecrutement::STATUT_POURVUE => 'success',
            OffreRecrutement::STATUT_EXPIREE => 'secondary',
        ];

        yield IdField::new('id')->hideOnForm();

        yield FormField::addPanel('Offre publiée par l\'entreprise')->setIcon('fas fa-building');
        yield AssociationField::new('entreprise', 'Entreprise');
        yield TextField::new('poste', 'Poste');
        yield ChoiceField::new('typeContrat', 'Type de contrat')->setChoices($typeChoices);
        yield TextField::new('localisation', 'Localisation')->hideOnIndex();
        // renderAsSwitch(false) : le switch interactif par défaut d'EasyAdmin
        // génère un <label> systématiquement vide (bug du template vendor),
        // laissant le contrôle sans nom accessible. Le badge statique Oui/Non
        // porte son propre texte visible, donc pas de souci d'accessibilité.
        yield BooleanField::new('teletravail', 'Télétravail')->renderAsSwitch(false);
        yield TextField::new('salaire', 'Salaire')->hideOnIndex();
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield TextareaField::new('competences', 'Compétences recherchées')->hideOnIndex();
        yield DateTimeField::new('dateExpiration', 'Expire le')
            ->formatValue(static fn (?\DateTimeInterface $value) => $value?->format('d/m/Y') ?? 'Sans date limite');

        yield FormField::addPanel('Suivi Dis Pos')->setIcon('fas fa-clipboard-check');
        yield ChoiceField::new('statut', 'Statut')
            ->setChoices($statutChoices)
            ->renderAsBadges($statutBadges);

        yield DateTimeField::new('createdAt', 'Publiée le')->hideOnForm();
    }
}
