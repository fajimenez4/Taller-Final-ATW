<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Author;
use PDO;

class AuthorRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM author"); //salida sql
        $list = [];
        while ($row = $stmt->fetch()) {
            $list[] = $this->hydrate($row); //row -> fila sql
        }
        return $list;
    }

    public function findById(int $id): ?object
    {
        $sql = "SELECT * FROM author WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof Author) {
            throw new \InvalidArgumentException('Author expected');
        }

        $sql = "INSERT INTO author
                (first_name,last_name,username,email,password,orcid,afiliation)
                VALUES(:fn,:ln,:usrn,:email,:paswd,:orcid,:aff)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':fn'       => $entity->getFirstName(),
            ':ln'       => $entity->getLastName(),
            ':usrn'     => $entity->getUsername(),
            ':email'    => $entity->getEmail(),
            ':paswd'    => $entity->getPassword(),
            ':orcid'    => $entity->getOrcid(),
            ':aff'      => $entity->getAfiliation()
        ]);
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Author) {
            throw new \InvalidArgumentException('Author expected');
        }

        $sql = "UPDATE author SET 
                    first_name     =:fn,
                    last_name      =:ln,
                    username       =:usrn,
                    email          =:email,
                    password       =:paswd,
                    orcid          =:orcid
                    afiliation     =aff
                WHERE id =:id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':fn'       => $entity->getFirstName(),
            ':ln'       => $entity->getLastName(),
            ':usrn'     => $entity->getUsername(),
            ':email'    => $entity->getEmail(),
            ':paswd'    => $entity->getPassword(),
            ':orcid'    => $entity->getOrcid(),
            ':aff'      => $entity->getAfiliation(),
            ':id'       => $entity->getId()
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM author WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    //convierte filas sql a Author
    private function hydrate(array $row): Author
    {
        $author = new Author(
            (int)$row['id'],
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
        return $author;
    }
}