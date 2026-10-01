<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\UserStatsProvider;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly UserStatsProvider $userStatsProvider,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->overrideTemplate('crud/detail', 'admin/user_detail.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('roles');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield EmailField::new('email', 'Email');
        yield TextField::new('firstName', 'Prénom');
        yield TextField::new('lastName', 'Nom');
        yield ArrayField::new('roles', 'Rôles');
        yield TextField::new('locale', 'Langue')->hideOnIndex();
        yield TextField::new('country', 'Pays')->hideOnIndex();
        yield TextField::new('bio', 'Bio')->hideOnIndex()->hideOnForm();
        yield DateTimeField::new('createdAt', 'Inscrit le')->hideWhenCreating()->hideWhenUpdating();
        // Password is a hashed value — not editable through this screen.
    }

    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        if (Crud::PAGE_DETAIL === $this->getContext()?->getCrud()?->getCurrentPage()) {
            $user = $responseParameters->get('entity')?->getInstance();
            if ($user instanceof User) {
                $responseParameters->set('userStats', $this->userStatsProvider->getStatsForUser($user));
            }
        }

        return $responseParameters;
    }
}
