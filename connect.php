<?php

function database_connect(){
	try
	{
		$db = new PDO('mysql:host=localhost;port=3306;dbname=fakenews', 'localmysqluser', 'foufoune');
		$db->exec("SET CHARACTER SET utf8");
	}
	catch(Exception $e)
	{
		die('Erreur : '.$e->getMessage());
	}
	return $db;
}

?>
