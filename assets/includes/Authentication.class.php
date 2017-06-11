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
		
		protected $user_is_logged_in = false;
		protected $db;
		public $success;
		public $error;
	

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
		protected function databaseConnect() {
			$this->db = new mysqli(HOST, USER, PASS, DATABASE);
	
			if($this->db->connect_errno > 0)
				$this->error = "Error connecting to the database " . $db->connect_error;
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
				$this->error = "Compila i campi";
			elseif(empty($_POST['username']))
				$this->error = "Inserisci il nome utente!";
			elseif(empty($_POST['password']))
				$this->error = "Inserisci la password!";

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
			$stmt = $this->db->prepare("SELECT username, password, first_name, last_name, email, avatar, last_login, active FROM swp_user WHERE username = ? OR email = ?");
			
			$stmt->bind_param("ss", $_POST['username'], $_POST['username']);
			$stmt->execute();
			$stmt->bind_result($user, $pass, $first_name, $last_name, $email, $avatar, $last_login, $active);
			
			if($stmt->fetch() != NULL)
				if(password_verify($_POST['password'], $pass)) {
					if(boolval($active)) {
						$_SESSION['username'] = $user;
						$_SESSION['first_name'] = $first_name;
						$_SESSION['last_name'] = $last_name;
						$_SESSION['email'] = $email;
						$_SESSION['avatar'] = $avatar;
						$_SESSION['last_login'] = $last_login;
						
						$_SESSION['user_is_logged_in'] = true;
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
						$this->error = "Questo accout non è ancora attivo, controlla la tua casella di posta per il link di attivazione";
				} else
					$this->error = "La password inserita non è corretta.";
			else 
				$this->error = "L'username inserito non esiste.";
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
			$this->success = "Ti sei disconnesso";
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
	
	
	class Registration extends Authentication {
		
		public function __construct() {
			$this->databaseConnect();
			
			if(isset($_POST['register'])) {
				$username = $_POST['username'];
				$password = $_POST['password'];
				$password2 = $_POST['password2'];
				$first_name = $_POST['first_name'];
				$last_name = $_POST['last_name'];
				$email = $_POST['email'];
				
				$this->registerUser($username, $password, $password2, $first_name, $last_name, $email);
			}
			
			if(isset($_GET['action']) && $_GET['action'] == "verify_user") {
				$email = $_GET['email'];
				$token = $_GET['token'];
				
				$this->confirmUser($email, $token);
			}
		}
		
		private function registerUser($username, $password, $password2, $first_name, $last_name, $email) {
			// Variable Filtering to prevent SQL Injection
			$username = filter_var($username, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$password = filter_var($password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$first_name = filter_var($first_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$last_name = filter_var($last_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$email = filter_var($email, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			
			// Accessing global $realm value instead of local one
			global $realm;
			
			$stmt = $this->db->prepare("SELECT username, email FROM swp_user WHERE username = ? OR email = ?");
			
			$stmt->bind_param("ss", $username, $email);
			$stmt->execute();
			if($stmt->fetch() == NULL) {
				$stmt->free_result();
				
				if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
					if(strlen($username) <= 25) {
						if($password == $password2) {
							$stmt = $this->db->prepare("INSERT INTO swp_user (username, password, digesta1, first_name, last_name, email) VALUES (?, ?, ?, ?, ?, ?)");
							$digesta1 = md5("$username:$realm:$password");
							
							$password = password_hash($password, PASSWORD_DEFAULT);
							$stmt->bind_param("ssssss", $username, $password, $digesta1, $first_name, $last_name, $email);
							
							if($stmt->execute()) {
								$stmt->free_result();
								
								$stmt = $this->db->prepare("INSERT INTO swp_user_confirmation (user_email, token, creation_date) VALUES (?, ?, CURRENT_TIMESTAMP)");
								
								$token = bin2hex(random_bytes(20));
								$stmt->bind_param("ss", $email, $token);
								
								if($stmt->execute())
									if($this->sendConfirmationEmail($email, $first_name, $token))
										$this->success = "Registrazione effettuata con successo. Una mail con il link di attivazione è stata inviata all'indirizzo di posta specificato";
									else 
										$this->error = "Impossibile inviare la mail di conferma.";
								else
									$this->error = "Errore. Impossibile procedere con la registrazione";
							} else {
								$this->error = "Errore. Impossibile procedere con la registrazione";
							}
						} else {
							$this->error = "Le password non corrispondono";
						}
					} else {
						$this->error = "L'username non può essere più lungo di 25 caratteri.";
					}
				} else {
					$this->error = "La mail inserita non è valida";
				}
			} else {
				$this->error = "L'username o la mail scelti sono già stati usati";
			}
		}
		
		private function confirmUser($email, $token) {
			$stmt = $this->db->prepare("SELECT c.creation_date, u.active FROM swp_user_confirmation c, swp_user u WHERE c.user_email = u.email AND c.user_email = ? AND c.token = ?");
			$stmt->bind_param("ss", $email, $token);
			$stmt->execute();
			$stmt->bind_result($creation_date, $active);
			
			if($stmt->fetch() != NULL) {
				if(!boolval($active)) {
					$differnce = time() - strtotime($creation_date);
					if($differnce < 86400) {
						$stmt->free_result();
						
						$query = "UPDATE swp_user SET active = 1 WHERE email = '$email'";
						if($this->db->query($query)) {
							$this->success = "Il tuo account è stato attivato con successo";
							$this->sendSuccessEmail($email);
						}
					} else {
						$this->error = "Questo link è scaduto";
					}
				} else {
					$this->error = "Questo account è gia attivo";
				}
			} else {
				$this->error = "Questo link non è stato trovato, potrebbe essere scaduto.";
			}
		}
		
		
		private function sendConfirmationEmail($email, $first_name, $token) {
			$subject = "Swap - Verifica Registrazione";
			
			$headers = "From: admin@fortelli.it Swap\r\n";
			$headers .= "Reply-To: admin@fortelli.it\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			
			$confirm_url = "https://fortelli.it/swap/login/?action=verify_user&email=$email&token=$token";
			
			$content = '<!doctype html><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head> <title></title> <meta http-equiv="X-UA-Compatible" content="IE=edge"> <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <style type="text/css"> #outlook a{padding: 0;}.ReadMsgBody{width: 100%;}.ExternalClass{width: 100%;}.ExternalClass *{line-height: 100%;}body{margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;}table, td{border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;}img{border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;}p{display: block; margin: 13px 0;}</style> <style type="text/css"> @media only screen and (max-width:480px){@-ms-viewport{width: 320px;}@viewport{width: 320px;}}</style><!--[if mso]><xml> <o:OfficeDocumentSettings> <o:AllowPNG/> <o:PixelsPerInch>96</o:PixelsPerInch> </o:OfficeDocumentSettings></xml><![endif]--><!--[if lte mso 11]><style type="text/css"> .outlook-group-fix{width:100% !important;}</style><![endif]--> <style type="text/css"> @font-face{font-family: "lovelo"; src: url("https://fortelli.it/swap/assets/fonts/lovelo_black-webfont.eot"); src: url("https://fortelli.it/swap/assets/fonts/lovelo_black-webfont.eot?#iefix") format("embedded-opentype"), url("https://fortelli.it/swap/assets/fonts/lovelo_black-webfont.woff2") format("woff2"), url("https://fortelli.it/swap/assets/fonts/lovelo_black-webfont.woff") format("woff"), url("https://fortelli.it/swap/assets/fonts/lovelo_black-webfont.ttf") format("truetype"), url("https://fortelli.it/swap/assets/fonts/lovelo_black-webfont.svg#loveloblack") format("svg"); font-weight: normal; font-style: normal;}</style> <style type="text/css"> @media only screen and (min-width:480px){.mj-column-per-100{width: 100%!important;}.mj-column-px-450{width: 450px!important;}}</style></head><body style="background: #ecf0f1;"> <div style="background-color:#ecf0f1;"><!--[if mso | IE]> <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"> <tr> <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--> <div style="margin:0px auto;max-width:600px;background:#fff;"> <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;background:#fff;" align="center" border="0"> <tbody> <tr> <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:20px 0px;"><!--[if mso | IE]> <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"><![endif]--> <div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"> <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"> <tbody> <tr> <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="left"> <table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0px;" align="left" border="0"> <tbody> <tr> <td style="width:220px;"> <a href="#" target="_blank"><img alt="" title="" height="auto" src="https://fortelli.it/swap/assets/img/swap_logo_web_complete.png" style="border:none;border-radius:0px;display:block;outline:none;text-decoration:none;width:100%;height:auto;" width="220"></a> </td></tr></tbody> </table> </td></tr></tbody> </table> </div><!--[if mso | IE]> </td></tr></table><![endif]--> </td></tr></tbody> </table> </div><!--[if mso | IE]> </td></tr></table><![endif]--><!--[if mso | IE]> <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"> <tr> <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--> <div style="margin:0px auto;max-width:600px;background:linear-gradient(135deg, #ee9b35 0%,#e7045a 100%);"> <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;background:linear-gradient(135deg, #ee9b35 0%,#e7045a 100%);" align="center" border="0"> <tbody> <tr> <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:20px 0px;"><!--[if mso | IE]> <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:450px;"><![endif]--> <div class="mj-column-px-450 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"> <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"> <tbody> <tr> <td style="word-wrap:break-word;font-size:0px;padding:25px;" align="center"> <div class="" style="cursor:auto;color:#fff;font-family:lovelo;font-size:45px;line-height:45px;text-align:center;">Ciao ' . $first_name . '!</div></td></tr><tr> <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="left"> <div class="" style="cursor:auto;color:#fff;font-family:sans-serif;font-size:16px;line-height:22px;text-align:left;">Grazie per esserti registrato a Swap! Ora devi solo attivare il tuo account in modo da poterlo usare.</div></td></tr><tr> <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="left"> <div class="" style="cursor:auto;color:#fff;font-family:sans-serif;font-size:16px;line-height:22px;text-align:left;">Clicca il link qui sotto per procedere!</div></td></tr><tr> <td style="word-wrap:break-word;font-size:0px;padding:20px;" align="center"> <table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:separate;" align="center" border="0"> <tbody> <tr> <td style="border:none;border-radius:3px;color:#fff;cursor:auto;padding:10px 25px;" align="center" valign="middle" bgcolor="#074d7b"><a href="' . $confirm_url . '" style="text-decoration:none;line-height:100%;background:#074d7b;color:#fff;font-family:lovelo;font-size:30px;font-weight:normal;text-transform:none;margin:0px;" target="_blank">Attiva il tuo account</a></td></tr></tbody> </table> </td></tr><tr> <td style="word-wrap:break-word;font-size:0px;"> <div style="font-size:1px;line-height:40px;white-space:nowrap;"> </div></td></tr><tr> <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="left"> <div class="" style="cursor:auto;color:#fff;font-family:sans-serif;font-size:16px;line-height:22px;text-align:left;">Se il link qui sopra non funziona copia ed incolla il seguente URL nella barra deli indirizzi del browser</div></td></tr><tr> <td style="word-wrap:break-word;font-size:0px;padding:10px;"> <div style="margin:0px auto;border-radius:3px;max-width:450px;background:#fff;"> <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;border-radius:3px;background:#fff;" align="center" border="0"> <tbody> <tr> <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:10px;"><!--[if mso | IE]> <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:undefined;width:NaNpx;"><![endif]--> <div class="" style="cursor:auto;color:#626262;font-family:sans-serif;font-size:16px;line-height:22px;text-align:center;"><a href="' . $confirm_url . '">' . $confirm_url . '</a></div><!--[if mso | IE]> </td></tr></table><![endif]--> </td></tr></tbody> </table> </div></td></tr></tbody> </table> </div><!--[if mso | IE]> </td></tr></table><![endif]--> </td></tr></tbody> </table> </div><!--[if mso | IE]> </td></tr></table><![endif]--><!--[if mso | IE]> <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"> <tr> <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--> <div style="margin:0px auto;max-width:600px;background:#fff;"> <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;background:#fff;" align="center" border="0"> <tbody> <tr> <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:20px 0px;"><!--[if mso | IE]> <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"><![endif]--> <div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"> <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"> <tbody> <tr> <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="center"> <div class="" style="cursor:auto;color:#626262;font-family:sans-serif;font-size:11px;line-height:22px;text-align:center;">Il Team di Swap © 2017</div></td></tr></tbody> </table> </div><!--[if mso | IE]> </td></tr></table><![endif]--> </td></tr></tbody> </table> </div><!--[if mso | IE]> </td></tr></table><![endif]--> </div></body></html>';

			if(mail($email, $subject, $content, $headers)) 
				return true;
			else 
				return false;		
		}
	
		private function sendSuccessEmail($email) {
			// Get the user first name
			$stmt = $this->db->prepare("SELECT first_name FROM swp_user WHERE email = ?");
			$stmt->bind_param("s", $email);
			$stmt->execute();
			$stmt->bind_result($first_name);
			$stmt->fetch();
			
			$stmt->free_result();
			
			$subject = "Swap - Conferma Registrazione";
				
			$headers = "From: admin@fortelli.it Swap\r\n";
			$headers .= "Reply-To: admin@fortelli.it\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
			$login_url = "https://fortelli.it/swap/login/?email=$email";
			
			$content = '<!doctype html><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head> <title></title> <meta http-equiv="X-UA-Compatible" content="IE=edge"> <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <style type="text/css"> #outlook a{padding: 0;}.ReadMsgBody{width: 100%;}.ExternalClass{width: 100%;}.ExternalClass *{line-height: 100%;}body{margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;}table, td{border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;}img{border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;}p{display: block; margin: 13px 0;}</style> <style type="text/css"> @media only screen and (max-width:480px){@-ms-viewport{width: 320px;}@viewport{width: 320px;}}</style><!--[if mso]><xml> <o:OfficeDocumentSettings> <o:AllowPNG/> <o:PixelsPerInch>96</o:PixelsPerInch> </o:OfficeDocumentSettings></xml><![endif]--><!--[if lte mso 11]><style type="text/css"> .outlook-group-fix{width:100% !important;}</style><![endif]--> <style type="text/css"> @font-face{font-family: "lovelo"; src: url("https://fortelli.it/swap/assets/fonts/lovelo_black-webfont.eot"); src: url("https://fortelli.it/swap/assets/fonts/lovelo_black-webfont.eot?#iefix") format("embedded-opentype"), url("https://fortelli.it/swap/assets/fonts/lovelo_black-webfont.woff2") format("woff2"), url("https://fortelli.it/swap/assets/fonts/lovelo_black-webfont.woff") format("woff"), url("https://fortelli.it/swap/assets/fonts/lovelo_black-webfont.ttf") format("truetype"), url("https://fortelli.it/swap/assets/fonts/lovelo_black-webfont.svg#loveloblack") format("svg"); font-weight: normal; font-style: normal;}</style> <style type="text/css"> @media only screen and (min-width:480px){.mj-column-per-100{width: 100%!important;}.mj-column-px-450{width: 450px!important;}}</style></head><body style="background: #ecf0f1;"> <div style="background-color:#ecf0f1;"><!--[if mso | IE]> <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"> <tr> <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--> <div style="margin:0px auto;max-width:600px;background:#fff;"> <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;background:#fff;" align="center" border="0"> <tbody> <tr> <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:20px 0px;"><!--[if mso | IE]> <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"><![endif]--> <div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"> <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"> <tbody> <tr> <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="left"> <table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0px;" align="left" border="0"> <tbody> <tr> <td style="width:220px;"> <a href="#" target="_blank"><img alt="" title="" height="auto" src="https://fortelli.it/swap/assets/img/swap_logo_web_complete.png" style="border:none;border-radius:0px;display:block;outline:none;text-decoration:none;width:100%;height:auto;" width="220"></a> </td></tr></tbody> </table> </td></tr></tbody> </table> </div><!--[if mso | IE]> </td></tr></table><![endif]--> </td></tr></tbody> </table> </div><!--[if mso | IE]> </td></tr></table><![endif]--><!--[if mso | IE]> <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"> <tr> <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--> <div style="margin:0px auto;max-width:600px;background:linear-gradient(135deg, #ee9b35 0%,#e7045a 100%);"> <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;background:linear-gradient(135deg, #ee9b35 0%,#e7045a 100%);" align="center" border="0"> <tbody> <tr> <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:20px 0px;"><!--[if mso | IE]> <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:450px;"><![endif]--> <div class="mj-column-px-450 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"> <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"> <tbody> <tr> <td style="word-wrap:break-word;font-size:0px;padding:25px;" align="center"> <div class="" style="cursor:auto;color:#fff;font-family:lovelo;font-size:45px;line-height:45px;text-align:center;">Ciao ' . $first_name . '!</div></td></tr><tr> <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="left"> <div class="" style="cursor:auto;color:#fff;font-family:sans-serif;font-size:16px;line-height:22px;text-align:left;">Congratulazioni! La tua registrazione a Swap è completa. Adesso puoi effettuare il login e presonalizzare ulteriormente il tuo profilo. <br><br>Ti ricordiamo inoltre che con le credenzialiche hai usato per registrarti avrai accesso a Swap tramite webDAV. </div></td></tr><tr> <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="left"> <div class="" style="cursor:auto;color:#fff;font-family:sans-serif;font-size:16px;line-height:22px;text-align:left;">Che aspetti? Inizia a condividere!</div></td></tr><tr> <td style="word-wrap:break-word;font-size:0px;padding:20px;" align="center"> <table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:separate;" align="center" border="0"> <tbody> <tr> <td style="border:none;border-radius:3px;color:#fff;cursor:auto;padding:10px 25px;" align="center" valign="middle" bgcolor="#074d7b"><a href="' . $login_url .'" style="text-decoration:none;line-height:100%;background:#074d7b;color:#fff;font-family:lovelo;font-size:30px;font-weight:normal;text-transform:none;margin:0px;" target="_blank">Accedi a Swap</a></td></tr></tbody> </table> </td></tr><tr> <td style="word-wrap:break-word;font-size:0px;"> <div style="font-size:1px;line-height:40px;white-space:nowrap;"> </div></td></tr><tr> <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="left"> <div class="" style="cursor:auto;color:#fff;font-family:sans-serif;font-size:16px;line-height:22px;text-align:left;">Se il link qui sopra non funziona copia ed incolla il seguente URL nella barra deli indirizzi del browser per accedere a Swap</div></td></tr><tr> <td style="word-wrap:break-word;font-size:0px;padding:10px;"> <div style="margin:0px auto;border-radius:3px;max-width:450px;background:#fff;"> <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;border-radius:3px;background:#fff;" align="center" border="0"> <tbody> <tr> <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:10px;"><!--[if mso | IE]> <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:undefined;width:NaNpx;"><![endif]--> <div class="" style="cursor:auto;color:#626262;font-family:sans-serif;font-size:16px;line-height:22px;text-align:center;"><a href="' . $login_url .'">' . $login_url .'</a></div><!--[if mso | IE]> </td></tr></table><![endif]--> </td></tr></tbody> </table> </div></td></tr></tbody> </table> </div><!--[if mso | IE]> </td></tr></table><![endif]--> </td></tr></tbody> </table> </div><!--[if mso | IE]> </td></tr></table><![endif]--><!--[if mso | IE]> <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"> <tr> <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--> <div style="margin:0px auto;max-width:600px;background:#fff;"> <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;background:#fff;" align="center" border="0"> <tbody> <tr> <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:20px 0px;"><!--[if mso | IE]> <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"><![endif]--> <div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"> <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"> <tbody> <tr> <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="center"> <div class="" style="cursor:auto;color:#626262;font-family:sans-serif;font-size:11px;line-height:22px;text-align:center;">Il Team di Swap © 2017</div></td></tr></tbody> </table> </div><!--[if mso | IE]> </td></tr></table><![endif]--> </td></tr></tbody> </table> </div><!--[if mso | IE]> </td></tr></table><![endif]--> </div></body></html>';
			
			if(mail($email, $subject, $content, $headers)) 
				return true;
			else 
				return false;
		}
	}

	class UserProfileHandler extends Authentication {
		
		public function __construct() {
			$this->databaseConnect();
			
			if(isset($_POST['password-update'])) {
				$username = $_POST['username'];
				$old_password = $_POST['old-password'];
				$new_password = $_POST['new-password'];
				$new_password_2 = $_POST['new-password-2'];;
				$this->changePassword($username, $old_password, $new_password, $new_password_2);
			}
			
			if(isset($_FILES['profile'])) {
				$this->uploadImage($_POST['username']);
			}
			
			if(isset($_GET['action']) && $_GET['action'] == "remove-image") {
				if($_GET['username'] == $_SESSION['username']) {
					$this->removeImage($_GET['username']);
				} else {
					$this->error[1] = "Impossibile rimuovere l'immagine del profilo";
				}
			}
		}
		
		private function changePassword($username, $old_password, $new_password, $new_password_2) {
			$new_password = filter_var($new_password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$new_password_2 = filter_var($new_password_2, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			
			$stmt = $this->db->prepare("SELECT password FROM swp_user WHERE username = ?");
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$stmt->bind_result($db_password);
			$stmt->fetch();
			
			$stmt->free_result();
			
			if(!empty($old_password) && !empty($new_password) && !empty($new_password_2)) {
				// Accessing global $realm value instead of local one
				global $realm;
				
				if(password_verify($old_password, $db_password)) {
					if($new_password == $new_password_2) {
						$digesta1 = md5("$username:$realm:$new_password");
						$new_password = password_hash($new_password, PASSWORD_DEFAULT);
						
						$stmt = $this->db->prepare("UPDATE swp_user SET password = ? , digesta1 = ? WHERE username = ?");
						$stmt->bind_param("sss", $new_password, $digesta1, $username);
						
						if($stmt->execute())
							$this->success[0] = "La tua password è stata correttamente modificata";
						else
							$this->error[0] = "Impossibile modificare la password";
					} else {
						$this->error[0] = "Le due password non corrispondono";
					}
				} else {
					$this->error[0] = "La password inserita non è corretta";
				}
			} else {
				$this->error[0] = "Compila tutti i campi";
			}
		}
		
		private function uploadImage($username) {
			$allowed = array("jpg", "jpeg", "png", "gif");

			$extension = pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION);
			
			if(in_array(strtolower($extension), $allowed)){
			    $new_name = sha1(time() . sha1_file($_FILES['profile']['tmp_name']) . $username . random_int(0, 9999));
			    
			    try {
				    // Convert image to jpg
				    $img = new \claviska\SimpleImage();
					$img->fromFile($_FILES['profile']['tmp_name'])->autoOrient()->toFile("pictures/$new_name.jpg", "image/jpeg");
					
					$stmt = $this->db->prepare("UPDATE swp_user SET avatar = ? WHERE username = ?");
			        $stmt->bind_param("ss", $new_name, $username);
			        
					if($stmt->execute()) {
						$this->success[1] = "L'immagine è stata caricata con successo";
						$_SESSION['avatar'] = $new_name;
					}
			    } catch(Exception $e) {
				    $this->error[1] = $e->getMessage();
			    }
			    
			} else {
			    $this->error[1] = "Il file selezionato non è un' immagine";
			}
		}
		
		private function removeImage($username, $avatar = null) {
			$stmt = $this->db->prepare("UPDATE swp_user SET avatar = ? WHERE username = ?");
			$stmt->bind_param("ss", $avatar, $username);
			
			if($stmt->execute()) {
				$this->success[1] = "L'immagine è stata correttamente rimossa";
				$_SESSION['avatar'] = null;
			} else {
				$this->error[1] = "Impossibile rimuovere l'immagine del profilo";
			}
		}
		
	}

