<?php
namespace Controllers;
use Base;
use Models\NoteModel;
use Services\AuthService;
use App\Services\Http;

class NoteController {
  private NoteModel $notes; private ?int $userId = null;
  public function __construct(){ $this->notes = new NoteModel(Base::instance()->get('DB')); }

  // Hook appelé avant chaque action du contrôleur
  public function beforeroute() {
    $this->userId = AuthService::userIdOrNull();
    if (!$this->userId) { Http::error(401, 'unauthorized', 'Token missing or invalid'); exit; }
  }

  public function index() {
    $f3 = Base::instance();
    $limit = (int)($f3->get('GET.limit') ?? 50);
    $offset= (int)($f3->get('GET.offset') ?? 0);
    Http::ok($this->notes->allByUser($this->userId,$limit,$offset));
  }
  public function show($f3, $params) {
    $note = $this->notes->find($this->userId, (int)$params['id']);
    if (!$note) { Http::error(404, 'not found', 'Note not found'); return; }
    Http::ok($note);
  }
  public function store() {
    $in = json_decode(file_get_contents('php://input'), true) ?? [];
    if (!isset($in['title']) || trim($in['title'])==='') { Http::error(422, 'validation failed', 'Title is required'); return; }
    Http::created($this->notes->create($this->userId, $in));
  }
  public function update($f3, $params) {
    $in = json_decode(file_get_contents('php://input'), true) ?? [];
    $note = $this->notes->update($this->userId, (int)$params['id'], $in);
    if (!$note) { Http::error(404, 'not found', 'Note not found'); return; }
    Http::ok($note);
  }
  public function destroy($f3, $params) {
    $ok = $this->notes->delete($this->userId, (int)$params['id']);
    if (!$ok) { Http::error(404, 'not found', 'Note not found'); return; }
    Http::ok(['deleted'=>true]);
  }
}