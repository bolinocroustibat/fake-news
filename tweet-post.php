<?php

header('Content-Type: text/html; charset=utf-8');

include("class.googlesheet.php");
include("class.person.php");
include("class.word.php");
include("class.sentence.php");

include("db/connect.php");
$db = db_connect();

// Create the character
$person = new Person($db);
$person_pic_filename = $person->getPicture();

// Create the sentence
$sentence = new Sentence($db, $person);
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

\Codebird\Codebird::setConsumerKey($ini['twitter_consumer_key'], $ini['twitter_consumer_secret']);
$cb = \Codebird\Codebird::getInstance();
$cb->setToken($ini['twitter_access_token'], $ini['twitter_access_token_secret']);

$params = array('status' => $tweet);
$reply = $cb->statuses_update($params);

if ($reply) {
	echo 'Le tweet suivant a ete poste :<br />"' . $tweet . '"';
} else { // CAREFUL : ERROR RETURN DOES NOT WORK
	echo "Erreur ! Le tweet n'a pas ete poste";
}
