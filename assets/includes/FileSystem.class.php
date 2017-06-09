<?php
	
	class FileSystem {
		private $response;
		
		
		public function __construct() {
			if(isset($_GET['delete'])) {
				$this->deleteFile($_GET['delete']);
			}
			
			if(isset($_POST['create_folder'])) {
				$this->createFolder($_POST['folder_name'], $_POST['folder_path'], boolval($_POST['recursive']));
			}
		}
		
		
		private function deleteFile($file) {
			$file = urldecode($file);
			
			if(!unlink($file))
				$response = "Impossibile eliminare il file";
			else
				$response = "Il file è stato correttamente eliminato!";
		}
		
		private function createFolder($name, $path, $recursive = false) {
			$path = "$path/$name";

			if(!(($recursive) ? mkdir($path, 0755, true) : mkdir($path, 0755)))
				$feedback = "<p class='alert error'>Errore. Impossibile creare la cartella.</p>";
			else
				$feedback = "<p class='alert success'>Cartella creata correttamente!</p>";
		}
	}