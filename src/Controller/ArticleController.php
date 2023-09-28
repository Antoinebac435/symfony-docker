<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article ;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;


#[Route('/article')]

class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    #[Route('/cree', name: 'app_article_cree')]
    public function creeArticle (EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $article->setTitre('Mon super 1er article')
        ->setTexte('Et licet quocumque oculos flexeris feminas adfatim multas spectare cirratas, quibus, si nupsissent, per aetatem ter iam nixus poterat suppetere liberorum, ad usque taedium pedibus pavimenta tergentes iactari volucriter gyris, dum exprimunt innumera simulacra, quae finxere fabulae theatrales.')
        ->setEtat(true)
        ->setDate(new DateTimeImmutable()); 
        // dd($article); Comme un return (le programme après s'arrête)

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($article);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new article with id '.$article->getId());
    }

    #[Route('/voir/{id}', name: 'app_article_voir')]
    public function voir(EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article found for id '.$id
            );
        }

        return $this->render('/article/index.html.twig', ['article' => $article]);
    }

    #[Route('/voir/', name: 'app_article_voir')]
    public function voirTout(EntityManagerInterface $entityManager): Response
    {
        $allArticle = $entityManager->getRepository(Article::class)->findAll();

        if (!$allArticle) {
            throw $this->createNotFoundException(
                'Empty'
            );
        }

     
        return $this->render('/article/index.html.twig', ['allArticle' => $allArticle]);
    }
}
