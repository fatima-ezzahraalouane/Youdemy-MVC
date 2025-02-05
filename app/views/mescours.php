<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header('Location: login.php');
    exit();
}

require_once '../../BackEnd/config/Database.php';

$id_usersite = $_SESSION['user_id'];

try {
    // Connexion à la base de données
    $database = new Database();
    $pdo = $database->getConnection();

    // Récupérer les cours auxquels l'utilisateur est inscrit
    $query = "
    SELECT c.id_cours, c.titre, c.description, c.image_url, c.contenu_type, cat.nom AS categorie, u.username AS enseignant
    FROM inscription i
    JOIN cours c ON i.id_cours = c.id_cours
    JOIN categorie cat ON c.id_categorie = cat.id_categorie
    JOIN usersite u ON c.id_usersite = u.id_usersite
    WHERE i.id_usersite = :id_usersite
";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_usersite', $id_usersite, PDO::PARAM_INT);
    $stmt->execute();
    $mesCours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Cours</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .course-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .course-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .course-card-body {
            padding: 16px;
        }

        .course-card-title {
            font-size: 1.25rem;
            margin-bottom: 8px;
        }

        .course-card-category {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 8px;
        }

        .back-button {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container py-4">
           <!-- Bouton Retour -->
           <div class="back-button text-start">
            <a href="cours.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour à la liste des cours</a>
        </div>
        <h1 class="text-center mb-4">Mes Cours</h1>
        <div class="row">
            <?php if (!empty($mesCours)): ?>
                <?php foreach ($mesCours as $cours): ?>
                    <div class="col-md-4 mb-4">
                        <div class="course-card">
                            <img src="<?= htmlspecialchars($cours['image_url']); ?>" alt="<?= htmlspecialchars($cours['titre']); ?>">
                            <div class="course-card-body">
                                <h5 class="course-card-title"><?= htmlspecialchars($cours['titre']); ?></h5>
                                <p class="course-card-category"><?= htmlspecialchars($cours['categorie']); ?></p>
                                <p class="text-muted">Enseignant : <?= htmlspecialchars($cours['enseignant']); ?></p>
                                <a href="cours_details.php?id=<?= htmlspecialchars($cours['id_cours']); ?>" class="btn btn-primary w-100">Accéder</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Vous n'êtes inscrit(e) à aucun cours pour le moment.</p>
            <?php endif; ?>
        </div>
          <!-- Bouton Retour en bas de page -->
          <div class="back-button text-start">
            <a href="cours.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour à la liste des cours</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
