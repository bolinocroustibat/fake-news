<?php

class Word {

	private $word_string;

	public function getWordString() {
		return $this->word_string;
	}
	
	public function __construct($sheet_name) {
		$words_array = $this->csvSingleColumnToArray($sheet_name);
		if ($words_array != ''){
			$this->word_string = $this->chooseRandomString($words_array); // launch the function for choosing a random line and put it in the property
		}
	}
	
	public function csvSingleColumnToArray($sheet_name) {
		static $temp_table;
		$cachefile = dirname(__FILE__)."/cache/csv_cache_".$sheet_name.".json";
		$temp_table = json_decode(file_get_contents($cachefile) ); // ...on récupère les données à partir du fichier de cache
		$words_array = array();
		foreach( $temp_table as $line => $row) { // met dans le bon ordre
			if ($row[0] !='') { // enlève les cellules vides
				$words_array[] = $row[0];
			}
		}
		return ($words_array);
	}
	
	private function chooseRandomString($words_array) {
		$random_row = rand (0,count($words_array)-1);
		$string = $words_array[$random_row];
		return $string;
	}
}

?>