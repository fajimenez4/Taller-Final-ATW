CREATE TABLE author(
    id int AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    orcid VARCHAR(20) NOT NULL,
    afiliation VARCHAR(50) NOT NULL
);

CREATE TABLE publication (
    id int AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description VARCHAR(100) NOT NULL,
    publication_date DATE NOT NULL,
    author_id int NOT NULL,
    type ENUM('book','article') NOT NULL,
    Foreign Key (author_id) REFERENCES author(id)
        ON DELETE CASCADE
);

CREATE TABLE book(
    publication_id int AUTO_INCREMENT PRIMARY KEY,
    isbn VARCHAR(20) NOT NULL,
    genere VARCHAR(20) NOT NULL,
    edition int NOT NULL,
    Foreign Key (publication_id) REFERENCES publication(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE article (
    publication_id int AUTO_INCREMENT PRIMARY KEY,
    doi VARCHAR(20) NOT NULL,
    abstract VARCHAR(300) NOT NULL,
    keywords VARCHAR(50) NOT NULL,
    indexation VARCHAR(20) NOT NULL,
    magazine VARCHAR(50) NOT NULL,
    area VARCHAR(50) NOT NULL,
    Foreign Key (publication_id) REFERENCES publication(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)

DROP TABLE book;
DROP TABLE article;

CALL sp_book_list();

CALL sp_find_book(1)

CALL sp_create_book( 'ATW con PHP',
                    'TEST',
                    '25/05/1989',
                    'FICCION',
                    '1');
