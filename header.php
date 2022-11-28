<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebisüsteem</title>
	<style>
		body {
			background-color: <?php echo $_SESSION["user_bg_color"]; ?>;
			color: <?php echo $_SESSION["user_txt_color"]; ?>
		}
	</style>
	<?php 
		if (isset($style_sheets) and !empty($style_sheets)) {
			foreach ($style_sheets as $style_sheet) {
				echo '<link rel="stylesheet" href="';
				echo $style_sheet;
				echo '">' ."\n";
			}
		}

		// javascripti jaoks
		// <script src="javascript/check_file_size.js" defer></script>

		if (isset($javascripts) and !empty($javascripts)) {
			foreach ($javascripts as $javascript) {
				echo '<script src="';
				echo $javascript;
				echo '" defer></script>' ."\n";
			}
		}
	?>
</head>
<body>
	<img src="pics/vp_banner_gs.png" alt="b�nner">
	<h1>Veebisüsteem</h1>
	<p>See leht on loodud õppetöö raames ja ei sisalda tõsiseltvõetavat sisu!</p>
	<p>Õppetöö toimus <a href="https://www.tlu.ee" target="_blank">Tallinna Ülikoolis</a> Digitehnoloogiate instituudis.</p>
