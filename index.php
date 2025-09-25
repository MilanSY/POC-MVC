<?php

require_once 'models/Media.php';
require_once 'models/Album.php';
require_once 'models/Book.php';
require_once 'models/Movie.php';
require_once 'models/Song.php';
require_once 'models/User.php';
require_once 'var/MediaRepository.php';
require_once 'var/PasswordValidator.php';

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
        include 'controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->loginForm();
        }
        break;
        
    case 'register':
        include 'controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register();
        } else {
            $controller->registerForm();
        }
        break;
        
    case 'logout':
        include 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
        
    default:
        // Page 404
        http_response_code(404);
        include 'views/errors/404.php';
        break;
}
