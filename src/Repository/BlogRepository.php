<?php

namespace App\Repository;

use App\Entity\Blog;
use App\Entity\User;
use App\Filter\BlogFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Blog>
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }


    /**
     * @return array<Blog>
     */
    public function getBlogs(): array
    {
        return $this
            ->createQueryBuilder('b')
            ->OrderBy('b.id', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();

    }

    public function findByBlogFilter(BlogFilter $blogFilter)
    {
        $blogs = $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')
            ->where('1 = 1')
        ;

        if($blogFilter->getUser()) {
            $blogs
                ->andWhere('b.user = :user')
                ->setParameter('user', $blogFilter->getUser());
        }

        if($blogFilter->getTitle()) {
            $blogs
                ->andWhere('b.title LIKE :title')
                ->setParameter('title', '%'.$blogFilter->getTitle().'%');
        }

        $blogs->addOrderBy('b.id', 'DESC');

        return $blogs;
    }

    public function getByTitle(string $title): ?Blog
    {
        return $this->findOneBy(['title' => $title]);
    }

}
