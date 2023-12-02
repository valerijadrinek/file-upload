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
    private $publicAssetBaseUrl;
    
    public function __construct( private Filesystem $filesystem, private RequestStackContext $requestStackContext,
                                private LoggerInterface $logger, string $uploadedAssetsBaseUrl) 
    {
        $this->publicAssetBaseUrl = $uploadedAssetsBaseUrl;
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
        $result = $this->filesystem->writeStream(
            self::ARTICLE_IMAGE . '/' . $filename,
            $stream
        );

        //checking for stream
        if ($result === false) {
            throw new \Exception(sprintf('Could not write uploaded file "%s"', $filename));
        }

        if (is_resource($stream)) { //must be added
            fclose($stream);
        }

        //deleting replaced filename
        if ($existingFilename) {
            try{
                $result = $this->filesystem->delete(self::ARTICLE_IMAGE.'/'.$existingFilename);

                //checking for deleting old files
                if ($result === false) {
                    throw new \Exception(sprintf('Could not write uploaded file "%s"', $filename));
                }

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
            ->getBasePath().$this->publicAssetBaseUrl.'/'.$path;
       // return 'uploads/'.$path;
    }
}