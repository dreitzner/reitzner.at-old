<?php
	header("Link: </css/201801301558.min.css>; rel=preload; as=style, </js/201801301551.min.js>; rel=preload; as=script, <https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js>; rel=preload; as=script, </img/wappenNoShadow.svg>; rel=preload; as=image, </img/mail.svg>; rel=preload; as=image");
	require 'functions.php';
	#get where we are
	$title = str_replace("/", "", $_SERVER['REQUEST_URI']);
	$title = ($title == "")?"Home":$title;
	$menu = getMenu();
	$section = getContent($title);
	$headerImg = file_exists("img/header/$title.jpg")? "/img/header/$title.jpg" : "/img/header/Home.jpg";
?>
<!DOCTYPE html>
<html lang="de">

<head>
	<title>reitzner.at - <?php echo $title;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="<?php echo trim( strip_tags($section) ); ?>">
	<meta name="keywords" content="Domenik Reitzner, Ing. Domenik Reitzner, Webdevelopment, Musik, Love &amp; Grace, FIREworship, <?php echo $title;?>">
	<meta name="author" content="Domenik Reitzner">
	<meta name="theme-color" content="#0C3D87" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<!-- OG -->
	<meta property="og:title" content="reitzner.at - <?php echo $title;?>" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="https://reitzner.at/<?php echo $title;?>/" />
	<meta property="og:image" content="https://reitzner.at<?php echo $headerImg?>" />
	<meta property="og:description" content="<?php echo trim( strip_tags($section) ); ?>" />
	<!-- Icon-->
	<link rel="icon" sizes="192x192" href="/img/favicon.png" type="image/png" />
	<script type="application/ld+json">
		{
			"@context": "http://schema.org",
			"@type": "Organization",
			"url": "https://reitzner.at/",
			"address": {
				"@type": "PostalAddress",
				"addressLocality": "Vienna, Austria",
				"postalCode": "A-1100",
				"streetAddress": "Per-Albin-Hanssonstraße 35"
			},
			"email": "domenik@reitzner.at",
			"name": "reitzner.at",
			"logo": "https://reitzner.at/img/favicon.png"
		}
	</script>
</head>

<body>
	<nav>
		<div id="mobileMenu"></div>
		<div id="logo">
			<img id="wappen" src="/img/wappenNoShadow.svg" height="48" alt="Wappen"/>
		</div>
		<?php
			echo $menu;
		?>
	</nav>
	<main>
		<noscript>Leider unterstützt Ihr Browser kein JavaScript oder Sie haben es deaktiviert.
			<br> Diese Seite funktioniert nicht so gut ohne :-)</noscript>
		<header>
			<img src="<?php echo $headerImg?>" alt="<?php echo $title;?>">
			<div id="title">reitzner.at - <?php echo $title;?></div>
		</header>
		<section>
			<?php
				echo $section;
			?>
		</section>

	</main>
	<a id="mail" href="mailto:domenik@reitzner.at?subject=reitner.at-Mail" target="parent">
		<img src="/img/mail.svg" alt="E-Mail" />
	</a>
	<div id="overlay"></div>
	<div id="css-features">	
		<script>
			function closeCssFeatures(){
				document.getElementById('css-features').style.display = "none";
			}
		</script>
		Ihr Browser unterstützt nicht alle aktuellen CSS &amp; JS Funktionen.
		<br> Bitte steigen Sie auf einen modernen Browser wie
		<a href="https://www.google.com/chrome/" target="_blank">Google Chrome</a> oder
		<a href="https://www.mozilla.org/en-US/firefox/new/" target="blank">Mozilla Firefox</a> um.<br>
		<span onclick="closeCssFeatures()" style="text-decoration:underline;cursor:pointer;color:blue;">[x]close</span>
	</div>
	<script async src="/js/201801301551.min.js"></script>
	<link rel="stylesheet" href="/css/201801301558.min.css"/>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
	<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>
	<script>
	WebFont.load({
		google: {
		families: ['Roboto']
		}
	});
	</script>
</body>

</html>