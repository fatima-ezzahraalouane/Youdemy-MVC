<?php

require_once '../../BackEnd/config/Database.php';
require_once '../../BackEnd/classes/Admin.php';
require_once '../../BackEnd/classes/Etudiant.php';
require_once '../../BackEnd/classes/Enseignant.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    try {
        $database = new Database();
        $conn = $database->getConnection();

        // Vérifiez quel rôle a été choisi
        if ($role === 'Étudiant') {
            $user = new Etudiant($username, $email, $password); // Instanciez la classe Étudiant
        } elseif ($role === 'Enseignant') {
            $user = new Enseignant($username, $email, $password); // Instanciez la classe Enseignant
        } else {
            throw new Exception("Rôle non valide.");
        }
        // Sauvegardez l'utilisateur dans la base de données
        $user->saveToDatabase();

        header("Location: login.php");
        exit;
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <!-- META ============================================= -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <meta name="robots" content="" />

    <!-- DESCRIPTION -->
    <meta name="description" content="EduChamp : Education HTML Template" />

    <!-- OG -->
    <meta property="og:title" content="EduChamp : Education HTML Template" />
    <meta property="og:description" content="EduChamp : Education HTML Template" />
    <meta property="og:image" content="" />
    <meta name="format-detection" content="telephone=no">

    <!-- FAVICONS ICON ============================================= -->
    <link rel="icon" href="../assets/images/youdemy2.png" type="image/x-icon" />
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/youdemy2.png" />

    <!-- PAGE TITLE HERE ============================================= -->
    <title>YOUDEMY FATIMA-EZZAHRA</title>

    <!-- MOBILE SPECIFIC ============================================= -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--[if lt IE 9]>
	<script src="../assets/js/html5shiv.min.js"></script>
	<script src="../assets/js/respond.min.js"></script>
	<![endif]-->

    <!-- All PLUGINS CSS ============================================= -->
    <link rel="stylesheet" type="text/css" href="../assets/css/assets.css">

    <!-- TYPOGRAPHY ============================================= -->
    <link rel="stylesheet" type="text/css" href="../assets/css/typography.css">

    <!-- SHORTCODES ============================================= -->
    <link rel="stylesheet" type="text/css" href="../assets/css/shortcodes/shortcodes.css">

    <!-- STYLESHEETS ============================================= -->
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link class="skin" rel="stylesheet" type="text/css" href="../assets/css/color/color-1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .role-container {
            display: flex;
            gap: 20px;
            /* margin: 15px 0; */
            justify-content: center;
        }

        .role-option {
            flex: 1;
            position: relative;
            max-width: 200px;
        }

        .role-input {
            display: none;
        }

        .role-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border: 2px solid #e7e7e7;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .role-icon {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: #666;
        }

        .role-text {
            font-weight: 500;
            color: #444;
        }

        .role-input:checked+.role-label {
            border-color: #4c1864;
            background-color: #f8f2ff;
        }

        .role-input:checked+.role-label .role-icon,
        .role-input:checked+.role-label .role-text {
            color: #4c1864;
        }

        .role-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .validation-message {
            font-size: 13px;
            margin-top: 5px;
            font-weight: 500;
        }
    </style>
</head>

<body id="bg">
    <div class="page-wraper">
        <div id="loading-icon-bx"></div>
        <div class="account-form">
            <div class="account-head" style="background-image:url(../assets/images/background/bg2.jpg);">
                <a href="index.php"><img src="../assets/images/youdemy.png" class="w-50" alt="icon"></a>
            </div>
            <div class="account-form-inner">
                <div class="account-container">
                    <div class="heading-bx left">
                        <h2 class="title-head">S'inscrire <span>Maintenant</span></h2>
                        <p>Connectez-vous à votre compte <a href="login.php">Cliquez ici</a></p>
                    </div>
                    <form method="POST" action="#" class="contact-bx">
                        <div class="row placeani">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <label>Votre Nom</label>
                                        <input name="username" type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <label>Votre Adresse E-mail</label>
                                        <input name="email" type="email" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <label>Votre Mot de Passe</label>
                                        <input name="password" type="password" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="role">Sélectionnez votre Rôle :</label>
                                    <div class="role-container">
                                        <div class="role-option">
                                            <input type="radio" name="role" id="student" value="Étudiant" class="role-input" checked>
                                            <label for="student" class="role-label">
                                                <i class="fa-solid fa-user-graduate role-icon"></i>
                                                <span class="role-text">Étudiant</span>
                                            </label>
                                        </div>
                                        <div class="role-option">
                                            <input type="radio" name="role" id="teacher" value="Enseignant" class="role-input">
                                            <label for="teacher" class="role-label">
                                                <i class="fa-solid fa-chalkboard-teacher role-icon"></i>
                                                <span class="role-text">Enseignant</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 m-b30">
                                <button name="submit" type="submit" value="Submit" class="btn button-md">S'inscrire</button>
                            </div>
                            <div class="col-lg-12">
                                <h6>Inscrivez-vous avec les réseaux sociaux</h6>
                                <div class="d-flex">
                                    <a class="btn flex-fill m-r5 facebook" href="#"><i class="fa fa-facebook"></i>Facebook</a>
                                    <a class="btn flex-fill m-l5 google-plus" href="#"><i class="fa fa-google-plus"></i>Google Plus</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.querySelector('form.contact-bx').addEventListener('submit', function(e) {
            let isValid = true;

            // Récupérer les champs
            const username = document.querySelector('input[name="username"]');
            const email = document.querySelector('input[name="email"]');
            const password = document.querySelector('input[name="password"]');

            // Regex pour l'email
            const emailRegex = /^[a-z0-9._%+-]+@gmail\.com$/;

            // Supprimer les anciens messages d'erreur
            removeValidationMessages();

            // Vérification du champ username
            if (username.value.trim() === '') {
                isValid = false;
                showValidationMessage(username, 'Le nom d\'utilisateur est requis.', false);
            } else {
                showValidationMessage(username, 'Nom d\'utilisateur valide.', true);
            }

            // Vérification du champ email
            if (email.value.trim() === '') {
                isValid = false;
                showValidationMessage(email, 'L\'adresse e-mail est requise.', false);
            } else if (!emailRegex.test(email.value.trim())) {
                isValid = false;
                showValidationMessage(email, 'Veuillez entrer une adresse e-mail valide (exemple@gmail.com).', false);
            } else {
                showValidationMessage(email, 'Adresse e-mail valide.', true);
            }

            // Vérification du champ mot de passe
            if (password.value.trim() === '') {
                isValid = false;
                showValidationMessage(password, 'Le mot de passe est requis.', false);
            } else if (password.value.length < 6) {
                isValid = false;
                showValidationMessage(password, 'Le mot de passe doit contenir au moins 6 caractères.', false);
            } else {
                showValidationMessage(password, 'Mot de passe valide.', true);
            }

            // Empêcher l'envoi si des erreurs sont présentes
            if (!isValid) {
                e.preventDefault();
            }
        });

        // Fonction pour afficher un message de validation sous un champ
        function showValidationMessage(input, message, isValid) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'validation-message';
            messageDiv.textContent = message;

            // Définir la couleur selon la validité
            if (isValid) {
                messageDiv.style.color = 'green'; // Vert si valide
            } else {
                messageDiv.style.color = 'red'; // Rouge sinon
            }

            input.parentElement.appendChild(messageDiv);
        }
        // Fonction pour supprimer les anciens messages de validation
        function removeValidationMessages() {
            document.querySelectorAll('.validation-message').forEach(msg => msg.remove());
        }
    </script>

    <!-- External JavaScripts -->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/vendors/bootstrap/js/popper.min.js"></script>
    <script src="../assets/vendors/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/vendors/bootstrap-select/bootstrap-select.min.js"></script>
    <script src="../assets/vendors/bootstrap-touchspin/jquery.bootstrap-touchspin.js"></script>
    <script src="../assets/vendors/magnific-popup/magnific-popup.js"></script>
    <script src="../assets/vendors/counter/waypoints-min.js"></script>
    <script src="../assets/vendors/counter/counterup.min.js"></script>
    <script src="../assets/vendors/imagesloaded/imagesloaded.js"></script>
    <script src="../assets/vendors/masonry/masonry.js"></script>
    <script src="../assets/vendors/masonry/filter.js"></script>
    <script src="../assets/vendors/owl-carousel/owl.carousel.js"></script>
    <script src="../assets/js/functions.js"></script>
    <script src="../assets/js/contact.js"></script>
    <script src='../assets/vendors/switcher/switcher.js'></script>
</body>

</html>