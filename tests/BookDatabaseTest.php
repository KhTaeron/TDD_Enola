<?php

namespace App\Tests;

use App\Entity\Book;
use App\Service\BookService;
use App\Service\WebService;
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

        $book = new Book();  // Pas d'ISBN
        $book->setTitle('Some title');
        $book->setAuthor('Some author');
        $book->setPublisher('Some publisher');
        $book->setFormat('Poche');
        $book->setAvailable(true);

        // Maintenant qu'on a validé, si on insère, on est sûr qu'on n'a pas d'erreur SQL
        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }


    public function testBookCreationInDB_AllFields(): void
    {
        $book = new Book('978-2755673135');
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
        $book = new Book('978-2755673135');
        $book->setTitle('');
        $book->setAuthor('');
        $book->setPublisher('');
        $book->setFormat('Inconnu');
        $book->setAvailable(true);

        // Mock du Web Service
        $webServiceMock = $this->createMock(WebService::class);
        $webServiceMock->method('fetchBookDetailsByIsbn')
            ->willReturn([
                'title' => 'Troublemaker',
                'author' => 'Laura Swan',
                'publisher' => 'Hachette',
                'format' => 'Broché'
            ]);

        // Créer une instance du service avec le mock du web service
        $bookService = new BookService($webServiceMock);

        $bookService->validateAndCompleteBook($book);

        // Vérification que les champs ont bien été complétés par le web service
        $this->assertEquals('Troublemaker', $book->getTitle());
        $this->assertEquals('Laura Swan', $book->getAuthor());
        $this->assertEquals('Hachette', $book->getPublisher());
        $this->assertEquals('Broché', $book->getFormat());
    }

    public function testBookCreationInDB_WithDuplicateIsbn_ShouldThrowException(): void
    {
        $book1 = new Book('978-2755673135');
        $book1->setTitle('Fourth Wing');
        $book1->setAuthor('Rebecca Yarros');
        $book1->setPublisher('Hugo Roman');
        $book1->setFormat('Broché');
        $book1->setAvailable(true);
    
        // Persister le premier livre
        $this->entityManager->persist($book1);
        $this->entityManager->flush();
    
        // Créer un deuxième livre avec le même ISBN
        $book2 = new Book('978-2755673135');
        $book2->setTitle('Livre dupliqué');
        $book2->setAuthor('Rebecca Yarros');
        $book2->setPublisher('Hugo Roman');
        $book2->setFormat('Broché');
        $book2->setAvailable(true);
    
        // Exception pour le duplicata
        $this->expectException(\Doctrine\DBAL\Exception\UniqueConstraintViolationException::class);
        $this->expectExceptionMessage('Duplicate entry');
    
        // Essayer d'ajouter le deuxième livre avec le même ISBN
        $this->entityManager->persist($book2);
        $this->entityManager->flush();
    }
    
    protected function tearDown(): void
    {
        $conn = static::getContainer()->get('doctrine')->getConnection();
        $conn->rollBack(); // Restaure l'état de la base de données après chaque test

        parent::tearDown();
    }
}
