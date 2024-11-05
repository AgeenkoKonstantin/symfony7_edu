<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{

    #[Route('/blog/{id}', name: 'blog_view')]
    public function index(Blog $blog): Response
    {
        $form = $this->createForm(
            CommentType::class,
            null,
            ['action' => $this->generateUrl(
                'blog_add_comment', ['blog' => $blog->getId()])]
        );
        return $this->render('default/blog.html.twig', ['blog' => $blog, 'form' => $form->createView()]);
    }


    #[Route('/exp', name: 'blog_exp')]
    public function experimental(EntityManagerInterface $entityManager): Response
    {
        $blog = new Blog($this->getUser());
        $blog->setTitle('Blog experimental')->setDescription('Blog experimental')->setText('Blog experimental');

        $blog->addComment((new Comment())->setText('qwe asd zxc'));


        $entityManager->persist($blog);
        $entityManager->flush();
        exit;
    }

}