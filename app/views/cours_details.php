<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header('Location: login.php');
    exit();
}

require_once '../../BackEnd/config/Database.php';
require_once '../../BackEnd/classes/Cours.php';
require_once '../../BackEnd/classes/CoursVideo.php';
require_once '../../BackEnd/classes/CoursDoc.php';

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$courseId) {
    die("Erreur : Aucun cours sélectionné.");
}

try {
    $database = new Database();
    $pdo = $database->getConnection();

    // Fetch basic course details
    $query = "SELECT * FROM cours WHERE id_cours = :id_cours";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_cours', $courseId, PDO::PARAM_INT);
    $stmt->execute();
    $courseData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$courseData) {
        die("Erreur : Le cours demandé n'existe pas.");
    }

    // Instantiate the correct child class based on contenu_type
    $course = null;
    if ($courseData['contenu_type'] === 'Vidéo') {
        $course = new CoursVideo($courseData);
    } elseif ($courseData['contenu_type'] === 'Document') {
        $course = new CoursDoc($courseData);
    } else {
        die("Erreur : Type de contenu non pris en charge.");
    }

    // Use methods from the class
    $categoryName = $course->getCategoryName();
    $teacherName = $course->getTeacherName();
    $tags = $course->getTags();
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du cours - <?= htmlspecialchars($course->getTitre()); ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #1a1a1a;
            line-height: 1.6;
        }

        .course-banner {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            /* Changed to violet gradient */
            color: #fff;
            padding: 60px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .course-banner::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='rgba(255,255,255,0.05)' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.6;
        }

        .course-banner h1 {
            font-size: 2.75rem;
            font-weight: 700;
            margin: 0 0 20px 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .course-banner p {
            font-size: 1.25rem;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.9;
        }

        .course-details {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 300px 1fr 300px;
            gap: 30px;
        }

        .course-info,
        .course-content,
        .course-tags {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .course-info:hover,
        .course-content:hover,
        .course-tags:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .course-info img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .course-info p {
            margin: 12px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .course-info strong {
            color: #4b5563;
            min-width: 120px;
        }

        .course-content {
            min-height: 400px;
        }

        .course-content h2 {
            color: #1a1a1a;
            font-size: 1.5rem;
            margin: 0 0 20px 0;
            padding-bottom: 12px;
            border-bottom: 2px solid #e5e7eb;
        }

        .course-content video {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .course-content .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #8b5cf6;
            /* Changed to match banner gradient */
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }

        .course-content .btn:hover {
            background: #6d28d9;
            /* Changed to match banner gradient */
        }

        .course-tags h2 {
            color: #1a1a1a;
            font-size: 1.5rem;
            margin: 0 0 20px 0;
            padding-bottom: 12px;
            border-bottom: 2px solid #e5e7eb;
        }

        .course-tags ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .course-tags ul li {
            background: #e5e7eb;
            color: #4b5563;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .course-tags ul li:hover {
            background: #8b5cf6;
            /* Changed to match banner gradient */
            color: white;
        }

        @media (max-width: 1024px) {
            .course-details {
                grid-template-columns: 1fr 1fr;
            }

            .course-info {
                grid-column: span 2;
                max-width: none;
            }
        }

        @media (max-width: 768px) {
            .course-banner h1 {
                font-size: 2rem;
            }

            .course-banner p {
                font-size: 1.125rem;
            }

            .course-details {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .course-info {
                grid-column: span 1;
            }
        }

        @media (max-width: 480px) {
            .course-banner {
                padding: 40px 16px;
            }

            .course-details {
                padding: 16px;
                margin: 20px auto;
            }

            .course-info,
            .course-content,
            .course-tags {
                padding: 16px;
            }
        }

        .back-button {
            margin: 20px;
            text-align: center;
        }

        .back-button .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1rem;
            color: #fff;
            background: #8b5cf6;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .back-button .btn:hover {
            background: #6d28d9;
        }
    </style>
</head>

<body>
    <div class="course-banner">
        <h1><?= htmlspecialchars($course->getTitre()); ?></h1>
        <p><?= htmlspecialchars($course->getDescription()); ?></p>
    </div>
    <div class="course-details">
        <div class="course-info">
            <img src="<?= htmlspecialchars($course->getImageUrl()); ?>" alt="<?= htmlspecialchars($course->getTitre()); ?>" class="course-image">
            <p><strong>Type :</strong> <?= htmlspecialchars($course->getContenuType()); ?></p>
            <p><strong>Catégorie :</strong> <?= htmlspecialchars($categoryName); ?></p>
            <p><strong>Créé par :</strong> <?= htmlspecialchars($teacherName); ?></p>
            <p><strong>Date de création :</strong> <?= htmlspecialchars($course->getDateCreation()); ?></p>
        </div>
        <div class="course-content">
            <h2>Contenu du cours</h2>
            <?php if ($course instanceof CoursVideo): ?>
                <iframe width="560" height="315" src="<?= htmlspecialchars($course->getContenu()); ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            <?php elseif ($course instanceof CoursDoc): ?>
                <a href="<?= htmlspecialchars($course->getContenu()); ?>" class="btn" target="_blank">Afficher le document</a>

            <?php endif; ?>
        </div>
        <div class="course-tags">
            <h2>Tags associés</h2>
            <?php if (!empty($tags)): ?>
                <ul>
                    <?php foreach ($tags as $tag): ?>
                        <li><?= htmlspecialchars($tag); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucun tag associé à ce cours.</p>
            <?php endif; ?>
        </div>
    </div>
    <!-- Add Back Button -->
    <div class="back-button">
        <a href="cours.php" class="btn">Retour aux cours</a>
    </div>
</body>

</html>