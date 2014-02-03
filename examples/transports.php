<?php

require_once '../vendor/autoload.php';

use \Lx\JsonRpcXp\Server as Server;

use \Lx\JsonRpcXp\Transport;


$server = new Server();

/**
 * Simple http transport
 */
$transport = new Transport\Http();
$transport->handle($server);


/**
 * Read from file, respond to stdout
 */
$transport = new Transport\File('./request.json', 'php://stdout');
$transport->handle($server);
