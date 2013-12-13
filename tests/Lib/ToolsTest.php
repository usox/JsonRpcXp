<?php

namespace Lx\JsonRpcXp\Lib;


class ToolsTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \Lx\JsonRpcXp\Lib\Tools
	 */
	private $object;

	public function setUp() {
		$this->object = new Tools();
	}

	/**
	 * @return array
	 */
	public function arrayProvider() {
		return array(
			array(array(), Tools::ARRAY_TYPE_EMPTY),
			array(array(1, 2, 3), Tools::ARRAY_TYPE_LIST),
			array(array('foo' => 'bar'), Tools::ARRAY_TYPE_DICT),
			array(array('foo' => 'bar', 42), Tools::ARRAY_TYPE_MIXED),
		);
	}

	/**
	 * @test
	 * @testdox Tools::getArrayType() detects correct array type
	 * @dataProvider arrayProvider
	 * 
	 * @param array $array
	 * @param int $expected
	 */
	public function getArrayType($array, $expected) {
		$this->assertEquals(
			$expected,
			$this->object->getArrayType($array)
		);
	}
}
