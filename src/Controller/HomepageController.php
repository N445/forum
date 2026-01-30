<?php

namespace App\Controller;

use App\Entity\Post\Post;
use App\Repository\Post\PostRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomepageController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
    )
    {
    }

    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        return $this->render('homepage/index.html.twig', [
            'posts' => $this->postRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_post')]
    public function post(#[MapEntity] Post $post): Response
    {
        return $this->render('homepage/post.html.twig', [
            'post' => $post,
        ]);
    }
}
