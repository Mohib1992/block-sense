<?php

namespace BlockSense\Util\Networking;

class WebSocketServer
{
    private $server;
    private $clients = [];
    private $port;

    public function __construct(int $port = 8080)
    {
        $this->port = $port;
    }

    public function start(): void
    {
        $this->server = stream_socket_server("tcp://0.0.0.0:{$this->port}", $errno, $errstr);

        if (!$this->server) {
            throw new \RuntimeException("Failed to start WebSocket server: $errstr ($errno)");
        }

        $this->clients = [$this->server];

        while (true) {
            $read = $this->clients;
            $write = $except = null;

            if (stream_select($read, $write, $except, null) === false) {
                break;
            }

            foreach ($read as $client) {
                if ($client === $this->server) {
                    $this->handleNewConnection();
                } else {
                    $this->handleClientData($client);
                }
            }
        }
    }

    public function broadcast(string|array $message): void
    {

        $message = is_array($message) ? json_encode($message) : $message;


        foreach ($this->clients as $client) {
            if ($client !== $this->server) {
                fwrite($client, $this->frame($message));
            }
        }
    }

    private function handleNewConnection(): void
    {
        $client = stream_socket_accept($this->server);
        $this->handshake($client);
        $this->clients[] = $client;
    }

    private function handleClientData($client): void
    {
        $data = fread($client, 8192);

        if (strlen($data) === 0) {
            $this->removeClient($client);
            return;
        }

        // Handle WebSocket frames if needed
    }

    private function handshake($client): bool
    {
        $headers = [];
        $data = fread($client, 8192);

        if (preg_match('/Sec-WebSocket-Key: (.*)\r\n/', $data, $matches)) {
            $key = base64_encode(pack(
                'H*',
                sha1($matches[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')
            ));

            $headers = [
                "HTTP/1.1 101 Switching Protocols",
                "Upgrade: websocket",
                "Connection: Upgrade",
                "Sec-WebSocket-Accept: $key"
            ];

            fwrite($client, implode("\r\n", $headers) . "\r\n\r\n");
            return true;
        }

        return false;
    }

    private function frame(string $message): string
    {
        $length = strlen($message);

        if ($length <= 125) {
            return "\x81" . chr($length) . $message;
        } elseif ($length <= 65535) {
            return "\x81\x7E" . pack("n", $length) . $message;
        } else {
            return "\x81\x7F" . pack("J", $length) . $message;
        }
    }

    private function removeClient($client): void
    {
        $index = array_search($client, $this->clients);
        if ($index !== false) {
            fclose($client);
            unset($this->clients[$index]);
        }
    }
}
