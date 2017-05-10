<meta charset="UTF-8" />

<title>Le générateur de fake news</title>

<meta name="Description" content="<?php if(isset($sentence) && $sentence!=''){echo addslashes($sentence);}else{echo "Engendrez des rumeurs foireuses d'extrême-droite !";} ?>" />
<meta name="Generator" content="Notepad++" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">	

<meta property="og:locale" content="fr_FR">
<meta property="og:site_name" content="Le générateur de fake news" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?php echo($actual_link);?>" />
<meta property="og:title" content='<?php if(isset($sentence) && $sentence!=''){echo addslashes($sentence);}else{echo 'Le générateur de fake news';} ?>' />
<meta property="og:description" content='<?php if(isset($sentence) && $sentence!=''){echo addslashes($sentence);}else{echo 'Engendrez des rumeurs foireuses d’extrême-droite !';} ?>' />
<meta property="og:image" content='<?php if(isset($picture) && $picture!=''){echo 'http://adriencarpentier.com/fake-news/photos/'.$picture;}else{echo 'http://www.adriencarpentier.com/fake-news/style/background_470x314.jpg';} ?>' />
<meta property="og:image:width" content="470" />
<meta property="og:image:height" content="314" />
<meta property="fb:app_id" content="1542429205992863" />

<link rel="apple-touch-icon" sizes="180x180" href="style/favicons/apple-touch-icon.png">
<link rel="icon" type="image/png" href="style/favicons/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="style/favicons/favicon-16x16.png" sizes="16x16">
<link rel="manifest" href="style/favicons/manifest.json">
<link rel="mask-icon" href="style/favicons/safari-pinned-tab.svg" color="#5bbad5">
<link rel="shortcut icon" href="style/favicons/favicon.ico">
<meta name="msapplication-config" content="style/favicons/browserconfig.xml">
<meta name="theme-color" content="#ffffff">

<link href="style/normalize.css" rel="stylesheet" type="text/css" media="all">
<link href="style/style.css" rel="stylesheet" type="text/css" media="all">

<link href="https://fonts.googleapis.com/css?family=Anton" rel="stylesheet"> 

<script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>