<?php
	/**
	 * Swap
	 * Share your files, in a snap!
	 *
	 * Swap is a simple file browser that
	 * let you share your file in a fast
	 * and easy way.
	 *
	 * @author Filippo Fortino <filippofortino@gmail.com>
	 * @version 1.1.0
	 */
	
	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/config.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/upload.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/Authentication.class.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/FileSystem.class.php';
	
	$auth = new Authentication();
	
	// Session user messages
	//$_SESSION['feedback'] = "";

	// Disable browser cache
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	
	if($auth->getLoginStatus()) {
		$file_system = new FileSystem();
	}
	
	/**
	 * Create Folders
	 */
	if(isset($_POST['create_folder'])) {
		if($auth->getLoginStatus()) {
			$folder_name = $_POST['folder_name'];
			$path = $_POST['folder_path'] . "/$folder_name";
			$recursive = boolval($_POST['recursive']);
			
			if(!(($recursive) ? mkdir($path, 0755, true) : mkdir($path, 0755)))
				$feedback = "<p class='alert error'>Errore. Impossibile eliminare la cartella.</p>";
			else
				$feedback = "<p class='alert success'>Cartella creata correttamente!</p>";
		} else
			$feedback = "<p class='alert error'>Errore. Non disponi dei permessi necessari per eseguire questa operazione</p>";
	}
	
// 	print_r($_SESSION);
?>
<!DOCTYPE html>
<html>
<head lang="it">
	<meta charset="utf-8">

	<meta name="application-name" content="Swap" />
	<meta name="description" content="Swap. Share your files, in a snap!" />
	<meta name="author" content="Filippo Fortino" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#e73d41">
	<meta name="msapplication-navbutton-color" content="#e73d41">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	
	<meta property="og:title" content="Swap - Share your files, in a snap!" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="https://fortelli.it/swap/" />
	<meta property="og:description" content="Swap - Share your files, in a snap!" />
	<meta property="og:image" content="https://www.fortelli.it/swap/assets/img/swap-s-logo-325x325png" />
	<meta property="og:image:secure_url" content="https://www.fortelli.it/swap/assets/img/swap-s-logo-325x325.png" />
	<meta property="og:image:type" content="image/png" />
	<meta property="og:image:width" content="325" />
	<meta property="og:image:height" content="325" />
	<meta property="og:locale" content="it_IT" />

	<title>Swap - Share your files, in a snap!</title>

	<!-- Styles -->
	<link href="assets/css/styles.css" rel="stylesheet" />
	<link href="assets/css/font-awesome.min.css" rel="stylesheet" />
	<link href="https://unpkg.com/tippy.js/dist/tippy.css" rel="stylesheet">

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
		<header>
			<div id="logo">
				<a href="/swap/#<?php echo $dir; ?>"><img src="assets/img/swap_logo_web-small.png" /></a>
				<p>Share your files, <span>in a snap!</span></p>
			</div>
			<div id="header--side">
				<div class="search">
					<input type="search" placeholder="Cerca.." />
					<button type="button"><i class="fa fa-search"></i></button>
				</div>
				
				<div class="user--login">
					<?php
						if($auth->getLoginStatus()) {
							if(is_null($_SESSION['avatar'])) {
								$letter = strtoupper(substr($_SESSION['first_name'], 0, 1));
								if(!isset($_SESSION['user_color'])) $_SESSION['user_color'] = getRandomColor();
								
								echo "<span id='no-profile-trigger' class='no-profile-link' data-letter='$letter' style='background-color: " . $_SESSION['user_color'] . ";'></span>";
							} else {
								$avatar = $_SESSION['avatar'];
								echo "<span id='no-profile-trigger' class='no-profile-link'><img src='profile/pictures/$avatar' alt='User Profile Image'></span>";
							}
						} else {
							echo "<a id='login-link' href='login/'><i class='fa fa-sign-in' aria-hidden='true'></i><span>Login</span></a>";
						}
					?>
						<div id="user-details-tooltip" style="display: none;">
							<div>
								<?php
									if(is_null($_SESSION['avatar'])) {
										echo "<span class='no-profile-link' data-letter='$letter' style='background-color: " . $_SESSION['user_color'] . ";'></span>";
									} else {
										echo "<img src='profile/pictures/$avatar' alt='User Profile Image'>";
									}
								
								?>
							</div>
							<div>
								<?php
									echo "<h1>" . $_SESSION['first_name'] . " " . $_SESSION['last_name'] . "</h1>";
									echo "<h2>" . $_SESSION['email'] . "</h2>";
								?>
								<div id="profile-button--wrapper">
									<a id="profile-link" href="profile/">Profilo</a>
									<a id="logout-link" href="?action=logout">Esci</a>
								</div>
							</div>
						</div>
<!-- 					<a id="login-link" href="login/"><i class="fa fa-sign-in" aria-hidden="true"></i><span>Login</span></a> -->
<!-- 					<a href="profile/"><img src="profile/pictures/default-avatar.jpg" alt="User Profile Image"></a> -->
<!-- 					<a id="no-profile-link" href="profile/" data-letter="F" style="background-color: red;"></a> -->
				</div>
			</div>
		</header>

		<div class="breadcrumbs"></div>
		<?php
			if(isset($feedback)) echo $feedback;
			if(isset($_SESSION['feedback'])) { echo $_SESSION['feedback']; unset($_SESSION['feedback']); }
		?>
		<div id="content">
			<aside>
				<input type="checkbox" id="upload" name="check[1][]" />
				<label for="upload" class="nav-button" id="upload-button"><i class="fa fa-arrow-up"></i>Upload</label>

				<form class="nav-form" id="upload-form" method="post" action="" enctype="multipart/form-data">
	                <input type="file" name="upl[]" id="file-uploader" multiple /><br />
	                <ul id="files-list"></ul>
	                <input type="submit" value="Carica" />
	                <input type="hidden" class="dir" name="dir" value="" />
				</form>
				<?php if($auth->getLoginStatus()): ?>
				<input type="checkbox" id="folder" name="check[1][]" />
				<label for="folder" class="nav-button" id="dir-button"><i class="fa fa-folder"></i>Nuova cartella</label>
				
				<form class="nav-form" id="folder-form" action="" method="post">
					<input type="text" id="foldername" name="folder_name" placeholder="Nome cartella">
					
					<div id="recursive-wrapper">
						<label for="recursive">Creazione ricorsiva</label>
						<input type="checkbox" id="recursive" name="recursive" value="true">
					</div>
					
	                <input type="submit" name="create_folder" value="Crea" />
	                <input type="hidden" class="dir" name="folder_path" value="" />
				</form>
				<?php endif; ?>
			</aside>
			<div style="clear: both"></div>
			
			<?php echo ($auth->getLoginStatus()) ? "<ul class='data loggedin'></ul>" : "<ul class='data'></ul>"; ?>

			<div class="nothingfound six">
				<div class="nofiles"></div>
				<span>Nessun file.</span>
			</div>
		</div>
	</div>

	<footer>
        <p>
	        Swap. Made with <i class="fa fa-heart"></i> by <a href="mailto:filippofortino@gmail.com">Filippo Fortino</a>
	    </p>
    </footer>

	<!-- JS -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="assets/js/mousetrap.js"></script>
	<script src="assets/js/jquery.stick-kit.min.js"></script>
	<script src="https://unpkg.com/tippy.js/dist/tippy.min.js"></script>
	<script src="assets/js/script.js"></script>
</body>
</html>