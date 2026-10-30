<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');
header("Content-Security-Policy: default-src 'self'; img-src 'self'; style-src 'self'; script-src 'self'; frame-ancestors 'none'; base-uri 'none'; form-action 'self'");
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');

const STEAM_API = 'https://api.steampowered.com/ISteamRemoteStorage/GetCollectionDetails/v1/';
const MAX_DEPTH = 3; // how deep nested collections are followed

function e(string $s): string
{
	return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Accepts a Steam share link or a bare ID and returns the collection ID, or null if invalid.
 * Only steamcommunity.com links are accepted; the server never fetches the user-supplied URL.
 */
function parseCollectionId(string $input): ?string
{
	$input = trim($input);
	if (preg_match('/^\d{1,20}$/', $input)) {
		return $input;
	}
	$parts = parse_url($input);
	if (!$parts || !in_array(strtolower($parts['host'] ?? ''), ['steamcommunity.com', 'www.steamcommunity.com'], true)) {
		return null;
	}
	parse_str($parts['query'] ?? '', $query);
	$id = $query['id'] ?? '';
	return is_string($id) && preg_match('/^\d{1,20}$/', $id) ? $id : null;
}

/** POSTs to the Steam API and returns the collectiondetails array, or null on failure. */
function fetchCollectionDetails(array $ids): ?array
{
	$fields = ['collectioncount' => count($ids)];
	foreach (array_values($ids) as $i => $id) {
		$fields["publishedfileids[$i]"] = $id;
	}
	$body = http_build_query($fields);

	if (function_exists('curl_init')) {
		$ch = curl_init(STEAM_API);
		curl_setopt_array($ch, [
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $body,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT        => 10,
			CURLOPT_PROTOCOLS      => CURLPROTO_HTTPS,
		]);
		$response = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
		curl_close($ch);
	} else {
		$context = stream_context_create(['http' => [
			'method'        => 'POST',
			'header'        => 'Content-Type: application/x-www-form-urlencoded',
			'content'       => $body,
			'timeout'       => 10,
			'ignore_errors' => true,
		]]);
		$response = @file_get_contents(STEAM_API, false, $context);
		$status = isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m) ? (int)$m[1] : 0;
	}

	if (!is_string($response) || $status !== 200) {
		return null;
	}
	$data = json_decode($response, true);
	return $data['response']['collectiondetails'] ?? null;
}

/**
 * Returns the unique mod IDs in a collection, following nested collections.
 * Returns null if the API call failed, or an empty array if the ID is not a valid collection.
 */
function getModIds(string $collectionId): ?array
{
	$mods = [];
	$seen = [$collectionId => true];
	$pending = [$collectionId];

	for ($depth = 0; $pending && $depth < MAX_DEPTH; $depth++) {
		$details = fetchCollectionDetails($pending);
		if ($details === null) {
			return null;
		}
		$pending = [];
		foreach ($details as $collection) {
			foreach ($collection['children'] ?? [] as $child) {
				$id = (string)($child['publishedfileid'] ?? '');
				if (!preg_match('/^\d+$/', $id) || isset($seen[$id])) {
					continue;
				}
				$seen[$id] = true;
				// filetype 0 = workshop item, 2 = nested collection
				if ((int)($child['filetype'] ?? 0) === 2) {
					$pending[] = $id;
				} else {
					$mods[] = $id;
				}
			}
		}
	}

	sort($mods, SORT_NUMERIC);
	return $mods;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: index.php', true, 303);
	exit;
}

$option = ($_POST['option'] ?? '') === 'xml' ? 'xml' : 'list';
$collectionId = parseCollectionId((string)($_POST['link'] ?? ''));
$error = null;
$mods = [];

if ($collectionId === null) {
	$error = 'That does not look like a Steam Collection share link. It should look like https://steamcommunity.com/sharedfiles/filedetails/?id=1234567890';
} else {
	$result = getModIds($collectionId);
	if ($result === null) {
		$error = 'Could not reach the Steam API. Please try again later.';
	} elseif (!$result) {
		$error = 'No mods found. Make sure the link points to a public Steam Collection.';
	} else {
		$mods = $result;
	}
}

if ($option === 'list') {
	$output = implode("\r\n", $mods);
} else {
	$output = '';
	foreach ($mods as $id) {
		$output .= "<ModItem>\r\n<Name>$id.sbm</Name>\r\n<PublishedFileId>$id</PublishedFileId>\r\n</ModItem>\r\n";
	}
}
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
<?php if ($error !== null): ?>
	<h1>Something went wrong</h1>
	<p class="error"><?= e($error) ?></p>
	<p><a href="index.php">Go back</a></p>
<?php else: ?>
	<?php if ($option === 'list'): ?>
		<h1>Below are your mod IDs in list format</h1>
	<?php else: ?>
		<h1>Below are the mods in xml format.</h1>
		<h2>Modifying Your Sandbox_config.sbc</h2>
		<p>Once you've verified your server is stopped, it's now safe to open the Sandbox_config.sbc file with a text editor. There will be a lot of text here, so just hit Ctrl+F to open up the search function of your editor. Search for 'mods'. It should bring you to a line of code that looks like this:</p>
		<p><code>&lt;Mods /&gt;</code></p>
		<p>The first step is to open up this element so you can add a list of mods to it. To do that, replace &lt;Mods /&gt; with:</p>
		<p><code>&lt;Mods&gt;</code><br><code>&lt;/Mods&gt;</code></p>
		<p>Between these two elements, you can now add information you copy below.</p>
	<?php endif; ?>
	<p><?= count($mods) ?> mods found.</p>
	<button type="button" id="copy">Copy to Clipboard</button><br>
	<textarea id="mods" name="mods" rows="50" readonly><?= e($output) ?></textarea>
	<p><a href="index.php">Convert another collection</a></p>
<?php endif; ?>
</div>
<script src="script.js"></script>
</body>
</html>
