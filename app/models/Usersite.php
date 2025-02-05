<?php
require_once '../../BackEnd/config/Database.php';

class Usersite
{
    protected $pdo;
    protected $id_usersite;
    protected $username;
    protected $email;
    protected $password;
    protected $id_role;
    protected $statut;
    protected $is_approved;
    protected $created_at;

    public function __construct($username, $email, $password, $id_role, $statut = 'actif', $is_approved = false)
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
        $this->username = $username;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_BCRYPT); // Hashage sécurisé du mot de passe
        $this->id_role = $id_role;
        $this->statut = $statut;
        if ($is_approved) {
            $this->is_approved = 'TRUE'; // Si approuvé
        } else {
            $this->is_approved = 'FALSE'; // Si non approuvé
        }
        $this->created_at = date('Y-m-d H:i:s');
    }

    public function saveToDatabase()
    {
        $stmt = $this->pdo->prepare("INSERT INTO usersite (username, email, password, id_role, statut, is_approved) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $this->username,
            $this->email,
            $this->password,
            $this->id_role,
            $this->statut,
            $this->is_approved
        ]);
        $this->id_usersite = $this->pdo->lastInsertId();
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getIdRole()
    {
        return $this->id_role;
    }
}
