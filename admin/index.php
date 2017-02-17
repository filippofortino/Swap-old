<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/config.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/Authentication.class.php';
	
	$auth = new Authentication();
	
	if(isset($_GET['hash']) && !empty($_GET['password'])) {
		$password = $_GET['password'];
		echo password_hash($password, PASSWORD_DEFAULT);
	}
	
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Swap Admin Login</title>
	</head>
	<body>
		<h1>Swap Admin Login</h1>
		
		<?php if(isset($auth->feedback)) echo $auth->feedback; ?>
		<form id="form" name="form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
			Username: <input type="text" id="username" name="username" placeholder="Inserisci username...">
			Password: <input type="password" id="password" name="password" placeholder="Inserisci password..">
			<input type="submit" id="submit" name="login" value="Accedi">
		</form>
		<a href="?action=logout">Logout</a>
		
		<h2>Genera Password</h2>
		
		<form id="passgen" name="passgen" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
			<input type="text" id="password" name="password">
			<input type="submit" name="hash" value="Hash!">
		</form>
	</body>
</html>
