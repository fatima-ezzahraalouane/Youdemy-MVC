<?php

require_once 'Usersite.php';

class Enseignant extends Usersite
{
    public function __construct($username, $email, $password)
    {
        parent::__construct( $username, $email, $password, 3, 'inactif', false); // id_role = 3 pour Enseignant
    }

    public function approveAccount()
    {
        $this->is_approved = true;
        $this->statut = 'actif';

        $stmt = $this->pdo->prepare("UPDATE usersite SET is_approved = ?, statut = ? WHERE id_usersite = ?");
        $stmt->execute([1, 'actif', $this->id_usersite]);
    }
}
