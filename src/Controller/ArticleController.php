<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use App\Service\UploadHelper;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Sluggable\Util\Urlizer;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index( ArticleRepository $repository): Response
    {
        $articles = $repository->getAll();
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }


    #[Route('/article/new', name: 'app_article_new')]
    public function new(EntityManagerInterface $em, Request $request, UploadHelper $uploadHelper)
        {
            $form = $this->createForm(ArticleFormType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                /** @var Article $article */
                $article = $form->getData();
                 /** @var UploadedFile $uploadedFile  */
                $uploadedFile = $form['fileNameImage']->getData();
                if($uploadedFile) {
                    $newFilename = $uploadHelper->uploadArticleImage($uploadedFile);

                    $article->setFileName($newFilename);
                }
                
                $em->persist($article);
                $em->flush();

                $this->addFlash('success', 'Article Created!');
                return $this->redirectToRoute('app_article');
            }
            return $this->render('form/test_form.html.twig', [
                'articleForm' => $form->createView()
            ]);
        }

        #[Route('/article/{id<\d+>}/edit', name: 'app_article_edit')]
        public function edit(Article $article, Request $request, EntityManagerInterface $em, UploadHelper $uploadHelper)
        {
            $form = $this->createForm(ArticleFormType::class, $article);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $form['fileNameImage']->getData();
                if ($uploadedFile) {
                    $newFilename = $uploadHelper->uploadArticleImage($uploadedFile);
                    $article->setFileName($newFilename);
                }
                $em->persist($article);
                $em->flush();
                $this->addFlash('success', 'Article Updated! Inaccuracies squashed!');
                return $this->redirectToRoute('app_article_edit', [
                    'id' => $article->getId(),
                ]);
            }
            return $this->render('form/edit.html.twig', [
                'articleForm' => $form->createView()
            ]);
        }


    
    }

