<?php
require_once 'Usersite.php';
require_once 'Categorie.php';
require_once 'Tags.php';

class Admin extends Usersite {
    private $categorie;
    private $tag;

    public function __construct($pdo, $username, $email, $password) {
        parent::__construct($pdo, $username, $email, $password, 1); // id_role = 1 for Admin

        $this->categorie = new Categorie(null, '', '', $pdo);
        $this->tag = new Tag($pdo);
    }

    // Validate teacher accounts
    public function approveTeacher($teacher_id) {
        $stmt = $this->pdo->prepare("UPDATE usersite SET is_approved = 1, statut = 'actif' WHERE id_usersite = ?");
        $stmt->execute([$teacher_id]);
    
        return "Compte enseignant approuvé avec succès.";
    }
    

    // Manage users (activate, suspend, delete)
    public function manageUser($user_id, $action) {
        switch ($action) {
            case 'activate':
                $stmt = $this->pdo->prepare("UPDATE usersite SET statut = 'actif' WHERE id_usersite = ?");
                $message = "Utilisateur activé avec succès.";
                break;
            case 'suspend':
                $stmt = $this->pdo->prepare("UPDATE usersite SET statut = 'suspendu' WHERE id_usersite = ?");
                $message = "Utilisateur suspendu avec succès.";
                break;
            case 'delete':
                $stmt = $this->pdo->prepare("DELETE FROM usersite WHERE id_usersite = ?");
                $message = "Utilisateur supprimé avec succès.";
                break;
            default:
                throw new Exception("Action invalide.");
        }
        $stmt->execute([$user_id]);
        return $message;
    }
    

    // Approve or reject a course
    public function manageCourse($course_id, $action) {
        $stmt = $this->pdo->prepare("UPDATE cours SET statut = ? WHERE id_cours = ?");
        $status = ($action === 'approve') ? 'publie' : 'rejete';
        $stmt->execute([$status, $course_id]);
    
        return "Cours " . ($action === 'approve' ? "approuvé" : "rejeté") . " avec succès.";
    }
    

    // Add a new category
    // public function addCategory($nom, $image_url) {
    //     $stmt = $this->pdo->prepare("INSERT INTO categorie (nom, image_url) VALUES (?, ?)");
    //     $stmt->execute([$nom, $image_url]);
    //     return "Category added successfully.";
    // }

    public function ajouterCategorie($nom, $image_url)
    {
        if (Categorie::categorieExiste($nom)) {
            return "La catégorie existe déjà.";
        }

        $this->categorie->setNom($nom);
        $this->categorie->setImageUrl($image_url);

        return $this->categorie->ajouterCategorie();
    }

    // Modify a category
    // public function updateCategory($id_categorie, $nom, $image_url) {
    //     $stmt = $this->pdo->prepare("UPDATE categorie SET nom = ?, image_url = ? WHERE id_categorie = ?");
    //     $stmt->execute([$nom, $image_url, $id_categorie]);
    //     return "Category updated successfully.";
    // }

    public function afficherCategories()
    {
        return Categorie::afficherCategorie();
    }

    public function modifierCategorie($id_categorie, $nouveauNom, $nouvelleImage)
    {
        $this->categorie->setIdCategorie($id_categorie);
        $this->categorie->setNom($nouveauNom);
        $this->categorie->setImageUrl($nouvelleImage);

        return $this->categorie->modifierCategorie();
    }

    // Delete a category
    // public function deleteCategory($id_categorie) {
    //     $stmt = $this->conn->prepare("DELETE FROM categorie WHERE id_categorie = ?");
    //     $stmt->execute([$id_categorie]);
    //     return "Category deleted successfully.";
    // }

    public function supprimerCategorie($id_categorie)
    {
        $this->categorie->setIdCategorie($id_categorie);
        return $this->categorie->supprimerCategorie();
    }

    // Add a new tag
    // public function addTag($nom) {
    //     $stmt = $this->conn->prepare("INSERT INTO tag (nom) VALUES (?)");
    //     $stmt->execute([$nom]);
    //     return "Tag added successfully.";
    // }

    public function ajouterTag($nomTag)
    {
        return $this->tag->createOrGetTag($nomTag);
    }

    // Bulk insert tags
    // public function bulkInsertTags($tags) {
    //     $tags = explode(',', $tags);
    //     foreach ($tags as $tag) {
    //         $tag = trim($tag);
    //         if (!empty($tag)) {
    //             $this->addTag($tag);
    //         }
    //     }
    //     return "Tags bulk inserted successfully.";
    // }

    public function ajouterTagsMultiples(array $tags)
    {
        return $this->tag->createMultipleTags($tags);
    }

    public function afficherTags()
    {
        return $this->tag->getAllTags();
    }

    public function modifierTag($id_tag, $nouveauNom)
    {
        return $this->tag->updateTag($id_tag, $nouveauNom);
    }

    public function supprimerTag($id_tag)
    {
        return $this->tag->deleteTag($id_tag);
    }

    public function lierTagAuCours($coursId, $tagId)
    {
        return $this->tag->linkTagToCours($coursId, $tagId);
    }

    // Get global statistics
    public function getStatistics() {
        $stats = [];

        // Total courses
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total_courses FROM cours");
        $stmt->execute();
        $stats['total_courses'] = $stmt->fetchColumn();

         // Total students (id_role = 2)
    $stmt = $this->pdo->prepare("SELECT COUNT(*) as total_students FROM usersite WHERE id_role = 2");
    $stmt->execute();
    $stats['total_students'] = $stmt->fetchColumn();

    // Total teachers (id_role = 3)
    $stmt = $this->pdo->prepare("SELECT COUNT(*) as total_teachers FROM usersite WHERE id_role = 3");
    $stmt->execute();
    $stats['total_teachers'] = $stmt->fetchColumn();

    // Total categories
    $stmt = $this->pdo->prepare("SELECT COUNT(*) as total_categories FROM categorie");
    $stmt->execute();
    $stats['total_categories'] = $stmt->fetchColumn();

        // Distribution by category
        $stmt = $this->pdo->prepare("SELECT c.nom, COUNT(*) as count FROM cours co JOIN categorie c ON co.id_categorie = c.id_categorie GROUP BY c.nom");
        $stmt->execute();
        $stats['category_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Course with the most students
        $stmt = $this->pdo->prepare("SELECT c.titre, COUNT(i.id_inscription) as students FROM cours c JOIN inscription i ON c.id_cours = i.id_cours GROUP BY c.id_cours ORDER BY students DESC LIMIT 1");
        $stmt->execute();
        $stats['top_course'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Top 3 teachers
        $stmt = $this->pdo->prepare("SELECT u.username, COUNT(c.id_cours) as courses FROM usersite u JOIN cours c ON u.id_usersite = c.id_usersite WHERE u.id_role = 3 GROUP BY u.id_usersite ORDER BY courses DESC LIMIT 3");
        $stmt->execute();
        $stats['top_teachers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $stats;
    }
}
?>