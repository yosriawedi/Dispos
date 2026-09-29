<?php

namespace App\Controller\Admin;

use App\Entity\DemandeConsultation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class DemandeConsultationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DemandeConsultation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('incubation')->hideOnForm();
        yield DateTimeField::new('dateDemande')->hideOnForm();
        yield TextareaField::new('message')->hideOnForm();
        yield ChoiceField::new('statut')->setChoices([
            'En attente' => DemandeConsultation::STATUT_EN_ATTENTE,
            'Traitée' => DemandeConsultation::STATUT_TRAITEE,
            'Refusée' => DemandeConsultation::STATUT_REFUSEE,
        ]);
        yield TextareaField::new('reponseAdmin', 'Réponse de l\'équipe DisPos');
    }
}
