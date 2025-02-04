<?php
require_once '../../BackEnd/config/Database.php';

abstract class Cours
{
    protected $pdo;
    protected $id_cours;
    protected $titre;
    protected $description;
    protected $image_url;
    protected $contenu;
    protected $contenu_type;
    protected $date_creation;
    protected $statut;
    protected $id_usersite;
    protected $id_categorie;

    public function __construct($data)
    {
        // var_dump($data);

        $database = new Database();
        $this->pdo = $database->getConnection();

        $this->id_cours = $data['id_cours'] ?? null;
        $this->titre = $data['titre'] ?? 'Titre non défini';
        $this->description = $data['description'] ?? 'Description non définie';
        $this->image_url = $data['image_url'] ?? 'default.jpg';
        $this->contenu = $data['contenu'] ?? 'Contenu non défini';
        $this->contenu_type = $data['contenu_type'] ?? null;
        $this->date_creation = $data['date_creation'] ?? date('Y-m-d H:i:s');
        $this->statut = $data['statut'] ?? 'en_attente';
        $this->id_usersite = $data['id_usersite'] ?? null;
        $this->id_categorie = $data['id_categorie'] ?? null;
    }

    public function setIdCours($id_cours)
    {
        $this->id_cours = $id_cours;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setImageUrl($image_url)
    {
        $this->image_url = $image_url;
    }

    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    }

    public function setContenuType($contenu_type)
    {
        $this->contenu_type = $contenu_type;
    }

    public function setDateCreation($date_creation)
    {
        $this->date_creation = $date_creation;
    }

    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    public function getIdCours()
    {
        return $this->id_cours;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getImageUrl()
    {
        return $this->image_url;
    }

    public function getContenu()
    {
        return $this->contenu;
    }

    public function getContenuType()
    {
        return $this->contenu_type;
    }

    public function getDateCreation()
    {
        return $this->date_creation;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setIdCategorie($id_categorie)
    {
        $this->id_categorie = $id_categorie;
    }

    public function getIdCategorie()
    {
        return $this->id_categorie;
    }

    public function setIdUsersite($id_usersite)
    {
        $this->id_usersite = $id_usersite;
    }

    public function getIdUsersite()
    {
        return $this->id_usersite;
    }

    // public function __get($ok) {
    //     return $this->ok;
    // }

    // public function __set($ok, $value)
    // {
    //     $this->ok = $value;
    // }

    public static function coursExiste($titre)
    {
        $database = new Database();
        $pdo = $database->getConnection();

        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM cours WHERE titre = :titre");
            $stmt->bindParam(':titre', $titre);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la vérification de l'existence de le cours : " . $e->getMessage());
        }
    }

    abstract public function ajouterCours();

    abstract public function afficherCours($page = 1, $limit = 9);

    abstract public function rechercherCoursParTitre($titre, $page = 1, $limit = 9);
    abstract public function getTotalCoursRecherche($titre);

    // public function ajouterCours()
    // {
    //     try {
    //         if (self::coursExiste($this->titre)) {
    //             throw new Exception("Le cours existe déjà.");
    //         }

    //         $stmt = $this->pdo->prepare(
    //             "INSERT INTO cours (titre, description, image_url, contenu, contenu_type, statut, id_usersite, id_categorie)
    //             VALUES (:titre, :description, :image_url, :contenu, :contenu_type, :statut, :id_usersite, :id_categorie)"
    //         );

    //         $stmt->bindParam(':titre', $this->titre);
    //         $stmt->bindParam(':description', $this->description);
    //         $stmt->bindParam(':image_url', $this->image_url);
    //         $stmt->bindParam(':contenu', $this->contenu);
    //         $stmt->bindParam(':contenu_type', $this->contenu_type);
    //         $stmt->bindParam(':statut', $this->statut);
    //         $stmt->bindParam(':id_usersite', $this->id_usersite, PDO::PARAM_INT);
    //         $stmt->bindParam(':id_categorie', $this->id_categorie, PDO::PARAM_INT);

    //         $stmt->execute();
    //         $this->id_cours = $this->pdo->lastInsertId();

    //         return "Cours ajouté avec succès.";
    //     } catch (PDOException $e) {
    //         throw new Exception("Erreur lors de l'ajout du cours : " . $e->getMessage());
    //     }
    // }

    // public function afficherCours($contenuType = null, $page = 1, $limit = 9)
    // {
    //     try {
    //         $offset = ($page - 1) * $limit;

    //         $query = "SELECT * FROM cours";

    //         if ($contenuType) {
    //             $query .= " WHERE contenu_type = :contenu_type";
    //         }

    //         $query .= " LIMIT :limit OFFSET :offset";

    //         $stmt = $this->pdo->prepare($query);

    //         if ($contenuType) {
    //             $stmt->bindParam(':contenu_type', $contenuType, PDO::PARAM_STR);
    //         }

    //         $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    //         $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

    //         $stmt->execute();
    //         $coursResultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //         foreach ($coursResultats as &$cours) {
    //             $stmtUser = $this->pdo->prepare("SELECT username FROM usersite WHERE id_usersite = :id_usersite");
    //             $stmtUser->bindParam(':id_usersite', $cours['id_usersite'], PDO::PARAM_INT);
    //             $stmtUser->execute();
    //             $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    //             $stmtCategorie = $this->pdo->prepare("SELECT nom FROM categorie WHERE id_categorie = :id_categorie");
    //             $stmtCategorie->bindParam(':id_categorie', $cours['id_categorie'], PDO::PARAM_INT);
    //             $stmtCategorie->execute();
    //             $categorie = $stmtCategorie->fetch(PDO::FETCH_ASSOC);

    //             $cours['enseignant'] = $user['username'] ?? 'Inconnu';
    //             $cours['categorie'] = $categorie['nom'] ?? 'Non défini';
    //         }

    //         return $coursResultats;
    //     } catch (PDOException $e) {
    //         throw new Exception("Erreur lors de la récupération des cours : " . $e->getMessage());
    //     }
    // }


    public function modifierCours()
    {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE cours SET titre = :titre, description = :description, image_url = :image_url, contenu = :contenu, contenu_type = :contenu_type, statut = :statut
                WHERE id_cours = :id_cours"
            );

            $stmt->bindParam(':titre', $this->titre, PDO::PARAM_STR);
            $stmt->bindParam(':description', $this->description, PDO::PARAM_STR);
            $stmt->bindParam(':image_url', $this->image_url, PDO::PARAM_STR);
            $stmt->bindParam(':contenu', $this->contenu, PDO::PARAM_STR);
            $stmt->bindParam(':contenu_type', $this->contenu_type, PDO::PARAM_STR);
            $stmt->bindParam(':statut', $this->statut, PDO::PARAM_STR);
            $stmt->bindParam(':id_cours', $this->id_cours, PDO::PARAM_INT);

            $stmt->execute();
            return "Cours modifié avec succès.";
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la modification du cours : " . $e->getMessage());
        }
    }

    public function supprimerCours()
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM cours WHERE id_cours = :id_cours");
            $stmt->bindParam(':id_cours', $this->id_cours, PDO::PARAM_INT);
            $stmt->execute();
            return "Cours supprimé avec succès.";
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression du cours : " . $e->getMessage());
        }
    }

    public function getCoursByCategorie($id_categorie)
{
    try {
        $query = "SELECT * FROM cours WHERE id_categorie = :id_categorie AND statut = 'publie'";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id_categorie', $id_categorie, PDO::PARAM_INT);
        $stmt->execute();

        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultats as &$cours) {
            $stmtUser = $this->pdo->prepare("SELECT username FROM usersite WHERE id_usersite = :id_usersite");
            $stmtUser->bindParam(':id_usersite', $cours['id_usersite'], PDO::PARAM_INT);
            $stmtUser->execute();
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

            $stmtCategorie = $this->pdo->prepare("SELECT nom FROM categorie WHERE id_categorie = :id_categorie");
            $stmtCategorie->bindParam(':id_categorie', $cours['id_categorie'], PDO::PARAM_INT);
            $stmtCategorie->execute();
            $categorie = $stmtCategorie->fetch(PDO::FETCH_ASSOC);

            $cours['enseignant'] = $user['username'] ?? 'Inconnu';
            $cours['categorie'] = $categorie['nom'] ?? 'Non défini';
        }

        return $resultats;
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la récupération des cours par catégorie : " . $e->getMessage());
    }
}


    // public function getCoursByCategorie($id_categorie)
    // {
    //     try {
    //         $query = "SELECT * FROM cours WHERE id_categorie = :id_categorie";
    //         $stmt = $this->pdo->prepare($query);
    //         $stmt->bindParam(':id_categorie', $id_categorie, PDO::PARAM_INT);
    //         $stmt->execute();

    //         $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //         $cours = [];
    //         foreach ($resultats as $row) {
    //             $cours[] = new static($row);
    //         }
    //         return $cours;
    //     } catch (PDOException $e) {
    //         throw new Exception("Erreur lors de la récupération des cours par catégorie : ", $e->getMessage());
    //     }
    // }

    public function getCoursByEnseignant($id_usersite)
    {
        try {
            $query = "SELECT * FROM cours WHERE id_usersite = :id_usersite AND statut = 'publie'";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id_usersite', $id_usersite, PDO::PARAM_INT);
            $stmt->execute();

            $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $cours = [];
            foreach ($resultats as $row) {
                $cours[] = new static($row);
            }
            return $cours;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des cours par enseignant : " . $e->getMessage());
        }
    }

    public function getTotalCours($contenuType = null)
    {
        try {
            $query = "SELECT COUNT(*) as total FROM cours";
            if ($contenuType) {
                $query .= " WHERE contenu_type = :contenu_type";
            }

            $stmt = $this->pdo->prepare($query);

            if ($contenuType) {
                $stmt->bindParam(':contenu_type', $contenuType, PDO::PARAM_STR);
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération du total des cours : " . $e->getMessage());
        }
    }



    public function getCategoryName()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT nom FROM categorie WHERE id_categorie = :id_categorie");
            $stmt->bindParam(':id_categorie', $this->id_categorie, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() ?? 'Non définie';
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération de la catégorie : " . $e->getMessage());
        }
    }

    public function getTeacherName()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT username FROM usersite WHERE id_usersite = :id_usersite");
            $stmt->bindParam(':id_usersite', $this->id_usersite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() ?? 'Inconnu';
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération de l'enseignant : " . $e->getMessage());
        }
    }

    public function getTags()
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT t.nom 
                FROM tag t
                JOIN cours_tag ct ON t.id_tag = ct.id_tag
                WHERE ct.id_cours = :id_cours
            ");
            $stmt->bindParam(':id_cours', $this->id_cours, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN) ?? [];
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des tags : " . $e->getMessage());
        }
    }

//     public function rechercherCoursParTitre($titre, $page = 1, $limit = 9)
// {
//     try {
//         $offset = ($page - 1) * $limit;

//         $query = "SELECT * FROM cours WHERE titre LIKE :titre LIMIT :limit OFFSET :offset";

//         $stmt = $this->pdo->prepare($query);
//         $searchTitre = "%$titre%";
//         $stmt->bindParam(':titre', $searchTitre, PDO::PARAM_STR);
//         $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
//         $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

//         $stmt->execute();
//         $coursResultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

//         foreach ($coursResultats as &$cours) {
//             $stmtUser = $this->pdo->prepare("SELECT username FROM usersite WHERE id_usersite = :id_usersite");
//             $stmtUser->bindParam(':id_usersite', $cours['id_usersite'], PDO::PARAM_INT);
//             $stmtUser->execute();
//             $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

//             $stmtCategorie = $this->pdo->prepare("SELECT nom FROM categorie WHERE id_categorie = :id_categorie");
//             $stmtCategorie->bindParam(':id_categorie', $cours['id_categorie'], PDO::PARAM_INT);
//             $stmtCategorie->execute();
//             $categorie = $stmtCategorie->fetch(PDO::FETCH_ASSOC);

//             $cours['enseignant'] = $user['username'] ?? 'Inconnu';
//             $cours['categorie'] = $categorie['nom'] ?? 'Non défini';
//         }

//         return $coursResultats;

//     } catch (PDOException $e) {
//         throw new Exception("Erreur lors de la recherche des cours : " . $e->getMessage());
//     }
// }

// public function getTotalCoursRecherche($titre)
// {
//     try {
//         $query = "SELECT COUNT(*) as total FROM cours WHERE titre LIKE :titre";

//         $stmt = $this->pdo->prepare($query);
//         $searchTitre = "%$titre%";
//         $stmt->bindParam(':titre', $searchTitre, PDO::PARAM_STR);

//         $stmt->execute();
//         $result = $stmt->fetch(PDO::FETCH_ASSOC);

//         return $result['total'] ?? 0;

//     } catch (PDOException $e) {
//         throw new Exception("Erreur lors de la récupération du total des résultats de recherche : " . $e->getMessage());
//     }
// }






}
