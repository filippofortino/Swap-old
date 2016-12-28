<?php
	if(!set_time_limit(0)) {
		echo "Error while trying to expand maximum execution time limit.";
	}
	
	require_once 'assets/includes/SimpleImage.php';
		
	$img = new abeautifulsite\SimpleImage();
	
	$photos = array_diff(scandir('Home/londra2016/'), array('..', '.'));
	$i = 0;
	
	
	foreach($photos as $photo) {
		
		$info = new SplFileInfo($photo);
		
		if($info->getExtension() == "jpg") {
			try {
				$img = new abeautifulsite\SimpleImage('Home/londra2016/' . $photo);
				$img->thumbnail(100,100)->auto_orient()->save('images/compressed/londra2016old/' . $photo);
				
				echo ++$i . ": $photo -> Done <br>";
			} catch(Exception $e) {
				echo 'Error: ' . $e->getMessage() . "<br>";
			}
		}
	}
?>