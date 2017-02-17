<?php
	// Make sure the script can handle large folders/files
	ini_set('max_execution_time', 600);
	ini_set('memory_limit','4096M');
	
	function zipData($source, $destination) {
		if (extension_loaded('zip')) {
			if (file_exists($source)) {
				$zip = new ZipArchive();
				if ($zip->open($destination, ZIPARCHIVE::CREATE)) {
					$source = realpath($source);
					if (is_dir($source)) {
						$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
						foreach ($files as $file) {
							$file = realpath($file);
							if (is_dir($file)) {
								$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
							} else if (is_file($file)) {
								$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
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
	
	/*if(!zipData('Home/londra2016', 'archive/tmp/londra2016.zip')) {
		echo "Error";
	} else {
		echo 'Finished.';
	}*/
	
	shell_exec("zip -r london.zip Home/londra2016");
	
?>