<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Startup;
use App\Entity\Project;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        // Tags
        $tags = [];
        foreach ([
            ['IA / Machine Learning', 'ia', '#6c63ff'],
            ['Blockchain', 'blockchain', '#00d2ff'],
            ['SaaS', 'saas', '#ff6584'],
            ['FinTech', 'fintech', '#ffd700'],
            ['HealthTech', 'healthtech', '#4ade80'],
            ['EdTech', 'edtech', '#f87171'],
            ['GreenTech', 'greentech', '#34d399'],
        ] as [$name, $slug, $color]) {
            $tag = (new Tag())->setName($name)->setSlug($slug)->setColor($color);
            $manager->persist($tag);
            $tags[$slug] = $tag;
        }

        // Admin
        $admin = (new User())
            ->setEmail('admin@dispos.io')
            ->setFirstName('Admin')
            ->setLastName('Dis Pos')
            ->setRoles(['ROLE_ADMIN', 'ROLE_USER'])
            ->setBio('Administrator of Dis Pos platform')
            ->setCountry('Tunisia');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // Talent 1
        $talent1 = (new User())
            ->setEmail('talent@dispos.io')
            ->setFirstName('Sarah')
            ->setLastName('Benali')
            ->setRoles(['ROLE_TALENT'])
            ->setBio('Développeuse full-stack spécialisée React & Symfony. 5 ans d\'expérience dans les startups.')
            ->setCountry('France');
        $talent1->setPassword($this->passwordHasher->hashPassword($talent1, 'talent123'));
        $manager->persist($talent1);

        // Talent 2
        $talent2 = (new User())
            ->setEmail('designer@dispos.io')
            ->setFirstName('Karim')
            ->setLastName('Mansouri')
            ->setRoles(['ROLE_TALENT'])
            ->setBio('UI/UX Designer passionné par les expériences digitales innovantes.')
            ->setCountry('Maroc');
        $talent2->setPassword($this->passwordHasher->hashPassword($talent2, 'talent123'));
        $manager->persist($talent2);

        // Investor
        $investor = (new User())
            ->setEmail('investor@dispos.io')
            ->setFirstName('Fatima')
            ->setLastName('Zahra')
            ->setRoles(['ROLE_INVESTOR'])
            ->setBio('Business Angel · 15+ investissements en Afrique et Europe')
            ->setCountry('Tunisie');
        $investor->setPassword($this->passwordHasher->hashPassword($investor, 'investor123'));
        $manager->persist($investor);

        // Startup owner 1
        $founder1 = (new User())
            ->setEmail('startup1@dispos.io')
            ->setFirstName('Yann')
            ->setLastName('Ouarda')
            ->setRoles(['ROLE_STARTUP'])
            ->setCountry('Tunisie');
        $founder1->setPassword($this->passwordHasher->hashPassword($founder1, 'startup123'));
        $manager->persist($founder1);

        // Startup 1
        $startup1 = (new Startup())
            ->setName('AgroSmart AI')
            ->setDescription('Nous révolutionnons l\'agriculture avec l\'intelligence artificielle. Notre plateforme analyse les données satellitaires, météo et IoT pour optimiser les rendements agricoles de 40%. Présents en Tunisie, Maroc et Algérie.')
            ->setSector('AgriTech')
            ->setCountry('Tunisie')
            ->setStage('seed')
            ->setFoundedAt(new \DateTimeImmutable('2023-06-01'))
            ->setOwner($founder1);
        $startup1->addTag($tags['ia'])->addTag($tags['saas']);
        $manager->persist($startup1);

        // Project for Startup 1
        $project1 = (new Project())
            ->setTitle('Développeur Backend Python / FastAPI')
            ->setDescription('Nous cherchons un développeur backend expérimenté pour renforcer notre équipe d\'ingénieurs. Vous travaillerez sur notre API de traitement de données agricoles, l\'intégration de modèles ML et l\'optimisation des performances.')
            ->setStatus(Project::STATUS_OPEN)
            ->setNeededSkills('Python, FastAPI, PostgreSQL, ML, Docker')
            ->setTeamSize(1)
            ->setStartup($startup1);
        $manager->persist($project1);

        $project2 = (new Project())
            ->setTitle('Lead Designer UI/UX — App mobile (iOS/Android)')
            ->setDescription('Création de l\'expérience utilisateur de notre application mobile destinée aux agriculteurs. Vous serez responsable de la conception des flows, des maquettes et du design system complet.')
            ->setStatus(Project::STATUS_OPEN)
            ->setNeededSkills('Figma, Mobile UX, Design System, Prototypage')
            ->setTeamSize(1)
            ->setStartup($startup1);
        $manager->persist($project2);

        // Startup owner 2
        $founder2 = (new User())
            ->setEmail('startup2@dispos.io')
            ->setFirstName('Lina')
            ->setLastName('Cherif')
            ->setRoles(['ROLE_STARTUP'])
            ->setCountry('France');
        $founder2->setPassword($this->passwordHasher->hashPassword($founder2, 'startup123'));
        $manager->persist($founder2);

        // Startup 2
        $startup2 = (new Startup())
            ->setName('EduVerse')
            ->setDescription('La plateforme EdTech qui démocratise l\'accès à l\'éducation de qualité en Afrique. Cours en ligne, mentorat live, et certification reconnue par les entreprises. Plus de 50 000 apprenants actifs.')
            ->setSector('EdTech')
            ->setCountry('France')
            ->setStage('series_a')
            ->setWebsite('https://eduverse.io')
            ->setFoundedAt(new \DateTimeImmutable('2022-01-15'))
            ->setOwner($founder2);
        $startup2->addTag($tags['edtech'])->addTag($tags['saas']);
        $manager->persist($startup2);

        $project3 = (new Project())
            ->setTitle('Ingénieur Full-Stack React / Node.js')
            ->setDescription('Rejoignez l\'équipe technique d\'EduVerse pour construire les prochaines fonctionnalités de notre LMS. Vous participerez à l\'architecture de notre plateforme de streaming de cours et au développement des outils de collaboration en ligne.')
            ->setStatus(Project::STATUS_OPEN)
            ->setNeededSkills('React, Node.js, TypeScript, AWS, WebRTC')
            ->setTeamSize(2)
            ->setStartup($startup2);
        $manager->persist($project3);

        $manager->flush();
    }
}
