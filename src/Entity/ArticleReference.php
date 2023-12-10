<?php

namespace App\Entity;

use App\Service\UploadHelper;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ArticleReferenceRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleReferenceRepository::class)]
class ArticleReference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['main'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reference')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    #[ORM\Column(length: 255)]
    #[Groups(['main'])]
    private ?string $filename = null;

    #[ORM\Column(length: 255)]
    #[Groups(['main', 'input'])]
    #[Assert\NotBlank()]
    #[Assert\Length(min:6, max:100)]
    private ?string $originalFilename = null;

    #[ORM\Column(length: 255)]
    #[Groups(['main'])]
    private ?string $mimeType = null;

    #[ORM\Column]
    private ?int $position = null;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): static
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getFilePath(): string
    {
        return UploadHelper::ARTICLE_REFERENCE.'/'.$this->getFilename();
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }
}
