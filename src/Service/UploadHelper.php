<?php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use League\Flysystem\Filesystem;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class UploadHelper 
{
    const ARTICLE_IMAGE = 'article_image';
    
    public function __construct( private Filesystem $filesystem, private RequestStackContext $requestStackContext,
                                private LoggerInterface $logger) 
    {
        
    }
    public function uploadArticleImage(File $file, ?string $existingFilename) : string
    {

        $uuid = Uuid::v4();

        if($file instanceof UploadedFile) {
            $originalFileName = $file->getClientOriginalName();
        } else {
            $originalFileName = $file->getFilename();
        }
        
        $filename = Urlizer::urlize(pathinfo($originalFileName, PATHINFO_FILENAME)) .'-'. $uuid . '.' . $file->guessExtension(); 
        $stream = fopen($file->getPathname(), 'r');
        $this->filesystem->writeStream(
            self::ARTICLE_IMAGE . '/' . $filename,
            $stream
        );

        if (is_resource($stream)) { //must be added
            fclose($stream);
        }

        //deleting replaced filename
        if ($existingFilename) {
            try{
                $this->filesystem->delete(self::ARTICLE_IMAGE.'/'.$existingFilename);
            } catch ( FileNotFoundException $e) {

                $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFilename));
            }
            
        }
       

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