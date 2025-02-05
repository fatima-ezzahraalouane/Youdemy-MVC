<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header('Location: login.php');
    exit();
}

require_once '../../BackEnd/config/Database.php';
require_once '../../BackEnd/classes/Categorie.php';
require_once '../../BackEnd/classes/Cours.php';
require_once '../../BackEnd/classes/CoursDoc.php';
require_once '../../BackEnd/classes/CoursVideo.php';
require_once '../../BackEnd/classes/Tags.php';

try {
    $categories = Categorie::afficherCategorie();
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}



// Gestion de la recherche et de la pagination
$titreRecherche = isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '';
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$limit = 9;
$offset = ($page - 1) * $limit;


$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : null;


try {
    // Gestion des cours (CoursVideo ou CoursDocument)
    // $listeCours = [];
    // $totalCours = 0;

    $coursVideo = new CoursVideo([]);
    $coursDoc = new CoursDoc([]);


    if ($categoryFilter) {
        $resultatsVideo = $coursVideo->getCoursByCategorie($categoryFilter);
        $resultatsDoc = $coursDoc->getCoursByCategorie($categoryFilter);
    } elseif (!empty($titreRecherche)) {
        $resultatsVideo = $coursVideo->rechercherCoursParTitre($titreRecherche, 1, PHP_INT_MAX);
        $resultatsDoc = $coursDoc->rechercherCoursParTitre($titreRecherche, 1, PHP_INT_MAX);
    } else {
        $resultatsVideo = $coursVideo->afficherCours(1, PHP_INT_MAX);
        $resultatsDoc = $coursDoc->afficherCours(1, PHP_INT_MAX);
    }

    // Fusionner les résultats tout en évitant les doublons
    $tousLesCours = array_merge($resultatsVideo, $resultatsDoc);
    $tousLesCoursUniques = array_values(array_column($tousLesCours, null, 'id_cours'));

    $totalCours = count($tousLesCoursUniques);
    $totalPages = max(ceil($totalCours / $limit), 1);

    $listeCours = array_slice($tousLesCoursUniques, $offset, $limit);
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
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

    <!-- REVOLUTION SLIDER CSS ============================================= -->
    <link rel="stylesheet" type="text/css" href="../assets/vendors/revolution/css/layers.css">
    <link rel="stylesheet" type="text/css" href="../assets/vendors/revolution/css/settings.css">
    <link rel="stylesheet" type="text/css" href="../assets/vendors/revolution/css/navigation.css">
    <!-- REVOLUTION SLIDER END -->
</head>

<body id="bg">
    <div class="page-wraper">
        <!-- <div id="loading-icon-bx"></div> -->

        <!-- Header Top ==== -->
        <header class="header rs-nav">
            <div class="top-bar">
                <div class="container">
                    <div class="row d-flex justify-content-between">
                        <div class="topbar-left">
                            <ul>
                                <li><a href="#"><i class="fa fa-question-circle"></i>Poser une question</a></li>
                                <li><a href="javascript:;"><i class="fa fa-envelope-o"></i>Support@youdemy.com</a></li>
                            </ul>
                        </div>
                        <div class="topbar-right">
                            <ul>
                                <li><a href="javascript:;" class="btn-link"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="javascript:;" class="btn-link"><i class="fa fa-google-plus"></i></a></li>
                                <li><a href="javascript:;" class="btn-link"><i class="fa fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sticky-header navbar-expand-lg">
                <div class="menu-bar clearfix">
                    <div class="container clearfix">
                        <!-- Header Logo ==== -->
                        <div class="menu-logo">
                            <a href="accueil.php"><img src="../assets/images/youdemy2.png" alt=""></a>
                        </div>
                        <!-- Mobile Nav Button ==== -->
                        <button class="navbar-toggler collapsed menuicon justify-content-end" type="button" data-toggle="collapse" data-target="#menuDropdown" aria-controls="menuDropdown" aria-expanded="false" aria-label="Toggle navigation">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                        <!-- Author Nav ==== -->
                        <div class="secondary-menu">
                            <div class="secondary-inner">
                                <ul>
                                    <li><a href="logout.php" class="btn btn-primary rounded-pill">Se déconnecter</a></li>
                                    <!-- Search Button ==== -->
                                    <li class="search-btn"><button id="quik-search-btn" type="button" class="btn-link"><i class="fa fa-search"></i></button></li>
                                </ul>
                            </div>
                        </div>
                        <!-- Search Box ==== -->
                        <div class="nav-search-bar">
                            <form action="#">
                                <input name="search" value="" type="text" class="form-control" placeholder="Tapez pour rechercher">
                                <span><i class="ti-search"></i></span>
                            </form>
                            <span id="search-remove"><i class="ti-close"></i></span>
                        </div>
                        <!-- Navigation Menu ==== -->
                        <div class="menu-links navbar-collapse collapse justify-content-start" id="menuDropdown">
                            <div class="menu-logo">
                                <a href="accueil.php"><img src="../assets/images/youdemy.png" alt="logo"></a>
                            </div>
                            <ul class="nav navbar-nav">
                                <li class="add-mega-menu"><a href="accueil.php">Accueil </a></li>
                                <li class="active"><a href="cours.php" class="active">Cours </a></li>
                                <li class="add-mega-menu"><a href="mescours.php">Mes Cours </a>
                            </ul>
                            <div class="nav-social-link">
                                <a href="javascript:;"><i class="fa fa-facebook"></i></a>
                                <a href="javascript:;"><i class="fa fa-google-plus"></i></a>
                                <a href="javascript:;"><i class="fa fa-linkedin"></i></a>
                            </div>
                        </div>
                        <!-- Navigation Menu END ==== -->
                    </div>
                </div>
            </div>
        </header>
        <!-- header END ==== -->
        <!-- Content -->
        <div class="page-content bg-white">
            <!-- inner page banner -->
            <div class="page-banner ovbl-dark" style="background-image:url(assets/images/banner/banner3.jpg);">
                <div class="container">
                    <div class="page-banner-entry">
                        <h1 class="text-white">Nos cours</h1>
                    </div>
                </div>
            </div>
            <!-- Breadcrumb row -->
            <div class="breadcrumb-row">
                <div class="container">
                    <ul class="list-inline">
                        <li><a href="accueil.php">Accueil</a></li>
                        <li>Nos cours</li>
                    </ul>
                </div>
            </div>
            <!-- Breadcrumb row END -->
            <!-- inner page banner END -->
            <div class="content-block">
                <!-- About Us -->
                <div class="section-area section-sp1">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-3 col-md-4 col-sm-12 m-b30">
                                <div class="widget courses-search-bx placeani">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <form method="GET" action="cours.php">
                                                <label>Rechercher des cours</label>
                                                <input name="search" type="text" class="form-control" value="<?php echo htmlspecialchars($titreRecherche); ?>">
                                                <button type="submit" class="btn btn-primary mt-1">Rechercher</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="widget widget_archive">
                                    <h5 class="widget-title style-1">Catégorie</h5>
                                    <ul>
                                        <?php if (!empty($categories)) : ?>
                                            <?php foreach ($categories as $cat) : ?>
                                                <li><a href="cours.php?category=<?php echo $cat->getIdCategorie(); ?>">
                                                        <?php echo htmlspecialchars($cat->getNom()); ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <li>Aucune catégorie disponible pour le moment.</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-8 col-sm-12">
                                <div class="row">
                                    <?php if (!empty($listeCours)) : ?>
                                        <?php foreach ($listeCours as $coursItem) : ?>
                                            <div class="col-md-6 col-lg-4 col-sm-6 m-b30">
                                                <div class="cours-bx">
                                                    <div class="action-box">
                                                        <img src="<?php echo htmlspecialchars($coursItem['image_url'] ?? ''); ?>" alt="Image du cours">
                                                        <a href="cours_details.php?id=<?php echo $coursItem['id_cours']; ?>" class="btn">Voir les détails</a>
                                                        </div>
                                                    <div class="info-bx text-center">
                                                        <h5><a href="#"><?php echo htmlspecialchars($coursItem['titre'] ?? 'Titre non disponible'); ?></a></h5>
                                                        <span><?php echo htmlspecialchars($coursItem['categorie'] ?? 'Catégorie non définie'); ?></span>
                                                    </div>
                                                    <div class="cours-more-info">
                                                        <div class="review">
                                                            <h6><?php echo htmlspecialchars($coursItem['enseignant'] ?? 'Enseignant inconnu'); ?></h6>
                                                        </div>
                                                        <!-- inscrire a un cours -->
                                                        <div class="text-center">
                                                            <a href="../actions/inscrire.php?id_cours=<?php echo $coursItem['id_cours']; ?>" class="btn btn-primary mt-1" style="margin-left : 15px;" onclick="return confirm('Voulez-vous vraiment vous inscrire à ce cours ?');">
                                                            S'inscrire</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <p>Aucun cours trouvé pour la recherche "<?php echo htmlspecialchars($titreRecherche); ?>"</p>
                                    <?php endif; ?>
                                    <div class="col-lg-12 m-b20">
                                        <div class="pagination-bx rounded-sm gray clearfix">
                                            <ul class="pagination">
                                                <?php if ($page > 1) : ?>
                                                    <li><a href="?search=<?php echo urlencode($titreRecherche); ?>&page=<?php echo $page - 1; ?>">Précédent</a></li>
                                                <?php endif; ?>

                                                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                                    <li class="<?php echo $i == $page ? 'active' : ''; ?>">
                                                        <a href="?search=<?php echo urlencode($titreRecherche); ?>&category=<?php echo $categoryFilter; ?>&page=<?php echo $i; ?>">
                                                            <?php echo $i; ?>
                                                        </a>
                                                    </li>
                                                <?php endfor; ?>

                                                <?php if ($page < $totalPages) : ?>
                                                    <li><a href="?search=<?php echo urlencode($titreRecherche); ?>&page=<?php echo $page + 1; ?>">Suivant</a></li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- contact area END -->

        </div>
        <!-- Content END-->
        <!-- Footer ==== -->
        <footer>
            <div class="footer-top">
                <div class="pt-exebar">
                    <div class="container">
                        <div class="d-flex align-items-stretch">
                            <div class="pt-logo mr-auto">
                                <a href="index.html"><img src="assets/images/logo-white.png" alt="" /></a>
                            </div>
                            <div class="pt-social-link">
                                <ul class="list-inline m-a0">
                                    <li><a href="#" class="btn-link"><i class="fa fa-facebook"></i></a></li>
                                    <li><a href="#" class="btn-link"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="#" class="btn-link"><i class="fa fa-linkedin"></i></a></li>
                                    <li><a href="#" class="btn-link"><i class="fa fa-google-plus"></i></a></li>
                                </ul>
                            </div>
                            <div class="pt-btn-join">
                                <a href="#" class="btn ">Join Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 col-md-12 col-sm-12 footer-col-4">
                            <div class="widget">
                                <h5 class="footer-title">Sign Up For A Newsletter</h5>
                                <p class="text-capitalize m-b20">Weekly Breaking news analysis and cutting edge advices on job searching.</p>
                                <div class="subscribe-form m-b20">
                                    <form class="subscription-form" action="http://educhamp.themetrades.com/demo/assets/script/mailchamp.php" method="post">
                                        <div class="ajax-message"></div>
                                        <div class="input-group">
                                            <input name="email" required="required" class="form-control" placeholder="Your Email Address" type="email">
                                            <span class="input-group-btn">
                                                <button name="submit" value="Submit" type="submit" class="btn"><i class="fa fa-arrow-right"></i></button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-5 col-md-7 col-sm-12">
                            <div class="row">
                                <div class="col-4 col-lg-4 col-md-4 col-sm-4">
                                    <div class="widget footer_widget">
                                        <h5 class="footer-title">Company</h5>
                                        <ul>
                                            <li><a href="index.html">Home</a></li>
                                            <li><a href="about-1.html">About</a></li>
                                            <li><a href="faq-1.html">FAQs</a></li>
                                            <li><a href="contact-1.html">Contact</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-4 col-lg-4 col-md-4 col-sm-4">
                                    <div class="widget footer_widget">
                                        <h5 class="footer-title">Get In Touch</h5>
                                        <ul>
                                            <li><a href="http://educhamp.themetrades.com/admin/index.html">Dashboard</a></li>
                                            <li><a href="blog-classic-grid.html">Blog</a></li>
                                            <li><a href="portfolio.html">Portfolio</a></li>
                                            <li><a href="event.html">Event</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-4 col-lg-4 col-md-4 col-sm-4">
                                    <div class="widget footer_widget">
                                        <h5 class="footer-title">Courses</h5>
                                        <ul>
                                            <li><a href="courses.html">Courses</a></li>
                                            <li><a href="courses-details.html">Details</a></li>
                                            <li><a href="membership.html">Membership</a></li>
                                            <li><a href="profile.html">Profile</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 col-md-5 col-sm-12 footer-col-4">
                            <div class="widget widget_gallery gallery-grid-4">
                                <h5 class="footer-title">Our Gallery</h5>
                                <ul class="magnific-image">
                                    <li><a href="assets/images/gallery/pic1.jpg" class="magnific-anchor"><img src="assets/images/gallery/pic1.jpg" alt=""></a></li>
                                    <li><a href="assets/images/gallery/pic2.jpg" class="magnific-anchor"><img src="assets/images/gallery/pic2.jpg" alt=""></a></li>
                                    <li><a href="assets/images/gallery/pic3.jpg" class="magnific-anchor"><img src="assets/images/gallery/pic3.jpg" alt=""></a></li>
                                    <li><a href="assets/images/gallery/pic4.jpg" class="magnific-anchor"><img src="assets/images/gallery/pic4.jpg" alt=""></a></li>
                                    <li><a href="assets/images/gallery/pic5.jpg" class="magnific-anchor"><img src="assets/images/gallery/pic5.jpg" alt=""></a></li>
                                    <li><a href="assets/images/gallery/pic6.jpg" class="magnific-anchor"><img src="assets/images/gallery/pic6.jpg" alt=""></a></li>
                                    <li><a href="assets/images/gallery/pic7.jpg" class="magnific-anchor"><img src="assets/images/gallery/pic7.jpg" alt=""></a></li>
                                    <li><a href="assets/images/gallery/pic8.jpg" class="magnific-anchor"><img src="assets/images/gallery/pic8.jpg" alt=""></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 text-center"> <a target="_blank" href="https://www.templateshub.net">Templates Hub</a></div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Footer END ==== -->
        <button class="back-to-top fa fa-chevron-up"></button>
    </div>
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