<?php

namespace App\Controller\Admin;

use App\Entity\CandidatureFormateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CandidatureFormateurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CandidatureFormateur::class;
    }
}
