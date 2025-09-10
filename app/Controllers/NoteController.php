<?php
namespace Controllers;
use Base;
use Models\NoteModel;
use Services\AuthService;
use Services\Http;
use Services\Json;

class NoteController {
  private NoteModel $notes; private ?int $userId = null;
  public function __construct(){ $this->notes = new NoteModel(Base::instance()->get('DB')); }

  // Hook appelé avant chaque action du contrôleur
  public function beforeroute() {
    $this->userId = AuthService::userIdOrNull();
    if (!$this->userId) { Http::error(401, 'unauthorized', 'Token missing or invalid'); exit; }
  }

  /**
   * List notes with pagination
   */
  public function index() {
    $f3 = Base::instance();
    $limit = (int)($f3->get('GET.limit') ?? 50);
    $offset= (int)($f3->get('GET.offset') ?? 0);
    Http::ok($this->notes->allByUser($this->userId,$limit,$offset));
  }

  /**
   * Show a single note
   */
  public function show($f3, $params) {
    $note = $this->notes->find($this->userId, (int)$params['id']);
    if (!$note) { Http::error(404, 'not found', 'Note not found'); return; }
    Http::ok($note);
  }

  /**
   * Create a new note
   */
  public function store() {
    // Is content-type JSON?
    if (!Json::isJsonContentType()) { Http::error(415, 'invalid content-type', 'Content-Type must be application/json'); return; }

    $in = Json::readBody();
    if ($in === null) {
        Http::badRequest('Invalid JSON payload');
        return;
    }

    $title = trim((string)($in['title'] ?? ''));
    if ($title === '') {
        Http::error(422, 'validation_failed', 'Title is required');
        return;
    }

    $in['title'] = $title;

    Http::created($this->notes->create($this->userId, $in));
  }

  /**
   * Update an existing note for the authenticated user.
   *
   * @param \Base $f3 The F3 framework instance.
   * @param array $params Route parameters, expects 'id' for the note ID.
   * @return void
   */
  public function update($f3, $params) {
    if (!Json::isJsonContentType()) { Http::error(415,'unsupported_media_type','Expected application/json'); return; }

    // Read and validate JSON body
    $in = Json::readBody();
    if ($in === null) {
        Http::badRequest('Invalid JSON payload');
        return;
    }

    // Validate title if provided
    $title = trim((string)($in['title'] ?? ''));
    if ($title === '') {
        Http::error(422, 'validation_failed', 'Title is required');
        return;
    }
    
    $in['title'] = $title;

    $note = $this->notes->update($this->userId, (int)$params['id'], $in);
    if (!$note) {
        Http::error(404, 'not_found', 'Note not found');
        return;
    }

    Http::ok($note);
  }
  
  /**
   * 
   */
  public function destroy($f3, $params) {
    $ok = $this->notes->delete($this->userId, (int)$params['id']);
    if (!$ok) { Http::error(404, 'not found', 'Note not found'); return; }
    Http::ok(['deleted'=>true]);
  }
}