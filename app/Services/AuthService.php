<?php
namespace Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Base;
use Exception;

class AuthService {
    public static function tokenFor(int $userId): string {
        $f3 = Base::instance();
        $payload = [
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + 3600*8
        ];
        return JWT::encode($payload, $f3->get('JWT_SECRET'), 'HS256');
    }

    public static function userIdOrNull(): ?int {
        $f3 = Base::instance();
        $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!preg_match('/Bearer\s+(.*)$/i', $hdr, $m)) return null;
        try {
            $decoded = JWT::decode($m[1], new Key($f3->get('JWT_SECRET'),'HS256'));
            return isset($decoded->sub) ? (int)$decoded->sub : null;
        } catch (Exception $e) {
            return null;
        }
    }
}
