<?php

include("class.googlesheet.php");
include("class.person.php");
include("class.word.php");
include("class.sentence.php");

include("db/connect.php");

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

/* ENREGISTREMENT DANS LA BDD */
$db = db_connect();
$ip = $_SERVER["REMOTE_ADDR"];
$db->query('INSERT INTO fakenews (hash,sentence,pic_filename,ip) VALUES("'.$hash.'","'.$sentence.'","'.$person_pic_filename.'","'.$ip.'")');

/* AFFCIHAGE SOUS FORME D'OBJET JSON POUR AJAX */
$data = [
	'hash' => $hash,
	'sentence' => $sentence,
	'picture' => $person_pic_filename
];
$jsonData = json_encode($data); // encode in a JSON object
echo $jsonData;

?>