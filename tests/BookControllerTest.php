<?php
namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testBookController_CreateBook(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/books', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'isbn' => '978-2755673159',
            'title' => 'Onyx Storm',
            'author' => 'Rebecca Yarros',
            'publisher' => 'Hugo Roman',
            'format' => 'Broché'
        ]));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testBookController_FindABook(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/books/1');

        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testBookController_SearchByIsbn(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/books/search?isbn=978-2755673159');

        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($data);
        $this->assertSame("978-2755673159", $data[0]['isbn']);
    }

    public function testBookController_SearchByTitle(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/books/search?title=Onyx+Storm');

        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($data);
        $this->assertStringContainsString("Onyx Storm", $data[0]['title']);
    }

    public function testBookController_SearchByAuthor(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/books/search?author=Rebecca+Yarros');

        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($data);
        $this->assertStringContainsString("Rebecca Yarros", $data[0]['author']);
    }

    public function testBookController_UpdateABook(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/books/1');
        $this->assertResponseStatusCodeSame(200);

        $originalData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('title', $originalData);

        $updatedData = [
            'title' => 'Nouveau Titre',
            'author' => $originalData['author'],
            'publisher' => $originalData['publisher'],
            'format' => $originalData['format'],
        ];

        $client->request('PUT', '/api/books/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($updatedData));

        $this->assertResponseStatusCodeSame(200);

        $client->request('GET', '/api/books/1');
        $updatedBook = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame('Nouveau Titre', $updatedBook['title']);
    }
    public function testBookController_DeleteABook(): void
    {
        $client = static::createClient();

        // Vérifier que le livre existe avant suppression
        $client->request('GET', '/api/books/1');
        $this->assertResponseStatusCodeSame(200);

        // Supprimer le livre
        $client->request('DELETE', '/api/books/1');
        $this->assertResponseStatusCodeSame(204); // 204 No Content

        $client->request('GET', '/api/books/1');
        $this->assertResponseStatusCodeSame(404); // 404 : non trouvé
    }
}
