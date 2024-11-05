<?php

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

// Display as JSON
echo json_encode([
	'hash' => $hash,
	'sentence' => $sentence_string,
	'picture' => $person_pic_filename
]);
