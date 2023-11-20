<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Article;
use App\Form\ArticleFormType;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }


    #[Route('/article/new', name: 'app_article_new')]
    public function new(EntityManagerInterface $em, Request $request)
        {
            $form = $this->createForm(ArticleFormType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                /** @var Article $article */
                $article = $form->getData();
                dd($form['imageFile']->getData());
                $em->persist($article);
                $em->flush();
                $this->addFlash('success', 'Article Created!');
                return $this->redirectToRoute('app_article');
            }
            return $this->render('article_admin/new.html.twig', [
                'articleForm' => $form->createView()
            ]);
        }
    
    }

