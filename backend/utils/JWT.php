<?php
/**
 * JWT Utility
 * JSON Web Token generation and verification
 */

class JWT {
    
    private static $secret;
    private static $algorithm = 'HS256';
    
    /**
     * Initialize JWT with secret key
     */
    public static function init() {
        self::$secret = JWT_SECRET;
    }
    
    /**
     * Generate JWT token
     */
    public static function generate($payload, $expiresIn = null) {
        self::init();
        
        $header = [
            'typ' => 'JWT',
            'alg' => self::$algorithm
        ];
        
        $issuedAt = time();
        $expire = $expiresIn ? $issuedAt + $expiresIn : $issuedAt + SESSION_LIFETIME;
        
        $tokenPayload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expire
        ]);
        
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($tokenPayload));
        
        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", self::$secret, true);
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }
    
    /**
     * Verify and decode JWT token
     */
    public static function verify($token) {
        self::init();
        
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return null;
        }
        
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
        
        $signature = self::base64UrlDecode($signatureEncoded);
        $expectedSignature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", self::$secret, true);
        
        if (!hash_equals($signature, $expectedSignature)) {
            return null;
        }
        
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        
        if (!$payload || !isset($payload['exp'])) {
            return null;
        }
        
        if ($payload['exp'] < time()) {
            return null; // Token expired
        }
        
        return $payload;
    }
    
    /**
     * Extract token from Authorization header
     */
    public static function extractFromHeader() {
        $headers = getallheaders();
        
        if (!isset($headers['Authorization'])) {
            return null;
        }
        
        $authHeader = $headers['Authorization'];
        
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Base64 URL encode
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL decode
     */
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
