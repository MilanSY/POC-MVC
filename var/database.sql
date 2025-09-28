-- =============================================
-- Script SQL pour la médiathèque POC-MVC
-- =============================================

-- Supprimer les tables existantes si elles existent
DROP TABLE IF EXISTS songs;

DROP TABLE IF EXISTS albums;

DROP TABLE IF EXISTS movies;

DROP TABLE IF EXISTS books;

DROP TABLE IF EXISTS medias;

-- =============================================
-- Table pour l'authentification des utilisateurs
-- =============================================

-- Table des utilisateurs
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- =============================================
-- Création des tables
-- =============================================

-- Table principale Media
CREATE TABLE medias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(255) NOT NULL,
    auteur VARCHAR(255) NOT NULL,
    disponible BOOLEAN DEFAULT TRUE,
    type_media ENUM('book', 'movie', 'album') NOT NULL,
    borrowed_by INT DEFAULT NULL,
    FOREIGN KEY (borrowed_by) REFERENCES users (id) ON DELETE SET NULL,
    INDEX idx_borrowed_by (borrowed_by),
    INDEX idx_type_media (type_media)
);

-- Table Books
CREATE TABLE books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    media_id INT NOT NULL,
    page_number INT NOT NULL,
    FOREIGN KEY (media_id) REFERENCES medias (id) ON DELETE CASCADE
);

-- Table Movies avec enum pour les genres
CREATE TABLE movies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    media_id INT NOT NULL,
    duration VARCHAR(20) NOT NULL, -- Durée au format string (ex: "2h 30min")
    genre ENUM(
        'Action',
        'Comédie',
        'Drame',
        'Science-Fiction',
        'Horreur',
        'Documentaire'
    ) NOT NULL,
    FOREIGN KEY (media_id) REFERENCES medias (id) ON DELETE CASCADE
);

-- Table Albums
CREATE TABLE albums (
    id INT PRIMARY KEY AUTO_INCREMENT,
    media_id INT NOT NULL,
    track_number INT NOT NULL,
    editor VARCHAR(255) NOT NULL,
    FOREIGN KEY (media_id) REFERENCES medias (id) ON DELETE CASCADE
);

-- Table Songs (relation directe avec albums)
CREATE TABLE songs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    duration VARCHAR(20) NOT NULL, -- Durée au format string (ex: "3min 45s")
    note INT DEFAULT 0 CHECK (
        note >= 0
        AND note <= 5
    ),
    album_id INT NOT NULL,
    FOREIGN KEY (album_id) REFERENCES albums (id) ON DELETE CASCADE,
    INDEX idx_album_id (album_id)
);

-- =============================================
-- Insertion des données de test
-- =============================================

-- === LIVRES ===
INSERT INTO
    medias (
        titre,
        auteur,
        disponible,
        type_media
    )
VALUES (
        'Le Petit Prince',
        'Antoine de Saint-Exupéry',
        TRUE,
        'book'
    ),
    (
        '1984',
        'George Orwell',
        TRUE,
        'book'
    ),
    (
        'L\'Étranger',
        'Albert Camus',
        TRUE,
        'book'
    ),
    (
        'Harry Potter à l\'école des sorciers',
        'J.K. Rowling',
        TRUE,
        'book'
    ),
    (
        'Le Seigneur des Anneaux',
        'J.R.R. Tolkien',
        TRUE,
        'book'
    ),
    (
        'Dune',
        'Frank Herbert',
        TRUE,
        'book'
    );

INSERT INTO
    books (media_id, page_number)
VALUES (1, 96), -- Le Petit Prince
    (2, 328), -- 1984
    (3, 186), -- L'Étranger
    (4, 320), -- Harry Potter
    (5, 1216), -- Le Seigneur des Anneaux
    (6, 896);
-- Dune

-- === FILMS ===
INSERT INTO
    medias (
        titre,
        auteur,
        disponible,
        type_media
    )
VALUES (
        'Inception',
        'Christopher Nolan',
        TRUE,
        'movie'
    ),
    (
        'Le Parrain',
        'Francis Ford Coppola',
        TRUE,
        'movie'
    ),
    (
        'Pulp Fiction',
        'Quentin Tarantino',
        TRUE,
        'movie'
    ),
    (
        'Interstellar',
        'Christopher Nolan',
        TRUE,
        'movie'
    ),
    (
        'La Liste de Schindler',
        'Steven Spielberg',
        TRUE,
        'movie'
    ),
    (
        'Blade Runner 2049',
        'Denis Villeneuve',
        TRUE,
        'movie'
    ),
    (
        'Amélie Poulain',
        'Jean-Pierre Jeunet',
        TRUE,
        'movie'
    ),
    (
        'Shining',
        'Stanley Kubrick',
        TRUE,
        'movie'
    );

INSERT INTO
    movies (media_id, duration, genre)
VALUES (7, '2h 28min', 'Science-Fiction'), -- Inception
    (8, '2h 55min', 'Drame'), -- Le Parrain
    (9, '2h 34min', 'Action'), -- Pulp Fiction
    (10, '2h 49min', 'Science-Fiction'), -- Interstellar
    (11, '3h 15min', 'Drame'), -- La Liste de Schindler
    (12, '2h 44min', 'Science-Fiction'), -- Blade Runner 2049
    (13, '2h 2min', 'Comédie'), -- Amélie Poulain
    (14, '2h 26min', 'Horreur');
-- Shining

-- === ALBUMS ===
INSERT INTO
    medias (
        titre,
        auteur,
        disponible,
        type_media
    )
VALUES (
        'Abbey Road',
        'The Beatles',
        TRUE,
        'album'
    ),
    (
        'Thriller',
        'Michael Jackson',
        TRUE,
        'album'
    ),
    (
        'OK Computer',
        'Radiohead',
        TRUE,
        'album'
    ),
    (
        'Dark Side of the Moon',
        'Pink Floyd',
        TRUE,
        'album'
    ),
    (
        'Nevermind',
        'Nirvana',
        TRUE,
        'album'
    );

INSERT INTO
    albums (
        media_id,
        track_number,
        editor
    )
VALUES (15, 17, 'Apple Records'), -- Abbey Road
    (16, 9, 'Epic Records'), -- Thriller
    (17, 12, 'Parlophone'), -- OK Computer
    (18, 10, 'Harvest Records'), -- Dark Side of the Moon
    (19, 12, 'DGC Records');
-- Nevermind

-- === CHANSONS ===
INSERT INTO
    songs (title, duration, note, album_id)
VALUES
    -- Songs pour Abbey Road (album_id = 1)
    ('Come Together', '4min 20s', 5, 1),
    ('Something', '3min 3s', 4, 1),
    ('Maxwell\'s Silver Hammer', '3min 17s', 3, 1),
    ('Oh! Darling', '3min 16s', 4, 1),
    ('Octopus\'s Garden', '2min 51s', 3, 1),
    ('I Want You (She\'s So Heavy)', '7min 47s', 5, 1),
    ('Here Comes the Sun', '3min 5s', 5, 1),
    ('Because', '2min 45s', 4, 1),
    ('You Never Give Me Your Money', '4min 2s', 4, 1),
    ('Sun King', '2min 26s', 3, 1),
    ('Mean Mr. Mustard', '1min 6s', 2, 1),
    ('Polythene Pam', '1min 12s', 3, 1),
    ('She Came in Through the Bathroom Window', '1min 57s', 3, 1),
    ('Golden Slumbers', '1min 31s', 4, 1),
    ('Carry That Weight', '1min 36s', 4, 1),
    ('The End', '2min 20s', 5, 1),
    ('Her Majesty', '23s', 2, 1),

    -- Songs pour Thriller (album_id = 2)
    ('Wanna Be Startin\' Somethin\'', '6min 3s', 4, 2),
    ('Baby Be Mine', '4min 20s', 3, 2),
    ('The Girl Is Mine', '3min 42s', 3, 2),
    ('Thriller', '5min 57s', 5, 2),
    ('Beat It', '4min 18s', 5, 2),
    ('Billie Jean', '4min 54s', 5, 2),
    ('Human Nature', '4min 6s', 4, 2),
    ('P.Y.T. (Pretty Young Thing)', '3min 59s', 4, 2),
    ('The Lady in My Life', '5min', 3, 2),

    -- Songs pour OK Computer (album_id = 3)
    ('Airbag', '4min 44s', 4, 3),
    ('Paranoid Android', '6min 23s', 5, 3),
    ('Subterranean Homesick Alien', '4min 27s', 4, 3),
    ('Exit Music (For a Film)', '4min 24s', 5, 3),
    ('Let Down', '4min 59s', 4, 3),
    ('Karma Police', '4min 21s', 5, 3),
    ('Fitter Happier', '1min 57s', 2, 3),
    ('Electioneering', '3min 50s', 3, 3),
    ('Climbing Up the Walls', '4min 45s', 4, 3),
    ('No Surprises', '3min 48s', 4, 3),
    ('Lucky', '4min 19s', 3, 3),
    ('The Tourist', '5min 24s', 4, 3);

