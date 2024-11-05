<?php

class Sentence
{

	private $db;
	private $person;
	private $sentence_id;
	private $sentence_string;

	public function __construct(PDO $db, Person $person, int $min_line = NULL, int $max_line = NULL)
	{
		$this->db = $db;
		$this->person = $person;

		if (($min_line === NULL) && ($max_line === NULL)) {
			// If there is no specific sentence range ID wanted...
			// ...choose a random row from the DB
			$req = $db->query('SELECT * FROM phrases ORDER BY RANDOM() LIMIT 1');
			$res = $req->fetch();
		} else {
			$req = $db->query('SELECT * FROM phrases WHERE id BETWEEN ' . $min_line . ' AND ' . $max_line . ' ORDER BY RANDOM() LIMIT 1');
			$res = $req->fetch();
		}
		if ($res) {
			$this->sentence_id = $res[0];
			$sentence_string = $this->create($res);
			$this->sentence_string = $this->correct($sentence_string);
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

	private function create(array $sentence_struc): string
	{
		// Get all tables names
		$req = $this->db->query('SELECT col0 FROM table_names');
		$res = $req->fetchAll();
		$table_names = array();
		foreach ($res as $row) {
			$table_names[] = $row[0];
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

	private function correct(string $sentence_string): string
	{
		// Strip spaces at the beginning and end of the sentence
		$sentence_string = trim($sentence_string);

		// Replace multiple spaces by a single space
		$sentence_string = preg_replace('/\s+/', ' ', $sentence_string);

		// Remove spaces before punctuation
		$sentence_string = preg_replace('/\s([.,)])/u', '$1', $sentence_string);

		// Remove spaces after punctuation
		$sentence_string = preg_replace('/([(])\s/u', '$1', $sentence_string);

		$correction_array = array(
			" à le " => " au ",
			" à les " => " aux ",
			" de de " => " de ",
			" de des " => " des ",
			" de le " => " du ",
			" de les " => " des ",
			" en la " => " en ",
			" en le " => " en ",
			" en les " => " en ",
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
		);

		if ($this->person->getGender() == "f") {
			$correction_array = array_merge($correction_array, array(
				" il " => " elle ",
				" Il " => " Elle ",
				"qu’il " => "qu'elle ",
				"l’intéressé " => " l’intéressée ",
				" impliqué " => " impliquée ",
				" concerné " => " concernée ",
				" soupçonné " => " soupçonnée ",
				" suspecté " => " suspectée ",
				" soutenu " => " soutenue ",
				"cocu" => "cocue",
			));
		}

		// Apply replacements
		$sentence_string = strtr($sentence_string, $correction_array);

		// Handle replacements involving vowels
		$sentence_string = preg_replace_callback('/\b(de|que|la|le) ([aâeêéèiîoôuyAÂEÊÉÈIÎOUY])/', function ($matches) {
			return substr($matches[1], 0, -1) . '’' . $matches[2];
		}, $sentence_string);

		// Uppercase the first char
		if (in_array(substr($sentence_string, 0, 3), ['“', '"'])) {
			// Uppercase the second char, if the first char is a double quote
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
