<?php

class GoogleSheet
{

	private $db;
	private $sheet_url;
	private $sheet_id;

	public function getSheetId()
	{
		return $this->sheet_id;
	}

	public function __construct(PDO $db, string $sheet_url)
	{
		$this->db = $db;
		$this->sheet_url = $sheet_url;
		preg_match("'\/spreadsheets\/d\/([a-zA-Z0-9-_]+)'", $this->sheet_url, $sheet_id); // extract the ID from the URL
		$this->sheet_id = $sheet_id[1];
	}

	public function buildAllTables(): void
	{
		// Build index table referencing the Google Sheet GIDs
		$this->buildTable('table_names', '0');
		// Get the GIDs from the table_names table
		$req = $this->db->query('SELECT * FROM table_names');
		$res = $req->fetchAll();
		// Build all other tables from the GIDs
		foreach ($res as $row) {
			$this->buildTable($row[1], $row[2]);
		}
	}

	public function buildTable(string $table_name, string $gid): void
	{
		$csvfile = fopen('https://docs.google.com/spreadsheet/pub?key=' . $this->sheet_id . '&output=csv&gid=' . $gid, 'r');

		if (!$csvfile) {
			echo ("Error reading a tab from Google Sheet!\n");
		} else {
			echo ("Building table '" . $table_name . "' from Google Sheet...\n");

			// Get the data from the CSV file
			fgetcsv($csvfile, 10000, ","); // Skip the first line
			while (($csv = fgetcsv($csvfile, 10000, ",")) !== FALSE) {
				$data[] = $csv;
			}
			fclose($csvfile);

			// Drop the table if it exists
			$this->db->query('DROP TABLE IF EXISTS `' . $table_name . '`');

			// Count the number of columns in the CSV file
			$columns_count = count($data[0]);

			// Build the SQL query string depending on the number of columns
			$query = 'CREATE TABLE `' . $table_name . '` (id INTEGER NOT NULL PRIMARY KEY';
			for ($i = 0; $i < $columns_count; $i++) {
				$query .= ', col' . $i . ' VARCHAR(255)';
			}
			$query .= ')';
			$this->db->query($query);

			// Add rows to the table
			foreach ($data as $row) {
				$query = 'INSERT INTO `' . $table_name . '` (';
				for ($i = 0; $i < $columns_count; $i++) {
					$query .= 'col' . $i . ', ';
				}
				$query = rtrim($query, ', ') . ') VALUES (';
				for ($i = 0; $i < $columns_count; $i++) {
					$query .= ':col' . $i . ', ';
				}
				$query = rtrim($query, ', ') . ')';
				$req = $this->db->prepare($query);
				for ($i = 0; $i < $columns_count; $i++) {
					$req->bindValue(':col' . $i, $row[$i]);
				}
				$req->execute();
			}

			echo ("DB table '" . $table_name . "' (re)-created from Google Sheet\n");

		}
	}

}
