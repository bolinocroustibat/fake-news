<?php

include("class.googlesheet.php");
include("class.person.php");
include("class.word.php");
include("class.sentence.php");

/* CREATION DE LA TABLE D'INDEX */
include('googlesheet_url.php');
$gsheet = new GoogleSheet($googleSheetUrl);
$gid_table = $gsheet->getGidTable();

/* CREATION DU PERSONNAGE */
$person = new Person();
$person_array = $person->getPersonArray();
$person_pic_filename = $person->getPersonPicture();

/* CREATION DU TITRE */
$sentence_obj = new Sentence($gid_table,"phrases",$person_array);
$sentence = $sentence_obj->getSentenceString();

$hash = hash('md5',$sentence); // Génère le hash
$data = array(
	'hash' => $hash,
	'sentence' => $sentence,
	'picture' => $person_pic_filename
);
$jsonData = json_encode($data); // encode in a JSON object
if(!empty($data)){
	file_put_contents('generated_projects_FR.json', $jsonData.';', FILE_APPEND | LOCK_EX); // FILE_APPEND so it puts the data at the end / LOCK_EX so the file is not writable while open
}  // Ajoute les triplets hash+sentence+picture généré dans le fichier de BDD, sous forme d'objets séparés par des ";"

echo $jsonData;

?>