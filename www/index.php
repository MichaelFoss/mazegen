<?php

header('Content-Type: text/html; charset=utf-8');

require_once('../config.php');
require_once('../lib/maze.php');

if (isset($_POST['width']) && is_int((int)$_POST['width'] * 1) && is_int((int)$_POST['height'])) {
	$width = $_POST['width'];
	$height = $_POST['height'];
	$maze = new Maze($width, $height);
}
else {
	$width = DEFAULT_MAZE_WIDTH;
	$height = DEFAULT_MAZE_HEIGHT;
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
	<h1>Maze Generator</h1>
	<form method="post"<?=isset($_POST['width']) ? ' class="hidden"' : ''; ?>>
		<?php foreach (array('width', 'height') as $dimension): ?>
			<div>
				<label for="<?=$dimension; ?>"><?=ucwords($dimension); ?>:</label>
				<select id="<?=$dimension; ?>" name="<?=$dimension; ?>">
				<?php for ($i = 1; $i <= 15; $i++): ?>
					<option value="<?=$i; ?>"<?=$$dimension == $i ? ' selected="selected"' : ''; ?>><?=$i; ?></option>
				<?php endfor; ?>
				</select>
			</div>
		<?php endforeach; ?>
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
	<nav>
		<div>
			<button id="move-up">&#x2191;</button>
		</div>
		<div>
			<button id="move-left">&#x2190;</button>
			<button id="move-right">&#x2192;</button>
		</div>
		<div>
			<button id="move-down">&#x2193;</button>
		</div>
	</nav>
</body>
</html>
