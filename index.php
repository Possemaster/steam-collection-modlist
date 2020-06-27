<html>
<head>
<link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
<div>
<h1>Generate a list or xml of MOD IDs from a Steam collection.</h1>
<form action="modlist.php" method="post">
<label>Do you want a list or a xml format?</label></br>
<input type="radio" id="list" name="option" value="list">
<label for="list">List Format</label></br>
<input type="radio" id="xml" name="option" value="xml">
<label for="female">XML Format</label>
</br>
</br>
<label for="link">Provide your Steam Collection Share Link below:</label>
<input type="text" id="link" name="link" placeholder="Steam Collection Share Link.."><br>
<input type="submit" value="Generate Mod IDs">
</form>
<h2>How to get your Collection Share Link?</h2>
<img src="collection.png" class="center"/>
<img src="share.png" class="center"/>
</div>
</body>
</html>