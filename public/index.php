<?php
require __DIR__.'/../vendor/autoload.php';

$f3 = Base::instance();
$config = require __DIR__.'/../config/config.php';

$f3->set('DEBUG', $config['debug']);
$f3->set('AUTOLOAD', 'app/');

// DB SQLite (fichier local)
$dbPath = __DIR__.'/../data/app.db';
$f3->set('DB', new DB\SQL('sqlite:'.$dbPath));
$f3->set('JWT_SECRET', $config['jwt_secret']);

// Réponses JSON + CORS dev
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require __DIR__.'/../routes.php';
$f3->run();
