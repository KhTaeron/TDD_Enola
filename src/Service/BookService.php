<?php

namespace App\Service;

use App\Entity\Book;
use InvalidArgumentException;

class BookService
{
    private $webService;

    public function __construct($webService)
    {
        $this->webService = $webService;
    }

    public function validateAndCompleteBook(Book $book): void
    {
        if (empty($book->getIsbn())) {
            throw new InvalidArgumentException("L'ISBN est obligatoire.");
        }

        if (empty($book->getTitle()) || empty($book->getAuthor()) || empty($book->getPublisher()) || empty($book->getFormat())) {
            $this->completeBookFromWebService($book);
        }
    }

    private function completeBookFromWebService(Book $book): void
    {
        // Appel au web service pour récupérer les informations manquantes
        $data = $this->webService->fetchBookDetailsByIsbn($book->getIsbn());

        // Compléter le livre avec les informations récupérées
        if ($data) {
            $book->setTitle($data['title'] ?? 'Unknown Title');
            $book->setAuthor($data['author'] ?? 'Unknown Author');
            $book->setPublisher($data['publisher'] ?? 'Unknown Publisher');
            $book->setFormat($data['format'] ?? 'Unknown Format');
        }
    }
}
