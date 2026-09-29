<?php

namespace App\Controller\Admin;

use App\Entity\Startup;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class StartupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Startup::class;
    }
}
