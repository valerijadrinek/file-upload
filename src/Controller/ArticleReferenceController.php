<?php

namespace App\Controller;

use App\Entity\Article;
use App\Service\UploadHelper;
use Doctrine\ORM\EntityManager;
use App\Entity\ArticleReference;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\Serializer\SerializerInterface;

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
                    'maxSize' => '5M',
                    'mimeTypes' => [
                        'image/*',
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'text/plain'
                    ]
                ])
            ]
        );
        if ($violations->count() > 0) {
            return $this->json($violations, 400);
        }
         $filename = $uploadHelper->uploadArticleReference($uploadedFile);
         $articleReference = new ArticleReference($article);
         $articleReference->setFilename($filename);
         $articleReference->setOriginalFilename($uploadedFile->getClientOriginalName() ?? $filename);
         $articleReference->setMimeType($uploadedFile->getMimeType() ?? 'application/octet-stream');
         $entityManager->persist($articleReference);
         $entityManager->flush();

           return $this->json(
            $articleReference,
            201,
            [],
            [
                'groups' => ['main']
            ]
        );
    }

    #[Route('/article/{id}/references', name: 'app_article_list_references', methods:'GET')]
    public function getArticleReferences(Article $article)
    {

        return $this->json($article->getReference(),
                            200,
                            [],
                            [
                                'groups' => ['main']
                            ]);
    }

    #[Route('/article/reference/{id}/download', name: 'app_article_reference_download', methods:'GET')]
    public function downloadArticleReference(ArticleReference $reference, UploadHelper $uploadHelper)
    {

        //this way streaming output to user echoing out content without eating a memory
        $article = $reference->getArticle();
        $response = new StreamedResponse(function() use ($reference, $uploadHelper) {
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $uploadHelper->readStream($reference->getFilePath(), false);
            stream_copy_to_stream($fileStream, $outputStream);
        });

        $response->headers->set('Content-Type', $reference->getMimeType());
        $disposition = HeaderUtils::makeDisposition(//forcing browser to always download the file, vot to try to open it
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $reference->getOriginalFilename()
        );
        $response->headers->set('Content-Disposition', $disposition); //forcing browser to always download the file, vot to try to open it
        return $response;

    }

    #[Route('/article/reference/{id}/delete', name: 'app_article_reference_delete', methods:'DELETE')]
    public function deleteArticleReference(ArticleReference $reference, UploadHelper $uploadHelper, EntityManagerInterface $entityManager)
    {

        $article = $reference->getArticle();

        $entityManager->remove($reference);
        $entityManager->flush();
        $uploadHelper->deleteFile($reference->getFilePath(), false);
        return new Response(null, 204);
    }

    #[Route('/article/reference/{id}/update', name: 'app_article_reference_update', methods:'PATCH, PUT')]
    public function updateArticleReference(ArticleReference $reference, UploadHelper $uploaderHelper, EntityManagerInterface $entityManager,
                                            SerializerInterface $serializer, Request $request)
    {
        $article = $reference->getArticle();
        
        $serializer->deserialize(
            $request->getContent(),
            ArticleReference::class,
            'json',
            [
                'object_to_populate' => $reference,
                'groups' => ['input']
            ]
        );
        $entityManager->persist($reference);
        $entityManager->flush();
        return $this->json(
            $reference,
            200,
            [],
            [
                'groups' => ['main']
            ]
        );
    }


}
