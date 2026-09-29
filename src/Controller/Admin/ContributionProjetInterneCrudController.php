<?php

namespace App\Controller\Admin;

use App\Entity\ContributionProjetInterne;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ContributionProjetInterneCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContributionProjetInterne::class;
    }
}
