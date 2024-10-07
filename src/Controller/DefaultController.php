<?php

namespace App\Controller;

use App\Entity\Blog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'blog_default')]
    public function index(EntityManagerInterface $em): Response
    {

        $blog = (new Blog())
            ->setTitle("Title")
            ->setText("Text")
            ->setDescription("Description");

        $em->persist($blog);
        $em->flush();

        return $this->render('default/index.html.twig', []);
    }
}
