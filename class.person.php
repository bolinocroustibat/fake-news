<?php

class Person {

	private $person_array; // Array with person name string and picture

	public function getPersonArray() {
		return $this->person_array;
	}
	
	public function getPersonName() {
		return $this->person_array[0];
	}
	
	public function getPersonPicture() {
		return $this->person_array[1];
	}
	
	public function getPersonScandal() {
		return $this->person_array[2];
	}
	
	public function getPersonGenre() {
		return $this->person_array[3];
	}
	
	public function __construct() {
		$this->person_array = $this->csvChooseNameAndPicture();
	}
	
	private function csvChooseNameAndPicture() {
		static $words_array;
		$cachefile = dirname(__FILE__)."/cache/csv_cache_PERSONNE.json";
		$words_array = json_decode(file_get_contents($cachefile) ); // ...on récupère les données à partir du fichier de cache
		if ($words_array != ''){
			$random_row = rand (0,count($words_array)-1);
			$person_array = $words_array[$random_row];
		}
		return $person_array;
	}	
}

?>