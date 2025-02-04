<?php
require_once '../../BackEnd/classes/Admin.php';
require_once '../../BackEnd/config/Database.php';

session_start();

// Vérifier si l'utilisateur est connecté en tant qu'admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: ../pages/login.php');
    exit();
}

// Vérifier si une requête POST est reçue
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($course_id && in_array($action, ['approve', 'reject'])) {
        $db = new Database();
        $pdo = $db->getConnection();

        // Initialiser l'administrateur
        $stmt = $pdo->prepare("SELECT * FROM usersite WHERE id_usersite = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $adminData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($adminData) {
            try {
                $admin = new Admin($pdo, $adminData['username'], $adminData['email'], $adminData['password']);
                $message = $admin->manageCourse($course_id, $action);
                header("Location: ../pages/dashboard_admin.php?message=" . urlencode($message));
                exit();
            } catch (Exception $e) {
                header("Location: ../pages/dashboard_admin.php?message=" . urlencode("Erreur : " . $e->getMessage()));
                exit();
            }
        }
    }

    // Rediriger avec un message d'erreur si quelque chose ne va pas
    header("Location: ../pages/dashboard_admin.php?message=" . urlencode("Erreur : Action invalide."));
    exit();
}
?>
