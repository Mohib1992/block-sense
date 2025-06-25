<?php

namespace BlockSense\Providers;

/**
 * Interface for WebSocket server implementations.
 *
 * This interface defines the contract for WebSocket server implementations
 * that can broadcast messages to connected clients. It provides a standardized
 * way to send messages to all connected WebSocket clients.
 *
 * @package BlockSense\Providers
 * @since 1.0.0
 */
interface WebSocketServerInterface
{
    /**
     * Broadcasts a message to all connected WebSocket clients.
     *
     * This method sends the specified message to all currently connected
     * WebSocket clients. The message is typically sent as a JSON string
     * or plain text depending on the implementation.
     *
     * @param string $message The message to broadcast to all connected clients.
     *                        This should be a valid string that can be sent
     *                        over the WebSocket connection.
     *
     * @return void This method does not return a value.
     *
     * @throws \RuntimeException If the broadcast operation fails due to
     *                          network issues, server errors, or other
     *                          connection problems.
     *
     * @example
     * ```php
     * $webSocketServer = new WebSocketServer();
     * $webSocketServer->broadcast('{"type": "notification", "message": "Hello World"}');
     * ```
     */
    public function broadcast(string $message): void;
}
