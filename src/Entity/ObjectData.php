<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ObjectData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $title = null;

    #[ORM\ManyToOne]
    private ?Category $category = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_reference = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $details = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $info1 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $info2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(options: ["default" => false])]
    private bool $removed = false;

    public function __construct()
    {
        $this->createdDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    public function getIdReference(): ?int
    {
        return $this->id_reference;
    }

    public function setIdReference(?int $id_reference): void
    {
        $this->id_reference = $id_reference;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): void
    {
        $this->details = $details;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function getInfo1(): ?string
    {
        return $this->info1;
    }

    public function setInfo1(?string $info1): void
    {
        $this->info1 = $info1;
    }
}
