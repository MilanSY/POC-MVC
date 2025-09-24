-- =============================================
-- Script SQL pour la médiathèque POC-MVC
-- =============================================

-- Supprimer les tables existantes si elles existent
DROP TABLE IF EXISTS album_songs;

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
    borrowed_by_user_id INT DEFAULT NULL,
    FOREIGN KEY (borrowed_by_user_id) REFERENCES users (id) ON DELETE SET NULL,
    INDEX idx_borrowed_by (borrowed_by_user_id),
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
    duration DECIMAL(6, 2) NOT NULL, -- Durée en minutes
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

-- Table Songs
CREATE TABLE songs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(255) NOT NULL,
    duration DECIMAL(6, 2) NOT NULL, -- Durée en minutes
    note INT DEFAULT 0 CHECK (
        note >= 0
        AND note <= 5
    )
);

-- Table de liaison Album-Songs
CREATE TABLE album_songs (
    album_id INT,
    song_id INT,
    track_position INT,
    PRIMARY KEY (album_id, song_id),
    FOREIGN KEY (album_id) REFERENCES albums (id) ON DELETE CASCADE,
    FOREIGN KEY (song_id) REFERENCES songs (id) ON DELETE CASCADE
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
VALUES (7, 148.0, 'Science-Fiction'), -- Inception
    (8, 175.0, 'Drame'), -- Le Parrain
    (9, 154.0, 'Action'), -- Pulp Fiction
    (10, 169.0, 'Science-Fiction'), -- Interstellar
    (11, 195.0, 'Drame'), -- La Liste de Schindler
    (12, 164.0, 'Science-Fiction'), -- Blade Runner 2049
    (13, 122.0, 'Comédie'), -- Amélie Poulain
    (14, 146.0, 'Horreur');
-- Shining

-- === CHANSONS ===
INSERT INTO
    songs (titre, duration, note)
VALUES
    -- Songs pour Abbey Road
    ('Come Together', 4.33, 5),
    ('Something', 3.05, 4),
    (
        'Maxwell\'s Silver Hammer',
        3.28,
        3
    ),
    ('Oh! Darling', 3.27, 4),
    ('Octopus\'s Garden', 2.51, 3),
    (
        'I Want You (She\'s So Heavy)',
        7.47,
        5
    ),
    ('Here Comes the Sun', 3.05, 5),
    ('Because', 2.46, 4),
    (
        'You Never Give Me Your Money',
        4.02,
        4
    ),
    ('Sun King', 2.26, 3),
    ('Mean Mr. Mustard', 1.06, 2),
    ('Polythene Pam', 1.13, 3),
    (
        'She Came in Through the Bathroom Window',
        1.57,
        3
    ),
    ('Golden Slumbers', 1.31, 4),
    ('Carry That Weight', 1.36, 4),
    ('The End', 2.20, 5),
    ('Her Majesty', 0.23, 2),

-- Songs pour Thriller
(
    'Wanna Be Startin\' Somethin\'',
    6.03,
    4
),
('Baby Be Mine', 4.20, 3),
('The Girl Is Mine', 3.42, 3),
('Thriller', 5.57, 5),
('Beat It', 4.18, 5),
('Billie Jean', 4.54, 5),
('Human Nature', 4.06, 4),
(
    'P.Y.T. (Pretty Young Thing)',
    3.59,
    4
),
(
    'The Lady in My Life',
    5.00,
    3
),

-- Songs pour OK Computer
('Airbag', 4.44, 4),
('Paranoid Android', 6.23, 5),
(
    'Subterranean Homesick Alien',
    4.27,
    4
),
(
    'Exit Music (For a Film)',
    4.24,
    5
),
('Let Down', 4.59, 4),
('Karma Police', 4.21, 5),
('Fitter Happier', 1.57, 2),
('Electioneering', 3.50, 3),
(
    'Climbing Up the Walls',
    4.45,
    4
),
('No Surprises', 3.48, 4),
('Lucky', 4.19, 3),
('The Tourist', 5.24, 4);

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

-- === LIAISON ALBUMS-SONGS ===
-- Abbey Road (album_id = 1)
INSERT INTO
    album_songs (
        album_id,
        song_id,
        track_position
    )
VALUES (1, 1, 1),
    (1, 2, 2),
    (1, 3, 3),
    (1, 4, 4),
    (1, 5, 5),
    (1, 6, 6),
    (1, 7, 7),
    (1, 8, 8),
    (1, 9, 9),
    (1, 10, 10),
    (1, 11, 11),
    (1, 12, 12),
    (1, 13, 13),
    (1, 14, 14),
    (1, 15, 15),
    (1, 16, 16),
    (1, 17, 17);

-- Thriller (album_id = 2)
INSERT INTO
    album_songs (
        album_id,
        song_id,
        track_position
    )
VALUES (2, 18, 1),
    (2, 19, 2),
    (2, 20, 3),
    (2, 21, 4),
    (2, 22, 5),
    (2, 23, 6),
    (2, 24, 7),
    (2, 25, 8),
    (2, 26, 9);

-- OK Computer (album_id = 3)
INSERT INTO
    album_songs (
        album_id,
        song_id,
        track_position
    )
VALUES (3, 27, 1),
    (3, 28, 2),
    (3, 29, 3),
    (3, 30, 4),
    (3, 31, 5),
    (3, 32, 6),
    (3, 33, 7),
    (3, 34, 8),
    (3, 35, 9),
    (3, 36, 10),
    (3, 37, 11),
    (3, 38, 12);