<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
</head>

<body>

<?php

include("connex.php");

foreach($gid_table as $key => $value) {
	$csvfile = fopen('https://docs.google.com/spreadsheet/pub?key='.$google_sheet_id.'&output=csv&gid='.$value, 'r');
	if (!$csvfile) {
		echo ("Erreur de lecture d'une table du Google Sheet !<br/> ");
	} else{
		$cache_dir = dirname(__FILE__)."/cache/";
		$cache_filename = "csv_cache_".$key.".json";
		if (!is_dir($cache_dir) or !is_writable($cache_dir)) {	// Error if directory doesn't exist or isn't writable.
			echo ("Erreur ! Le répertoire de cache <i>".$cache_dir."</i> n'existe pas ou n'a pas les autorisations d'écriture nécéssaires.<br/>");
		} elseif (is_file($cache_filename) and !is_writable($cache_filename)) { // Error if the file exists and isn't writable.
			echo ("Erreur ! Le répertoire de cache est OK, mais le des fichiers de cache <i>".$cache_filename."</i> n'existe pas ou n'a pas les autorisations d'écriture nécéssaires.<br/>");
		} else {
			$temp_table = array();
			while($row = fgetcsv($csvfile)) {
				// $row = array_map( "utf8_encode", $row ); // à chacune des entrées la fonction utf8_encode est appliquée
				$temp_table[] = $row; // get the data from the CSV Google Sheet
			}
			fclose($csvfile);
			file_put_contents($cache_dir.$cache_filename, json_encode($temp_table) ); // put the data in the cachefile
			echo ("Fichier de cache <i>".$cache_filename."</i> mis à jour !<br/>");
		}
	}
}

echo "<hr />";

?>

</body>

</html>