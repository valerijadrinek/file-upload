<?php
namespace App\Service;

use Symfony\Component\Uid\Uuid;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadHelper 
{
    private $uploadsPath;
    public function __construct(string $uploadsPath) 
    {
        $this->uploadsPath = $uploadsPath;
    }
    public function uploadArticleImage(UploadedFile $uploadedFile) : string
    {
        $destination = $this->uploadsPath. '/article_image';

        $uuid = Uuid::v4();
        
        $filename = Urlizer::urlize($uploadedFile->getClientOriginalName()) .'-'. $uuid . '.' . $uploadedFile->guessExtension(); 
        $uploadedFile->move($destination, 
                            $filename);

        return $filename;
    }
}