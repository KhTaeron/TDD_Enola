<?php
namespace App\Service;

class WebService
{
    public function fetchBookDetailsByIsbn(string $isbn): array
    {
        return [
            'title' => 'Troublemaker',
            'author' => 'Laura Swan',
            'publisher' => 'Hachette',
            'format' => 'Broché',
        ];
    }
}
