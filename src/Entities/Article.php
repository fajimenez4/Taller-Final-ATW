<?php
declare(strict_types=1);

namespace App\Entities;

class Article extends Publication
{
    private string $doi;
    private string $journal;

    public function __construct(
        ?int $id,
        string $title,
        string $description,
        \DateTime $publicationDate,
        Author $author,
        string $doi,
        string $journal
    ) {
        parent::__construct($id, $title, $description, $publicationDate, $author);
        $this->doi = $doi;
        $this->journal = $journal;
    }

    public function getDoi(): string
    {
        return $this->doi;
    }

    public function getJournal(): string
    {
        return $this->journal;
    }

    public function setDoi(string $doi): void
    {
        $this->doi = $doi;
    }

    public function setJournal(string $journal): void
    {
        $this->journal = $journal;
    }
}
