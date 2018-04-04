<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2016, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace lithium\tests\integration\analysis;

use lithium\core\Libraries;
use lithium\analysis\Logger;
use lithium\aop\Filters;

/**
 * Logger adapter integration test cases
 */
class LoggerTest extends \lithium\test\Integration {

	public function testWriteFilter() {
		$base = Libraries::get(true, 'resources') . '/tmp/logs';
		$this->skipIf(!is_writable($base), "Path `{$base}` is not writable.");

		Filters::apply('lithium\analysis\Logger', 'write', function($params, $next) {
			$params['message'] = 'Filtered Message';
			return $next($params);
		});

		$config = array('default' => array(
			'adapter' => 'File', 'timestamp' => false, 'format' => "{:message}\n"
		));
		Logger::config($config);

		$result = Logger::write('info', 'Original Message');
		$this->assertFileExists($base . '/info.log');

		$expected = "Filtered Message\n";
		$result = file_get_contents($base . '/info.log');
		$this->assertEqual($expected, $result);

		Filters::apply('lithium\analysis\Logger', 'write', false);
		unlink($base . '/info.log');
	}
}

?>