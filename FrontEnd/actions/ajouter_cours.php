<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: ../pages/login.php?error=session_expired");
    exit();
}

// Récupération de l'ID utilisateur
$id_usersite = $_SESSION['user_id'];
require_once '../../BackEnd/config/Database.php';
require_once '../../BackEnd/classes/Cours.php';
require_once '../../BackEnd/classes/CoursDoc.php';
require_once '../../BackEnd/classes/CoursVideo.php';
require_once '../../BackEnd/classes/Tags.php';

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupération des données du formulaire
        $titre = htmlspecialchars($_POST['titre']);
        $description = htmlspecialchars($_POST['description']);
        $image_url = htmlspecialchars($_POST['image_url']);
        $contenu_type = htmlspecialchars($_POST['contenu_type']);
        $contenu = htmlspecialchars($_POST['contenu']);
        $id_categorie = intval($_POST['id_categorie']);
        $tags = isset($_POST['tags']) ? $_POST['tags'] : []; // Tableau de tags
        $id_usersite = $_SESSION['user_id']; // L'ID de l'enseignant connecté (issu de la session)

        // Vérification de l'existence du cours
        if (Cours::coursExiste($titre)) {
            throw new Exception("Un cours avec ce titre existe déjà !");
        }
      
        
        // Création de l'objet cours en fonction du type
        if ($contenu_type === 'Document') {
            $cours = new CoursDoc([
                'titre' => $titre,
                'description' => $description,
                'image_url' => $image_url,
                'contenu' => $contenu,
                'contenu_type' => $contenu_type,
                'id_usersite' => $id_usersite,
                'id_categorie' => $id_categorie,
                'statut' => 'en_attente', // Par défaut
            ]);
        } elseif ($contenu_type === 'Vidéo') {
            $cours = new CoursVideo([
                'titre' => $titre,
                'description' => $description,
                'image_url' => $image_url,
                'contenu' => $contenu,
                'contenu_type' => $contenu_type,
                'id_usersite' => $id_usersite,
                'id_categorie' => $id_categorie,
                'statut' => 'en_attente', // Par défaut
            ]);
        } else {
            throw new Exception("Type de contenu non valide.");
        }

        // Ajout du cours dans la base de données
        $cours->ajouterCours();

        // Ajout des tags associés au cours
        $tagClass = new Tag($db);
        foreach ($tags as $tagId) {
            $tagClass->linkTagToCours($cours->getIdCours(), $tagId);
        }

        // Redirection avec succès
        header("Location: ../pages/dashboard_ense.php?success=cours_ajoute");
        exit();
    } catch (Exception $e) {
        // Redirection avec une erreur
        header("Location: ../pages/dashboard_ense.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}
