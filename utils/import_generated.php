<?php

include("db/connect.php");
$db = db_connect();

$generated_projects_json = file_get_contents("generated_projects_FR.json");
$generated_projects_table = array_reverse(explode(';', $generated_projects_json));

foreach ($generated_projects_table as $obj) {
	$array = json_decode($obj, true);
	$hash = $array["hash"];
	$sentence = $array["sentence"];
	$person_pic_filename = $array["picture"];
	$req = $db->prepare('INSERT INTO generated (hash, sentence, pic_filename) VALUES (:hash, :sentence, :pic_filename)');
	$req->execute([
		':hash' => $hash,
		':sentence' => $sentence,
		':pic_filename' => $person_pic_filename
	]);
}
