<?php
// Health (non protégé)
$f3->route('GET /', function() {
  echo json_encode(['ok'=>true]);
});

// Auth (public)
$f3->route('POST /auth/register', 'Controllers\\AuthController->register');
$f3->route('POST /auth/login',    'Controllers\\AuthController->login');

// Notes (protégées via beforeroute dans le contrôleur)
$f3->route('GET    /api/v1/notes',        'Controllers\\NoteController->index');
$f3->route('GET    /api/v1/notes/@id',    'Controllers\\NoteController->show');
$f3->route('POST   /api/v1/notes',        'Controllers\\NoteController->store');
$f3->route('PUT    /api/v1/notes/@id',    'Controllers\\NoteController->update');
$f3->route('DELETE /api/v1/notes/@id',    'Controllers\\NoteController->destroy');
