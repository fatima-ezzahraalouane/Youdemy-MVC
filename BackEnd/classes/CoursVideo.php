<?php

class CoursVideo extends Cours
{
    public function ajouterCours()
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO cours (titre, description, image_url, contenu, contenu_type, statut, id_usersite, id_categorie)
                VALUES (:titre, :description, :image_url, :contenu, 'Vidéo', :statut, :id_usersite, :id_categorie)"
            );

            $stmt->bindParam(':titre', $this->titre);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':image_url', $this->image_url);
            $stmt->bindParam(':contenu', $this->contenu);
            $stmt->bindParam(':statut', $this->statut);
            $stmt->bindParam(':id_usersite', $this->id_usersite, PDO::PARAM_INT);
            $stmt->bindParam(':id_categorie', $this->id_categorie, PDO::PARAM_INT);

            $stmt->execute();
            $this->id_cours = $this->pdo->lastInsertId();

            return "Cours vidéo ajouté avec succès.";
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'ajout du cours vidéo : " . $e->getMessage());
        }
    }

    public function afficherCours($page = 1, $limit = 9)
    {
        try {
            $offset = ($page - 1) * $limit;

            $stmt = $this->pdo->prepare(
                "SELECT * FROM cours WHERE contenu_type = 'Vidéo' AND statut = 'publie' LIMIT :limit OFFSET :offset"
            );
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();

            $coursResultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($coursResultats as &$cours) {
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

            return $coursResultats;
            // return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'affichage des cours vidéo : " . $e->getMessage());
        }
    }



    public function rechercherCoursParTitre($titre, $page = 1, $limit = 9)
    {
        try {
            $offset = ($page - 1) * $limit;
            $query = "SELECT * FROM cours WHERE contenu_type = 'Vidéo' AND titre ILIKE :titre AND statut = 'publie' LIMIT :limit OFFSET :offset";

            $stmt = $this->pdo->prepare($query);
            $searchTitre = "%$titre%";
            $stmt->bindParam(':titre', $searchTitre, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            $coursResultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($coursResultats as &$cours) {
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

            return $coursResultats;
            // return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la recherche des cours vidéo : " . $e->getMessage());
        }
    }

    public function getTotalCoursRecherche($titre)
    {
        try {
            $query = "SELECT COUNT(*) as total FROM cours WHERE contenu_type = 'Vidéo' AND titre ILIKE :titre AND statut = 'publie'";

            $stmt = $this->pdo->prepare($query);
            $searchTitre = "%$titre%";
            $stmt->bindParam(':titre', $searchTitre, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération du total des cours vidéo : " . $e->getMessage());
        }
    }
}
