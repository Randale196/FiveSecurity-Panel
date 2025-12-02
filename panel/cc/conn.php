<?php
include('../config.php');

$ip = $database_config['host'];
$user = $database_config['username'];
$password = $database_config['password'];
$db = "auth";
$db2 = "serverbans";
$db3 = "panel";
$db4 = "counter";

try {
	$db = new PDO("mysql:host=$ip;dbname=$db", $user, $password);
	$db->exec("SET CHARSET UTF8");
	$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Unexcepted error on database connection.");
}

try {
	$db2 = new PDO("mysql:host=$ip;dbname=$db2", $user, $password);
	$db2->exec("SET CHARSET UTF8");
	$db2->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Unexcepted error on database connection.");
}

try {
	$db3 = new PDO("mysql:host=$ip;dbname=$db3", $user, $password);
	$db3->exec("SET CHARSET UTF8");
	$db3->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Unexcepted error on database connection.");
}

try {
	$db4 = new PDO("mysql:host=$ip;dbname=$db4", $user, $password);
	$db4->exec("SET CHARSET UTF8");
	$db4->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Unexcepted error on database connection.");
}

?>