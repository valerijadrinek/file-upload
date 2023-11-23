<?php
namespace App\Service;

use Symfony\Component\Uid\Uuid;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadHelper 
{
    const ARTICLE_IMAGE = 'article_image';
    private $uploadsPath;
    public function __construct(string $uploadsPath) 
    {
        $this->uploadsPath = $uploadsPath;
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
        return 'uploads/'.$path;
    }
}