<?php 
class Database {
    private $host = 'localhost';
    private $db_name = 'youdemyMvc';
    private $username = 'postgres';
    private $password = '1234567890';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "pgsql:host={$this->host};dbname={$this->db_name};",
                $this->username,
                password: $this->password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connexion à la base de données réussie !";
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
        return $this->conn;
    }

    public function closeConnection() {
        $this->conn = null;
    }
}



// $j = (new Database())->getConnection();
// echo "dd";
?>