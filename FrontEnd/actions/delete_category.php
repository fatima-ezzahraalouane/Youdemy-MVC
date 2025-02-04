<?php
require_once '../../BackEnd/classes/Admin.php';
require_once '../../BackEnd/config/Database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_categorie = $_POST['id_categorie'] ?? '';

    if (!empty($id_categorie)) {
        $db = new Database();
        $pdo = $db->getConnection();

        $stmt = $pdo->prepare("SELECT * FROM usersite WHERE id_usersite = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $adminData = $stmt->fetch(PDO::FETCH_ASSOC);

        $admin = new Admin($pdo, $adminData['username'], $adminData['email'], $adminData['password']);
        $message = $admin->supprimerCategorie($id_categorie);
        header("Location: ../pages/dashboard_admin.php?message=" . urlencode($message));
    } else {
        header("Location: ../pages/dashboard_admin.php?message=" . urlencode("L'identifiant de la catégorie est obligatoire."));
    }
    exit;
}
