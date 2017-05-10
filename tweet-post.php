<?php

header('Content-Type: text/html; charset=utf-8');

include("class.googlesheet.php");
include("class.person.php");
include("class.word.php");
include("class.sentence.php");

include("connex.php");

/* CREATION DE LA TABLE D'INDEX */
$gsheet = new GoogleSheet("https://docs.google.com/spreadsheets/d/1sVkvvJCLckEJslV4kS6io0Y9hGLELZnnJd87Kkejces/edit#gid=0");
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
$bdd = database_connect();
$ip = "";
if (isset($_SERVER["REMOTE_ADDR"])){
	$ip = $_SERVER["REMOTE_ADDR"];
};
$bdd->query('INSERT INTO fakenews (hash,sentence,pic_filename,ip) VALUES("'.$hash.'","'.$sentence.'","'.$person_pic_filename.'","'.$ip.'")');

$tweet = $sentence.' adriencarpentier.com/post-verites/'.$hash.'.html';

// require codebird
require_once(__DIR__.'/twitter-codebird/codebird.php');

\Codebird\Codebird::setConsumerKey("xFj0rVXBGfRKAGgdzikcBkqBu", "SXa0ZYJ3XOEQFNhg39x8IXaxiQZnDiuZZuANpofUsOP4xeQMuM");
$cb = \Codebird\Codebird::getInstance();
$cb->setToken("851065918511820800-SL1hzD90KSvxE9do5AczJIEEiCKOe2E", "Y6szmw7p2u1lTLIKOYB9hsID6MfRW2kqHSwLehrSC0iGv");
 
$params = array('status' => $tweet);
$reply = $cb->statuses_update($params);

if ($reply){
	echo 'Le tweet suivant a ete poste :<br />"'.$tweet.'"';
} else { // CAREFUL : ERROR RETURN DOES NOT WORK
	echo "Erreur ! Le tweet n'a pas ete poste";
}

?>