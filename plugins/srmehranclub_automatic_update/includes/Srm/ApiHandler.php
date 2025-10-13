<?php

class Srm_ApiHandler {
    private static string $apiKey;

    public static function register(): void {
        self::$apiKey = Srm_License::get_license_key();
    }

    public static function get(string $url, array $params = []): array {
        if (empty(self::$apiKey)) {
            return ['success' => false, 'error' => 'API key is missing'];
        }

        $query = http_build_query($params);
        $fullUrl = $url . '?' . $query;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GLBLICENSE_SERVER_URL.'/'.$fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer ' . self::$apiKey
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        return self::parseResponse($response, $httpCode, $error);
    }

   



    

    public static function post(string $url, array $data = []): array {
        if (empty(self::$apiKey)) {
            return ['success' => false, 'error' => 'API key is missing'];
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GLBLICENSE_SERVER_URL.'/'.$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . self::$apiKey
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        return self::parseResponse($response, $httpCode, $error);
    }

    private static function parseResponse($response, $httpCode, $error = ''): array {
        if (!empty($error)) {
            return ['success' => false, 'error' => $error];
        }
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'data' => json_decode($response, true)];
        }
        
        return ['success' => false, 'error' => json_decode($response, true) ?? 'Unknown error'];
    }
}