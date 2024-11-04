<?php

header('Content-Type: text/html; charset=utf-8');

include("class.googlesheet.php");
include("class.person.php");
include("class.word.php");
include("class.sentence.php");

include("db/connect.php");
$db = db_connect();

// Build the DB tables from Google Sheet
include('googlesheet_url.php');
$gsheet = new GoogleSheet($db, $googleSheetUrl);

// Create the character
$person = new Person($db);
$person_pic_filename = $person->getPicture();

// Create the sentence
$sentence = new Sentence($db, "phrases", $person);
$sentence_string = $sentence->getString();

$hash = hash('md5', $sentence_string);

// Save in the DB
$ip = $_SERVER["REMOTE_ADDR"];
$req = $db->prepare('INSERT INTO generated (hash, sentence, pic_filename, ip) VALUES (:hash, :sentence, :pic_filename, :ip)');
$req->execute([
	':hash' => $hash,
	':sentence' => $sentence_string,
	':pic_filename' => $person_pic_filename,
	':ip' => $ip
]);

$tweet = $sentence_string . ' adriencarpentier.com/post-verites/' . $hash . '.html';

// require codebird
require_once(__DIR__ . '/twitter-codebird/codebird.php');

\Codebird\Codebird::setConsumerKey("xFj0rVXBGfRKAGgdzikcBkqBu", "SXa0ZYJ3XOEQFNhg39x8IXaxiQZnDiuZZuANpofUsOP4xeQMuM");
$cb = \Codebird\Codebird::getInstance();
$cb->setToken("851065918511820800-SL1hzD90KSvxE9do5AczJIEEiCKOe2E", "Y6szmw7p2u1lTLIKOYB9hsID6MfRW2kqHSwLehrSC0iGv");

$params = array('status' => $tweet);
$reply = $cb->statuses_update($params);

if ($reply) {
	echo 'Le tweet suivant a ete poste :<br />"' . $tweet . '"';
} else { // CAREFUL : ERROR RETURN DOES NOT WORK
	echo "Erreur ! Le tweet n'a pas ete poste";
}
