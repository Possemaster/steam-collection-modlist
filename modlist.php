<html>
<head>
<link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
<div>
<?php
$input = $_POST["link"];
$option = $_POST["option"];
$file = file_get_contents($input);
$muster = '<a href="https:\/\/steamcommunity.com\/sharedfiles\/filedetails\/\?id=([0-9]+)">';

preg_match_all($muster, $file, $match);

$arr = array_unique(array_values($match[1]));
sort($arr);

if ($option == "list") {
	echo "<h1>Below are your mod IDs in " . $option . " format</h1>";
	echo '<button onclick="copyToClipboard()">Copy to Clipboard</button></br>';
	echo '<textarea style="width:100%" id="mods" name="mods" rows=50>';
	foreach($arr as $val) {
		echo $val . "&#13;&#10;";
}
echo "</textarea>";
}
else
{
	echo "<h1>Below are the mods in " . $option . " format.</h1>";
	echo '<h2>Modifying Your Sandbox_config.sbc</h2>';
	echo "<p>Once you've verified your server is stopped, it's now safe to open the Sandbox_config.sbc file with a text editor. There will be a lot of text here, so just hit Ctrl+F to open up the search function of your editor. Search for 'mods'. It should bring you to a line of code that looks like this:</p>";
	echo "<p><code>&lt;Mods /&gt;</code></p>";
	echo "<p>The first step is to open up this element so you can add a list of mods to it. To do that, replace &lt;Mods /&gt; with:</p>";
	echo "<p><code>&lt;Mods&gt;</code><br><code>&lt;/Mods&gt;</code></p>";
	echo "<p>Between these two elements, you can now add information you copy below.</p>";
	echo '<button onclick="copyToClipboard()">Copy to Clipboard</button></br>';
	echo '<textarea style="width:100%" id="mods" name="mods" rows=50>';
	foreach($arr as $val) {
		echo "&lt;ModItem&gt;&#13;&#10;";
		echo "&lt;Name&gt;" . $val . ".sbm&lt;/Name&gt;&#13;&#10;";
		echo "&lt;PublishedFileId&gt;" . $val . "&lt;/PublishedFileId&gt;&#13;&#10;";
		echo "&lt;/ModItem&gt;&#13;&#10;";
}
echo "</textarea>";
}

?>
</div>
<script>
function copyToClipboard() {
  var copyText = document.getElementById("mods");
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");
  alert("Copied the text to the clipboard.");
}
</script>
</body>
</html>