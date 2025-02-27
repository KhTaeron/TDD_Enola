<?php

namespace App\Tests;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BookDatabaseTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        // Récupérer la connexion à la base de données
        $conn = static::getContainer()->get('doctrine')->getConnection();
        $conn->beginTransaction();

        // Créer ou nettoyer la base de données de test avant chaque test
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->entityManager);
        $classes = [$this->entityManager->getClassMetadata(Book::class)];
        $schemaTool->createSchema($classes);
    }

    public function testBookCreationInDBWithMissingIsbn_ShouldThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("L'ISBN est obligatoire.");

        $book = new Book();
        $book->setTitle('Test Book');
        $book->setAuthor('John Doe');
        $book->setPublisher('Test Publisher');
        $book->setFormat('Poche');
        $book->setAvailable(true);

        // Tenter de persister le livre sans ISBN
        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }

    public function testBookCreationInDB_AllFields(): void
    {
        $book = new Book();
        $book->setIsbn('978-2755673135');
        $book->setTitle('Fourth Wing');
        $book->setAuthor('Rebecca Yarros');
        $book->setPublisher('Hugo Roman');
        $book->setFormat('Broché');
        $book->setAvailable(true);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        // Vérifier que le livre est bien enregistré dans la base de données
        $savedBook = $this->entityManager->getRepository(Book::class)->findOneBy(['isbn' => '978-2755673135']);

        // Assertions pour vérifier les données enregistrées
        $this->assertNotNull($savedBook);
        $this->assertEquals('978-2755673135', $savedBook->getIsbn());
        $this->assertEquals('Fourth Wing', $savedBook->getTitle());
        $this->assertEquals('Rebecca Yarros', $savedBook->getAuthor());
        $this->assertEquals('Hugo Roman', $savedBook->getPublisher());
        $this->assertEquals('Broché', $savedBook->getFormat());
        $this->assertTrue($savedBook->isAvailable());
    }

    // Complétion des champs manquants si ISBN remplis mais pas autres champs
    public function testBookCreationInDBWithMissingFields_ShouldFetchDataFromWebService(): void
    {
        $book = new Book();
        $book->setIsbn('978-2755673135');
        $book->setTitle('');
        $book->setAuthor('');
        $book->setPublisher('');
        $book->setFormat('');
        $book->setAvailable(true);

        // Simuler l'appel au web service
        $this->mockWebServiceToCompleteBook($book);

        // Vérification que les champs ont été complétés par le web service
        $this->assertNotEmpty($book->getTitle());
        $this->assertNotEmpty($book->getAuthor());
        $this->assertNotEmpty($book->getPublisher());
        $this->assertNotEmpty($book->getFormat());
    }

    protected function tearDown(): void
    {
        $conn = static::getContainer()->get('doctrine')->getConnection();
        $conn->rollBack(); // Restaure l'état de la base de données après chaque test

        parent::tearDown();
    }
}
