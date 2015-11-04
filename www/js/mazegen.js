window.maze = {
	x: -1,
	y: -1,
	width: -1,
	height: -1,
	points: 0,
	steps: 0,
	treasures: 0,
	init: function() {
		// Grab main DOM element
		var $maze = $('#maze');
		// Initialize data
		window.maze.x = $maze.data('x');
		window.maze.y = $maze.data('y');
		window.maze.width = $maze.data('width');
		window.maze.height = $maze.data('height');
		// Light up all the pretty items
		$('div[data-treasure]').each(function() {
			var $this = $(this);
			$this.css('background-image', 'url(\'img/treasure/icon/' + $this.data('treasure').id + '.png\')');
		});
		// Bind arrow keys for movement
		$(document).keydown(window.maze.captureKey);
		window.maze.treasures = $maze.find('div[data-treasure]').length;
		// Bind arrow buttons for movement
		$('nav > div > button').on('click', window.maze.captureMoveButton);
		// Bind swiping for movement
		$('body').swipe({
			swipe: function(event, direction) {
				event.preventDefault();
				switch (direction) {
					case 'left':
						window.maze.moveLeft();
						break;
					case 'right':
						window.maze.moveRight();
						break;
					case 'up':
						window.maze.moveUp();
						break;
					case 'down':
						window.maze.moveDown();
						break;
				}
			}
		});
	},

	captureMoveButton: function(e) {
		switch ($(this).attr('id')) {
			case 'move-left': // left
				window.maze.moveLeft();
				break;

			case 'move-up': // up
				window.maze.moveUp();
				break;

			case 'move-right': // right
				window.maze.moveRight();
				break;

			case 'move-down': // down
				window.maze.moveDown();
				break;

			default:
				return; // exit this handler for other keys
		}
	},

	captureKey: function(e) {
		switch(e.which) {
			case 37: // left
				window.maze.moveLeft();
				break;

			case 38: // up
				window.maze.moveUp();
				break;

			case 39: // right
				window.maze.moveRight();
				break;

			case 40: // down
				window.maze.moveDown();
				break;

			default:
				return; // exit this handler for other keys
		}
		e.preventDefault(); // prevent the default action (scroll / move caret)
	},

	updateStatus: function() {
		$('#steps').text(window.maze.steps);
		$('#points').text(window.maze.points);
	},

	step: function() {
		window.maze.steps++;
		var $room = $('.room-' + window.maze.x + '-' + window.maze.y);
		var treasureData = $room.data('treasure');
		if (treasureData) {
			window.maze.points += (1 * treasureData.points);
			$room.data('treasure', null);
			$room.css('background-image', '');
			window.maze.treasures--;
			if (window.maze.treasures == 0) {
				$('form').removeClass('hidden');
				$('#game-over').removeClass('hidden');
				var rating = Math.ceil(window.maze.points / window.maze.steps * 100);
				if (rating < 40) {
					rating += ' Try Again :-(';
				}
				else if (rating < 70) {
					rating += '';
				}
				else if (rating < 80) {
					rating += ' Good! :-)';
				}
				else if (rating < 90) {
					rating += ' Great! :-D';
				}
				else if (rating < 100) {
					rating += ' Excellent! ╚(▲_▲)╝';
				}
				else {
					rating += ' Amazing!!! ( ͡° ͜ʖ ͡°)';
				}
				$('#rating').text(rating);
			}
		}
		window.maze.updateStatus();
	},

	moveUp: function() {
		if (window.maze.treasures == 0) {
			return;
		}
		var $room = $('.room-' + window.maze.x + '-' + window.maze.y);
		if (window.maze.y >= 1 && $room.data('exits').indexOf('n') !== -1) {
			$room.removeClass('position');
			window.maze.y--;
			$('.room-' + window.maze.x + '-' + window.maze.y).addClass('position');
			window.maze.step();
		}
	},

	moveDown: function() {
		if (window.maze.treasures == 0) {
			return;
		}
		var $room = $('.room-' + window.maze.x + '-' + window.maze.y);
		if (window.maze.y + 1 < window.maze.height && $room.data('exits').indexOf('s') !== -1) {
			$room.removeClass('position');
			window.maze.y++;
			$('.room-' + window.maze.x + '-' + window.maze.y).addClass('position');
			window.maze.step();
		}
	},

	moveLeft: function() {
		if (window.maze.treasures == 0) {
			return;
		}
		var $room = $('.room-' + window.maze.x + '-' + window.maze.y);
		if (window.maze.x >= 1 && $room.data('exits').indexOf('w') !== -1) {
			$room.removeClass('position');
			window.maze.x--;
			$('.room-' + window.maze.x + '-' + window.maze.y).addClass('position');
			window.maze.step();
		}
	},

	moveRight: function() {
		if (window.maze.treasures == 0) {
			return;
		}
		var $room = $('.room-' + window.maze.x + '-' + window.maze.y);
		if (window.maze.x + 1 < window.maze.width && $room.data('exits').indexOf('e') !== -1) {
			$room.removeClass('position');
			window.maze.x++;
			$('.room-' + window.maze.x + '-' + window.maze.y).addClass('position');
			window.maze.step();
		}
	}
};

$(function() {
	window.maze.init();
});
