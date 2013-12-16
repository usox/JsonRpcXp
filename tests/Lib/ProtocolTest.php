<?php
/**
 * JsonRpcXp
 *
 * Copyright (c) 2013-2014, Alexander Wühr <lx@boolshit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Alexander Wühr nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Lx\JsonRpcXp
 * @subpackage  Lib\Protocol
 * @author      Alexander Wühr <lx@boolshit.de>
 * @copyright   2013-2014 Alexander Wühr <lx@boolshit.de>
 * @license     http://opensource.org/licenses/MIT  The MIT License (MIT)
 * @link        https://github.com/l-x/JsonRpcXp
 */

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
