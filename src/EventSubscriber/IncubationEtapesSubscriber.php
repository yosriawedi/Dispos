<?php

namespace App\EventSubscriber;

use App\Entity\EtapeIncubation;
use App\Entity\Incubation;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

/**
 * À la création d'une Incubation, génère automatiquement ses 7 EtapeIncubation
 * (une par phase du parcours), toutes au statut initial "non_demarree".
 */
#[AsDoctrineListener(event: Events::postPersist)]
class IncubationEtapesSubscriber
{
    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Incubation) {
            return;
        }

        if (!$entity->getEtapes()->isEmpty()) {
            // Sécurité : ne jamais dupliquer si les étapes existent déjà
            // (ex. rechargement de fixtures avec des entités pré-remplies).
            return;
        }

        $entityManager = $args->getObjectManager();

        foreach (EtapeIncubation::PHASES_ORDONNEES as $phase) {
            $etape = (new EtapeIncubation())
                ->setIncubation($entity)
                ->setPhase($phase);
            $entity->getEtapes()->add($etape);
            $entityManager->persist($etape);
        }

        $entityManager->flush();
    }
}
