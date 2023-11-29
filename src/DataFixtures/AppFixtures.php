<?php

namespace App\DataFixtures;

use App\Factory\ArticleFactory;
use App\Entity\Article;
use App\Service\UploadHelper;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Zenstruck\Foundry\Factory;
use function Zenstruck\Foundry\faker;

class AppFixtures extends Fixture
{
   

    public function __construct(private UploadHelper $uploadHelper) 
                               
    {

    }
    public function load(ObjectManager $manager): void
    {

        
        ArticleFactory::createMany(6, function($count) use ($manager) {
            $article = new Article();
            $imageFilename = $this->fakeUploadImage();
            $article->setFileName($imageFilename);

            return $article;

    });

        $manager->flush();
    }

    private function fakeUploadImage()
    {
       $articleImages = [
            'priroda1.jpeg',
            'priroda2.jpeg',
            'priroda3.png',
            'priroda4.png',
            'priroda5.png',
            'priroda6.png',
        ];
            $randomImage = array_rand($articleImages);
            $fs = new Filesystem();
            $targetPath = sys_get_temp_dir().'/'.$randomImage;
            $fs->copy(__DIR__.'/slike/'.$randomImage, $targetPath, true);
            return $this->uploadHelper
                ->uploadArticleImage(new File($targetPath), null);
    }
}
