<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header('Location: login.php');
    exit(); 
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php?error=session_expired");
    exit();
}

$id_usersite = $_SESSION['user_id'];

require_once '../../BackEnd/config/Database.php';
require_once '../../BackEnd/classes/Categorie.php';
require_once '../../BackEnd/classes/Tags.php';



$database = new Database();
$db = $database->getConnection();

try {
    // Récupération des catégories
    $categories = Categorie::afficherCategorie();
    if (!$categories) {
        throw new Exception("Erreur lors de la récupération des catégories.");
    }

    // Récupération des tags
    $tagClass = new Tag($db);
    $tags = $tagClass->getAllTags();
    if (!$tags) {
        throw new Exception("Erreur lors de la récupération des tags.");
    }
} catch (Exception $e) {
    // Affiche une erreur si les données ne peuvent pas être récupérées
    die("Erreur : " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- FAVICONS ICON ============================================= -->
    <link rel="icon" href="../assets/images/youdemy2.png" type="image/x-icon" />
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/youdemy2.png" />
    <title>Tableau de Bord Enseignant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .course-actions button {
            margin: 0 2px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar py-3">
            <div class="position-sticky">
            <div class="text-center mb-4">
    <img src="../assets/images/youdemy2.png" alt="Teacher Profile" class="shadow-sm" style="height: 80px; width: 80px; object-fit: cover;">
    <h5 class="mt-3 fw-bold text-primary"><?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></h5>
    <p class="text-muted">Enseignant Youdemy</p>
</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#dashboard">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#courses">
                            <i class="bi bi-book"></i> Mes Cours
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#statistics">
                            <i class="bi bi-graph-up"></i> Statistiques
                        </a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-key"></i> Se déconnecter
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Tableau de Bord Enseignant</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                        <i class="bi bi-plus-circle"></i> Nouveau Cours
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card bg-primary text-white stat-card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total des Cours</h5>
                            <h2 class="display-4">12</h2>
                            <!-- <p class="mb-0"><i class="bi bi-arrow-up"></i> +2 ce mois</p> -->
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card bg-success text-white stat-card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Cours Vidéo</h5>
                            <h2 class="display-4">8</h2>
                            <!-- <p class="mb-0">67% du total</p> -->
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card bg-info text-white stat-card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Cours Document</h5>
                            <h2 class="display-4">4</h2>
                            <!-- <p class="mb-0">33% du total</p> -->
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card bg-warning text-white stat-card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Étudiants Inscrits</h5>
                            <h2 class="display-4">156</h2>
                            <!-- <p class="mb-0"><i class="bi bi-arrow-up"></i> +12 cette semaine</p> -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course List -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Mes Cours</h5>
                    <div class="input-group w-25">
                        <input type="text" class="form-control" placeholder="Rechercher...">
                        <button class="btn btn-outline-secondary" type="button"><i class="bi bi-search"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Type</th>
                                    <th>Catégorie</th>
                                    <th>Étudiants</th>
                                    <th>Date de création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Introduction à Python</td>
                                    <td><span class="badge bg-primary">Vidéo</span></td>
                                    <td>Programmation</td>
                                    <td>45</td>
                                    <td>2024-01-15</td>
                                    <td class="course-actions">
                                        <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-info"><i class="bi bi-eye"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Bases de données SQL</td>
                                    <td><span class="badge bg-success">Document</span></td>
                                    <td>Base de données</td>
                                    <td>32</td>
                                    <td>2024-01-10</td>
                                    <td class="course-actions">
                                        <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-info"><i class="bi bi-eye"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Nouveau Cours</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="../actions/ajouter_cours.php" method="POST">
                    <div class="row mb-3">
                        <!-- Titre -->
                        <div class="col-md-6">
                            <label for="titre" class="form-label">Titre</label>
                            <input type="text" class="form-control" id="titre" name="titre" required>
                        </div>
                        <!-- Lien de l'image -->
                        <div class="col-md-6">
                            <label for="image_url" class="form-label">Lien de l'image</label>
                            <input type="text" class="form-control" id="image_url" name="image_url" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- Description -->
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- Type de contenu -->
                        <div class="col-md-6">
                            <label for="contenu_type" class="form-label">Type de Contenu</label>
                            <select class="form-select" id="contenu_type" name="contenu_type" required>
                                <option value="Vidéo">Vidéo</option>
                                <option value="Document">Document</option>
                            </select>
                        </div>
                        <!-- Lien de contenu -->
                        <div class="col-md-6">
                            <label for="contenu" class="form-label">Lien de Contenu</label>
                            <input type="text" class="form-control" id="contenu" name="contenu" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- Catégorie -->
                        <div class="col-md-6">
                            <label for="id_categorie" class="form-label">Catégorie</label>
                            <select class="form-select" id="id_categorie" name="id_categorie" required>
                                <?php foreach ($categories as $categorie): ?>
                                    <option value="<?= $categorie->getIdCategorie(); ?>"><?= htmlspecialchars($categorie->getNom()); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Tags -->
                        <div class="col-md-6">
                            <label for="tags" class="form-label">Tags</label>
                            <select class="form-select" id="tags" name="tags[]" multiple required>
                                <?php foreach ($tags as $tag): ?>
                                    <option value="<?= $tag['id_tag']; ?>"><?= htmlspecialchars($tag['nom']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <!-- Boutons Annuler et Ajouter -->
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter le Cours</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>