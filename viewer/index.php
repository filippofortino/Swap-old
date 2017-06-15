<?php	
	if(isset($_GET['file'])) {
		$filename =  "../" . urldecode($_GET['file']);
		
		$mime = mime_content_type(realpath($filename));
		
		// If it's not a text file just redirect to it
		// and let the browser handle it
		if(substr($mime, 0, 4) != "text") {
			header("Location: $filename");
		}
		
		$breadcrumbs = explode("/", substr($filename, 3));
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		
		<meta name="application-name" content="Swap" />
		<meta name="description" content="Swap. Share your files, in a snap!" />
		<meta name="author" content="Filippo Fortino" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="theme-color" content="#e73d41">
		<meta name="msapplication-navbutton-color" content="#e73d41">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
		
		<meta property="og:title" content="Swap Viewer | <?php echo basename($filename); ?>" />
		<meta property="og:type" content="website" />
		<meta property="og:url" content="https://fortelli.it/swap/" />
		<meta property="og:description" content="Swap - Share your files, in a snap!" />
		<meta property="og:image" content="https://www.fortelli.it/swap/assets/img/swap-s-logo-325x325png" />
		<meta property="og:image:secure_url" content="https://www.fortelli.it/swap/assets/img/swap-s-logo-325x325.png" />
		<meta property="og:image:type" content="image/png" />
		<meta property="og:image:width" content="325" />
		<meta property="og:image:height" content="325" />
		<meta property="og:locale" content="it_IT" />
		
		<title>Swap Viewer | <?php echo basename($filename); ?></title>
		
		<!-- Styles -->
		<link href="../assets/css/swap-viewer.css" rel="stylesheet">
		<link href="../assets/css/font-awesome.min.css" rel="stylesheet" />
		<link href="../assets/css/prism.css" rel="stylesheet">
	</head>
	<body>
		<?php
			// Allow showing files only inside the 'Home' folder
			if(substr($filename, 0, 8) != "../Home/" || !file_exists(realpath($filename))) :
		?>
		<div id="error">
			<div>
				<p>Oops... There was an error</p><br>
				<p>The file you requested couldn't be found on our servers</p>
				<a href="/ff">Home</a>
			</div>
		</div>
		<?php else: ?>
		<header>
			<a href="https://fortelli.it/swap"><img src="../assets/img/swap_logo_web-small.png" alt="swap-logo"><span>Viewer</a>
		</header>
		<div id="filename">
			<?php foreach($breadcrumbs as $folder) echo "$folder <i class='fa fa-angle-right arrow'></i> "; ?>
		</div>
		<main>
			<pre data-src="<?php echo $filename; ?>"></pre>
		</main>
		
		<!-- Javascript -->
		<script src="../assets/js/prism.js"></script>
		<?php endif; ?>
	</body>
</html>