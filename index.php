<?php
header("Content-Security-Policy: default-src 'self'; img-src 'self'; style-src 'self'; script-src 'self'; frame-ancestors 'none'; base-uri 'none'; form-action 'self'");
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Steam Collection Mod IDs</title>
<link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
<div>
<h1>Generate a list or xml of MOD IDs from a Steam collection.</h1>
<form action="modlist.php" method="post">
<label>Do you want a list or a xml format?</label><br>
<input type="radio" id="list" name="option" value="list" checked>
<label for="list">List Format</label><br>
<input type="radio" id="xml" name="option" value="xml">
<label for="xml">XML Format</label>
<br>
<br>
<label for="link">Provide your Steam Collection Share Link below:</label>
<input type="text" id="link" name="link" placeholder="https://steamcommunity.com/sharedfiles/filedetails/?id=..." required maxlength="300"><br>
<input type="submit" value="Generate Mod IDs">
</form>
<h2>How to get your Collection Share Link?</h2>
<img src="collection.png" class="center" alt="Click the Share button on your Steam Collection page">
<img src="share.png" class="center" alt="Copy the link from the Share dialog">
</div>
</body>
</html>
