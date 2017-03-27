<?php

class GoogleSheet {

	private $sheet_url;
	private $sheet_id;
	private $gid_table;
	
	public function getSheetId() {
		return $this->sheet_id;
	}
	
	public function getGidTable() {
		return $this->gid_table;
	}
	
	public function __construct($sheet_url) {
		$this->sheet_url = $sheet_url;
		$this->SheetURLtoID($sheet_url);
		$csv_index_cachefile = dirname(__FILE__)."/cache/csv_cache_index.json";
		if (file_exists($csv_index_cachefile)) { // if the index cache file exists...
			$this->gid_table = $this->CsvToArrayKeys('index'); // ...build the gid table for this object
		} else {
			$this->BuildAllCache();
		}
	}
	
	public function SheetURLtoID() {
		preg_match("'\/spreadsheets\/d\/([a-zA-Z0-9-_]+)'", $this->sheet_url, $sheet_id); // extract the ID from the URL
		$this->sheet_id = $sheet_id[1];
	}
	
	public function BuildAllCache() {
		$this->BuildCacheFile('index','0'); // build main gid table cache file
		$this->gid_table = $this->CsvToArrayKeys('index');
		foreach($this->gid_table as $key => $value) { // build the other cache files
			$this->BuildCacheFile($key,$value);
		}
	}
	
	public function BuildCacheFile($key,$gid) {
		$csvfile = fopen('https://docs.google.com/spreadsheet/pub?key='.$this->sheet_id.'&output=csv&gid='.$gid, 'r');
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
	
	public function CsvToArrayKeys($sheet_name) { // build 2-dimension array with keys
		static $temp_table;
		$cachefile = dirname(__FILE__)."/cache/csv_cache_".$sheet_name.".json";
		$temp_table = json_decode(file_get_contents($cachefile) ); // ...on récupère les données à partir du fichier de cache
		$words_array = array();
		foreach( $temp_table as $line => $row) { // met dans le bon ordre
			if ($row[0] !='') { // enlève les cellules vides
				$words_array[$row[0]] = $row[1];
			}
		}
		array_splice($words_array,0,3); // enlève les 3 premières lignes du tableau
		return ($words_array);
	}
	
}

?>