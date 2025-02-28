<?php
namespace App\Controller;

use App\Entity\Book;
use App\Service\BookService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/books')]
class BookController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private BookService $bookService;

    public function __construct(EntityManagerInterface $entityManager, BookService $bookService)
    {
        $this->entityManager = $entityManager;
        $this->bookService = $bookService;
    }

    #[Route('', methods: ['POST'])]
    public function createBook(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $book = new Book($data['isbn']);
            $book->setTitle($data['title'] ?? '');
            $book->setAuthor($data['author'] ?? '');
            $book->setPublisher($data['publisher'] ?? '');
            $book->setFormat($data['format'] ?? '');
            $book->setAvailable(true);

            // Validation et complétion avec le service
            $this->bookService->validateAndCompleteBook($book);

            $this->entityManager->persist($book);
            $this->entityManager->flush();

            return $this->json($book, 201);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getBook(int $id): JsonResponse
    {
        $book = $this->entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            return $this->json(['error' => 'Livre non trouvé'], 404);
        }

        return $this->json($book);
    }

    
}
