<?php
namespace Models; use DB\SQL;

class UserModel {
  private SQL $db;
  public function __construct(SQL $db) { $this->db = $db; }

  public function create(string $email, string $passwordHash): array {
    $now = gmdate('c');
    $this->db->exec('INSERT INTO users(email,password_hash,created_at) VALUES(?,?,?)', [$email,$passwordHash,$now]);
    $id = (int)$this->db->lastInsertId();
    return $this->findById($id);
  }
  public function findByEmail(string $email): ?array {
    $rows = $this->db->exec('SELECT id,email,password_hash,created_at FROM users WHERE email=?', [$email]);
    return $rows[0] ?? null;
  }
  public function findById(int $id): ?array {
    $rows = $this->db->exec('SELECT id,email,created_at FROM users WHERE id=?', [$id]);
    return $rows[0] ?? null;
  }
}