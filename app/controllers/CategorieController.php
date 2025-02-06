<?php 
require_once __DIR__ . '../config/Database.php';
require_once __DIR__ . '../models/Categorie.php';

class CategorieController {
    private $categorieModel;

    // public function __construct() {
    //     $this->categorieModel = new Categorie();
    // }

    // public function index() {
    //     $categories = $this->categorieModel->getAllCategories();
    // }

    public function add() 
    {

        session_start();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $image_url = trim($_POST['image_url'] ?? '');
        
            if (!empty($nom)) {
                $db = new Database();
                $pdo = $db->getConnection();
        
                $stmt = $pdo->prepare("SELECT * FROM usersite WHERE id_usersite = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $adminData = $stmt->fetch(PDO::FETCH_ASSOC);
        
                $admin = new Admin($pdo, $adminData['username'], $adminData['email'], $adminData['password']);
                $message = $admin->ajouterCategorie($nom, $image_url);
                header("Location: index.php?controller=admin&action=dashboard&message=" . urlencode($message));
            } else {
                header("Location: index.php?controller=admin&action=dashboard&message" . urlencode("Le nom de la catégorie est obligatoire."));
            }
            exit;
        }
    }

    public function edit()
    {

        session_start();

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_categorie = $_POST['id_categorie'] ?? '';
            $nom = trim($_POST['nom'] ?? '');
            $image_url = trim($_POST['image_url'] ?? '');
        
            if (!empty($id_categorie) && !empty($nom)) {
                $db = new Database();
                $pdo = $db->getConnection();
        
                $stmt = $pdo->prepare("SELECT * FROM usersite WHERE id_usersite = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $adminData = $stmt->fetch(PDO::FETCH_ASSOC);
        
                $admin = new Admin($pdo, $adminData['username'], $adminData['email'], $adminData['password']);
                $message = $admin->modifierCategorie($id_categorie, $nom, $image_url);
                header("Location: index.php?controller=admin&action=dashboard&message=" . urlencode($message));
            } else {
                header("Location: index.php?controller=admin&action=dashboard&message=" . urlencode("Les informations de modification sont incomplètes."));
            }
            exit;
        }
    }

    public function delete()
    {

        session_start();

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_categorie = $_POST['id_categorie'] ?? '';
        
            if (!empty($id_categorie)) {
                $db = new Database();
                $pdo = $db->getConnection();
        
                $stmt = $pdo->prepare("SELECT * FROM usersite WHERE id_usersite = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $adminData = $stmt->fetch(PDO::FETCH_ASSOC);
        
                $admin = new Admin($pdo, $adminData['username'], $adminData['email'], $adminData['password']);
                $message = $admin->supprimerCategorie($id_categorie);
                header("Location: index.php?controller=admin&action=dashboard&message=" . urlencode($message));
            } else {
                header("Location: index.php?controller=admin&action=dashboard&message=" . urlencode("L'identifiant de la catégorie est obligatoire."));
            }
            exit;
        }
    }
}
?>