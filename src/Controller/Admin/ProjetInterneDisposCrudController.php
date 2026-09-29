<?php

namespace App\Controller\Admin;

use App\Entity\ProjetInterneDispos;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProjetInterneDisposCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProjetInterneDispos::class;
    }
}
