<?php

namespace App\Factory;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;
use App\Service\UploadHelper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Zenstruck\Foundry\Factory;
use function Zenstruck\Foundry\faker;

/**
 * @extends ModelFactory<Article>
 *
 * @method        Article|Proxy create(array|callable $attributes = [])
 * @method static Article|Proxy createOne(array $attributes = [])
 * @method static Article|Proxy find(object|array|mixed $criteria)
 * @method static Article|Proxy findOrCreate(array $attributes)
 * @method static Article|Proxy first(string $sortedField = 'id')
 * @method static Article|Proxy last(string $sortedField = 'id')
 * @method static Article|Proxy random(array $attributes = [])
 * @method static Article|Proxy randomOrCreate(array $attributes = [])
 * @method static ArticleRepository|RepositoryProxy repository()
 * @method static Article[]|Proxy[] all()
 * @method static Article[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Article[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Article[]|Proxy[] findBy(array $attributes)
 * @method static Article[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Article[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class ArticleFactory extends ModelFactory
{
    private static $articleImages = [
        'asteroid.jpeg',
        'mercury.jpeg',
        'lightspeed.png',
    ];
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(private Filesystem $filesystem,
                                private UploadHelper $uploadHelper,
                                )
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
       

        
        return [
            'body' => self::faker()->paragraph(),
            'title' => self::faker()->sentence(),
            'filename' => self::faker()->file('F:\zadaci\fileUploading/src/Factory/slike', 'F:\zadaci\fileUploading/public/uploads', true),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
        // ->instantiateWith(function( Article $article): object {
           
        //         return new Article(); // ... your own logic
          //  })
            
        ;
    }

    protected static function getClass(): string
    {
        return Article::class;
    }
}
