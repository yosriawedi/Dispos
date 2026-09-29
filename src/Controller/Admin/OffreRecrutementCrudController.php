<?php

namespace App\Controller\Admin;

use App\Entity\OffreRecrutement;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OffreRecrutementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OffreRecrutement::class;
    }
}
