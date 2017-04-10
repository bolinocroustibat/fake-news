<?php

include("connex.php");	
$bdd = database_connect();

$generated_projects_json = file_get_contents("generated_projects_FR.json"); //charge le fichier qui contient l'objet JSON
$generated_projects_table = array_reverse(explode(';',$generated_projects_json));

foreach($generated_projects_table as $obj){
	$array = json_decode($obj,true);
	$hash = $array["hash"];
	$sentence = $array["sentence"];
	$person_pic_filename = $array["picture"];
	$bdd->query('INSERT INTO fakenews (hash,sentence,pic_filename) VALUES("'.$hash.'","'.$sentence.'","'.$person_pic_filename.'")');
}



	
