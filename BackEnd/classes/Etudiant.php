<?php

require_once 'Usersite.php';

class Etudiant extends Usersite {
    public function __construct($username, $email, $password) {
        parent::__construct( $username, $email, $password, 2, 'actif', true); // id_role = 2 pour Étudiant
    }
}

?>
