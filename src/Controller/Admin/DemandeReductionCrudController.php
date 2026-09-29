<?php

namespace App\Controller\Admin;

use App\Entity\DemandeReduction;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DemandeReductionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DemandeReduction::class;
    }
}
