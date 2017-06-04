<?php
	require_once  $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/SimpleImage.php';
	
	// Default size
	$size = 100;
	
	if(isset($_GET['size'])) $size = $_GET['size'];
	
	if(isset($_GET['image'])) {
		$name = $_GET['image'];
		
		if(isset($_GET['type']) && $_GET['type'] == "H") { // Filemanager picture handler
			$name = urldecode($name);
			
			$cache_image = "../images/compressed/" . substr($name, 5);
			if(file_exists($cache_image)) {
				$img = new \claviska\SimpleImage();
				$img->fromFile($cache_image)->toScreen();
			}
			
			$image = "../$name";
		} else { // Profile picture handler
			$image =  "../profile/pictures/test.jpg";
		}
		
		try {
			$img = new \claviska\SimpleImage();
			$img->fromFile($image)->thumbnail($size, $size)->autoOrient()->toScreen();
		
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}
?>