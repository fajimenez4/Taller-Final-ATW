<?php
declare (strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';

use App\Repositories\BookRepository; 
use App\Repositories\AuthorRepository;

$bookRepository = new BookRepository();
$books = $bookRepository->findAll();
echo "Libros encontrados:\n";
        foreach ($books as $book) {
            echo "  - Título: " . $book->getTitle() . ", Autor: " . $book->getAuthor()->getFirstName() . " " . $book->getAuthor()->getLastName() . "\n";
        }

$authorRepository = new AuthorRepository();
$authors = $authorRepository->findAll();
echo "Autores encontrados:\n";
foreach ($authors as $author) {
    echo "  - Autor: " . $author->getFirstName() . " " . $author->getLastName() . "\n";
}



