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

    // creditLine
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $info1 = null;

    // culture
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $culture = null;

    // objectBeginDate
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?string $objectBeginDate = null;

    // objectEndDate
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?string $objectEndDate = null;

    // dimensions
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dimensions = null;

    // country
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $country = null;

    // objectURL
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $objectURL = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(options: ["default" => false])]
    private bool $removed = false;

    #[ORM\Column(options: ["default" => false])]
    private bool $processed = false;

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

    public function getObjectURL(): ?string
    {
        return $this->objectURL;
    }

    public function setObjectURL(?string $objectURL): void
    {
        $this->objectURL = $objectURL;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getDimensions(): ?string
    {
        return $this->dimensions;
    }

    public function setDimensions(?string $dimensions): void
    {
        $this->dimensions = $dimensions;
    }

    public function getObjectEndDate(): ?string
    {
        return $this->objectEndDate;
    }

    public function setObjectEndDate(?string $objectEndDate): void
    {
        $this->objectEndDate = $objectEndDate;
    }

    public function getObjectBeginDate(): ?string
    {
        return $this->objectBeginDate;
    }

    public function setObjectBeginDate(?string $objectBeginDate): void
    {
        $this->objectBeginDate = $objectBeginDate;
    }

    public function getCulture(): ?string
    {
        return $this->culture;
    }

    public function setCulture(?string $culture): void
    {
        $this->culture = $culture;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(?\DateTimeInterface $createdDate): void
    {
        $this->createdDate = $createdDate;
    }

    public function isProcessed(): bool
    {
        return $this->processed;
    }

    public function setProcessed(bool $processed): void
    {
        $this->processed = $processed;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    public function setRemoved(bool $removed): void
    {
        $this->removed = $removed;
    }

    public function getCategoryId(): ?String
    {
        try {
            return $this->category?->getId();
        } catch (\Exception $err) {
            return null;
        }
    }

    public function getCategoryTitle(): String
    {
        try {
            return $this->category?->getTitle() ?? "";
        } catch (\Exception $err) {
            return "";
        }
    }
}
