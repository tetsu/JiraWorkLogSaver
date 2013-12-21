<?php

require 'autoload.php';

class CI_Unit_Test extends PHPUnit_Framework_TestCase
{
	private $test_folder;
	private $test_uri;
	private $count = 0;
	private $tests = array();
	
	public function __construct()
	{
		$this->test_folder = APPPATH . 'controllers/tests';
		$this->test_uri = 'http://localhost:8888/index.php/tests/';
		
		if (is_dir($this->test_folder) === FALSE) {
			exit("$this->test_folder is not a folder!\n");
		}
		if (parse_url($this->test_uri) === FALSE) {
			exit("$this->test_uri is not a valid URI!\n");
		}
		
		$this->get_ci_unit_test();
	}

	private function get_ci_unit_test()
	{
		$passed = 0;
		$failed = 0;
		
		foreach (glob("$this->test_folder/*.php") as $filename) {
			require $filename;
			$filename = basename($filename, '.php');
			$class = ucfirst($filename);
			$methods = get_class_methods($class);
			$tests = array();
			
			// access to all methods which begin with test
			foreach ($methods as $method) {
				if (substr($method, 0, 4) === 'test') {
					//echo "\n$class::$method()\n";
					
					$test_uri = $this->test_uri . $filename . '/' . $method;
					$test_name = ' ' . $test_uri;
					
					$page = file_get_contents($test_uri);
					if ($page === FALSE) {
						exit("Can't get $test_uri\n");
					}
					
					$lines = explode("\n", $page);
					
					foreach ($lines as $line) {
						if (preg_match('!<span style="color: #C00;">Failed</span>!u', $line))
						{
							$tests[$test_name][] = 'Failed';
							$failed = 1;	// If there is a failed test, the test method ends
						}
						else if (preg_match('!<span style="color: #0C0;">Passed</span>!u', $line))
						{
							$tests[$test_name][] = 'Passed';
							$passed++;
						}
					}
					
					if ( ! array_key_exists($test_name, $tests)) {
						exit("No test results: $test_uri\n");
					}
				}
			}
		}
		
		$this->count = $passed + $failed;
		$this->tests = $tests;
	}
	
	public function count()
	{
		return $this->count;
	}

	public function test()
	{
		// Passed tests
		foreach ($this->tests as $test_name => $tests) {
			foreach ($tests as $test) {
				$expected = 'Passed';
				$actual = $test;

				if ($actual === $expected) {
					$this->setName($test_name);
					$this->assertSame($expected, $actual);
				}
			}
		}
		
		// Failed tests
		foreach ($this->tests as $test_name => $tests) {
			foreach ($tests as $test) {
				$expected = 'Passed';
				$actual = $test;

				if ($actual !== $expected) {
					$this->setName($test_name);
					$this->assertSame($expected, $actual);
				}
			}
		}
	}
}