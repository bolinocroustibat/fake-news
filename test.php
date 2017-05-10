<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8" />
</head>

<body>

<form action="test.php" method="get">

	<?php
	include("class.googlesheet.php");
	include("class.person.php");
	include("class.word.php");
	include("class.sentence.php");
	if (isset($_GET['sentence_id'])) {
		$sentence_min = $sentence_max = addslashes($_GET['sentence_id']);
	}
	else {
		$sentence_min = 5;
		$sentence_max = 67;
	}
	// CREATION DE LA TABLE D'INDEX ET DU CACHE S'IL N'EXISTE PAS
	$gsheet = new GoogleSheet("https://docs.google.com/spreadsheets/d/1sVkvvJCLckEJslV4kS6io0Y9hGLELZnnJd87Kkejces/edit#gid=0");
	$gid_table = $gsheet->getGidTable();
	if (isset($_GET['refresh'])) { // Pour forcer le rafraîchissement du cache
		$gsheet->BuildAllCache();
	}
	/* CREATION DU PERSONNAGE */
	$person = new Person();
	$person_array = $person->getPersonArray();
	$person_pic_filename = $person->getPersonPicture();
	/* CREATION DE LA RUMEUR  */
	$sentence_obj = new Sentence($gid_table,"phrases",$person_array,$sentence_min,$sentence_max);
	$sentence_id = $sentence_obj->getSentenceId();
	$sentence_string = $sentence_obj->getSentenceString();
	?>
	
	<hr/>
	
	<h3>Test de génération de rumeur de type ligne #<input type="number" name="sentence_id" value ="<?php echo $sentence_id ?>"/> :</h3>
	<p><?php echo $sentence_string; ?><p/>
	
	<input type="submit" value="re-tester avec cette ligne">

</form>  

<form action="test.php" method="get">
	<input type="submit" value="re-tester au hasard">
</form>

</body>

</html>