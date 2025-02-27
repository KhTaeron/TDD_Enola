<?php

namespace App\Tests;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    public function testBookCreation()
    {
        $book = new Book();
        $book->setIsbn('978-2755673135');
        $book->setTitle('Fourth Wing');
        $book->setAuthor('Rebecca Yarros');
        $book->setPublisher('Hugo Roman');
        $book->setFormat('Broché');
        $book->setAvailable(true);

        $this->assertEquals('978-2755673135', $book->getIsbn());
        $this->assertEquals('Fourth Wing', $book->getTitle());
        $this->assertEquals('Rebecca Yarros', $book->getAuthor());
        $this->assertEquals('Hugo Roman', $book->getPublisher());
        $this->assertEquals('Broché', $book->getFormat());
        $this->assertTrue($book->isAvailable());
    }

    public function testBookCreation_InvalidIsbn()
    {
        $this->expectException(\InvalidArgumentException::class);

        $book = new Book();
        $book->setIsbn('1234'); // ISBN invalide
    }

    public function testBookCreation_MissingFields()
    {
        $this->expectException(\InvalidArgumentException::class);

        $book = new Book();
        $book->setIsbn('978-2755673135'); 
    }

    public function testBookCreation_InvalidFormat()
    {
        $this->expectException(\InvalidArgumentException::class);

        $book = new Book();
        $book->setIsbn('978-2755673135');
        $book->setTitle('Fourth Wing');
        $book->setAuthor('Rebecca Yarros');
        $book->setPublisher('Hugo Roman');
        $book->setFormat('Inconnu'); // Format invalide
        $book->setAvailable(true);
    }
}