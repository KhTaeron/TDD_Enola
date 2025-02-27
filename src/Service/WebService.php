<?php
namespace App\Service;

class WebService
{
    public function fetchBookDetailsByIsbn(string $isbn): array
    {
        // Ici, tu pourrais utiliser une bibliothèque HTTP pour faire la requête API (comme Guzzle, Symfony HttpClient)
        // Cette fonction doit renvoyer un tableau associatif avec les informations du livre

        // Exemple de retour simulé (en vrai, cela viendrait du web service)
        return [
            'title' => 'Troublemaker',
            'author' => 'Laura Swan',
            'publisher' => 'Hachette',
            'format' => 'Broché',
        ];
    }
}
