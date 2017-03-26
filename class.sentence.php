<?php

class Sentence {

	private $all_sentences_struc_array;
	private $person_array;
	private $sentence_struc;
	private $sentence_id;
	private $sentence_string;
	
	public function __construct($gid_table, $sheet_name, $person_array, $min_line = NULL, $max_line = NULL) {
		$this->person_array = $person_array;
		$this->all_sentences_struc_array = $this->csv_sentences_structures_to_array($sheet_name); // get all sentence structures in an array
		if (($min_line === NULL) && ($max_line === NULL)){ // If there is no specific sentence range ID wanted...
			$random_row = rand (0,count($this->all_sentences_struc_array)-1); // choose one random sentence structure
			$this->sentence_id = $random_row;
		}
		else {
			$random_row = rand ($min_line-5,$max_line-5); // choose one random sentence structure within the range
			$this->sentence_id = $random_row; // set the sentence structure ID from the argument
		}
		if (isset($this->all_sentences_struc_array[$this->sentence_id])){ // if the line exists (if the array structure exists)
			$sentence_struc = $this->all_sentences_struc_array[$this->sentence_id]; // set the sentence structure 1-dimension array for this object
			$this->sentence_struc = $sentence_struc; // update the sentence structure of this object
			$sentence_string = $this->create_sentence($gid_table,$sentence_struc); // create sentence from sentence structure
			$this->sentence_string = $this->correct_sentence($sentence_string);		
		} else {
			$this->sentence_struc = "ERREUR, CETTE STRUCTURE DE PHRASE N'EST PAS DEFINIE";
			$this->sentence_string = "ERREUR, CETTE STRUCTURE DE PHRASE N'EST PAS DEFINIE";
		}
	}
	
	public function getSentenceStructArray() {
		return $this->sentence_struc;
	}
	
	public function getSentenceId() {
		return $this->sentence_id+5;
	}
	
	public function getSentenceString() {
		return $this->sentence_string;
	}
	
	private function csv_sentences_structures_to_array($sheet_name) {
		static $temp_table;
		$cachefile = dirname(__FILE__)."/cache/csv_cache_".$sheet_name.".json";
		$temp_table = json_decode(file_get_contents($cachefile) ); // ...on récupère les données à partir du fichier de cache
		$all_sentences_struc_array = array();
		foreach( $temp_table as $line => $row) { // met dans le bon ordre
			foreach ($row as $column => $value) {
				if ($value !='') { // enlève les cellules vides
					$all_sentences_struc_array[$line][$column] = $value;
				}
			}
		}
		array_splice($all_sentences_struc_array,0,4); // enlève les 4 premières lignes du tableau
		return ($all_sentences_struc_array);
	}
	
	private function create_sentence($gid_table,$sentence_struc, $person_array){
		$sentence_string='';
		$i=0;
		while (isset($sentence_struc[$i])) { // on éxécute la boucle tant qu'on n'a pas une cellule vide
			if ($sentence_struc[$i] == "SELF"){ // si c'est la personne de l'objet)
				if ($i == 0) { // pas d'espace avant si c'est le premier mot de la phrase,
					$sentence_string=$sentence_string.$this->person_array[0];	// on prend le nom de la personne de l'objet
				} else {
					$sentence_string=$sentence_string.' '.$this->person_array[0]; // on prend le nom de la personne de l'objet
				}
			} elseif ($sentence_struc[$i] == "SELFGATE"){
				if ($i == 0) { // pas d'espace avant si c'est le premier mot de la phrase,
					$sentence_string=$sentence_string.$this->person_array[2];	// on prend le nom de la personne de l'objet
				} else {
					$sentence_string=$sentence_string.' '.$this->person_array[2]; // on prend le nom de la personne de l'objet
				}
			} elseif (in_array($sentence_struc[$i], array_keys($gid_table))){ // si il s'agit d'un code de mot parmi ceux dont le nombre et le genre doivent être déterminés
				$word = new Word($sentence_struc[$i]);
				$word_string = $word->getWordString();
				if (($i == 0) || (substr($sentence_struc[$i], 0, 1) == ".") || (substr($sentence_struc[$i], 0, 1) == ",")) { // pas d'espace avant si c'est le premier mot de la phrase, ou si le mot est un point ou une virgule
					$sentence_string=$sentence_string.$word_string;
				} else {
					$sentence_string=$sentence_string.' '.$word_string;
				}
			} else { // si l'élément est inconnu, c'est que c'est un mot et pas un code !
				if (($i == 0) || (substr($sentence_struc[$i], 0, 1) == ".") || (substr($sentence_struc[$i], 0, 1) == ",")) { // pas d'espace avant si c'est le premier mot de la phrase, ou si le mot suivant est un point ou une virgule
					$sentence_string=$sentence_string.$sentence_struc[$i];
				} else {
					$sentence_string=$sentence_string.' '.$sentence_struc[$i];
				}
			}
			$i++;
		};
		return ($sentence_string);
	}
	
	private function correct_sentence($sentence_string) {
		if ((strpos($sentence_string, ' il ') !== false) && (strpos($sentence_string, 'il s’agit') == false) && ($this->person_array[3] == "f")) { // si on trouve un pronom "il" dans la phrase et que la personne est féminin
			$sentence_string = str_replace(" il ", " elle ", $sentence_string);
		}
		$correction_array = array(
		" à le " => " au ",
		" à les " => " aux ",
		" de a" => " d’a",
		" de e" => " d’e",
		" de ê" => " d’ê",
		" de ê" => " d’ê",
		" de é" => " d’é",
		" de è" => " d’è",
		" de h" => " d’h",
		" de i" => " d’i",
		" de u" => " d’u",
		" de A" => " d’A",
		" de E" => " d’E",
		" de I" => " d’I",	
		" de de " => " de ",	
		" de des " => " des ",
		" de le " => " du ",
		" le la " => " la ",			
		" de les " => " des ",
		" le le " => " le ",			
		" le les " => " les ",		
		" un l’" => " un ",	
		" un la " => " une ",
		" un le " => " un ",	
		" un les " => " des ",
		" son la " => " sa ",
		" son le " => " son ",		
		" son l’" => " son ",
		" son les " => " ses ",	
		" de le " => " du ",
		" que a" => " qu’a",
		" que e" => " qu’e",
		" que i" => " qu’i",
		" que u" => " qu’u",
		" que A" => " qu’A",			
		" que E" => " qu’E",	
		" que I" => " qu’I",
		" que U" => " qu’U",
		);
		$sentence_string = strtr($sentence_string,$correction_array);
		if (substr($sentence_string, 0, 3) == '“' || substr($sentence_string, 0, 3) == '"'){ // if the first char is a double quote
			$second_char = substr($sentence_string, 3, 1);
			$second_char_caps = $this->frenchUcfirst($second_char);
			$sentence_string = substr_replace($sentence_string,$second_char_caps, 3, 1); // replace the second char
		} else{
			$sentence_string = $this->frenchUcfirst($sentence_string);
		}
		return $sentence_string;
	}
	
	private function frenchUcfirst($string) { 
		$strlen = mb_strlen($string, "utf8");
		$firstChar = mb_substr($string, 0, 1, "utf8");
		$then = mb_substr($string, 1, $strlen - 1, "utf8");
		return mb_strtoupper($firstChar, "utf8") . $then;
		return $string;
	}
}

?>