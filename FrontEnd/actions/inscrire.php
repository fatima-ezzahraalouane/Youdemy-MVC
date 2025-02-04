<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header('Location: ../pages/login.php');
    exit();
}

require_once '../../BackEnd/config/Database.php';

// Vérifier si l'ID du cours est fourni
if (!isset($_GET['id_cours']) || empty($_GET['id_cours'])) {
    header('Location: ../pages/cours.php?error=no_course_id');
    exit();
}

$id_cours = (int)$_GET['id_cours'];
$id_usersite = $_SESSION['user_id'];

try {
    // Connexion à la base de données
    $database = new Database();
    $pdo = $database->getConnection();

    // Vérifier si l'utilisateur est déjà inscrit au cours
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM inscription WHERE id_usersite = :id_usersite AND id_cours = :id_cours");
    $checkStmt->execute([':id_usersite' => $id_usersite, ':id_cours' => $id_cours]);
    if ($checkStmt->fetchColumn() > 0) {
        header("Location: ../pages/cours.php?error=already_enrolled&id_cours=$id_cours");
        exit();
    }

    // Ajouter une nouvelle inscription
    $stmt = $pdo->prepare("INSERT INTO inscription (id_usersite, id_cours, date_inscription) VALUES (:id_usersite, :id_cours, NOW())");
    $stmt->execute([':id_usersite' => $id_usersite, ':id_cours' => $id_cours]);

    // Rediriger avec un message de succès
    header("Location: ../pages/cours.php?success=course_enrolled&id_cours=$id_cours");
    exit();

} catch (PDOException $e) {
    // Rediriger avec un message d'erreur
    header("Location: ../pages/cours.php?error=db_error&message=" . urlencode($e->getMessage()));
    exit();
}
