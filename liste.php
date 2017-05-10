<!DOCTYPE html>
<html lang="fr">

<head>
<?php
$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
include("header.php");

if (!empty($_GET['page'])){
	$page = $_GET['page'];
	$first_char = (200000*$page)-200000;
	$last_char = 200000*$page;
} else {
	$first_char = 0;
	$last_char = 200000;
}
?>
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
		<h1>Le générateur de<div class="big_h1">fake news</div></h1>
		
		<h2>toutes les rumeurs</h2>
		
			<div id="pages">
				<?php
					// PAGINATION
					include("connex.php");	
					$bdd = database_connect();
					$req = $bdd->query("SELECT COUNT(*) FROM fakenews");
					$rep = $req->fetch();
					$total_news = $rep[0];
					$nb_news_page = 500;
					$total_pages = round($total_news / $nb_news_page, 0);
					$page = 1;
					if(!empty($_GET['page'])) {
						$page = addslashes($_GET['page']);
					}
					// BOUTONS DE NAVIGATION
					if ($page > 1) { echo '<a href="liste.html?page='.($page-1).'">rumeurs suivantes</a> - ';	}
					if ($page < $total_pages) { echo '<a href="liste.html?page='.($page+1).'">rumeurs précédentes</a>';	}
				?>
			</div>
		
		
		<ul>
			<?php
			// AFFICHAGE DES RUMEURS
			$min = ($page-1)*$nb_news_page;
			$req = $bdd->query("SELECT id,hash,sentence,pic_filename FROM fakenews ORDER BY id DESC LIMIT $min,$nb_news_page");
			$rep = $req->fetchAll();
			foreach($rep as $key=>$obj) { // parcourt chaque ligne du tableau PHP dans l'ordre inverse
				if (!empty($rep[$key])) {
					$id= $rep[$key]['id'];
					$hash= $rep[$key]['hash'];
					$sentence= $rep[$key]['sentence'];
					$picture= $rep[$key]['pic_filename'];
					echo ('<li>Rumeur n°'.($id).' :<a href="'.$hash.'.html"><div class="project-wrapper"><img class="image" src="./photos/'.$picture.'"/><div class="project">'.$sentence.'</div><div class="site">adriencarpentier.com</div></a></li>');
				}
			}
			?>
		</ul>

		</div>
	
	</div>

</body>

</html>