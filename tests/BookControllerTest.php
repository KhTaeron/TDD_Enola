<?php
namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testBookController_CreateBook(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/books', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'isbn' => '978-2755673135',
            'title' => 'Troublemaker',
            'author' => 'Laura Swan',
            'publisher' => 'Hachette',
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
}
