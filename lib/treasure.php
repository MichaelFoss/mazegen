<?php

/**
 * Treasure class used for creating items.
 **/
class Treasure {
	/**
	 * The name of the treasure.
	 *
	 * @var string
	 */
	protected $name = '';
	/**
	 * The amount of points for the treasure.
	 *
	 * @var int
	 */
	protected $points = 0;
	/**
	 * The expiration in steps for the treasure,
	 * or 0 if there is no expiration.
	 *
	 * @var int
	 */
	protected $expiration = 0;
	/**
	 * The frequency of a treasure appearing
	 * relative to all other treasures.
	 *
	 * @var int
	 */
	protected $frequency = 1;
	/**
	 * All treasures stored in
	 * the data/treasure.txt file,
	 * used for retrieving new Treasure objects
	 * and lazily-loaded the first time it happens;
	 * objects are keyed by their image names.
	 *
	 * @var Treasure[]
	 */
	protected static $treasures = null;
	/**
	 * The sum of all treasures' frequencies;
	 * used for obtaining a random treasure.
	 *
	 * @var int
	 */
	protected static $frequencySum = 0;

	/**
	 * Default constructor.
	 *
	 * @param string $name
	 * @param int $points
	 * @param int $expiration
	 * @param int $frequency
	 */
	public function __construct($name, $points, $expiration, $frequency) {
		$this->name = $name;
		$this->points = $points;
		$this->expiration = $expiration;
		$this->frequency = $frequency;
		self::$frequencySum += $frequency;
	}

	/**
	 * Given a treasure name, normalizes it by replacing all
	 * uppercase letters with lowercase ones and all
	 * non-alphanumeric characters with dashes.
	 *
	 * @param $name
	 * @return string
	 */
	protected static function normalizeName($name) {
		return preg_replace("/[^a-z0-9 ]/", '-', strtolower($name));
	}

	/**
	 * Loads all treasures from the treasures data file,
	 * storing them in the static $treasures variable.
	 */
	protected static function loadTreasures() {
		self::$treasures = array();
		$lines = explode("\n", file_get_contents(__DIR__ . '/../' . TREASURE_FILE));
		// Parse the lines
		foreach ($lines as $line) {
			$line = trim($line);
			// Skip blank lines
			if (!$line) {
				continue;
			}
			// Skip comments
			if (substr($line, 0, 1) == '#' || substr($line, 0, 1) == ';') {
				continue;
			}
			$parts = explode(',', $line);
			// Make sure we have all pieces
			if (count($parts) != 4) {
				continue;
			}
			for ($i = 0; $i < 4; $i++) {
				$parts[$i] = trim($parts[$i]);
			}
			// All treasures must have a name
			if (!$parts[0]) {
				continue;
			}
			// All treasures must have valid points
			// (though they can be negative! nasty!)
			if ($parts[1] * 1 != $parts[1]) {
				continue;
			}
			// All treasures must have valid expirations
			// (even if they're 0)
			if ($parts[2] * 1 != $parts[2] || $parts[2] < 0) {
				continue;
			}
			// All treasures must have valid frequencies
			if ($parts[3] * 1 != $parts[3] || $parts[3] < 0) {
				continue;
			}
			// Check for duplicate treasures
			$key = self::normalizeName($parts[0]);
			if (in_array($key, array_keys(self::$treasures))) {
				continue;
			}
			// Everything looks good, add the treasure
			self::$treasures[$key] = new Treasure($parts[0], $parts[1], $parts[2], $parts[3]);
		}
	}

	/**
	 * Gets a random treasure, or null if none exist.
	 *
	 * @return Treasure|null
	 */
	public static function get() {
		// Lazily-load the treasures if we haven't yet
		if (is_null(self::$treasures)) {
			self::loadTreasures();
		}
		// If we have no treasures, return null
		if (!count(self::$treasures)) {
			return null;
		}
		// Find a frequency between 1 and the max frequencies
		$chosenFrequency = rand(1, self::$frequencySum);
		// Iterate over each treasure, subtracting the frequency
		foreach (self::$treasures as $treasure) {
			$chosenFrequency -= $treasure->frequency;
			// If we've hit the bottom, that's our treasure
			if ($chosenFrequency <= 0) {
				return $treasure;
			}
		}
		// If for whatever reason we made it here, just return null
		// (though this should never happen)
		return null;
	}

	/**
	 * Determines if a random treasure is present or not
	 * for any given scenario (usualy room generation).
	 */
	public static function hasTreasure() {
		return (float)rand()/(float)getrandmax() <= (float)TREASURE_FREQUENCY;
	}

	/**
	 * Gets the name of the treasure.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Gets the amount of points for the treasure.
	 *
	 * @return int
	 */
	public function getPoints() {
		return $this->points;
	}

	/**
	 * Gets the expiration in steps for the treasure,
	 * or 0 if there is no expiration.
	 *
	 * @return int
	 */
	public function getExpiration() {
		return $this->expiration;
	}

	/**
	 * Gets the JSON equivalent of the object.
	 *
	 * @return string
	 */
	public function toJSON() {
		return json_encode(array(
			'id' => self::normalizeName($this->name),
			'name' => $this->name,
			'points' => $this->points,
			'expiration' => $this->expiration,
			'frequency' => $this->frequency,
		));
	}
}
