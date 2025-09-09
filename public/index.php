<?php
require __DIR__ . '/../vendor/autoload.php';

$f3 = \Base::instance();
$f3->set('DEBUG', 3);
$f3->set('AUTOLOAD', 'app/'); // charge app/Controllers, app/Models, etc.
$f3->set('JWT_SECRET', getenv('JWT_SECRET') ?: 'dev-secret-change-me');

// DB SQLite (fichier: data/app.db)
$f3->set('DB', new DB\SQL('sqlite:' . __DIR__ . '/../data/app.db'));

// JSON & CORS minimal pour tests
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

// Charger routes
require __DIR__ . '/../routes.php';

$f3->run();
