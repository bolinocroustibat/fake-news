<?php

class Person
{

	private $person_array; // Array with person name string and picture

	public function __construct(PDO $db)
	{
		$req = $db->query('SELECT * FROM PERSONNE ORDER BY RANDOM() LIMIT 1');
		$this->person_array = $req->fetch();
	}

	public function getArray(): array
	{
		return $this->person_array;
	}

	public function getName(): string
	{
		return $this->person_array[1];
	}

	public function getPicture(): string
	{
		return $this->person_array[2];
	}

	public function getHashtag(): string
	{
		return $this->person_array[3];
	}

	public function getGender(): string
	{
		return $this->person_array[4];
	}

}
