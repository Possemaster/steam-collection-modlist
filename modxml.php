<html>
<head>
<link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
<div>
<h2>Below are your mod IDs</h2>
<?php
$input = $_POST["link"];
$file = file_get_contents($input);
$muster = '<a href="https:\/\/steamcommunity.com\/sharedfiles\/filedetails\/\?id=([0-9]+)">';

preg_match_all($muster, $file, $match);

$arr = array_unique(array_values($match[1]));
sort($arr);

foreach($arr as $val) {
	print "\<Name\>" . $val . "\.sbm\<\/Name\></br>";
	print "\<PublishedFileId\>" . $val . "\<\/PublishedFileId\></br>";
}

?>
</div>
</body>
</html>