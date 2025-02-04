<?php
require_once '../../BackEnd/config/Database.php';
require_once '../../BackEnd/classes/Tags.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $pdo = $db->getConnection();

    $tag = new Tag($pdo);
    $id_tag = $_POST['id_tag'] ?? null;
    $nom = $_POST['nom'] ?? '';

    if ($id_tag && !empty($nom)) {
        $result = $tag->updateTag($id_tag, $nom);
        if ($result) {
            header('Location: ../pages/dashboard_admin.php?message=Tag modifié avec succès.');
        } else {
            header('Location: ../pages/dashboard_admin.php?error=Erreur lors de la modification du tag.');
        }
    } else {
        header('Location: ../pages/dashboard_admin.php?error=Le nom du tag est requis.');
    }
}
