<?php

require_once('treasure.php');

/**
 * Room class, used to form mazes.
 **/
class Room {
	/**
	 * The exits for the room.
	 *
	 * @var bool[]
	 */
	protected $exits;
	/**
	 * The treasure in the room, or null if there isn't any.
	 *
	 * @var Treasure|null
	 */
	protected $treasure;

	/**
	 * Sets the treasure in the room,
	 * or deletes it if set to null.
	 *
	 * @param Treasure|null $treasure
	 */
	public function setTreasure(Treasure $treasure = null) {
		$this->treasure = $treasure;
	}

	/**
	 * Room constructor.
	 *
	 * @param Treasure|null $treasure
	 */
	public function __construct($treasure = null) {
		$this->exits = array(
			Direction::NORTH => false,
			Direction::SOUTH => false,
			Direction::EAST => false,
			Direction::WEST => false,
		);
		$this->treasure = $treasure;
	}

	/**
	 * Gets the room's treasure,
	 * or null if it doesn't exist.
	 *
	 * @return null|Treasure
	 */
	public function getTreasure() {
		return $this->treasure;
	}

	/**
	 * Gets whether an exit is available or not
	 * given the direction.
	 *
	 * @var int $direction Direction constant
	 * @return boolean
	 */
	public function isExit($direction) {
		return $this->exits[$direction];
	}

	/**
	 * Sets whether an exit is available or not
	 * given the direction.
	 *
	 * @var int $direction Direction constant
	 * @param boolean $isExit
	 */
	public function setExit($direction, $isExit) {
		$this->exits[$direction] = $isExit;
	}
}
