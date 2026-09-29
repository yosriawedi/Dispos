<?php

namespace App\Controller\Admin;

use App\Entity\CandidatureRecrutement;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CandidatureRecrutementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CandidatureRecrutement::class;
    }
}
