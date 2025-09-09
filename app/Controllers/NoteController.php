<?php
namespace Controllers;
use Base;
use Models\NoteModel;
use Services\AuthService;

class NoteController {
    private NoteModel $notes; private ?int $userId = null;
    public function __construct(){ $this->notes = new NoteModel(Base::instance()->get('DB')); }

    public function beforeroute() {
        $this->userId = AuthService::userIdOrNull();
        if (!$this->userId) { http_response_code(401); echo json_encode(['error'=>'unauthorized']); exit; }
    }

    public function index() {
        $f3 = Base::instance();
        $limit = (int)($f3->get('GET.limit') ?? 50);
        $offset= (int)($f3->get('GET.offset') ?? 0);
        echo json_encode($this->notes->allByUser($this->userId,$limit,$offset));
    }

    public function show($f3, $params) {
        $note = $this->notes->find($this->userId, (int)$params['id']);
        if (!$note) { http_response_code(404); echo json_encode(['error'=>'not found']); return; }
        echo json_encode($note);
    }

    public function store() {
        $in = json_decode(file_get_contents('php://input'), true) ?? [];
        if (!isset($in['title']) || trim($in['title'])==='') { http_response_code(422); echo json_encode(['error'=>'title required']); return; }
        echo json_encode($this->notes->create($this->userId,$in));
    }

    public function update($f3, $params) {
        $in = json_decode(file_get_contents('php://input'), true) ?? [];
        $note = $this->notes->update($this->userId,(int)$params['id'],$in);
        if (!$note) { http_response_code(404); echo json_encode(['error'=>'not found']); return; }
        echo json_encode($note);
    }

    public function destroy($f3, $params) {
        $ok = $this->notes->delete($this->userId,(int)$params['id']);
        if (!$ok) { http_response_code(404); echo json_encode(['error'=>'not found']); return; }
        echo json_encode(['deleted'=>true]);
    }
}
