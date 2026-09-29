<?php

namespace App\Controller\Admin;

use App\Entity\InscriptionSession;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class InscriptionSessionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return InscriptionSession::class;
    }
}
