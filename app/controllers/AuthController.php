<?php
session_start();

require_once __DIR__ . '../../config/Database.php';
require_once __DIR__ . '../../models/Usersite.php';

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Vérification si la requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    try {
        // Préparation de la requête pour trouver l'utilisateur par email
        $stmt = $db->prepare("SELECT * FROM usersite WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification si l'utilisateur existe
        if ($user && password_verify($password, $user['password'])) {
            // Vérification si l'utilisateur est un enseignant et non approuvé
            if ($user['id_role'] == 3 && $user['statut'] !== 'actif') {
                header("Location: ../../FrontEnd/pages/login.php?error=approval_pending");
                exit();
            }

            // Stockage des informations utilisateur en session
            $_SESSION['user_id'] = $user['id_usersite'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['id_role'];

              // Redirigez en fonction du rôle de l'utilisateur
        if ($user['id_role'] == 1) {
            header("Location: ../views/dashboard_admin.php");
        } elseif ($user['id_role'] == 2) {
            header("Location: ../../FrontEnd/pages/accueil.php");
        } elseif ($user['id_role'] == 3) {
            header("Location: ../../FrontEnd/pages/dashboard_ense.php");
        }
        exit();
 
        } else {
            // Redirection vers login.php avec une erreur
            header('Location: ../../FrontEnd/pages/login.php?error=invalid_credentials');
            exit;
        }
    } catch (Exception $e) {
        // Redirection avec une erreur générale
        header('Location: ../../FrontEnd/pages/login.php?error=server_error');
        exit;
    }
}
