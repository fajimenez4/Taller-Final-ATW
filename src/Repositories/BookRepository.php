<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Config\Database;
use App\Entities\Author;
use App\Entities\Book;
use App\Interfaces\RepositoryInterface;
use PDO;

class BookRepository implements RepositoryInterface
{
    private PDO $db;
    private AuthorRepository $authorRepo;
    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->authorRepo = new AuthorRepository();
    }

    public function create(object $entity): bool
    {
        if(!$entity instanceof Book)
        {
            throw new \InvalidArgumentException('Book  expected');
        }

        $stmt = $this->db->prepare("CALL sp_create_book(:t,:d,:dt,:aid,:i,:g:,:ed)");
        $ok = $stmt->execute([
            ':t' => $entity->getTitle(),
            ':d' => $entity->getDescription(),
            ':dt' => $entity->getPublicationDate()->format('Y-m-d'),
            ':aid' => $entity->getAuthor()->getId(),
            ':i' => $entity->getIsbn(),
            ':g' => $entity->getGenere(),
            ':ed' => $entity->getEdition()
        ]);

        $stmt->closeCursor();
        return $ok;
    }
    public function findById(int $id): ?object
    {
        $stmt = $this->db->prepare("CALL sp_find_book(:id)");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();

        return $row ? $this->hydrate($row) : null;
    }

    public function update(object $entity): bool
    {
        if(!$entity instanceof Book)
        {
            throw new \InvalidArgumentException('Book expected');
        }

        $stmt = $this->db->prepare("CALL sp_update_book(:id,:t,:d,:dt,:aid,:i,:g,:ed)");
        $ok = $stmt->execute([
            ':id' => $entity->getId(),
            ':t' => $entity->getTitle(),
            ':d' => $entity->getDescription(),
            ':dt' => $entity->getPublicationDate()->format('Y-m-d'),
            ':aid' => $entity->getAuthor()->getId(),
            ':i' => $entity->getIsbn(),
            ':g' => $entity->getGenere(),
            ':ed' => $entity->getEdition()
        ]);

        if ($ok) {
            $entity->setId((int) $this->db->lastInsertId());
        }
        $stmt->closeCursor();
        return $ok;
    }
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("CALL sp_delete_book(:id)");
        $ok =$stmt->execute([':id' => $id]);
        if (!$ok) {
            $stmt->fetch();
        }
        $stmt->closeCursor();
        return $ok;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("Call sp_book_list();");
        $rows = [];
        $stmt->closeCursor();
        $out = [];
        foreach ($rows as $r) {
            $out[] = $this->hydrate($r);
        }
        return $out;
    }

    private function hydrate(array $row): Book
    {
        $author = new Author(
            (int) $row['id'],
            $row['first_name'],
            $row['last_name'],
            $row['username'],
            $row['email'],
            'temporal',
            $row['orcid'],
            $row['afiliation']
        );
        //REEMPLAZAR HASH SIN REGENERAR
        $ref = new \ReflectionClass($author);
        $prop = $ref->getProperty('password');
        $prop->setAccessible(true);
        $prop->setValue($author, $row['password']);

        return new Book(
            (int) $row['publication_id'],
            $row['title'],
            $row['description'],
            new \DateTime($row['publication_date']),
            $author,
            $row['isbn'],
            $row['genere'],
            (int) $row['edition'],
        );
    }
}
