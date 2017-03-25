<?php

include("connex.php");
include("class.person.php");
include("class.word.php");
include("class.sentence.php");

$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

/* CREATION DU PERSONNAGE */
$person = new Person();
$person_array = $person->getPersonArray();
$person_pic_filename = $person->getPersonPicture();

/* CREATION DU TITRE */
$sentence_obj = new Sentence($gid_table,"phrases",$person_array);
$sentence = $sentence_obj->getSentenceString();

$hash = hash('md5',$sentence); // Génère le hash
$data = json_encode(array(
	'hash' => $hash,
	'sentence' => $sentence,
	'picture' => $person_pic_filename
)); // Crée un JSON avec la phrase et le hash, et l'affiche pour qu'il soit récupéré par Ajax
$inp = file_get_contents('generated_projects_FR.json');
$tempArray = json_decode($inp);
array_push($tempArray, $data);
$jsonData = json_encode($tempArray);
if(isset($jsonData) && !empty($jsonData) && $jsonData!=''){
	file_put_contents('generated_projects_FR.json', $jsonData, LOCK_EX);
}  // Ajoute le triplet hash+sentence+picture généré dans le JSON

echo $data;

?>