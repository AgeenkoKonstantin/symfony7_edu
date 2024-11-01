<?php

namespace App\MessageHandler;


use App\Message\ContentWatchJob;
use App\Repository\BlogRepository;
use App\Service\ContentWatchApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ContentWatchHandler
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BlogRepository         $blogRepository,
        private readonly ContentWatchApi        $contentWatchApi)
    {
    }

    public function __invoke(
        ContentWatchJob $contentWatchJob)
    {
        $id = (int)$contentWatchJob->getContent();

        $blog = $this->blogRepository->find($id);

        $blog->setPercent(
                $this->contentWatchApi->checkText($blog->getText())
            );

        $this->entityManager->flush();
    }
}