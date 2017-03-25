﻿<!DOCTYPE html>
<html lang="fr">

<?php
$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
// RECUPERATION DES DONNEES EN CAS DE CHARGEMENT DE LA PAGE AVEC HASH
if(isset($_GET['hash']) && $_GET['hash']!='') { // Si on recoit un hash
	$hash = $_GET['hash'];
	$generated_projects_json = file_get_contents("generated_projects_FR.json"); //charge le fichier qui contient l'objet JSON
	$generated_projects_table = json_decode($generated_projects_json,true); // transforme l'objet JSON en tableau PHP
	foreach ($generated_projects_table as $row_obj) { // parcourt chaque ligne du tableau PHP
		$row = json_decode($row_obj,true); // tranforme l'objet-ligne en tableau
		if ($hash == $row["hash"]){
			$sentence = $row["sentence"];
			$picture = $row["picture"];
		}
	}
}

?>

<head>
<?php include("header.php"); ?>
</head>
	
	<script>
		function generate_data() { // si le bouton de génération a été cliqué
			$(".project, .site").hide("fade");
			$(".image").hide("fade", function() { // when the image is also hidden, launch the ajax call
				$.ajax({
					type: "POST",		
					url: 'ajax_generate_FR.php',
					success: function (data) {
						var dataObj = jQuery.parseJSON(data);
						hash = dataObj.hash;
						sentence = dataObj.sentence;
						picture = dataObj.picture;
						$(".image").attr("src","./photos/"+picture);
						$(".project").html(sentence);
						history.pushState(sentence, sentence, hash+'.html'); // change l'URL dynamiquement
						if ((typeof sentence !== 'undefined') && (typeof hash !== 'undefined')) { // si les variables existent
							document.getElementById('ShareFacebook').href = 'http://www.facebook.com/sharer/sharer.php?u='+window.location.href; // met à jour le lien de partage Facebook
							document.getElementById('ShareTwitter').href = 'http://twitter.com/?status='+sentence+' via adriencarpentier.com/post-verites'; // met à jour le lien de partage Twitter
						}
					}
				})
				$(".project-wrapper").css("visibility", "visible");
				$(".share-wrapper").css("visibility", "visible");
			});
			$(".image, .project, .site").show("fade");
		}
		function read_data(sentence, picture) { // si on a reçu les données grâce au hash dans l'URL, utilisée en argument du body onload
			$('.project-wrapper').css('visibility', 'visible');
			$('.share-wrapper').css('visibility', 'visible');
			$('.image').attr('src','./photos/'+picture);
			$('.project').html(sentence);
		}
	</script>
</head>

<body<?php if(isset($sentence) && $sentence!='' && isset($picture) && $picture!=''){echo ' onload="read_data(\''.addslashes($sentence).'\',\''.addslashes($picture).'\')"';} ?>>

	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		ga('create', 'UA-68594064-1', 'auto');
		ga('send', 'pageview');
	</script>
		
	<div id="main-wrapper">

		<h1>Le générateur de<div class="big_h1">post-vérités</div></h1>

		<input type="button" onClick="generate_data()" value="engendrer une rumeur">
		<div class="project-wrapper" style="visibility:hidden;">
			<img class="image" />
			<div class="project"></div>
			<div class="site">adriencarpentier.com</div>
		</div>
		<div class="share-wrapper">
			<a id="ShareFacebook" href="http://www.facebook.com/sharer/sharer.php<?php if(isset($hash) && $hash!=''){echo '?u='.$actual_link;}?>">Partager sur Facebook</a><a id="ShareTwitter" href="http://twitter.com/?status=<?php if(isset($sentence) && $sentence!=''){echo $sentence.', via adriencarpentier.com/post-verites';}?>">Partager sur Twitter</a>
		</div>	

	</div>
	
	<a href="liste.html" id="list-link">toutes les rumeurs engendrées</a>

</body>

</html>