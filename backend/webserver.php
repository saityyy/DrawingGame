<?php
// Your shell script
use MyApp\MyChat;

use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;

    require 'vendor/autoload.php';
    $ws = new WsServer(new MyChat);

    // Make sure you're running this as root
    $server = IoServer::factory(new HttpServer($ws),8080);
    $server->run();
