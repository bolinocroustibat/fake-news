<?php

class Sentence
{

	private $db;
	private $person;
	private $sentence_id;
	private $sentence_string;

	public function __construct($db, Person $person, int $min_line = NULL, int $max_line = NULL)
	{
		$this->db = $db;
		$this->person = $person;

		if (($min_line === NULL) && ($max_line === NULL)) {
			// If there is no specific sentence range ID wanted...
			// ...choose a random row from the DB
			$req = $db->query('SELECT * FROM phrases ORDER BY RAND() LIMIT 1');
			$res = $req->fetchrow();
		} else {
			$req = $db->query('SELECT * FROM phrases WHERE id BETWEEN ' . $min_line . ' AND ' . $max_line . ' ORDER BY RAND() LIMIT 1');
			$res = $req->fetchrow();
		}
		if ($res) {
			$this->sentence_id = $res[0];
			$sentence_string = $this->create($res, $person);
			$this->sentence_string = $this->correct($sentence_string, $person->getGender());
		} else {
			$this->sentence_id = "ERREUR, CETTE STRUCTURE DE PHRASE N'EST PAS DEFINIE";
			$this->sentence_string = "ERREUR, CETTE STRUCTURE DE PHRASE N'EST PAS DEFINIE";
		}
	}

	public function getId(): int
	{
		return $this->sentence_id;
	}

	public function getString(): string
	{
		return $this->sentence_string;
	}

	private function create(string $sentence_struc): string
	{
		// Get all tables names
		$req = $this->db->query('SHOW TABLES');
		$res = $req->fetchAll();
		$table_names = array();
		foreach ($res as $table) {
			$table_names[] = $res[0];
		}

		$sentence_string = '';
		$i = 1;
		while (isset($sentence_struc[$i])) { // on éxécute la boucle tant qu'on n'a pas une cellule vide
			if ($sentence_struc[$i] == "SELF") {
				// the word to add is the character name
				$string = $this->person->getName();
			} elseif ($sentence_struc[$i] == "SELFGATE") {
				// the word to add is the character hashtag
				$string = $this->person->getHashtag();
			} elseif (in_array($sentence_struc[$i], $table_names)) {
				// si il s'agit d'un code de mot parmi ceux dont le nombre et le genre doivent être déterminés
				$word = new Word($this->db, $sentence_struc[$i]); // on instancie le mot, choisi dans la table en question
				$string = $word->getString();
			} else { // si l'élément est inconnu, c'est que c'est un mot et pas un code !
				$string = $sentence_struc[$i];
			}
			$sentence_string = $this->addStringToSentence($sentence_string, $i, $string);
			$i++;
		};
		return $sentence_string;
	}

	private function addStringToSentence(string $sentence_string, int $i, string $string): string
	{
		if (($i == 0) || (substr($sentence_string, -3) == "“") || (substr($string, 0, 1) == ".") || (substr($string, 0, 1) == ",") || (substr($string, 0, 3) == "”")) { // pas d'espace avant si c'est le premier mot de la phrase, si le mot précédent se termine par un guillemet ouvrant, ou si le mot suivant est un point ou une virgule ou un guillemet fermant
			$sentence_string = $sentence_string . $string;
		} else {
			$sentence_string = $sentence_string . ' ' . $string;
		}
		return $sentence_string;
	}

	private function correct(string $sentence_string, string $gender): string
	{
		$correction_array = array(
			"( " => "(",
			" )" => ")",
			" ," => ",",
			" à le " => " au ",
			" à les " => " aux ",
			" de a" => " d’a",
			" de e" => " d’e",
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
			" de les " => " des ",
			" en la " => " en ",
			" en le " => " en ",
			" en les " => " en ",
			" la A" => " l’A",
			" la E" => " l’E",
			" la É" => " l’É",
			" la I" => " l’I",
			" la O" => " l’O",
			" la U" => " l’U",
			" le A" => " l’A",
			" le E" => " l’E",
			" le É" => " l’É",
			" le I" => " l’I",
			" le O" => " l’O",
			" le U" => " l’U",
			" le la " => " la ",
			" le le " => " le ",
			" le les " => " les ",
			" un l'" => " un ",
			" un l’" => " un ",
			" un la " => " une ",
			" un le " => " un ",
			" un les " => " des ",
			" son la " => " sa ",
			" son le " => " son ",
			" son l'" => " son ",
			" son l’" => " son ",
			" son les " => " ses ",
			" que a" => " qu’a",
			" que e" => " qu’e",
			" que i" => " qu’i",
			" que u" => " qu’u",
			" que A" => " qu’A",
			" que E" => " qu’E",
			" que I" => " qu’I",
			" que U" => " qu’U",
		);
		if ($gender == "f") {
			$correction_array = array_merge($correction_array, array(
				" il " => " elle ",
				" Il " => " Elle ",
				" il " => " elle ",
				"qu’il " => "qu'elle ",
				" né " => " née ",
				"Né " => "Née ",
				"iplômé " => "iplômée ",
				"marqué " => "marquée ",
				"obsédé " => "obsédée ",
				"l’intéressé " => " l’intéressée ",
			));
		}
		$sentence_string = strtr($sentence_string, $correction_array);
		if (substr($sentence_string, 0, 3) == '“' || substr($sentence_string, 0, 3) == '"') { // Uppercase the second char, if the first char is a double quote
			$second_char = substr($sentence_string, 3, 1); // Get the second char
			$second_char_caps = $this->frenchUcfirst($second_char); // Uppercase
			$sentence_string = substr_replace($sentence_string, $second_char_caps, 3, 1); // replace the second char
		} else {
			$sentence_string = $this->frenchUcfirst($sentence_string);
		}
		return $sentence_string;
	}

	private function frenchUcfirst(string $string): string
	{
		$strlen = mb_strlen($string, "utf8");
		$firstChar = mb_substr($string, 0, 1, "utf8");
		$then = mb_substr($string, 1, $strlen - 1, "utf8");
		return mb_strtoupper($firstChar, "utf8") . $then;
		return $string;
	}
}
