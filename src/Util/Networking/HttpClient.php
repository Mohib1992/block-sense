<?php
namespace BlockSense\Util\Networking;

class HttpClient {
    public function get(string $url, array $headers = []): array {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => $this->prepareHeaders($headers),
            CURLOPT_TIMEOUT => 30,
        ]);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new \RuntimeException('HTTP request failed: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        return $this->parseResponse($response);
    }
    
    public function post(string $url, array $data, array $headers = []): array {
        $ch = curl_init();
        
        $payload = json_encode($data);
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array_merge(
                ['Content-Type: application/json'],
                $this->prepareHeaders($headers)
            ),
        ]);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new \RuntimeException('HTTP request failed: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        return $this->parseResponse($response);
    }
    
    private function prepareHeaders(array $headers): array {
        $prepared = [];
        foreach ($headers as $key => $value) {
            $prepared[] = "$key: $value";
        }
        return $prepared;
    }
    
    private function parseResponse(string $response): array {
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to decode JSON response');
        }
        return $data;
    }
}