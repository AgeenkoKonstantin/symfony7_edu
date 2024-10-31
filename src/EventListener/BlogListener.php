<?php

namespace App\EventListener;

use App\Entity\Blog;
use App\Entity\User;
use App\Message\ContentWatchJob;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;


#[AsDoctrineListener(event: Events::postFlush, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postPersist, priority: 500, connection: 'default')]
class BlogListener
{

    private array $entities = [];

    public function __construct(private MessageBusInterface $bus)
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function postFlush(PostFlushEventArgs $event): void
    {
        foreach ($this->entities as $entity) {
            $this->bus->dispatch(new ContentWatchJob($entity->getId()));
        }
    }

    public function postPersist(PostPersistEventArgs $event): void
    {
        if($event->getObject() instanceof Blog) {
            $this->entities[] = $event->getObject();
        }
    }
}