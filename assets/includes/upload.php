<?php
	/**
	 * Swap Upload Script
	 *
	 * Simple algorithm that uploads one
	 * or multiple files.
	 *
	 * @author Filippo Fortino <filippofortino@gmail.com>
	 * @version 1.0.0
	 */

	/**
	 * getUrl
	 *
	 * Return the current page url.
	 *
	 * @return string
	 */
	function getUrl() {
		$url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
		$url .= ( $_SERVER["SERVER_PORT"] !== 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
		$url .= $_SERVER["REQUEST_URI"];
		return $url;
	}


	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/SimpleImage.php';

	// Store user alerts
	//$feedback = "";

	if(isset($_FILES['upl'])) {

		$total = count($_FILES['upl']['name']);

		$path = $_POST['dir'];

		for($i = 0; $i < $total; $i++) {
			$tmp_file_path = $_FILES['upl']['tmp_name'][$i];

			if ($tmp_file_path != ""){
			    $new_file_path = $path . "/" . $_FILES['upl']['name'][$i];

			    if(move_uploaded_file($tmp_file_path, $new_file_path)) {
				    if($i > 0)
				    	$feedback = "<p class='alert success'>I file sono stati caricati correttamente.</p>";
			        else
			        	$feedback = "<p class='alert success'>Il file è stato caricato correttamente.</p>";
			    } else {
				    if($i > 0)
				    	$feedback = "<p class='alert error'>Errore, impossibile caricare i file.</p>";
				    else
				    	$feedback = "<p class='alert error'>Errore, impossibile caricare il file.</p>";
			    }
			
			    $img_ext = array("jpg", "jpeg", "png", "gif");
			    $file_ext = pathinfo($_FILES['upl']['name'][$i], PATHINFO_EXTENSION);
			
			    // If the uploaded file is an image
			    if(in_array(strtolower($file_ext), $img_ext)) {
				    $dirpath = "images/compressed/" . substr($path, 5);

					// Create the new folder into the
					// 'compressed' directory
					if(!file_exists($dirpath)) {
						if(!mkdir($dirpath, 0777, true)) {
							$feedback = "<p class='alert error'>Errore nella creazione della cartella</p>";
						}
					}
					
					try {
						$img = new \claviska\SimpleImage();
						$img->fromFile($new_file_path)->thumbnail(100,100)->autoOrient()->toFile($dirpath . "/" . $_FILES['upl']['name'][$i]);

					} catch(Exception $e) {
						$feedback = "<p class='alert error'>" . $e->getMessage() . "</p>";
					}

			    }
		    }
		}
		header("Location: ". getUrl());
	}
?>
