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
			$this->sentence_id = $random_row; // set the sentence structure ID	from the argument
		}
		$sentence_struc = $this->all_sentences_struc_array[$this->sentence_id];  // set the sentence structure 1-dimension array for this object
		$this->sentence_struc = $sentence_struc; // update sentence structure array property of this object
		$sentence_string = $this->create_sentence($gid_table,$sentence_struc, $person_array); // create sentence from sentence structure
		$this->sentence_string = $this->correct_sentence($sentence_string);		
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
		$sentence_string = str_replace(" à le ", " au ", $sentence_string);
		$sentence_string = str_replace(" à les ", " aux ", $sentence_string);
		$sentence_string = str_replace(" de a", " d’a", $sentence_string);	
		$sentence_string = str_replace(" de e", " d’e", $sentence_string);
		$sentence_string = str_replace(" de ê", " d’ê", $sentence_string);	
		$sentence_string = str_replace(" de ê", " d’ê", $sentence_string);
		$sentence_string = str_replace(" de é", " d’é", $sentence_string);
		$sentence_string = str_replace(" de è", " d’è", $sentence_string);
		$sentence_string = str_replace(" de h", " d’h", $sentence_string);
		$sentence_string = str_replace(" de i", " d’i", $sentence_string);
		$sentence_string = str_replace(" de u", " d’u", $sentence_string);
		$sentence_string = str_replace(" de de ", " de ", $sentence_string);	
		$sentence_string = str_replace(" de des ", " des ", $sentence_string);
		$sentence_string = str_replace(" de le ", " du ", $sentence_string);
		$sentence_string = str_replace(" le la ", " la ", $sentence_string);			
		$sentence_string = str_replace(" de les ", " des ", $sentence_string);
		$sentence_string = str_replace(" le le ", " le ", $sentence_string);			
		$sentence_string = str_replace(" le les ", " les ", $sentence_string);		
		$sentence_string = str_replace(" un l’", " un ", $sentence_string);	
		$sentence_string = str_replace(" un la ", " une ", $sentence_string);
		$sentence_string = str_replace(" un le ", " un ", $sentence_string);	
		$sentence_string = str_replace(" un les ", " des ", $sentence_string);
		$sentence_string = str_replace(" son la ", " sa ", $sentence_string);
		$sentence_string = str_replace(" son le ", " son ", $sentence_string);		
		$sentence_string = str_replace(" son l’", " son ", $sentence_string);
		$sentence_string = str_replace(" son les ", " ses ", $sentence_string);			
		$sentence_string = str_replace(" de a", " d’a", $sentence_string);
		$sentence_string = str_replace(" de A", " d’A", $sentence_string);
		$sentence_string = str_replace(" de e", " d’e", $sentence_string);
		$sentence_string = str_replace(" de E", " d’E", $sentence_string);		
		$sentence_string = str_replace(" de le ", " du ", $sentence_string);
		$sentence_string = str_replace(" que e", " qu'e", $sentence_string);	
		$sentence_string = str_replace(" que i", " qu'i", $sentence_string);
		$sentence_string = str_replace(" que u", " qu'u", $sentence_string);
		if (substr($sentence_string, 0, 3) == "“"){ // if the first char is a double quote
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