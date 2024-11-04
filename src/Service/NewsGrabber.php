<?php

namespace App\Service;

use App\Entity\Blog;
use App\Repository\BlogRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DomCrawler\Crawler;

class NewsGrabber
{
    private LoggerInterface $logger;
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly BlogRepository $blogRepository,
        private readonly ParameterBagInterface $parameterBag,
        private readonly HttpClient $httpClient,
    ) {
    }

    /**
     * @param int|null $count
     * @param bool|null $dryRun
     * @return void
     */
    public function importNews(?int $count = null, ?bool $dryRun = false): void
    {
        $this->logger->notice("Importing news...");

        $parsedNews = [];

        $crawler = new Crawler($this->httpClient->get('https://www.engadget.com/news/'));
        $crawler->filter('h4.My\(0\) > a')->each(function (Crawler $crawler) use (&$parsedNews, $count) {

            if( $count && count($parsedNews) >= $count) {
                return;
            }

            $parsedNews[] = [
                'title' => $crawler->text(),
                'href' => $crawler->attr('href'),
            ];
        });
        unset($crawler);
        $this->logger->info(sprintf('Get %d news', count($parsedNews)));

        foreach ($parsedNews as &$item) {
            $crawler = new Crawler($this->httpClient->get('https://www.engadget.com' . $item['href']));
            $crawlerBody = $crawler->filter('div.caas-body')->first();
            $item['text'] = $crawlerBody->text();

            $this->logger->info(sprintf('Parsing news %s', $item['title']));
        }
        unset($item);


        $this->saveNews($parsedNews, $dryRun);
    }

    private function saveNews(array $parsedNews, bool $dryRun): void
    {
        $this->logger->notice("Save news");

        $blogUser = $this->userRepository->find($this->parameterBag->get('autoblog'));

        if(!$blogUser) {
            $this->logger->error(sprintf('User %d not found', $this->parameterBag->get('autoblog')));
            return;
        }

        if($dryRun) {
            return;
        }

        foreach ($parsedNews as $item) {
            if($this->blogRepository->getByTitle($item['title'])) {
                $this->logger->info(sprintf('News already exists %s', $item['title']));
                continue;
            }

            $this->logger->info(sprintf('Save blog %s', $item['title']));
            $blog = new Blog($blogUser);
            $blog
                ->setTitle($item['title'])
                ->setText($item['text'])
                ->setDescription(mb_substr($item['text'], 0, 200));

            $this->entityManager->persist($blog);
        }

        $this->entityManager->flush();


    }

    public function setLogger(LoggerInterface $logger): NewsGrabber
    {
        $this->logger = $logger;

        return $this;
    }


}