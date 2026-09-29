<?php

namespace App\Controller\Admin;

use App\Entity\DemandeEncadrement;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DemandeEncadrementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DemandeEncadrement::class;
    }
}
