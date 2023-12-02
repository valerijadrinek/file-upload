<?php

namespace App\Controller;

use App\Entity\Article;
use App\Service\UploadHelper;
use Doctrine\ORM\EntityManager;
use App\Entity\ArticleReference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleReferenceController extends AbstractController
{
    #[Route('/article/{id}/reference', name: 'app_article_reference_add')]
    public function uploadArticleReference(Article $article, Request $request, UploadHelper $uploadHelper, EntityManager $entityManager): Response
    {
         /** @var UploadedFile $uploadedFile */
         $uploadedFile = $request->files->get('reference');
         $filename = $uploadHelper->uploadArticleReference($uploadedFile);
         $articleReference = new ArticleReference($article);
         $articleReference->setFilename($filename);
         $articleReference->setOriginalFilename($uploadedFile->getClientOriginalName() ?? $filename);
         $articleReference->setMimeType($uploadedFile->getMimeType() ?? 'application/octet-stream');
         $entityManager->persist($articleReference);
         $entityManager->flush();
         return $this->redirectToRoute('app_article_edit', [
             'id' => $article->getId(),
         ]);
    }
}
