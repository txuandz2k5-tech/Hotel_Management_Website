<?php
namespace Shared\Auth;

class JWT {
    private static $secret = 'your-secret-key-change-this-in-production';

    /**
     * Encode JWT token
     */
    public static function encode($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $headerEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        $payload['iat'] = time();
        $payload['exp'] = time() + (24 * 60 * 60); // 24 hours
        $payloadEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

        $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, self::$secret, true);
        $signatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;
    }

    /**
     * Decode JWT token
     */
    public static function decode($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        $header = $parts[0];
        $payload = $parts[1];
        $signature = $parts[2];

        $expectedSignature = hash_hmac('sha256', $header . "." . $payload, self::$secret, true);
        $expectedSignatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));

        if (!hash_equals($signature, $expectedSignatureEncoded)) {
            return false;
        }

        $payloadDecoded = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);

        if ($payloadDecoded['exp'] < time()) {
            return false; // Token expired
        }

        return $payloadDecoded;
    }

    /**
     * Get token from Authorization header
     */
    public static function getTokenFromHeader() {
        $authHeader = null;

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (isset($headers['Authorization'])) {
                $authHeader = $headers['Authorization'];
            }
        }

        if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }

        if (!$authHeader && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Validate token and blacklist status
     */
    public static function validateToken() {
        $token = self::getTokenFromHeader();
        if (!$token) {
            return false;
        }

        if (self::isTokenBlacklisted($token)) {
            return false;
        }

        return self::decode($token);
    }

    /**
     * Blacklist a token so it cannot be used again
     */
    public static function blacklistToken($token) {
        $payload = self::decode($token);
        if (!$payload) {
            return false;
        }

        $blacklistFile = self::getBlacklistFilePath();
        $blacklist = self::loadBlacklist($blacklistFile);
        $blacklist[$token] = $payload['exp'];
        self::saveBlacklist($blacklistFile, $blacklist);
        return true;
    }

    /**
     * Check if token is blacklisted
     */
    public static function isTokenBlacklisted($token) {
        $blacklistFile = self::getBlacklistFilePath();
        $blacklist = self::loadBlacklist($blacklistFile);
        return isset($blacklist[$token]);
    }

    private static function getBlacklistFilePath() {
        $basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3);
        return $basePath . '/storage/token_blacklist.json';
    }

    private static function loadBlacklist($path) {
        if (!file_exists($path)) {
            return [];
        }

        $content = @file_get_contents($path);
        if (!$content) {
            return [];
        }

        $blacklist = json_decode($content, true);
        if (!is_array($blacklist)) {
            return [];
        }

        $currentTime = time();
        foreach ($blacklist as $token => $expiresAt) {
            if ($expiresAt <= $currentTime) {
                unset($blacklist[$token]);
            }
        }

        return $blacklist;
    }

    private static function saveBlacklist($path, $blacklist) {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            @mkdir($directory, 0755, true);
        }

        @file_put_contents($path, json_encode($blacklist));
    }
}