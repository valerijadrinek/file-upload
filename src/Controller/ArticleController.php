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
    public function new(EntityManagerInterface $em, Request $request)
        {
            $form = $this->createForm(ArticleFormType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                /** @var Article $article */
                $article = $form->getData();
                 /** @var UploadedFile $uploadedFile  */
                $uploadedFile = $form['fileNameImage']->getData();
                if($uploadedFile) {
                    $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/article_image';
                    $uuid = Uuid::v4();
                    $filename = Urlizer::urlize($uploadedFile->getClientOriginalName()) .'-'. $uuid . '.' . $uploadedFile->guessExtension(); 
                    $uploadedFile->move($destination, 
                                        $filename);
                    $article->setFileName($filename);
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
    
    }

