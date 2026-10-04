<?php

namespace App\Controller\Admin;

use App\Entity\SessionDocument;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\File;

class SessionDocumentCrudController extends AbstractCrudController
{
    public function __construct(
        #[Autowire(service: 'app.session_document_uploader')] private readonly FileUploader $uploader,
        private readonly RequestStack $requestStack,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return SessionDocument::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Support de cours')
            ->setEntityLabelInPlural('Supports de cours')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('session');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('session', 'Session de révision');
        yield TextField::new('originalName', 'Document')->hideOnForm();
        yield Field::new('documentFile', 'Fichier PDF')
            ->setFormType(FileType::class)
            ->setFormTypeOptions([
                'mapped' => false,
                'required' => Crud::PAGE_NEW === $pageName,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => ['application/pdf'],
                        'mimeTypesMessage' => 'Merci de déposer un fichier PDF',
                    ]),
                ],
            ])
            ->onlyOnForms();
        yield DateTimeField::new('createdAt', 'Ajouté le')->hideOnForm();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleUpload($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleUpload($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function handleUpload(SessionDocument $document): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $file = null;
        foreach ($request?->files->all() ?? [] as $group) {
            if (\is_array($group) && ($group['documentFile'] ?? null) instanceof UploadedFile) {
                $file = $group['documentFile'];
                break;
            }
        }

        if ($file instanceof UploadedFile) {
            $document->setOriginalName($file->getClientOriginalName());
            $document->setFilename($this->uploader->upload($file));
        }
    }
}
