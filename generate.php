<?php

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
$db->query('INSERT INTO fakenews (hash,sentence,pic_filename,ip) VALUES("' . $hash . '","' . $sentence_string . '","' . $person_pic_filename . '","' . $ip . '")');

// Display as JSON
echo json_encode([
	'hash' => $hash,
	'sentence' => $sentence_string,
	'picture' => $person_pic_filename
]);
