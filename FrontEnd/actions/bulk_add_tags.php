<?php
require_once '../../BackEnd/config/Database.php';
require_once '../../BackEnd/classes/Tags.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $pdo = $db->getConnection();

    $tag = new Tag($pdo);
    $tagsInput = $_POST['tags'] ?? '';

    if (!empty($tagsInput)) {
        $tagsArray = array_map('trim', explode(',', $tagsInput));
        $result = $tag->createMultipleTags($tagsArray);

        if ($result) {
            header('Location: ../pages/dashboard_admin.php?message=Tags ajoutés avec succès.');
        } else {
            header('Location: ../pages/dashboard_admin.php?error=Erreur lors de l\'ajout des tags.');
        }
    } else {
        header('Location: ../pages/dashboard_admin.php?error=Veuillez fournir une liste de tags.');
    }
}
