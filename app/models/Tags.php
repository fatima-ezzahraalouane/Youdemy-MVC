<?php
class Tag
{
    protected $pdo;
    private $table = 'tag';

    protected $id_tag;
    protected $nom;

    public function __construct($db)
    {
        $this->pdo = $db;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function createOrGetTag($name)
    {
        try {
            $query = "SELECT id_tag FROM tag WHERE nom = :name";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->execute();
            $tag = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($tag) {
                return $tag['id_tag'];
            }

            $query = "INSERT INTO tag (nom) VALUES (:name)";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->execute();

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur lors de la création du tag : " . $e->getMessage());
            return null;
        }
    }

    public function createMultipleTags(array $tag)
    {
        $tagIds = [];
        foreach ($tag as $tagName) {
            if (!empty($tagName)) {
                $tagIds[] = $this->createOrGetTag($tagName);
            }
        }
        return !empty($tagIds);
    }


    public function linkTagToCours($coursId, $tagId)
    {
        try {
            $query = "INSERT INTO cours_tag (id_cours, id_tag) VALUES (:id_cours, :id_tag)";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id_cours', $coursId);
            $stmt->bindParam(':id_tag', $tagId);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Erreur lors de la liaison du tag à cours : " . $e->getMessage());
            return false;
        }
    }

    public function getAllTags()
    {
        try {
            $query = "SELECT * FROM tag";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des tags : " . $e->getMessage());
            return [];
        }
    }

    public function updateTag($id, $newName)
    {
        try {
            $query = "UPDATE tag SET nom = :newName WHERE id_tag = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':newName', $newName);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du tag : " . $e->getMessage());
            return false;
        }
    }

    public function deleteTag($id)
    {
        try {
            $query = "DELETE FROM tag WHERE id_tag = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du tag : " . $e->getMessage());
            return false;
        }
    }


    public function addTag($nomTag)
    {
        $query = "INSERT INTO tag (nom) VALUES (:nom)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':nom', $nomTag, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function getTagsByCourseId($courseId)
    {
        try {
            $query = "SELECT t.nom FROM tag t
                  JOIN cours_tag ct ON t.id_tag = ct.id_tag
                  WHERE ct.id_cours = :id_cours";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id_cours', $courseId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des tags : " . $e->getMessage());
        }
    }
}
