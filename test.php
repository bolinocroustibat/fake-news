<meta charset="UTF-8" />

<title>Générateur de post-vérités</title>


<?php


		$sentence_string = "“les Françaises seront juges” aurait dit Jacques Cheminade à propos de ses emplois fictifs";

		if (substr($sentence_string, 0, 3) == "“"){ // if the first char is a double quote
			$second_char = substr($sentence_string, 3, 1);
			$second_char_caps = ucfirst($second_char);
			$sentence_string = substr_replace($sentence_string,$second_char_caps, 3, 1); // replace the second char
		}
		
		echo $sentence_string;

?>