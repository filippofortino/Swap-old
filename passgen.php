<?php
	if(isset($_GET['hash'])) {
		$password = $_GET['password'];
		echo password_hash($password, PASSWORD_DEFAULT);
	}
?>
<html>
	<head>
		<title>Swap Password Generator</title>
	</head>
	<body>
		<h1>Swap Password Generator</h1>
		
		<form id="passgen" name="passgen" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
			<input type="text" id="password" name="password">
			<input type="submit" name="hash" value="Hash!">
		</form>
	</body>
</html>
