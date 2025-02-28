<?php
namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
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

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function searchBooks(Request $request, BookRepository $bookRepository): JsonResponse
    {
        $isbn = $request->query->get('isbn');
        $title = $request->query->get('title');
        $author = $request->query->get('author');

        if ($isbn) {
            $books = $bookRepository->findBy(['isbn' => $isbn]);
        } elseif ($title) {
            $books = $bookRepository->createQueryBuilder('b')
                ->where('b.title LIKE :title')
                ->setParameter('title', "%$title%")
                ->getQuery()
                ->getResult();
        } elseif ($author) {
            $books = $bookRepository->createQueryBuilder('b')
                ->where('b.author LIKE :author')
                ->setParameter('author', "%$author%")
                ->getQuery()
                ->getResult();
        } else {
            return $this->json(['error' => 'Veuillez fournir un critère de recherche.'], 400);
        }

        if (!$books) {
            return $this->json(['error' => 'Aucun livre trouvé.'], 404);
        }

        return $this->json($books, 200, [], ['groups' => 'book:read']);
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

    #[Route('/{id}', methods: ['PUT'])]
    public function updateBook(int $id, Request $request): JsonResponse
    {
        $book = $this->entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            return $this->json(['error' => 'Livre non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['isbn']))
            $book->setIsbn($data['isbn']);
        if (isset($data['title']))
            $book->setTitle($data['title']);
        if (isset($data['author']))
            $book->setAuthor($data['author']);
        if (isset($data['publisher']))
            $book->setPublisher($data['publisher']);
        if (isset($data['format']))
            $book->setFormat($data['format']);
        if (isset($data['available']))
            $book->setAvailable($data['available']);

        $this->entityManager->flush();

        return $this->json($book);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteBook(int $id): JsonResponse
    {
        $book = $this->entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            return $this->json(['error' => 'Livre non trouvé'], 404);
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return $this->json(['message' => 'Livre supprimé'], 204);
    }


}
