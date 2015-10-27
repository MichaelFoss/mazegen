<?php

require_once('direction.php');

/**
 * Coordinates used to identify locations in the maze.
 **/
class Coordinates {
	/**
	 * The zero-based X coordinate.
	 *
	 * @var int
	 */
	protected $x;
	/**
	 * The zero-based Y coordinate.
	 *
	 * @var int
	 */
	protected $y;

	/**
	 * Coordinates constructor.
	 *
	 * @param int $x
	 * @param int $y
	 */
	public function __construct($x, $y) {
		$this->x = $x;
		$this->y = $y;
	}

	/**
	 * Gets the zero-based X coordinate.
	 *
	 * @return int
	 */
	public function getX() {
		return $this->x;
	}

	/**
	 * Sets the zero-based X coordinate.
	 *
	 * @param int $x
	 */
	public function setX($x) {
		$this->x = $x;
	}

	/**
	 * Gets the zero-based Y coordinate.
	 *
	 * @return int
	 */
	public function getY() {
		return $this->y;
	}

	/**
	 * Sets the zero-based Y coordinate.
	 *
	 * @param int $y
	 */
	public function setY($y) {
		$this->y = $y;
	}

	/**
	 * Gets a string repesentation of the object.
	 *
	 * @return string
	 */
	public function __toString() {
		return sprintf('%d,%d',
			$this->x,
			$this->y
		);
	}

	/**
	 * Gets a neighboring coodinate pair in the direction requested;
	 * if the direction would result in an out-of-bounds error,
	 * returns null.
	 *
	 * @param int $direction Direction constant
	 * @param int $maxWidth
	 * @param int $maxHeight
	 * @return Coordinates|null
	 */
	public function getNeighbor($direction, $maxWidth = null, $maxHeight = null) {
		if ($this->x >= 1 && $direction == Direction::WEST) {
			return new Coordinates($this->x - 1, $this->y);
		}
		if ($this->y >= 1 && $direction == Direction::NORTH) {
			return new Coordinates($this->x, $this->y - 1);
		}
		if ($direction == Direction::EAST && (
				!is_null($maxWidth) && $this->x + 1 < $maxWidth ||
				is_null($maxWidth)
			)) {
			return new Coordinates($this->x + 1, $this->y);
		}
		if ($direction == Direction::SOUTH && (
				!is_null($maxHeight) && $this->y + 1 < $maxHeight ||
				is_null($maxHeight)
			)) {
			return new Coordinates($this->x, $this->y + 1);
		}
		return null;
	}
}
