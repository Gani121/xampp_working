<?php
require dirname(__DIR__) . '\websocket_demo\vendor\autoload.php'; // Adjust path if necessary

// Disable deprecated warnings (temporary solution)
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\App;

class WebSocketServer implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;  // To store connected clients
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Broadcast message to all clients except the sender
        echo "Message from {$from->resourceId}: $msg\n";

        // Parse the message (assuming it's JSON formatted)
        $data = json_decode($msg, true);

        // Example routing logic: Route data from 'user.html' to 'hotel.html'
        if (isset($data['target']) && $data['target'] === 'hotel') {
            // Send the message to all clients except the sender
            foreach ($this->clients as $client) {
                if ($client !== $from) {
                    $client->send($msg);  // Send the message
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Remove the connection from the clients list
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
    }
}

$server = new App('localhost', 9999);
$server->route('/', new WebSocketServer);
$server->run();

echo "Server started on 9999\n";
