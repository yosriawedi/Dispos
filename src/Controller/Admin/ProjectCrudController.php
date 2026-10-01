<?php

namespace App\Controller\Admin;

use App\Entity\Project;
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

class ProjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Projet')
            ->setEntityLabelInPlural('Projets')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('status');
    }

    public function configureFields(string $pageName): iterable
    {
        $statusChoices = [
            'Ouvert' => Project::STATUS_OPEN,
            'En cours' => Project::STATUS_IN_PROGRESS,
            'Clôturé' => Project::STATUS_CLOSED,
        ];
        $statusBadges = [
            Project::STATUS_OPEN => 'info',
            Project::STATUS_IN_PROGRESS => 'info',
            Project::STATUS_CLOSED => 'secondary',
        ];

        yield IdField::new('id')->hideOnForm();
        yield TextField::new('title', 'Titre');
        yield AssociationField::new('startup', 'Startup');
        yield IntegerField::new('teamSize', 'Taille d\'équipe')->hideOnIndex();
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield TextareaField::new('neededSkills', 'Compétences recherchées')->hideOnIndex();
        yield ChoiceField::new('status', 'Statut')
            ->setChoices($statusChoices)
            ->renderAsBadges($statusBadges);
        yield DateTimeField::new('createdAt', 'Créé le')->hideOnForm();
    }
}
