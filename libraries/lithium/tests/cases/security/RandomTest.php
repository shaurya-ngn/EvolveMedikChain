<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2016, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace lithium\tests\cases\security;

use lithium\security\Random;

class RandomTest extends \lithium\test\Unit {

	/**
	 * Tests the random number generator.
	 */
	public function testRandomGenerator() {
		$check = array();
		$count = 25;
		for ($i = 0; $i < $count; $i++) {
			$result = Random::generate(8);
			$this->assertFalse(in_array($result, $check));
			$check[] = $result;
		}
	}

	/**
	 * Tests the random number generator with base64 encoding.
	 */
	public function testRandom64Generator() {
		$check = array();
		$count = 25;
		$pattern = "/^[0-9A-Za-z\.\/]{11}$/";
		for ($i = 0; $i < $count; $i++) {
			$result = Random::generate(8, array('encode' => Random::ENCODE_BASE_64));
			$this->assertPattern($pattern, $result);
			$this->assertFalse(in_array($result, $check));
			$check[] = $result;
		}
	}
}

?>