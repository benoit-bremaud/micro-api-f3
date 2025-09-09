<?php
namespace Models;
use DB\SQL;

class NoteModel {
    private SQL $db;
    public function __construct(SQL $db) { $this->db = $db; }

    public function allByUser(int $userId, int $limit=50, int $offset=0): array {
        return $this->db->exec(
            'SELECT id,title,content,created_at,updated_at FROM notes
             WHERE user_id=? ORDER BY id DESC LIMIT ? OFFSET ?',
            [$userId,$limit,$offset]
        );
    }

    public function find(int $userId, int $id): ?array {
        $rows = $this->db->exec(
            'SELECT id,title,content,created_at,updated_at FROM notes
             WHERE user_id=? AND id=?',
            [$userId,$id]
        );
        return $rows[0] ?? null;
    }

    public function create(int $userId, array $data): array {
        $now = gmdate('c');
        $title = trim($data['title'] ?? '');
        $content = (string)($data['content'] ?? '');
        $this->db->exec(
            'INSERT INTO notes(user_id,title,content,created_at,updated_at)
             VALUES(?,?,?,?,?)',
            [$userId,$title,$content,$now,$now]
        );
        $id = (int)$this->db->lastInsertId();
        return $this->find($userId,$id);
    }

    public function update(int $userId, int $id, array $data): ?array {
        $note = $this->find($userId,$id);
        if (!$note) return null;
        $title = trim($data['title'] ?? $note['title']);
        $content = array_key_exists('content',$data) ? (string)$data['content'] : $note['content'];
        $now = gmdate('c');
        $this->db->exec(
            'UPDATE notes SET title=?, content=?, updated_at=?
             WHERE id=? AND user_id=?',
            [$title,$content,$now,$id,$userId]
        );
        return $this->find($userId,$id);
    }

    public function delete(int $userId, int $id): bool {
        $res = $this->db->exec(
            'DELETE FROM notes WHERE id=? AND user_id=?',
            [$id,$userId]
        );
        return $res !== false;
    }
}
