<?php

namespace Lx\JsonRpcXp\Lib;


class ProtocolTest extends \PHPUnit_Framework_TestCase {

	private $object;

	public function setUp() {
		$this->object = new Protocol();
	}

	/**
	 * @return array
	 */
	public function protocolStringProvider() {
		return array(
			array(Protocol::JSONRPC_VERSION, true),
			array('2.0.1', false),
			array('2.0 something', false),
			array(2.0, false),
			array('', false),
			array('1.0', false),
			array('2.1', false),
			array('9999999999999999999999999', false),
			array(array(2,0), false),
			array(new \stdClass(), false),
		);
	}

	/**
	 * @test
	 * @testdox Protocol::validateJsonRpcVersion() properly checks version string
	 * @dataProvider protocolStringProvider
	 *
	 * @param mixed $version
	 * @param bool $expected
	 */
	public function validateJsonRpcVersion($version, $expected) {
		$this->assertEquals(
			$expected,
			$this->object->validateJsonRpcVersion($version)
		);
	}

	public function idProvider() {
		return array(
			array('some_id', true),
			array('some id', true),
			array(1234, true),
			array(12.34, true),
			array(null, true),
			array(array(1,2,3,4), false),
			array(new \stdClass(), false),
		);
	}

	/**
	 * @test
	 * @testdox Protocol::validateId() properly validates id
	 * @dataProvider idProvider
	 *
	 * @param mixed $id
	 * @param bool $expected
	 */
	public function validateId($id, $expected) {
		$this->assertEquals(
			$expected,
			$this->object->validateId($id)
		);
	}

	public function paramsProvider() {
		$object = new \stdClass();
		$object->foo = 'bar';
		$object->bar = 42;

		return array(
			array(array(), true),
			array(array(1, 2, 3), true),
			array(array('one' => 1, 'two' => 2, 'three' => 3), true),
			array(new \stdClass(), true),
			array($object, true),
			array(array('one' => 1, 2), false),
			array('string', false),
			array(42, false),
		);
	}

	/**
	 * @test
	 * @testdox Protocol::validateParams() properly validates params
	 * @dataProvider paramsProvider
	 *
	 * @param mixed $params
	 * @param bool $expected
	 *
	 * @todo Mock Tools::getArrayType() call in Protocol::validateParams()
	 */
	public function validateParams($params, $expected) {
		$this->assertEquals(
		     $expected,
			$this->object->validateParams($params)
		);
	}

	/**
	 * @return array
	 */
	public function methodProvider() {
		return array(
			array('some_method', true),
			array('SomeMethod', true),
			array('_someMethod', true),
			array('someMethöd', false),
			array('S0meM3thod', true),
			array('5omeMethod', false),
			array('S*meMethod', false),
			array(42, false),
			array(true, false),
		);
	}

	/**
	 * @test
	 * @testdox Protocol::validateMethod() properly validates method names
	 * @dataProvider methodProvider

	 * @param mixed $method
	 * @param bool $expected
	 */
	public function validateMethod($method, $expected) {
		$this->assertEquals(
			$expected,
			$this->object->validateMethod($method)
		);
	}

}
