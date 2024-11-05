<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8" />
</head>

<body>

	<form action="test.php" method="GET">

		<?php
		include("class.googlesheet.php");
		include("class.person.php");
		include("class.word.php");
		include("class.sentence.php");

		if (isset($_GET['sentence_id'])) {
			$sentence_min = $sentence_max = addslashes($_GET['sentence_id']);
		} else {
			$sentence_min = 5;
			$sentence_max = 67;
		}

		// Read .ini file
		$ini = parse_ini_file('config.ini');
	
		// Build the DB tables from Google Sheet
		$gsheet = new GoogleSheet($db, $ini['google_sheet_url']);

		// Force the refresh of the tables
		if (isset($_GET['refresh'])) {
			$gsheet->buildAllTables();
		}
	
		// Create the character
		$person = new Person($db);
		$person_pic_filename = $person->getPicture();

		// Create the fake news sentence
		$sentence = new Sentence($db, $person, $sentence_min, $sentence_max);
		$sentence_id = $sentence->getId();
		$sentence_string = $sentence->getString();
		?>

		<hr />

		<h3>Test de génération de rumeur de type ligne #<input type="number" name="sentence_id" value="<?php echo $sentence_id ?>" /> :</h3>
		<p><?php echo $sentence_string; ?></p>
		<input type="submit" value="re-tester avec cette ligne">

	</form>

	<form action="test.php" method="get">
		<input type="submit" value="re-tester au hasard">
	</form>

</body>

</html>