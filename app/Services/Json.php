<?php
namespace Services;

final class Json {
  /**
   * Read and decode JSON body
   * Returns null if JSON is invalid
   * Returns empty array if body is empty
   */
  public static function readBody(): ?array {
    $raw = file_get_contents('php://input');

    // If empty body, return empty array
    if ($raw === '' || $raw === false) {
      return [];
    }

    $data = json_decode($raw, true);

    // If JSON is invalid, return null
    if (json_last_error() !== JSON_ERROR_NONE) {
      return null;
    }

    // Ensure the result is an array
    return is_array($data) ? $data : [];
  }

  /**
   * Check for JSON content-type
   */
  public static function isJsonContentType(): bool {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    return stripos($contentType, 'application/json') !== false;
  }
}
