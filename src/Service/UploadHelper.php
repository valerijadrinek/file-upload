<?php
namespace App\Service;

use Symfony\Component\Uid\Uuid;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Asset\Context\RequestStackContext;

class UploadHelper 
{
    const ARTICLE_IMAGE = 'article_image';
    
    public function __construct( private string $uploadsPath, private RequestStackContext $requestStackContext) 
    {
        
    }
    public function uploadArticleImage(UploadedFile $uploadedFile) : string
    {

        $destination = $this->uploadsPath. '/' . self::ARTICLE_IMAGE;

        $uuid = Uuid::v4();
        
        $filename = Urlizer::urlize($uploadedFile->getClientOriginalName()) .'-'. $uuid . '.' . $uploadedFile->guessExtension(); 
        $uploadedFile->move($destination, 
                            $filename);

        return $filename;
    }

    public function getPublicPath(string $path): string
    {
        // needed if we deploy under a subdirectory
        return $this->requestStackContext
            ->getBasePath().'/uploads/'.$path;
       // return 'uploads/'.$path;
    }
}