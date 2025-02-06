<?php
require_once __DIR__ . '../../config/Database.php';

class Categorie
{
    protected $pdo;
    protected $id_categorie;
    protected $nom;
    protected $image_url;
    protected $total_courses;


    public function __construct($id_categorie, $nom, $image_url)
    {
        $database = new Database();
        $this->pdo = $database->getConnection();

        $this->id_categorie = $id_categorie;
        $this->nom = $nom;
        $this->image_url = $image_url;
    }

    public function setIdCategorie($id_categorie)
    {
        $this->id_categorie = $id_categorie;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function setImageUrl($image_url)
    {
        $this->image_url = $image_url;
    }

    public function getIdCategorie()
    {
        return $this->id_categorie;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getImageUrl()
    {
        return $this->image_url;
    }
    
    public function setTotalCourses($total)
{
    $this->total_courses = $total;
}

public function getTotalCourses()
{
    return $this->total_courses;
}

// public function getAllCategories() {
//     $query = "SELECT * FROM categorie";
//     return $this->pdo->fetchAll($query);
// }

    public static function categorieExiste($nom)
    {
        $database = new Database();
        $pdo = $database->getConnection();

        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM categorie WHERE nom = :nom");
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la vérification de l'existence de la catégorie : " . $e->getMessage());
        }
    }

    public function ajouterCategorie()
    {
        try {
            if (self::categorieExiste($this->nom)) {
                return "La catégorie existe déjà.";
            }

            $stmt = $this->pdo->prepare("INSERT INTO categorie (nom, image_url) VALUES (:nom, :image_url)");
            $this->id_categorie = $this->pdo->lastInsertId();
            $stmt->bindParam(':nom', $this->nom, PDO::PARAM_STR);
            $stmt->bindParam(':image_url', $this->image_url, PDO::PARAM_STR);
            $stmt->execute();
            $this->id_categorie = $this->pdo->lastInsertId();
            return "Catégorie ajoutée avec succès.";
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'ajout de la catégorie : " . $e->getMessage());
        }
    }

    public static function afficherCategorie()

    {
        $database = new Database();
        $pdo = $database->getConnection();
        try {
            $query = "
            SELECT c.*, 
                   (SELECT COUNT(*) 
                    FROM cours co 
                    WHERE co.id_categorie = c.id_categorie) as total_courses
            FROM categorie c
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $categories = [];
        foreach ($resultats as $row) {
            $categorie = new Categorie($row['id_categorie'], $row['nom'], $row['image_url']);
            $categorie->total_courses = $row['total_courses']; // Propriété dynamique
            $categories[] = $categorie;
        }
            return $categories;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des catégories : " . $e->getMessage());
        }
    }

    public function modifierCategorie()
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE categorie SET nom = :nom, image_url = :image_url Where id_categorie = :id_categorie");
            $stmt->bindParam(':nom', $this->nom, PDO::PARAM_STR);
            $stmt->bindParam(':image_url', $this->image_url, PDO::PARAM_STR);
            $stmt->bindParam(':id_categorie', $this->id_categorie, PDO::PARAM_INT);
            $stmt->execute();
            return "Catégorie modifiée avec succès.";
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la modification de la catégorie : " . $e->getMessage());
        }
    }

    public function supprimerCategorie()
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM categorie WHERE id_categorie = :id_categorie");
            $stmt->bindParam(':id_categorie', $this->id_categorie, PDO::PARAM_INT);
            $stmt->execute();
            return "Catégorie supprimée avec succès.";
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression de la catégorie : " . $e->getMessage());
        }
    }
}


?>