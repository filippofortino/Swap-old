<?php

	/**
	 * Swap
	 *
	 * class Authentication
	 *
	 * Class that handle the login/logout processes.
	 *
	 * @version 1.0
	 * @author Filippo Fortino <filippofortino@gmail.com>.
	 */
	class Authentication {

		private $user_is_logged_in = false;
		private $db;
		public $feedback = "";
	

		/**
		 * Construct of the class.
		 *
		 * Launches when the class is called and basically
		 * starts the application.
		 */
		public function __construct() {
			if($this->checkVersion()) $this->runApplication();
		}
		
		
		/**
		 * Connect to the database
		 */
		private function databaseConnect() {
			$this->db = new mysqli(HOST, USER, PASS, DATABASE);
	
			if($this->db->connect_errno > 0)
				$this->feedback = "<p class='alert error'>Error connecting to the database " . $db->connect_error . "</p>";
		}
		/**
		 * Check if the current php version is older than 5.5.0, if
		 * it is, it includes the PHP password compatiblity library
		 * to add the required functions.
		 */
		private function checkVersion() {
	        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
	            require_once 'password_compatibility_library.php';
	            return true;
	        } elseif (version_compare(PHP_VERSION, '5.5.0', '>='))
	            return true;
	
	        return false;
        }

		/**
		 * Handle the entire flow of the application.
		 */
		private function runApplication() {
			$this->startSession();
			$this->databaseConnect();
			$this->loginOrLogout();
		}

		/**
		 * Get the user login status.
		 *
		 * @return boolean
		 *  'true' if the user is logged in, 'false' if
		 *  it's not.
		 */
		public function getLoginStatus() {
			return $this->user_is_logged_in;
			//return $_SESSION['user_is_logged_in'];
		}

		/**
		 * Start the session.
		 */
		private function startSession() {
			if(session_status() == PHP_SESSION_NONE) session_start();
		}

		/**
		 * Check if the submitted fields are not empty.
		 *
		 * @return boolean
		 *  'true' if the fields are filled, 'false' if
		 *  both or just one of them are empty.
		 */
		private function checkLoginData() {
			if(!empty($_POST['username']) && !empty($_POST['password']))
				return true;
			elseif(empty($_POST['username']) && empty($_POST['password']))
				$this->feedback = "Compila i campi";
			elseif(empty($_POST['username']))
				$this->feedback = "Inserisci il nome utente!";
			elseif(empty($_POST['password']))
				$this->feedback = "Inserisci la password!";

			return false;
		}

		/**
		 * Check the password correctness and do the login.
		 * If the passwords matches sets the $_SESSIONS
		 * variables and the internal $user_is_logged_in variable.
		 * If the submitted password is wrong, sets the $feedback
		 * variable to an appropriate message.
		 */
		private function checkPasswordAndLogin() {
			//$sql = "SELECT * FROM `swp_user` WHERE 1";
			$stmt = $this->db->prepare("SELECT username, password FROM swp_user WHERE username = ? OR email = ?");
			
			$stmt->bind_param("ss", $_POST['username'], $_POST['username']);
			$stmt->execute();
			$stmt->bind_result($user, $pass);
			
			if($stmt->fetch() != NULL)
				if(password_verify($_POST['password'], $pass)) {
					$_SESSION['username'] = $user;
					$_SESSION['user_is_logged_in'] = true;
					$_SESSION['last_activity'] = time();
					$this->user_is_logged_in = true;
					
					$stmt->free_result();
					// Insert last login date into the database
					$stmt = $this->db->prepare("UPDATE swp_user SET last_login = CURRENT_TIMESTAMP WHERE username = ?");
					$stmt->bind_param("s", $user);
					$stmt->execute();
					
					
					/*$sql = "UPDATE swp_user SET last_login = CURRENT_TIMESTAMP WHERE username = $user";
					if(!$result = $this->db->query($sql))
						$this->feedback = 'There was an error running the query ' . $this->db->error;*/
					
				} else
					$this->feedback = "La password inserita non è corretta.";
			else 
				$this->feedback = "L'username inserito non esiste.";
		}

		/**
		 * Do the login with the session data.
		 */
		private function loginSessionData() {
			$this->user_is_logged_in = true;
		}

		/**
		 * Do the login with the user submitted post data.
		 */
		private function loginPostData() {
			if($this->checkLoginData())
				$this->checkPasswordAndLogin();
		}

		/**
		 * Do the logout.
		 */
		private function logout() {
			$_SESSION = array();
			session_destroy();
			$this->user_is_logged_in = false;
			$this->feedback = "Ti sei disconnesso";
		}

		/**
		 * Handle the login or logout process.
		 * If the $_GET['action'] variable is set to
		 * 'logout' calls the logout() function else it
		 * does the login either with post or session data.
		 */
		private function loginOrLogout() {
			if(isset($_GET['action']) && $_GET['action'] == "logout")
				$this->logout();
			elseif(!empty($_SESSION['username']) && ($_SESSION['user_is_logged_in']))
				$this->loginSessionData();
			elseif(isset($_POST['login']))
				$this->loginPostData();
		}
	}
