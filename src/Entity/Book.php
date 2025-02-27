<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    private string $isbn;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(length: 255)]
    private string $author;

    #[ORM\Column(length: 255)]
    private string $publisher;

    #[ORM\Column(length: 50)]
    private string $format;

    #[ORM\Column(type: 'boolean')]
    private bool $available = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setIsbn(string $isbn): void
    {
        if (!preg_match('/^\d{3}-\d{1,5}-\d{1,7}-\d{1,7}-\d{1}$/', $isbn)) {
            throw new \InvalidArgumentException("ISBN invalide.");
        }
        $this->isbn = $isbn;
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    public function setTitle(string $title): void
    {
        if (empty($title)) {
            throw new \InvalidArgumentException("Le titre est obligatoire.");
        }
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setAuthor(string $author): void
    {
        if (empty($author)) {
            throw new \InvalidArgumentException("L'auteur est obligatoire.");
        }
        $this->author = $author;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setPublisher(string $publisher): void
    {
        if (empty($publisher)) {
            throw new \InvalidArgumentException("L'éditeur est obligatoire.");
        }
        $this->publisher = $publisher;
    }

    public function getPublisher(): string
    {
        return $this->publisher;
    }

    public function setFormat(string $format): void
    {
        $validFormats = ['Poche', 'Broché', 'Grand format'];
        if (!in_array($format, $validFormats)) {
            throw new \InvalidArgumentException("Format invalide.");
        }
        $this->format = $format;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }
}
