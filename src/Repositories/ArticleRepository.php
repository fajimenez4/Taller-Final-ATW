<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Config\Database;
use App\Entities\Author;
use App\Entities\Article;
use App\Interfaces\RepositoryInterface;
use PDO;

class ArticleRepository implements RepositoryInterface
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
        if (!$entity instanceof Article) {
            throw new \InvalidArgumentException('Article expected');
        }

        $stmt = $this->db->prepare("CALL sp_create_article(:t,:d,:dt,:aid,:doi,:j)");
        $ok = $stmt->execute([
            ':t' => $entity->getTitle(),
            ':d' => $entity->getDescription(),
            ':dt' => $entity->getPublicationDate()->format('Y-m-d'),
            ':aid' => $entity->getAuthor()->getId(),
            ':doi' => $entity->getDoi(),
            ':j' => $entity->getJournal(),
        ]);

        $stmt->closeCursor();
        return $ok;
    }

    public function findById(int $id): ?object
    {
        $stmt = $this->db->prepare("CALL sp_article_by_id(:id)");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Article) {
            throw new \InvalidArgumentException('Article expected');
        }

        $stmt = $this->db->prepare("CALL sp_update_article(:id,:t,:d,:dt,:aid,:doi,:j)");
        $ok = $stmt->execute([
            ':id' => $entity->getId(),
            ':t' => $entity->getTitle(),
            ':d' => $entity->getDescription(),
            ':dt' => $entity->getPublicationDate()->format('Y-m-d'),
            ':aid' => $entity->getAuthor()->getId(),
            ':doi' => $entity->getDoi(),
            ':j' => $entity->getJournal()
        ]);

        $stmt->closeCursor();
        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("CALL sp_delete_article(:id)");
        $ok = $stmt->execute([':id' => $id]);
        $stmt->closeCursor();
        return $ok;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("CALL sp_article_list();");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        $out = [];
        foreach ($rows as $r) {
            $out[] = $this->hydrate($r);
        }
        return $out;
    }

    private function hydrate(array $row): Article
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
        $ref = new \ReflectionClass($author);
        $prop = $ref->getProperty('password');
        $prop->setAccessible(true);
        $prop->setValue($author, $row['password']);

        return new Article(
            (int) $row['publication_id'],
            $row['title'],
            $row['description'],
            new \DateTime($row['publication_date']),
            $author,
            $row['doi'],
            $row['journal']
        );
    }
}
