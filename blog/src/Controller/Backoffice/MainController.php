<?php

namespace App\Controller\Backoffice;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
     * @Route("/backoffice", name="backoffice_")
     */
class MainController extends AbstractController
{    
    
    /**
     * @Route("", name="browse"), methods={"GET"})
     */
    public function browse(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();
        return $this->render('backoffice/main/browse.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/read/{id}", name="read"), methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read($id, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->find($id); 
        return $this->render('backoffice/main/read.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"}, requirements={"id"="\d+"}) 
     */
    public function edit($id, Request $request, Article $article, ArticleRepository $articleRepository, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $article = $articleRepository->find($id); 
        $articleForm = $this->createForm(ArticleType::class, $article);
        $articleForm
            ->remove('date_created');
        $articleForm->handleRequest($request);

        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            //Make title into a slug for the URL
            $articleSlug = $slugger->slug($article->getId() . '-' . $article->getTitle());
            $article->setSlug($articleSlug);
            //Upload a file
            $uploadedFile = $articleForm['cover']->getData();
            $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
            //Slug the file name and save
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $sluggedName = $slugger->slug($originalFilename);
            $newFilename = $sluggedName.'.'.$uploadedFile->guessExtension();
            $uploadedFile->move(
                $destination,
                $newFilename
            );
            $article->setCover($newFilename);
            $article->setCover($newFilename);
            $entityManager = $doctrine->getManager();           
            $entityManager->flush();
            $this->addFlash('success', "L'article '{$article->getTitle()}' a été mis à jour");
            return $this->redirectToRoute('backoffice_browse');
        }
        return $this->render('backoffice/main/add.html.twig', [
            'article_form' => $articleForm->createView(),
            'article' => $article,
            'page' => 'edit',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET", "POST"})
     */
    public function add(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $articleForm = $this->createForm(ArticleType::class, $article);
        $articleForm
            ->remove('date_created');
        $articleForm->handleRequest($request);

        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            //Make title into a slug for the URL
            $articleSlug = $slugger->slug($article->getTitle());
            $article->setSlug($articleSlug);
            $article->setDateCreated(new DateTimeImmutable());
            //Upload a file
            $uploadedFile = $articleForm['cover']->getData();
            $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
            //Slug the file name and save
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $sluggedName = $slugger->slug($originalFilename);
            $newFilename = $sluggedName.'.'.$uploadedFile->guessExtension();
            $uploadedFile->move(
                $destination,
                $newFilename
            );
            $article->setCover($newFilename);

            $entityManager = $doctrine->getManager();           
            $entityManager->persist($article);  
            $entityManager->flush();
            $this->addFlash('success', "L'article '{$article->getTitle()}' a été crée");
            return $this->redirectToRoute('backoffice_browse');
        }
        return $this->render('backoffice/main/add.html.twig', [
            'article_form' => $articleForm->createView(),
            'article' => $article,
            'page' => 'create'
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function delete($id, Article $article, ArticleRepository $articleRepository, ManagerRegistry $doctrine): Response
    {
        $article = $articleRepository->find($id); 
        $this->addFlash('danger', "L'article '{$article->getTitle()}' a été éffacé");
        $entityManager = $doctrine->getManager();   
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('backoffice_browse');
    }

}
