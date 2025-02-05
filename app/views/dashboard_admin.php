<?php
session_start();
require_once '../../BackEnd/classes/Admin.php';
require_once '../../BackEnd/config/Database.php';


// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: login.php');
    exit();
}

$db = new Database();
$pdo = $db->getConnection();

// Fetch admin details from the database
$stmt = $pdo->prepare("SELECT * FROM usersite WHERE id_usersite = ?");
$stmt->execute([$_SESSION['user_id']]);
$adminData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$adminData) {
    header('Location: login.php');
    exit();
}

$admin = new Admin($pdo, $adminData['username'], $adminData['email'], $adminData['password']);

// Fetch statistics
$stats = $admin->getStatistics();

// Fetch all users
$stmt = $pdo->prepare("SELECT * FROM usersite");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all courses with teacher username and category name
$stmt = $pdo->prepare("
    SELECT c.id_cours, c.titre, c.statut, u.username as enseignant, cat.nom as categorie 
    FROM cours c 
    JOIN usersite u ON c.id_usersite = u.id_usersite 
    JOIN categorie cat ON c.id_categorie = cat.id_categorie
");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
$categories = Categorie::afficherCategorie();
$tags = $admin->afficherTags();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/images/youdemy2.png" type="image/x-icon" />
	<link rel="shortcut icon" type="image/x-icon" href="../assets/images/youdemy2.png" />
    <title>Dashboard Administrateur</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            background-color: #343a40;
        }

        .sidebar .nav-link {
            color: #fff;
            padding: 0.8rem 1rem;
        }

        .sidebar .nav-link:hover {
            background-color: #495057;
        }

        #dashboard,
        #users-section,
        #categories-section,
        #courses-section,
        #tags-section {
            scroll-margin-top: 100px;
            /* Décalage pour la lisibilité */
            margin-bottom: 50px;
            /* Espacement entre sections */
        }

        .sidebar .nav-link.active {
            background-color: #6d28d9;
            font-weight: bold;
        }

        .stat-card {
            border-left: 4px solid #6d28d9;
        }

        .table-actions a {
            margin: 0 5px;
        }

        .category-distribution {
            height: 300px;
        }

        .top-teachers {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .top-teachers img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .btn-violet {
            background-color: #6d28d9;
            color: white;
        }

        .bg-violet {
            background-color: #6d28d9;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="d-flex flex-column flex-shrink-0 p-3">
                    <h5 class="text-white">Dashboard Admin</h5>
                    <hr class="text-white">
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="#dashboard" class="nav-link active">
                                <i class="fas fa-home me-2"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#users-section" class="nav-link">
                                <i class="fas fa-users me-2"></i> Gestion des Utilisateurs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#categories-section" class="nav-link">
                                <i class="fas fa-list me-2"></i> Gestion des Catégories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#courses-section" class="nav-link">
                                <i class="fas fa-book me-2"></i> Gestion des Cours
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tags-section" class="nav-link">
                                <i class="fas fa-tags me-2"></i> Gestion des Tags
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link">
                                <i class="fas fa-key me-2"></i> Se déconnecter
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div id="dashboard" class="col-md-9 col-lg-10 ms-sm-auto px-4 py-3">
                <h2 class="mb-4">Tableau de bord</h2>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Cours</h5>
                                <h3 class="card-text"><?= $stats['total_courses'] ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Étudiants</h5>
                                <h3 class="card-text"><?= $stats['total_students'] ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Enseignants</h5>
                                <h3 class="card-text"><?= $stats['total_teachers'] ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Catégories</h5>
                                <h3 class="card-text"><?= $stats['total_categories'] ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Details -->
                <div class="row mb-4">
                    <!-- Course Distribution -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Répartition des Cours par Catégorie</h5>
                            </div>
                            <div class="card-body">
                                <div class="category-distribution">
                                    <canvas id="categoryChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Course and Teachers -->
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">Cours le Plus Populaire</h5>
                            </div>
                            <div class="card-body">
                                <h6><?= $stats['top_course']['titre'] ?? 'Aucun cours' ?></h6>
                                <p><?= $stats['top_course']['students'] ?? 0 ?> étudiants inscrits</p>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 85%">85% satisfaction</div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Top 3 Enseignants</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($stats['top_teachers'] as $teacher): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-user-circle me-3"></i>
                                        <div>
                                            <h6 class="mb-0"><?= $teacher['username'] ?></h6>
                                            <small><?= $teacher['courses'] ?> cours</small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Management Section -->
                <div id="users-section" class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Gestion des Utilisateurs</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $user['username'] ?></td>
                                        <td><?= $user['email'] ?></td>
                                        <td><?= $user['id_role'] == 1 ? 'Admin' : ($user['id_role'] == 2 ? 'Étudiant' : 'Enseignant') ?></td>
                                        <td><span class="badge bg-<?= $user['statut'] == 'actif' ? 'success' : ($user['statut'] == 'suspendu' ? 'warning' : 'danger') ?>"><?= $user['statut'] ?></span></td>
                                        <td class="table-actions">
                                            <!-- Formulaire pour gérer les actions utilisateur -->
                                            <form method="POST" action="../actions/manage_user.php" style="display:inline;">
                                                <input type="hidden" name="user_id" value="<?= $user['id_usersite'] ?>">
                                                <button type="submit" name="action" value="activate" class="btn btn-success btn-sm">Activer</button>
                                                <button type="submit" name="action" value="suspend" class="btn btn-warning btn-sm">Suspendre</button>
                                                <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Êtes-vous sûr(e) de vouloir supprimer cet utilisateur ?');">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Category Management Section -->
                <div id="categories-section" class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Gestion des Catégories</h5>
                        <button class="btn btn-violet" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus me-2"></i>Nouvelle Catégorie
                        </button>

                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Image url</th>
                                    <th>Nombre de Cours</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($category->getNom()) ?></td>
                                        <td><img src="<?= htmlspecialchars($category->getImageUrl()) ?>" alt="<?= htmlspecialchars($category->getNom()) ?>" width="100"></td>
                                        <td><?= htmlspecialchars($category->getTotalCourses()) ?></td>
                                        <td class="table-actions">
                                            <a href="#"
                                                class="btn btn-warning btn-sm"
                                                title="Modifier"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editCategoryModal<?= $category->getIdCategorie() ?>">
                                                Modifier
                                            </a>

                                            <form method="POST" action="../actions/delete_category.php" class="d-inline">
                                                <input type="hidden" name="id_categorie" value="<?= $category->getIdCategorie() ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Êtes-vous sûre de vouloir supprimer cette catégorie ?');"
                                                    title="Supprimer">
                                                    Supprimer
                                                </button>
                                            </form>

                                        </td>
                                    </tr>
                                    <div class="modal fade" id="editCategoryModal<?= $category->getIdCategorie() ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="../actions/edit_category.php">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Modifier la Catégorie</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id_categorie" value="<?= $category->getIdCategorie() ?>">
                                                        <div class="mb-3">
                                                            <label for="nom_category_<?= $category->getIdCategorie() ?>">Nom</label>
                                                            <input type="text" name="nom" id="nom_category_<?= $category->getIdCategorie() ?>"
                                                                class="form-control" value="<?= htmlspecialchars($category->getNom()) ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="image_category_<?= $category->getIdCategorie() ?>">URL de l'image</label>
                                                            <input type="text" name="image_url" id="image_category_<?= $category->getIdCategorie() ?>"
                                                                class="form-control" value="<?= htmlspecialchars($category->getImageUrl()) ?>">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-primary">Modifier</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Course Management Section -->
                <div id="courses-section" class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Gestion des Cours</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Enseignant</th>
                                    <th>Catégorie</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($course['titre']) ?></td>
                                        <td><?= htmlspecialchars($course['enseignant']) ?></td>
                                        <td><?= htmlspecialchars($course['categorie']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $course['statut'] == 'publie' ? 'success' : ($course['statut'] == 'rejete' ? 'danger' : 'warning') ?>">
                                                <?= htmlspecialchars($course['statut']) ?>
                                            </span>
                                        </td>
                                        <td class="table-actions">
                                            <!-- Formulaire pour gérer les actions -->
                                            <form method="POST" action="../actions/manage_course.php" style="display:inline;">
                                                <input type="hidden" name="course_id" value="<?= $course['id_cours'] ?>">
                                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approuver</button>
                                                <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Rejeter</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Gestion des Tags -->
                <div id="tags-section" class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Gestion des Tags</h5>
                        <button class="btn btn-violet" data-bs-toggle="modal" data-bs-target="#addTagModal">
                            <i class="fas fa-plus me-2"></i> Ajouter Tag(s)
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tags as $tag): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($tag['nom']) ?></td>
                                            <td>
                                                <!-- Modifier -->
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTagModal<?= $tag['id_tag'] ?>">Modifier</button>

                                                <!-- Supprimer -->
                                                <form method="POST" action="../actions/delete_tag.php" class="d-inline">
                                                    <input type="hidden" name="id_tag" value="<?= $tag['id_tag'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûre de vouloir supprimer ce tag ?');">Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Modal Modifier -->
                                        <div class="modal fade" id="editTagModal<?= $tag['id_tag'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="../actions/edit_tag.php">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Modifier le Tag</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id_tag" value="<?= $tag['id_tag'] ?>">
                                                            <div class="mb-3">
                                                                <label>Nom du Tag</label>
                                                                <input type="text" name="nom" value="<?= htmlspecialchars($tag['nom']) ?>" class="form-control" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <!-- Modal for Adding Category -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="../actions/add_category.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter une Catégorie</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nom_category">Nom</label>
                            <input type="text" name="nom" id="nom_category" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="image_category">URL de l'image</label>
                            <input type="text" name="image_url" id="image_category" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>





    <!-- Modal Ajouter Tag(s) -->
    <div class="modal fade" id="addTagModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter Tag(s)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Choisissez une option pour ajouter un ou plusieurs tags :</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSingleTagModal" data-bs-dismiss="modal">
                            Ajouter un seul Tag
                        </button>
                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addMultipleTagsModal" data-bs-dismiss="modal">
                            Ajouter plusieurs Tags
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajouter un seul Tag -->
    <div class="modal fade" id="addSingleTagModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="../actions/add_single_tag.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un seul Tag</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nom du Tag</label>
                            <input type="text" name="nom" class="form-control" placeholder="Nom du Tag" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ajouter plusieurs Tags -->
    <div class="modal fade" id="addMultipleTagsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="../actions/bulk_add_tags.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter plusieurs Tags</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Tags (séparés par des virgules)</label>
                            <textarea name="tags" class="form-control" rows="4" placeholder="Exemple : Tag1, Tag2, Tag3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Chart.js for Category Distribution -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_column($stats['category_distribution'], 'nom')) ?>,
                datasets: [{
                    label: 'Cours par Catégorie',
                    data: <?= json_encode(array_column($stats['category_distribution'], 'count')) ?>,
                    backgroundColor: ['#6d28d9', '#4c1d95', '#7c3aed', '#9333ea'],
                }]
            }
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>