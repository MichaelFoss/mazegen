<?php

header('Content-Type: text/html; charset=utf-8');

require_once('../config.php');
require_once('../lib/maze.php');

if (isset($_POST['width']) && is_int((int)$_POST['width'] * 1) && is_int((int)$_POST['height'])) {
	$width = $_POST['width'];
	$height = $_POST['height'];
	$maze = new Maze($width, $height);
}

?><!doctype html>
<html>
<head>
	<script src="js/jquery-2.1.3.min.js"></script>
	<script src="js/jquery.touchSwipe.min.js"></script>
	<script src="js/mazegen.js"></script>
	<link rel="stylesheet" href="css/mazegen.css" />
</head>
<body>
	<form method="post"<?=isset($_POST['width']) ? ' class="hidden"' : ''; ?>>
		<h1>Maze Generator</h1>
		<div>
			<label for="width">Width:</label>
			<input type="number" id="width" name="width"<?=isset($width) ? ' value="' . $width . '"' : ''; ?> />
		</div>
		<div>
			<label for="height">Height:</label>
			<input type="number" id="height" name="height"<?=isset($height) ? ' value="' . $height . '"' : ''; ?> />
		</div>
		<div>
			<input type="submit" value="Generate!" />
		</div>
	</form>
	<div id="status">
		<div>
			Steps: <span id="steps">0</span>
		</div>
		<div>
			Points: <span id="points">0</span>
		</div>
		<div id="game-over" class="hidden">
			<h2>Game Over</h2>
			<div>
				Rating: <span id="rating">0</span>
			</div>
		</div>
	</div>
	<?php
	if (isset($maze)) {
		echo $maze->toHTML();
	}
	?>
</body>
</html>
