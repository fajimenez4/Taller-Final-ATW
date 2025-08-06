<?php

declare(strict_types=1);

namespace App\Entities;

class Book extends Publication
{

    private string $isbn;
    private string $genere;
    private int $edition;

    public function __construct(
        ?int      $id,
        string    $title,
        string    $description,
        \DateTime $publicationDate,
        Author    $author,
        string    $isbn,
        string    $genere,
        int       $edition
    ) {
        parent::__construct($id, $title, $description, $publicationDate, $author);
        $this->setIsbn($isbn);
        $this->setGenere($genere);
        $this->setEdition($edition);
    }

    /* getters */
    public function getIsbn(): string
    {
        return $this->isbn;
    }
    public function getGenere(): string
    {
        return $this->genere;
    }
    public function getEdition(): int
    {
        return $this->edition;
    }

    /* setters */
    public function setIsbn(string $i): void
    {
        $this->isbn = trim($i);
    }
    public function setGenere(string $g): void
    {
        $this->genere = trim($g);
    }
    public function setEdition(int $e): void
    {
        if ($e < 1) {
            throw new \InvalidArgumentException('Edition >= 1');
        }
        $this->edition = $e;
    }
}