<?php

namespace App\Controller\Admin;

use App\Entity\ProjetInterneDispos;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProjetInterneDisposCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProjetInterneDispos::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Projet interne')
            ->setEntityLabelInPlural('Projets internes')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('statut')->add('domaine');
    }

    public function configureFields(string $pageName): iterable
    {
        $statutChoices = [
            'Ouvert' => ProjetInterneDispos::STATUT_OUVERT,
            'En cours' => ProjetInterneDispos::STATUT_EN_COURS,
            'Terminé' => ProjetInterneDispos::STATUT_TERMINE,
            'Archivé' => ProjetInterneDispos::STATUT_ARCHIVE,
        ];
        $statutBadges = [
            ProjetInterneDispos::STATUT_OUVERT => 'info',
            ProjetInterneDispos::STATUT_EN_COURS => 'info',
            ProjetInterneDispos::STATUT_TERMINE => 'success',
            ProjetInterneDispos::STATUT_ARCHIVE => 'secondary',
        ];

        yield IdField::new('id')->hideOnForm();
        yield TextField::new('titre', 'Titre');
        yield TextField::new('domaine', 'Domaine');
        yield IntegerField::new('placesMax', 'Places max')->hideOnIndex();
        yield DateTimeField::new('dateLimite', 'Date limite')
            ->formatValue(static fn (?\DateTimeInterface $value) => $value?->format('d/m/Y') ?? 'Sans date limite');
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield TextareaField::new('competencesRequises', 'Compétences requises')->hideOnIndex();
        yield ChoiceField::new('statut', 'Statut')
            ->setChoices($statutChoices)
            ->renderAsBadges($statutBadges);
        yield DateTimeField::new('createdAt', 'Créé le')->hideOnForm();
    }
}
