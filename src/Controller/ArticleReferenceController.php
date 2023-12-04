<?php

namespace App\Controller;

use App\Entity\Article;
use App\Service\UploadHelper;
use Doctrine\ORM\EntityManager;
use App\Entity\ArticleReference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleReferenceController extends AbstractController
{
    #[Route('/article/{id}/reference', name: 'app_article_reference_add')]
    public function uploadArticleReference(Article $article, Request $request, UploadHelper $uploadHelper, EntityManager $entityManager,                        ValidatorInterface $validator): Response
    {
         /** @var UploadedFile $uploadedFile */
        
         $uploadedFile = $request->files->get('reference');
         $violations = $validator->validate(
            $uploadedFile,
            [
                new NotBlank(),
                new File([
                    'maxSize' => '1M',
                    'mimeTypes' => [
                        'image/*',
                        'application/pdf',
                        'application/msword',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'text/plain'
                    ]
                    ])
            ]
             
        );
        if ($violations->count() > 0) {
            /** @var ConstraintViolation $violation */
            $violation = $violations[0];
            $this->addFlash('error', $violation->getMessage());
            return $this->redirectToRoute('admin_article_edit', [
                'id' => $article->getId(),
            ]);
        }
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

    #[Route('/article/reference/{id}/download', name: 'app_article_reference_download', methods:'GET')]
    public function downloadArticleReference(ArticleReference $reference)
    {
        

    }
}
