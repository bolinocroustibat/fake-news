<?php

include("db/connect.php");
include("class.googlesheet.php");

$db = db_connect();
// Build the DB tables from Google Sheet
include('googlesheet_url.php');
$gsheet = new GoogleSheet($db, $googleSheetUrl);
$gsheet->buildAllTables();
