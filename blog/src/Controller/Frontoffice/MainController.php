<?php

namespace App\Controller\Frontoffice;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/homepage", name="homepage_"), methods={"GET"})
*/
class MainController extends AbstractController
{
    /**
     * @Route("", name="browse"), methods={"GET"})
     */
    public function browse(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();
        return $this->render('frontoffice/main/browse.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/article/{slug}", name="read"), methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read($slug, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->find($slug);
        return $this->render('frontoffice/read.html.twig', [
            'article' => $article
        ]);
    }
}
