<?php

require_once('direction.php');
require_once('room.php');
require_once('coordinates.php');

/**
 * Maze class, used to generate mazes.
 **/
class Maze {
	/**
	 * A grid of rooms.
	 * 
	 * @var Room[][]
	 **/
	protected $rooms;
	/**
	 * The starting position in the maze.
	 *
	 * @var Coordinates
	 */
	protected $position;
	/**
	 * The width of the maze.
	 *
	 * @var int
	 */
	protected $width;
	/**
	 * The height of the maze.
	 *
	 * @var int
	 */
	protected $height;

	/**
	 * Opens or closes a room's door.
	 *
	 * @param int $x
	 * @param int $y
	 * @param int $direction Direction constant
	 * @param bool $isExit
	 * @return bool
	 */
	public function setExit($x, $y, $direction, $isExit) {
		// Sanity check going into the void
		if (
			$direction == Direction::NORTH && !$y ||
			$direction == Direction::WEST && !$x ||
			$direction == Direction::SOUTH && $y >= $this->height ||
			$direction == Direction::EAST && $x >= $this->width
		) {
			return false;
		}
		// Set the exit
		$this->rooms[$x][$y]->setExit($direction, $isExit);
		// Set the adjacent room's exit
		switch ($direction) {
			case Direction::NORTH:
				$this->rooms[$x][$y - 1]->setExit(Direction::SOUTH, $isExit);
				break;
			case Direction::SOUTH:
				$this->rooms[$x][$y + 1]->setExit(Direction::NORTH, $isExit);
				break;
			case Direction::EAST:
				$this->rooms[$x + 1][$y]->setExit(Direction::WEST, $isExit);
				break;
			case Direction::WEST:
				$this->rooms[$x - 1][$y]->setExit(Direction::EAST, $isExit);
				break;
			default:
				return false;
		}
		return true;
	}

	/**
	 * Generates a maze;
	 * must be at least 2x2.
	 */
	protected function generate() {
		// Find a starting point
		$this->position = new Coordinates(rand(0, $this->width - 1), rand(0, $this->height - 1));

		// Generate the grid, completely walled-off
		$this->rooms = array();
		for ($x = 0; $x < $this->width; $x++) {
			$this->rooms[$x] = array();
			for ($y = 0; $y < $this->height; $y++) {
				$this->rooms[$x][$y] = new Room();
				if (
					// No treasures at your feet!
					($this->position->getX() != $x || $this->position->getY() != $y)
					// Make sure you have a treasure
					&& Treasure::hasTreasure()
				) {
					$this->rooms[$x][$y]->setTreasure(Treasure::get());
				}
			}
		}

		// Algorithmically generate the maze
		$visited = array();
		for ($x = 0; $x < $this->width; $x++) {
			$visited[$x] = array();
			for ($y = 0; $y < $this->height; $y++) {
				$visited[$x][$y] = false;
			}
		}
		$open = array($this->position);
		$visited[$this->position->getX()][$this->position->getY()] = true;
		while (count($open)) {
			// Get the next random room
			$index = rand(0, count($open) - 1);
			$coordinates = array_splice($open, $index, 1);
			$coordinates = $coordinates[0];
			/* @var Coordinates $coordinates */
			$visited[$coordinates->getX()][$coordinates->getY()] = true;
			// Look for neighbors
			foreach (Direction::getDirections() as $direction) {
				$neighborCoordinates = $coordinates->getNeighbor($direction, $this->width, $this->height);
				if (!is_null($neighborCoordinates)) {
					// If not visited, push to $open
					if (!$visited[$neighborCoordinates->getX()][$neighborCoordinates->getY()]) {
						$open[] = $neighborCoordinates;
						$visited[$neighborCoordinates->getX()][$neighborCoordinates->getY()] = true;
						$this->setExit($coordinates->getX(), $coordinates->getY(), $direction, true);
					}
				}
			}
		}
		/**
		 * 1. Start at a particular cell and call it the "exit."
		 * 2. Mark the current cell as visited, and get a list of its neighbors.
		 * 		For each neighbor, starting with a randomly selected neighbor:
		 * 			a. If that neighbor hasn't been visited,
		 * 				remove the wall between this cell and that neighbor,
		 * 				and then recur with that neighbor as the current cell.
		 */
	}

	/**
	 * Gets an HTML-based interpretation of the maze.
	 *
	 * @return string HTML
	 */
	public function toHTML() {
		$s = '';
		$s .= sprintf('<div id="maze" data-x="%d" data-y="%d" data-width="%d" data-height="%d">' . PHP_EOL,
			$this->position->getX(),
			$this->position->getY(),
			$this->width,
			$this->height
		);
		for ($y = 0; $y < $this->height; $y++) {
			$s .= '<div class="row">';
			for ($x = 0; $x < $this->width; $x++) {
				$s .= '<div class="wall"></div>';
				if ($this->rooms[$x][$y]->isExit(Direction::NORTH)) {
					$s .= '<div class="door ns"></div>';
				}
				else {
					$s .= '<div class="wall ns"></div>';
				}
			}
			$s .= '<div class="wall"></div>';
			$s .= '</div>' . PHP_EOL;
			$s .= '<div class="row">';
			for ($x = 0; $x < $this->width; $x++) {
				if ($this->rooms[$x][$y]->isExit(Direction::WEST)) {
					$s .= '<div class="door ew"></div>';
				}
				else {
					$s .= '<div class="wall ew"></div>';
				}
				$s .= '<div class="room';
				$s .= ' room-' . $x . '-' . $y;
				if ($this->position->getX() == $x && $this->position->getY() == $y) {
					$s .= ' position';
				}
				$s .= '" data-exits="';
				foreach (Direction::getDirections() as $direction) {
					if ($this->rooms[$x][$y]->isExit($direction)) {
						$s .= $direction;
					}
				}
				$s .= '"';
				if ($treasure = $this->rooms[$x][$y]->getTreasure()) {
					$s .= " data-treasure='" . $treasure->toJSON() . "'";
				}
				$s .= '></div>';
			}
			$s .= '<div class="wall ew"></div>' . PHP_EOL;
			$s .= '</div>' . PHP_EOL;
		}
		$s .= '<div class="row">';
		$s .= str_repeat('<div class="wall"></div><div class="wall ns"></div>', $this->width) . '<div class="wall"></div>' . PHP_EOL;
		$s .= '</div>' . PHP_EOL;
		$s .= '</div>' . PHP_EOL;
		return $s;
	}

	/**
	 * Gets a string-based interpretation of the maze.
	 *
	 * @return string
	 */
	public function __toString() {
		$s = '';
		for ($y = 0; $y < $this->height; $y++) {
			for ($x = 0; $x < $this->width; $x++) {
				if ($this->rooms[$x][$y]->isExit(Direction::NORTH)) {
					$s .= '+ ';
				}
				else {
					$s .= '+-';
				}
			}
			$s .= '+' . PHP_EOL;
			for ($x = 0; $x < $this->width; $x++) {
				if ($this->rooms[$x][$y]->isExit(Direction::WEST)) {
					$s .= ' ';
				}
				else {
					$s .= '|';
				}
				if ($this->position->getX() == $x && $this->position->getY() == $y) {
					$s .= 'S';
				}
				elseif ($this->end->getX() == $x && $this->end->getY() == $y) {
					$s .= 'E';
				}
				else {
					$s .= ' ';
				}
			}
			$s .= '|' . PHP_EOL;
		}
		$s .= str_repeat('+-', $this->width) . '+' . PHP_EOL;
		return $s;
	}

	/**
	 * Maze constructor;
	 * must be at least 2x2.
	 *
	 * @var int $width
	 * @var int $height
	 */
	public function __construct($width, $height) {
		$this->width = $width;
		$this->height = $height;
		$this->generate();
	}
}
