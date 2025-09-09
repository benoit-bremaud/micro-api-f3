<?php
namespace App\Services;

final class Http {
    public static function ok($data): void {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public static function created($data): void {
        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public static function error(int $code, string $error, string $message = ''): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => $error,
            'message' => $message
        ]);
    }
}
