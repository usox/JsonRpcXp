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
 * @subpackage  Lib\Json
 * @author      Alexander Wühr <lx@boolshit.de>
 * @copyright   2013-2014 Alexander Wühr <lx@boolshit.de>
 * @license     http://opensource.org/licenses/MIT  The MIT License (MIT)
 * @link        https://github.com/l-x/JsonRpcXp
 */

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
