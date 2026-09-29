<?php

namespace App\Controller\Admin;

use App\Entity\Matiere;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MatiereCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Matiere::class;
    }
}
