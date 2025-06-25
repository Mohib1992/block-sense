<?php

namespace BlockSense\Util\Networking;

/**
 * HTTP Client for making GET and POST requests
 *
 * This class provides a simple interface for making HTTP requests using cURL.
 * It supports both GET and POST methods with custom headers and JSON payloads.
 *
 * @package BlockSense\Util\Networking
 * @author BlockSense
 * @since 1.0.0
 */
class HttpClient
{
    /** @var int HTTP status code for successful requests */
    public static int $HTTP_OK = 200;

    /** @var int HTTP status code for resource not found */
    public static int $HTTP_NOT_FOUND = 404;

    /**
     * Make a GET request to the specified URL
     *
     * Performs an HTTP GET request with optional custom headers.
     * The response is automatically parsed as JSON and returned as an array.
     *
     * @param string $url The URL to make the request to
     * @param array $headers Optional associative array of headers (key => value)
     * @return array The parsed JSON response as an associative array
     * @throws \RuntimeException When the HTTP request fails or JSON parsing fails
     * @example
     * $client = new HttpClient();
     * $response = $client->get('https://api.example.com/data', [
     *     'Authorization' => 'Bearer token123',
     *     'Accept' => 'application/json'
     * ]);
     */
    public function get(string $url, array $headers = []): array
    {
        // Initialize cURL session
        $ch = curl_init();

        // Configure cURL options for GET request
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,  // Return response as string instead of outputting
            CURLOPT_FOLLOWLOCATION => true,  // Follow redirects
            CURLOPT_HTTPHEADER => $this->prepareHeaders($headers),
            CURLOPT_TIMEOUT => 30,           // 30 second timeout
        ]);

        // Execute the request
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            throw new \RuntimeException('HTTP request failed: ' . curl_error($ch));
        }

        // Clean up cURL session
        curl_close($ch);

        // Parse and return the response
        return $this->parseResponse($response);
    }

    /**
     * Make a POST request to the specified URL with JSON data
     *
     * Performs an HTTP POST request with JSON payload and optional custom headers.
     * The request body is automatically JSON-encoded and the response is parsed as JSON.
     *
     * @param string $url The URL to make the request to
     * @param array $data The data to send in the request body (will be JSON-encoded)
     * @param array $headers Optional associative array of headers (key => value)
     * @return array The parsed JSON response as an associative array
     * @throws \RuntimeException When the HTTP request fails or JSON parsing fails
     * @example
     * $client = new HttpClient();
     * $response = $client->post('https://api.example.com/users', [
     *     'name' => 'John Doe',
     *     'email' => 'john@example.com'
     * ], [
     *     'Authorization' => 'Bearer token123'
     * ]);
     */
    public function post(string $url, array $data, array $headers = []): array
    {
        // Initialize cURL session
        $ch = curl_init();

        // JSON-encode the request data
        $payload = json_encode($data);

        // Configure cURL options for POST request
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,            // Use POST method
            CURLOPT_POSTFIELDS => $payload,  // Set the request body
            CURLOPT_RETURNTRANSFER => true,  // Return response as string
            CURLOPT_HTTPHEADER => array_merge(
                ['Content-Type: application/json'],  // Set JSON content type
                $this->prepareHeaders($headers)
            ),
        ]);

        // Execute the request
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            throw new \RuntimeException('HTTP request failed: ' . curl_error($ch));
        }

        // Clean up cURL session
        curl_close($ch);

        // Parse and return the response
        return $this->parseResponse($response);
    }

    /**
     * Prepare headers array for cURL
     *
     * Converts an associative array of headers into the format required by cURL.
     *
     * @param array $headers Associative array of headers (key => value)
     * @return array Array of header strings in format "Key: Value"
     * @example
     * $headers = ['Authorization' => 'Bearer token', 'Accept' => 'application/json'];
     * // Returns: ['Authorization: Bearer token', 'Accept: application/json']
     */
    private function prepareHeaders(array $headers): array
    {
        $prepared = [];
        foreach ($headers as $key => $value) {
            $prepared[] = "$key: $value";
        }
        return $prepared;
    }

    /**
     * Parse JSON response string into an array
     *
     * Decodes a JSON response string and returns it as an associative array.
     * Throws an exception if JSON parsing fails.
     *
     * @param string $response The JSON response string to parse
     * @return array The decoded JSON as an associative array
     * @throws \RuntimeException When JSON decoding fails
     */
    private function parseResponse(string $response): array
    {
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to decode JSON response');
        }
        return $data;
    }
}
