<?php

require_once 'models/Media.php';
require_once 'models/Album.php';
require_once 'models/Book.php';
require_once 'models/Movie.php';
require_once 'models/Song.php';
require_once 'models/User.php';
require_once 'var/MediaRepository.php';
require_once 'var/PasswordValidator.php';
require_once 'var/SessionManager.php';

// Démarrer la session
session_start();

$baseUrl = 'http://localhost:8000';

// Parse de l'URI
$uri = parse_url($_SERVER["REQUEST_URI"])["path"];
$path = explode("/", $uri);

// Déterminer la page à partir de l'URI
if (isset($path[1]) && !empty($path[1])) {
    $page = $path[1];
} else {
    $page = 'home';
}

$repository = new MediaRepository();

switch ($page) {
    case '':
    case 'home':
        include 'controllers/HomeController.php';
        $controller = new HomeController($repository);
        $controller->index();
        break;
        
    case 'books':
        include 'controllers/BookController.php';
        $controller = new BookController($repository);
        $controller->index();
        break;
        
    case 'albums':
        include 'controllers/AlbumController.php';
        $controller = new AlbumController($repository);
        $controller->index();
        break;
        
    case 'movies':
        include 'controllers/MovieController.php';
        $controller = new MovieController($repository);
        $controller->index();
        break;
        
    case 'login':
        include 'controllers/LoginController.php';
        $controller = new LoginController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } else {
            $controller->index();
        }
        break;
        
    case 'register':
        include 'controllers/RegisterController.php';
        $controller = new RegisterController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } else {
            $controller->index();
        }
        break;
        
    case 'logout':
        include 'controllers/LogoutController.php';
        $controller = new LogoutController();
        $controller->index();
        break;
        
    case 'add-book':
        include 'controllers/AddBookController.php';
        $controller = new AddBookController($repository);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } else {
            $controller->index();
        }
        break;
        
    case 'add-movie':
        include 'controllers/AddMovieController.php';
        $controller = new AddMovieController($repository);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } else {
            $controller->index();
        }
        break;
        
    case 'add-album':
        include 'controllers/AddAlbumController.php';
        $controller = new AddAlbumController($repository);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } else {
            $controller->index();
        }
        break;
        
    case 'add-songs':
        include 'controllers/AddSongsController.php';
        $controller = new AddSongsController($repository);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } else {
            $controller->index();
        }
        break;
        
    default:
        http_response_code(404);
        include 'views/errors/404.php';
        break;
}
