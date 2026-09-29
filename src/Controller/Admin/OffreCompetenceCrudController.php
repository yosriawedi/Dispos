<?php

namespace App\Controller\Admin;

use App\Entity\OffreCompetence;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OffreCompetenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OffreCompetence::class;
    }
}
