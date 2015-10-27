<?php

/**
 * Direction class, used to determine directions.
 */
class Direction {
	const NORTH = 'n';
	const SOUTH = 's';
	const EAST = 'e';
	const WEST = 'w';

	/**
	 * Gets an array of all possible directions.
	 *
	 * @return int[] Direction[]
	 */
	public static function getDirections() {
		return array(
			Direction::NORTH,
			Direction::EAST,
			Direction::SOUTH,
			Direction::WEST,
		);
	}
}