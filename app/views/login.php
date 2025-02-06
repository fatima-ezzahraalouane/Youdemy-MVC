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
	<link rel="icon" href="../../public/assets/images/youdemy2.png" type="image/x-icon" />
	<link rel="shortcut icon" type="image/x-icon" href="../../public/assets/images/youdemy2.png" />

	<!-- PAGE TITLE HERE ============================================= -->
	<title>YOUDEMY FATIMA-EZZAHRA</title>

	<!-- MOBILE SPECIFIC ============================================= -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!--[if lt IE 9]>
	<script src="../assets/js/html5shiv.min.js"></script>
	<script src="../assets/js/respond.min.js"></script>
	<![endif]-->

	<!-- All PLUGINS CSS ============================================= -->
	<link rel="stylesheet" type="text/css" href="../../public/assets/css/assets.css">

	<!-- TYPOGRAPHY ============================================= -->
	<link rel="stylesheet" type="text/css" href="../../public/assets/css/typography.css">

	<!-- SHORTCODES ============================================= -->
	<link rel="stylesheet" type="text/css" href="../../public/assets/css/shortcodes/shortcodes.css">

	<!-- STYLESHEETS ============================================= -->
	<link rel="stylesheet" type="text/css" href="../../public/assets/css/style.css">
	<link class="skin" rel="stylesheet" type="text/css" href="../../public/assets/css/color/color-1.css">

	<style>
		.popup {
			display: none;
			position: fixed;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			z-index: 1000;
			background-color: white;
			border: 1px solid #ccc;
			border-radius: 8px;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
			padding: 20px;
			width: 300px;
			text-align: center;
		}

		.popup h4 {
			margin: 0 0 10px;
			font-size: 18px;
			color: #d9534f;
		}

		.popup button {
			background-color: #d9534f;
			color: white;
			border: none;
			padding: 10px 20px;
			border-radius: 5px;
			cursor: pointer;
		}

		.popup button:hover {
			background-color: #c9302c;
		}

		.overlay {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0, 0, 0, 0.5);
			z-index: 999;
		}
	</style>
</head>

<body id="bg">
	<div class="page-wraper">
		<div id="loading-icon-bx"></div>
		<div class="account-form">
			<div class="account-head" style="background-image:url(../../public/assets/images/background/bg2.jpg);">
				<a href="index.php"><img src="../../public/assets/images/youdemy.png" class="w-50" alt="icon"></a>
			</div>
			<div class="account-form-inner">
				<div class="account-container">
					<div class="heading-bx left">
						<h2 class="title-head">Connectez-vous à votre <span>Compte</span></h2>
						<p>Vous n'avez pas de compte ? <a href="signup.php">Créez-en un ici</a></p>
					</div>
					<form method="POST" action="../controllers/AuthController.php" class="contact-bx">
						<div class="row placeani">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="input-group">
										<label>Votre Adresse E-mail</label>
										<input name="email" type="text" required="" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-lg-12">
								<div class="form-group">
									<div class="input-group">
										<label>Votre Mot de Passe</label>
										<input name="password" type="password" class="form-control" required="">
									</div>
								</div>
							</div>
							<div class="col-lg-12">
								<div class="form-group form-forget">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" id="customControlAutosizing">
										<label class="custom-control-label" for="customControlAutosizing">Souviens-toi de moi</label>
									</div>
									<a href="forget-password.html" class="ml-auto">Mot de passe oublié?</a>
								</div>
							</div>
							<div class="col-lg-12 m-b30">
								<button name="submit" type="submit" value="Submit" class="btn button-md">Se Connecter</button>
							</div>
							<div class="col-lg-12">
								<h6>Se connecter avec les réseaux sociaux</h6>
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

	<!-- Popup -->
	<div class="overlay"></div>
	<div class="popup" id="errorPopup">
		<h4 id="popupMessage"></h4>
		<button onclick="closePopup()">OK</button>
	</div>

	<script>
		// Vérifiez si une erreur est passée dans l'URL
		const urlParams = new URLSearchParams(window.location.search);
		const error = urlParams.get('error');

		if (error) {
			const popup = document.getElementById('errorPopup');
			const overlay = document.querySelector('.overlay');
			const message = document.getElementById('popupMessage');

			if (error === 'invalid_credentials') {
				message.textContent = 'Email ou mot de passe incorrect.';
			} else if (error === 'server_error') {
				message.textContent = 'Une erreur serveur est survenue. Veuillez réessayer.';
			} else if (error === 'approval_pending') {
				message.textContent = 'Votre compte enseignant est en attente d\'approbation par un administrateur.';
			} else if (error === 'role_unknown') {
				message.textContent = 'Rôle utilisateur inconnu.';
			}

			// Afficher le popup
			popup.style.display = 'block';
			overlay.style.display = 'block';
		}

		// Fermer le popup
		function closePopup() {
			const popup = document.getElementById('errorPopup');
			const overlay = document.querySelector('.overlay');
			popup.style.display = 'none';
			overlay.style.display = 'none';
		}
	</script>

	<!-- External JavaScripts -->
	<script src="../../public/assets/js/jquery.min.js"></script>
	<script src="../../public/assets/vendors/bootstrap/js/popper.min.js"></script>
	<script src="../../public/assets/vendors/bootstrap/js/bootstrap.min.js"></script>
	<script src="../../public/assets/vendors/bootstrap-select/bootstrap-select.min.js"></script>
	<script src="../../public/assets/vendors/bootstrap-touchspin/jquery.bootstrap-touchspin.js"></script>
	<script src="../../public/assets/vendors/magnific-popup/magnific-popup.js"></script>
	<script src="../../public/assets/vendors/counter/waypoints-min.js"></script>
	<script src="../../public/assets/vendors/counter/counterup.min.js"></script>
	<script src="../../public/assets/vendors/imagesloaded/imagesloaded.js"></script>
	<script src="../../public/assets/vendors/masonry/masonry.js"></script>
	<script src="../../public/assets/vendors/masonry/filter.js"></script>
	<script src="../../public/assets/vendors/owl-carousel/owl.carousel.js"></script>
	<script src="../../public/assets/js/functions.js"></script>
	<script src="../../public/assets/js/contact.js"></script>
	<script src='../../public/assets/vendors/switcher/switcher.js'></script>
</body>

</html>