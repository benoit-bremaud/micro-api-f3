<?php
require __DIR__.'/../vendor/autoload.php';

$f3 = Base::instance();
$dbPath = __DIR__.'/../data/app.db';
$f3->set('DB', new DB\SQL('sqlite:'.$dbPath));
$db = $f3->get('DB');

$db->exec('CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  email TEXT UNIQUE NOT NULL,
  password_hash TEXT NOT NULL,
  created_at TEXT NOT NULL
)');

$db->exec('CREATE TABLE IF NOT EXISTS notes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  title TEXT NOT NULL,
  content TEXT DEFAULT "",
  created_at TEXT NOT NULL,
  updated_at TEXT NOT NULL,
  FOREIGN KEY(user_id) REFERENCES users(id)
)');

echo "✅ Migrated\n";
