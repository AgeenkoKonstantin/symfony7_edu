<?php

namespace App\Tests\Kernel\Service;

use App\Factory\UserFactory;
use App\Repository\BlogRepository;
use App\Repository\UserRepository;
use App\Service\HttpClient;
use App\Service\NewsGrabber;
use App\Tests\Helpers\KernelTestCaseUnit;
use Psr\Log\LoggerInterface;

class NewsGrabberTest extends KernelTestCaseUnit
{


    public function testSomething(): void
    {
        self::bootKernel();

        /**
         * @AppEntityUserProxy
         */
        $user = UserFactory::createOne()->_real();

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('find')->willReturn($user);

        static::getContainer()->set(UserRepository::class, $userRepository);

        $httpClient = $this->createMock(HttpClient::class);
        $httpClient
            ->method('get')
            ->willReturnCallback(function ($url) {
                if ($url == 'https://www.engadget.com/news/') {
                    return file_get_contents('tests/DataProvider/index.html');
                } else {
                    static $index = 0;
                    return file_get_contents('tests/DataProvider/news' . ++$index . '.html');
                }
            })
        ;

        static::getContainer()->set(HttpClient::class, $httpClient);

        $blogRepository = static::getContainer()->get(BlogRepository::class);
        assert($blogRepository instanceof BlogRepository);

        $newsGrabber = static::getContainer()->get(NewsGrabber::class);
        assert($newsGrabber instanceof NewsGrabber);

        $logger = $this->createMock(LoggerInterface::class);

        $newsGrabber->setLogger($logger)->importNews();



        $blogs = $blogRepository->findAll();
        self::assertCount(20, $blogs);

    }
}
