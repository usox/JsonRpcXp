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
 * * Redistributions of source code must retain the above copyright
 * notice, this list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright
 * notice, this list of conditions and the following disclaimer in
 * the documentation and/or other materials provided with the
 * distribution.
 *
 * * Neither the name of Alexander Wühr nor the names of his
 * contributors may be used to endorse or promote products derived
 * from this software without specific prior written permission.
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
 * @package Lx\JsonRpcXp
 * @subpackage Transport
 * @author Alexander Wühr <lx@boolshit.de>
 * @copyright 2014 Alexander Wühr <lx@boolshit.de>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link https://github.com/l-x/JsonRpcXp
 */

namespace Lx\JsonRpcXp\Transport;

use Lx\JsonRpcXp\Server;

/**
 * Class File
 *
 * @package Lx\JsonRpcXp\Transport
 */
class File implements ITransport {

	/**
	 * @var string Input file name
	 */
	protected $in;

	/**
	 * @var string output file name
	 */
	protected $out;

	/**
	 * Constructor for transport class
	 *
	 * @param string $input
	 * @param string $output
	 */
	public function __construct($input, $output) {
		$this->in = $input;
		$this->out = $output;
	}

	/**
	 * Read request from file
	 *
	 * @return string
	 */
	protected function read() {
		return file_get_contents($this->in);
	}

	/**
	 * Write response to file
	 *
	 * @param string $data
	 */
	protected function write($data) {
		file_put_contents($this->out, $data);
		return $this;
	}

	/**
	 * Read request, pass it to the server and write the response
	 *
	 * @param Server $server
	 *
	 * @return void
	 */
	public function handle(Server $server) {
		$this->write(
		     $server->handle(
		            $this->read()
		     )
		);
	}
}
