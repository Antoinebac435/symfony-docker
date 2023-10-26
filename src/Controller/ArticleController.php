<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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

    #[IsGranted('ROLE_USER')]
    #[Route('/creer', name: 'app_article_creer')]
    public function creerArticle(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();

        $form = $this->createFormBuilder($article)
            ->add('titre', TextType::class)
            ->add('texte', TextareaType::class)
            ->add('save', SubmitType::class, ['label' => 'Créer l\'article'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_voir');
        }

        return $this->render('article/creer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/voir', name: 'app_article_tousvoir')]
    public function voirTout(EntityManagerInterface $entityManager): Response
    {
        $allArticle = $entityManager->getRepository(Article::class)->findAll();

        if (!$allArticle) {
            throw $this->createNotFoundException(
                'Empty'
            );
        }
        return $this->render('article/index.html.twig', ['allArticle' => $allArticle]);
    }

    #[Route("/delete/{id}", name: "app_article_delete")]
    public function deleteArticle($id, EntityManagerInterface $em): Response
    {
        $article = $em->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'Aucun article trouvé avec l\'id ' . $id
            );
        }

        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('app_article_tousvoir');
    }

    #[Route("/edit/{id}", name: "app_article_edit")]
    public function editArticle(
        $id,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $article = $em->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                "No article found for id " . $id
            );
        }

        if ($request->isMethod("POST")) {
            $article->setTitle($request->request->get("title"));
            $article->setDescription($request->request->get("description"));
            $em->flush();

            return $this->redirectToRoute("app_article_voir");
        }

        return $this->render("article/edit.html.twig", [
            "article" => $article,
        ]);
    }

    
}
