<?php
namespace App\Twig;

use Twig\TwigFunction;
use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use App\Service\UploadHelper;
class AppExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    private $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getUploadedAssetPath(string $path): string
    {
        return $this->container
            ->get(UploadHelper::class)
            ->getPublicPath($path);
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploaded_asset', [$this, 'getUploadedAssetPath'])
        ];
    }

    public static function getSubscribedServices() : array
    {
        return [
            UploadHelper::class,
        ];
    }
}