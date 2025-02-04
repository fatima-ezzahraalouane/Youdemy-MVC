<?php
require_once '../../BackEnd/config/Database.php';
require_once '../../BackEnd/classes/Tags.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $pdo = $db->getConnection();

    $tag = new Tag($pdo);
    $nom = $_POST['nom'] ?? '';

    if (!empty($nom)) {
        $result = $tag->createOrGetTag($nom);
        if ($result) {
            header('Location: ../pages/dashboard_admin.php?message=Tag ajouté avec succès.');
        } else {
            header('Location: ../pages/dashboard_admin.php?error=Erreur lors de l\'ajout du tag.');
        }
    } else {
        header('Location: ../pages/dashboard_admin.php?error=Le nom du tag est requis.');
    }
}
