<?php

namespace App\Controller\Admin;

use App\Entity\SessionRevision;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SessionRevisionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SessionRevision::class;
    }
}
