<?php

function db_connect(): PDO
{
	try {
		$db = new PDO("sqlite:db/fakenews.sqlite3");
	} catch (Exception $e) {
		die('Error connecting to DB: ' . $e->getMessage());
	}
	return $db;
}
