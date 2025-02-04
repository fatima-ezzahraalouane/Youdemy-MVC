<?php
require_once '../../BackEnd/classes/Admin.php';
require_once '../../BackEnd/config/Database.php';

session_start();

// Vérifier si l'utilisateur est connecté et est un admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: ../pages/login.php');
    exit();
}

// Vérification de la méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($user_id && in_array($action, ['activate', 'suspend', 'delete'])) {
        $db = new Database();
        $pdo = $db->getConnection();

        // Initialiser l'administrateur
        $stmt = $pdo->prepare("SELECT * FROM usersite WHERE id_usersite = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $adminData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($adminData) {
            $admin = new Admin($pdo, $adminData['username'], $adminData['email'], $adminData['password']);
            $message = $admin->manageUser($user_id, $action);

            // Redirection avec un message de confirmation
            header("Location: ../pages/dashboard_admin.php?message=" . urlencode($message));
            exit();
        }
    }

    // Redirection avec un message d'erreur
    header("Location: ../pages/dashboard_admin.php?message=" . urlencode("Erreur : Action utilisateur invalide."));
    exit();
}
?>
