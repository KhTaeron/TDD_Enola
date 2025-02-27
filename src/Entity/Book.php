<?php

namespace App\Entity;

use App\Repository\BookRepository;
use App\Validator\ISBNValidator;
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

    public function validateFields(): void
    {
        if (empty($this->isbn) || empty($this->title) || empty($this->author) || empty($this->publisher) || empty($this->format)) {
            throw new \InvalidArgumentException("Tous les champs du livre doivent être remplis.");
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setIsbn(string $isbn): void
    {
        if (empty($isbn)) {
            throw new \InvalidArgumentException("L'ISBN est obligatoire.");
        }
        $normalizedIsbn = str_replace('-', '', $isbn); // Supprime les tirets avant de tester l'isbn

        if (!ISBNValidator::validate($normalizedIsbn)) {
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
