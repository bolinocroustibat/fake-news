<?php

include("db/connect.php");
include("class.googlesheet.php");

$db = db_connect();

// Read .ini file
$ini = parse_ini_file('config.ini');

// Build the DB tables from Google Sheet
$gsheet = new GoogleSheet($db, $ini['google_sheet_url']);
$gsheet->buildAllTables();
