<?php
	
	class FileSystem {
		private $response;
		
		
		public function __construct() {
			if(isset($_GET['delete'])) {
				$this->deleteFile($_GET['delete']);
				echo $response;
			}
		}
		
		
		private function deleteFile($file) {
			$file = urldecode($file);
			
			if(!unlink($file))
				$response = "Impossibile eliminare il file";
			else
				$response = "Il file è stato correttamente eliminato!";
		}
	}