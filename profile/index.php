<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/config.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/Authentication.class.php';
	
	$auth = new Authentication();
	$profile = new UserProfileHandler();
	
	// Insert session data into variables for
	// easy access
	$username = $_SESSION['username'];
	$first_name = $_SESSION['first_name'];
	$last_name = $_SESSION['last_name'];
	$email = $_SESSION['email'];
	$last_login = $_SESSION['last_login'];
	
	if(!$auth->getLoginStatus()) (isset($_GET['redirect']) && $_GET['redirect'] == "home") ? header('Location: /swap/#Home') :header('Location: /swap/login');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">

		<meta name="application-name" content="Swap" />
		<meta name="description" content="Swap - Share your files, in a snap!" />
		<meta name="author" content="Filippo Fortino" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="theme-color" content="#e73d41">
		<meta name="msapplication-navbutton-color" content="#e73d41">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
		
		<meta property="og:title" content="<?php echo "$username@Swap | Profilo Utente"; ?>" />
		<meta property="og:type" content="website" />
		<meta property="og:url" content="https://fortelli.it/swap/" />
		<meta property="og:description" content="Swap - Share your files, in a snap!" />
		<meta property="og:image" content="https://www.fortelli.it/swap/assets/img/swap-s-logo-325x325png" />
		<meta property="og:image:secure_url" content="https://www.fortelli.it/swap/assets/img/swap-s-logo-325x325.png" />
		<meta property="og:image:type" content="image/png" />
		<meta property="og:image:width" content="325" />
		<meta property="og:image:height" content="325" />
		<meta property="og:locale" content="it_IT" />
	
		<title><?php echo "$username@Swap | Profilo Utente"; ?></title>
	
		<!-- Styles -->
		<link href="../assets/css/styles.css" rel="stylesheet" />
		<link href="../assets/css/font-awesome.min.css" rel="stylesheet" />
		<link href="https://unpkg.com/tippy.js/dist/tippy.css" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Roboto:500" rel="stylesheet" />
	
		<!--[if gte IE 9]>
			<style type="text/css">
				.gradient {
					filter: none;
				}
			</style>
		<![endif]-->
	
	</head>
	<body id="swap">
		<div class="filemanager">
			<header id="paste">
				<div id="logo">
					<a href="/swap/#<?php echo $dir; ?>"><img src="../assets/img/swap_logo_web-small.png" /></a>
					<p>Share your files, <span>in a snap!</span></p>
				</div>
			</header>
			
			<div id="content" class="profile">
				<div id="profile--image">
					<?php
						if(is_null($_SESSION['avatar'])) {
							$letter = strtoupper(substr($_SESSION['first_name'], 0, 1));
							if(!isset($_SESSION['user_color'])) $_SESSION['user_color'] = getRandomColor();
									
							echo "<span id='no-profile-trigger' class='no-profile-link' data-letter='$letter' style='background-color: " . $_SESSION['user_color'] . ";'></span>";
						} else {
							$avatar = $_SESSION['avatar'];
							echo "<img src='../profile/pictures/$avatar' alt='User Profile Image'>";
						}
					?>
				</div>
				
				<div id="profile--details">
					<?php echo "<h1>$first_name $last_name</h1>"; ?>
					
					<div id="profile--details-wrapper">
						<h2>Dati Utente</h2>
						<p>Nome: <span><?php echo $first_name; ?></span></p>
						<p>Cognome: <span><?php echo $last_name; ?></span></p>
						<p>Username: <span><?php echo $username; ?></span></p>
						<p>Email: <span><?php echo $email; ?></span></p>
						
						<div id="profile--password-update">
							<h2>Cambia Password</h2>
							<?php 
								if(isset($profile->error)) echo "<p class='box--alert box--error'>" . $profile->error ."</p>"; 
								if(isset($profile->success)) echo "<p class='box--alert box--success'>" . $profile->success ."</p>";
							?>
							<form id="form--password-update" name="password-update" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
								<label for="old-password">Password Attuale</label>
								<input type="password" id="input--old-password" name="old-password">
								
								<label for="new-password">Nuova Password</label>
								<input type="password" id="input--new-password" name="new-password">
								
								<label for="new-password-2">Ripeti Password</label>
								<input type="password" id="input--new-password-2" name="new-password-2">
								
								<input type="hidden" name="username" value="<?php echo $username; ?>">
								
								<input type="submit" id="input--submit" name="password-update" value="Modifica">
							</form>
						</div>
						
						<div id="profile--webdav">
							<h2>WebDAV</h2>
							<p class="webdav--description">Puoi accedere a swap tramite WebDAV utilizzando il seguente indirizzo:</p>
							<p class="webdav--box" data-clipboard-text="https://fortelli.it/swap/webdav" title="Clicca per copiare negli appunti">https://fortelli.it/swap/webdav</p>
							
							<p class="webdav--description">Le credenziali di accesso a WebDAV sono:</p>
							<p>Username: <span><?php echo $username; ?></p>
							<p>Password: <span>La tua password</span></p>
						</div>
						
						<h2>Disconnetiti</h2>
						<a class="logout-button" href="?action=logout&redirect=home">Esci dal tuo account</a>
					</div>
				</div>
			</div>
			
			<footer>
		        <p>Swap. Made with <i class="fa fa-heart"></i> by <a href="mailto:filippofortino@gmail.com">Filippo Fortino</a></p>
			</footer>
			
			<!-- JavaScript -->
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
			<script src="https://unpkg.com/tippy.js/dist/tippy.min.js"></script>
			<script src="../assets/js/clipboard.min.js"></script>
			<script>
				$(document).ready(function() {
					Tippy('.webdav--box', {
						size: 'small',
						theme: 'light',
						position: 'right'
					});
					
					var clipboard = new Clipboard('.webdav--box');

					clipboard.on('success', function(e) {
					    console.log(e);
					    alert(e.text + " è stato copiato negli appunti.");
					});
					clipboard.on('error', function(e) {
					    console.log(e);
					});
				});
			</script>
		</div>
	</body>
</html>