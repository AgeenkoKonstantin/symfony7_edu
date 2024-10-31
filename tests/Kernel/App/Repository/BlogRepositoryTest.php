<?php

namespace App\Tests\Kernel\App\Repository;

use App\Factory\UserFactory;
use App\Repository\BlogRepository;
use App\Tests\Helpers\KernelTestCaseUnit;
use App\Factory\BlogFactory;

class BlogRepositoryTest extends KernelTestCaseUnit
{

    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $user = UserFactory::createOne();

        BlogFactory::createOne( ['user' => $user, 'title' => 'blog title']);
        BlogFactory::createMany(7, ['user' => $user]);



        $blogRepository = static::getContainer()->get(BlogRepository::class);

        $blogs = $blogRepository->getBlogs();

        $this->assertCount(6, $blogs);
        $this->assertSame($blogs[0]->getTitle(), 'blog title');

    }
}
