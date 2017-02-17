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
	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/auth.php';

	// Disable browser cache
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
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
		<meta property="og:url" content="https://fortelli.it/swap/paste/" />
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
			<div class="search">
				<input type="search" placeholder="Cerca.." />
				<button type="button"><i class="fa fa-search"></i></button>
			</div>
		</header>

		<div class="breadcrumbs"></div>
		<?php if(isset($feedback)) echo $feedback; ?>
		<div id="content">
			<aside>
				<input type="checkbox" id="upload" />
				<label for="upload" id="upload-button"><i class="fa fa-arrow-up"></i>Upload</label>

				<form id="upload-form" method="post" action="" enctype="multipart/form-data">
	                <input type="file" name="upl[]" id="file-uploader" multiple /><br />
	                <ul id="files-list"></ul>
	                <input type="submit" value="Carica" />
	                <input type="hidden" id="dir" name="dir" value="" />
				</form>
			</aside>
			<div style="clear: both"></div>

			<ul class="data"></ul>

			<div class="nothingfound six">
				<div class="nofiles"></div>
				<span>Nessun file.</span>
			</div>
		</div>
	</div>

	<footer>
        <p>Swap. Made with <i class="fa fa-heart"></i> by <a href="mailto:filippofortino@gmail.com">Filippo Fortino</a></p>
    </footer>

	<!-- JS -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="assets/js/mousetrap.js"></script>
	<script src="assets/js/jquery.stick-kit.min.js"></script>
	<script src="assets/js/script.js"></script>
</body>
</html>