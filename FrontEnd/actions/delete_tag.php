<?php
require_once '../../BackEnd/config/Database.php';
require_once '../../BackEnd/classes/Tags.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $pdo = $db->getConnection();

    $tag = new Tag($pdo);
    $id_tag = $_POST['id_tag'] ?? null;

    if ($id_tag) {
        $result = $tag->deleteTag($id_tag);
        if ($result) {
            header('Location: ../pages/dashboard_admin.php?message=Tag supprimé avec succès.');
        } else {
            header('Location: ../pages/dashboard_admin.php?error=Erreur lors de la suppression du tag.');
        }
    } else {
        header('Location: ../pages/dashboard_admin.php?error=ID du tag invalide.');
    }
}
