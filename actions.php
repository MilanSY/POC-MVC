<?php

session_start();
require_once 'MediaRepository.php';

header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour effectuer cette action']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$action = $_POST['action'] ?? '';
$mediaId = (int)($_POST['media_id'] ?? 0);
$userId = (int)$_SESSION['user_id'];

if (!$mediaId) {
    echo json_encode(['success' => false, 'message' => 'ID de média invalide']);
    exit;
}

try {
    $repository = new MediaRepository();
    
    switch ($action) {
        case 'borrow':
            if (!$repository->canBorrowMedia($mediaId)) {
                echo json_encode(['success' => false, 'message' => 'Ce média n\'est pas disponible ou est déjà emprunté']);
                break;
            }
            
            $success = $repository->emprunterMedia($mediaId, $userId);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Média emprunté avec succès']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Impossible d\'emprunter ce média']);
            }
            break;
            
        case 'return':
            $success = $repository->rendreMedia($mediaId, $userId);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Média rendu avec succès']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Vous ne pouvez rendre que les médias que vous avez empruntés']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}