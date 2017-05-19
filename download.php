<?php

	/**
	 * Swap File Downloader
	 *
	 * Forces the download of any file
	 * without leting the browser try
	 * to visualize it.
	 *
	 * @author Filippo Fortino <filippofortino@gmail.com>
	 * @version 2.0
	 */

	/**
	 * zipData
	 *
	 * Create a zip archive from
	 * a file or a folder.
	 *
	 * @return boolean
	 */
	function zipData($source, $destination) {
		if (extension_loaded('zip')) {
			if (file_exists($source)) {
				$zip = new ZipArchive();
				if ($zip->open($destination, ZIPARCHIVE::CREATE)) {
					$source = realpath($source);
					if (is_dir($source)) {
						$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
						foreach ($files as $file) {
							if(basename($file) != '.' && basename($file) != '..') {
								$file = realpath($file);
								if (is_dir($file)) {
									$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
								} else if (is_file($file)) {
									$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
								}
							}
						}
					} else if (is_file($source)) {
						$zip->addFromString(basename($source), file_get_contents($source));
					}
				}
				return $zip->close();
			}
		}
		return false;
	}
	
	/**
	 * Get Folder Size
	 *
	 */
	function folderSize ($dir) {
	    $size = 0;
	    foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
	        $size += is_file($each) ? filesize($each) : folderSize($each);
	    }
	    return $size;
    }
    
    /**
	 * Convert bytes into
	 * human redable units.
	 */
	function bytesToSize($size) {
	    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	    $power = $size > 0 ? floor(log($size, 1024)) : 0;
	    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
	}

	$file = urldecode($_GET['file']);
	$response = false;
	
	if(!is_dir($file)) {
	
		// Allow download only from the "Home" or the "Private" directory
		if (substr($file, 0, 4) == "Home" && file_exists($file)) {
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename="'.basename($file).'"');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file));
		    readfile($file);
		    
		    $response = true;
		    exit;
		}
	} else {
		$folder = $file;
		if (substr($folder, 0, 4) == "Home" && file_exists($folder)) {
			$zipped_file = "archive/tmp/" . basename($folder) . ".zip";
			if(zipData($folder, $zipped_file)) {
				header("Location: $zipped_file");
/*
				header('Content-Description: File Transfer');
			    header('Content-Type: application/octet-stream');
			    header('Content-Disposition: attachment; filename="'.basename($zipped_file).'"');
			    header('Expires: 0');
			    header('Cache-Control: must-revalidate');
			    header('Pragma: public');
			    header('Content-Length: ' . filesize($zipped_file));
			    readfile($zipped_file); 
*/
			    
			    $response = true;
			    exit;
			} else {
				echo "Unable to zip!";
			}
		}
	}
	
	if(!$response) :
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>No file found</title>
	</head>
	<body>
		<h1>We're sorry but the file you requested couldn't be found on our servers.</h1>
		<?php if(!$file): ?>
		<h2>No file specified</h2>
		<?php else : ?>
		<h2>File: <?php echo $file; ?></h2>
		<?php endif; ?>

	</body>
</html>
<?php
	endif;
?>
