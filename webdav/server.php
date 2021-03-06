<?php
	use Sabre\DAV;
	use Sabre\DAV\Auth;
	
	date_default_timezone_set('Europe/Rome');
	
	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/config.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/swap/assets/includes/SabreDAV/vendor/autoload.php';
	
	$rootDirectory = new DAV\FS\Directory('../Home');
	$server = new DAV\Server($rootDirectory);
	$server->setBaseUri('/swap/webdav/');
	
	// The lock manager is reponsible for making sure users don't overwrite
	// each others changes.
	$lockBackend = new DAV\Locks\Backend\File('data/locks');
	$lockPlugin = new DAV\Locks\Plugin($lockBackend);
	$server->addPlugin($lockPlugin);
	
	$pdo = new \PDO('mysql:host=' . HOST . ';dbname=' . DATABASE,USER,PASS);
	$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$authBackend = new Auth\Backend\PDO($pdo);
	$authBackend->setRealm('SabreDAV');
	$authPlugin = new Auth\Plugin($authBackend);
	
	// Adding the plugin to the server.
	$server->addPlugin($authPlugin);
	
	// This ensures that we get a pretty index in the browser, but it is
	// optional.
	$server->addPlugin(new DAV\Browser\Plugin());

	$server->exec();

?>