<!DOCTYPE html>
<html lang="fr">

<head>
<?php include("header.php"); ?>
</head>

<body onload='$(".project-wrapper").css("visibility", "visible");'>

	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-2981674-13', 'auto');
	  ga('send', 'pageview');

	</script>

	<div id="main-wrapper">

		<!-- <h2>Plus aucune chance de voir le financement de son projet refusé, grâce à...</h2> -->
		<h1>Le générateur de<div class="big_h1">post-vérités</div></h1>
		
		<h2>toutes les rumeurs</h2>

		<ul>
			<?php		
			$generated_projects_json = file_get_contents("generated_projects_FR.json"); //charge le fichier qui contient l'objet JSON
			$generated_projects_table = array_reverse(json_decode($generated_projects_json,true)); // transforme l'objet JSON en tableau PHP, qu'on met à l'envers (chronologique)
			$total_nb = count($generated_projects_table); //nb d'entrées dans le tableau
			$nb=0;
			foreach ($generated_projects_table as $row_obj) { // parcourt chaque ligne du tableau PHP
				$row = json_decode($row_obj,true); // tranforme l'objet-ligne en tableau
				$hash = $row["hash"];
				$picture = $row["picture"];
				$sentence = $row["sentence"];
				echo ('<li>Rumeur n°'.($total_nb-$nb).' :<a href="'.$hash.'.html"><div class="project-wrapper"><img class="image" src="./photos/'.$picture.'"/><div class="project">'.$sentence.'</div><div class="site">adriencarpentier.com</div></a></li>');
				$nb++;
			}
			?>
		</ul>

		</div>
	
	</div>

</body>

</html>