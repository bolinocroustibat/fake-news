<?php

class Word
{

	private $chosen_row;

	public function getString(): string
	{
		return $this->chosen_row[1];
	}

	public function __construct(PDO $db, string $table_name)
	{
		$req = $db->query('SELECT * FROM `' . $table_name . '` ORDER BY RANDOM() LIMIT 1');
		$this->chosen_row = $req->fetch();
	}
}
