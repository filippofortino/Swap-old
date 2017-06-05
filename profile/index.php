<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/config.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/Authentication.class.php';
	
	$auth = new Authentication();
	
	// Insert session data into variables for
	// easy access
	$username = $_SESSION['username'];
	$first_name = $_SESSION['first_name'];
	$last_name = $_SESSION['last_name'];
	$email = $_SESSION['email'];
	$last_login = $_SESSION['last_login'];
	
	if(!$auth->getLoginStatus()) header('Location: /swap/login');
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
		
		<meta property="og:title" content="Swap | User Profile" />
		<meta property="og:type" content="website" />
		<meta property="og:url" content="https://fortelli.it/swap/" />
		<meta property="og:description" content="Swap - Share your files, in a snap!" />
		<meta property="og:image" content="https://www.fortelli.it/swap/assets/img/swap-s-logo-325x325png" />
		<meta property="og:image:secure_url" content="https://www.fortelli.it/swap/assets/img/swap-s-logo-325x325.png" />
		<meta property="og:image:type" content="image/png" />
		<meta property="og:image:width" content="325" />
		<meta property="og:image:height" content="325" />
		<meta property="og:locale" content="it_IT" />
	
		<title>Swap | User Profile</title>
	
		<!-- Styles -->
		<link href="../assets/css/styles.css" rel="stylesheet" />
		<link href="../assets/css/font-awesome.min.css" rel="stylesheet" />
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
							echo "<a href='profile/'><img src='profile/pictures/$avatar' alt='User Profile Image'></a>";
						}
					?>
				</div>
				
				<div id="profile--details">
					<?php
						echo "<h1>$first_name $last_name</h1>";
						echo "<p>Username: $username</p>";
						echo "<p>Email: $email</p>";
					?>
				</div>
			</div>
			
			<footer>
		        <p>Swap. Made with <i class="fa fa-heart"></i> by <a href="mailto:filippofortino@gmail.com">Filippo Fortino</a></p>
			</footer>
		</div>
	</body>
</html>