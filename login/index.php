<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/config.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/Authentication.class.php';
	
	$auth = new Authentication();
	$register = new Registration();
	
	$get_email = "";
	
	if(isset($_GET['email'])) {
		$get_email = $_GET['email'];
	}
	
	if($auth->getLoginStatus())	header('Location: /swap/#Home');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">

		<meta name="application-name" content="Swap" />
		<meta name="description" content="Swap - Share your files, in a snap!" />
		<meta name="author" content="Filippo Fortino" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="theme-color" content="#e73d41">
		<meta name="msapplication-navbutton-color" content="#e73d41">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
		
		<meta property="og:title" content="Swap | Login" />
		<meta property="og:type" content="website" />
		<meta property="og:url" content="https://fortelli.it/swap/" />
		<meta property="og:description" content="Swap - Share your files, in a snap!" />
		<meta property="og:image" content="https://www.fortelli.it/swap/assets/img/swap-s-logo-325x325png" />
		<meta property="og:image:secure_url" content="https://www.fortelli.it/swap/assets/img/swap-s-logo-325x325.png" />
		<meta property="og:image:type" content="image/png" />
		<meta property="og:image:width" content="325" />
		<meta property="og:image:height" content="325" />
		<meta property="og:locale" content="it_IT" />
	
		<title>Swap | Login</title>
	
		<!-- Styles -->
		<link href="../assets/css/styles.css" rel="stylesheet" />
		<link href="../assets/css/font-awesome.min.css" rel="stylesheet" />
		<link href="https://fonts.googleapis.com/css?family=Roboto:500" rel="stylesheet" />
	
		<!--[if gte IE 9]>
			<style type="text/css">
				.gradient {
					filter: none;
				}
			</style>
		<![endif]-->
	
	</head>
	<body id="swap">
		<div class="filemanager">
			<header id="paste">
				<div id="logo">
					<a href="/swap/#<?php echo $dir; ?>"><img src="../assets/img/swap_logo_web-small.png" /></a>
					<p>Share your files, <span>in a snap!</span></p>
				</div>
			</header>
			
			<div id="content" class="authentication">
				<div id="login">
					<h1>Accedi</h1>
					
					<div class="form-container">
						<?php 
							if(isset($auth->error)) echo "<p class='box--alert box--error'>" . $auth->error ."</p>"; 
							if(isset($auth->success)) echo "<p class='box--alert box--success'>" . $auth->success ."</p>";
						?>
						
						<form id="form--login" name="login-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
							<label for="username">Username o e-mail</label>
							<input type="text" id="input--username" name="username" value="<?php echo $get_email; ?>">
							
							<label for="password">Password</label>
							<input type="password" id="input--password" name="password">
							
							<label for="stay-logged-in">Rimani connesso</label>
							<input type="checkbox" id="input--stay-logged-in" name="stay-logged-in">
							
							<input type="submit" id="input--submit" name="login" value="Accedi">
						</form>
					</div>
				</div>
				
				<div id="registration">
					<h1>Registrati</h1>
					<div class="form-container">
						<?php 
							if(isset($register->error)) echo "<p class='box--alert box--error'>" . $register->error ."</p>";
							if(isset($register->success)) echo "<p class='box--alert box--success'>" . $register->success ."</p>";
							?>
						<form id="form--register" name="register-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
							<div id="field-separator--left">
								<label for="username">Username</label>
								<input type="text" id="input--username" name="username" tabindex="1">
								
								<label for="password">Password</label>
								<input type="password" id="input--password" name="password" tabindex="3">
								
								<label for="first-name">Nome</label>
								<input type="text" id="input--first-name" name="first_name" tabindex="5">
							</div>
							
							<div id="field-separator--right">
								<label for="email">E-mail</label>
								<input type="text" id="input--email" name="email" tabindex="2">
								
								<label for="password2">Ripeti password</label>
								<input type="password" id="input--password-2" name="password2" tabindex="4">
								
								<label for="last-name">Cognome</label>
								<input type="text" id="input--last-name" name="last_name" tabindex="6">
							</div>
							
							<input type="submit" id="input--submit" name="register" value="Registrati">
						</form>
						
						<!-- Form only displayed on moblile devices -->
						<form id="mobile-form" name="register-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
							<div>
								<label for="username">Username</label>
								<input type="text" id="input--username" name="username" tabindex="1">
								
								<label for="email">E-mail</label>
								<input type="text" id="input--email" name="email" tabindex="2">
								
								<label for="password">Password</label>
								<input type="password" id="input--password" name="password" tabindex="3">
								
								<label for="password2">Ripeti password</label>
								<input type="password" id="input--password-2" name="password2" tabindex="4">
								
								<label for="first-name">Nome</label>
								<input type="text" id="input--first-name" name="first_name" tabindex="5">
								
								<label for="last-name">Cognome</label>
								<input type="text" id="input--last-name" name="last_name" tabindex="6">
							</div>
							<input type="submit" id="input--submit" name="register" value="Registrati">
						</form>
						
						
					</div>
				</div>
			</div>
			
			<footer>
		        <p>Swap. Made with <i class="fa fa-heart"></i> by <a href="mailto:filippofortino@gmail.com">Filippo Fortino</a></p>
			</footer>
	</body>
</html>
