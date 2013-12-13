<?php

namespace Lx\JsonRpcXp\Lib;

require_once __DIR__.'/../../src/autoload.php';

class JsonTest extends \PHPUnit_Framework_TestCase {

	private $object;

	public function setUp() {
		$this->object = new Json();
	}

	public function encodeDecodeProvider() {
		return array(
			array(123456, 123456),
			array('123456', '"123456"'),
			array(array(1, 2, 3, 4, 5, 6), '[1,2,3,4,5,6]'),
			array(array("one" => '1', 'two' => '2', 'three' => 3), '{"one":"1","two":"2","three":3}'),
			array(array("one" => 1, 2), '{"one":1,"0":2}'),
		);
	}

	/**
	 * @test
	 * @testdox Json::encode() properly encodes data
	 *
	 * @dataProvider encodeDecodeProvider
	 * @param mixed $in
	 * @param string $expected
	 */
	public function encode($in, $expected) {
		$this->assertEquals($expected, $this->object->encode($in));
	}

	/**
	 * @test
	 * @testdox Json::decode() properly decodes data
	 *
	 * @dataProvider encodeDecodeProvider
	 *
	 * @param mixed $expected
	 * @param string $in
	 */
	public function decode($expected, $in) {
		$this->assertEquals($expected, $this->object->decode($in));
	}

	/**
	 * @test
	 * @testdox Json::decode() throws exception on invalid json input
	 *
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Could not parse json string
	 */
	public function decodeInvalidJson() {
		$this->object->decode('"Invalid json string');
	}
}
